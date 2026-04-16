<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function merge(Request $request)
    {
        $usernames = explode(',', $request->usernames);

        $usersData = [];
        $profiles = [];
        $years = [];

        foreach ($usernames as $username) {
            $username = trim($username);
            if (!$username) continue;

            $profileData = $this->fetchProfile($username);

            if (!$profileData) continue;

            $createdYear = (int)date('Y', strtotime($profileData['created_at']));
            $years[] = $createdYear;

            $profiles[] = [
                'name' => $profileData['name'] ?? null,
                'avatar' => $profileData['avatar_url'] ?? null,
                'url' => $profileData['html_url'] ?? null,
                'username' => $username,
                'created_year' => $createdYear
            ];
        }

        $minYear = count($years) ? min($years) : date('Y');
        $maxYear = date('Y');

        // ✅ Fetch contributions for all years (from minYear to maxYear)
        foreach ($usernames as $username) {

            $username = trim($username);
            if (!$username) continue;

            for ($y = $minYear; $y <= $maxYear; $y++) {

                $from = $y . "-01-01T00:00:00Z";
                $to   = $y . "-12-31T23:59:59Z";

                $data = $this->fetchContributions($username, $from, $to);
                $usersData[] = $data;
            }
        }

        $merged = $this->mergeContributions($usersData);
        $repos = $this->fetchRepositories($usernames);

        return response()->json([
            'profiles' => $profiles,
            'merged' => $merged,
            'repos' => $repos,
            'yearRange' => [
                'min' => $minYear,
                'max' => $maxYear
            ]
        ]);
    }

    /**
     * Fetch contributions from GitHub
     */
    function fetchContributions($username, $from = null, $to = null)
    {
        $query = '
        query($username:String!, $from:DateTime, $to:DateTime) {
            user(login: $username) {
                name
                avatarUrl
                url
                contributionsCollection(from: $from, to: $to) {
                    contributionCalendar {
                        weeks {
                            contributionDays {
                                date
                                contributionCount
                            }
                        }
                    }
                }
            }
        }';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GITHUB_TOKEN'),
        ])->post('https://api.github.com/graphql', [
            'query' => $query,
            'variables' => [
                'username' => $username,
                'from' => $from,
                'to' => $to
            ]
        ]);

        return $response->json();
    }

    /**
     * Merge contributions
     */
    function mergeContributions($usersData) {
        $merged = [];

        foreach ($usersData as $data) {
            if (!isset($data['data']['user'])) continue;

            $weeks = $data['data']['user']['contributionsCollection']['contributionCalendar']['weeks'];

            foreach ($weeks as $week) {
                foreach ($week['contributionDays'] as $day) {
                    $date = $day['date'];

                    if (!isset($merged[$date])) {
                        $merged[$date] = 0;
                    }

                    $merged[$date] += $day['contributionCount'];
                }
            }
        }

        return $merged; 
    }

    function fetchRepositories($usernames)
    {
        $allRepos = [];

        $usernames = array_filter(array_map('trim', $usernames));
        $totalUsers = count($usernames);

        if ($totalUsers === 0) return [];

        // ✅ Dynamic per user
        $perUser = ceil(6 / $totalUsers);

        foreach ($usernames as $username) {

            $response = Http::get("https://api.github.com/users/{$username}/repos", [
                'sort' => 'stars',
                'per_page' => $perUser
            ]);

            if ($response->successful()) {
                $repos = $response->json();

                foreach ($repos as $repo) {
                    $allRepos[] = [
                        'name' => $repo['name'],
                        'url' => $repo['html_url'],
                        'stars' => $repo['stargazers_count'],
                        'language' => $repo['language'],
                        'owner' => $username
                    ];
                }
            }
        }

        // Sort all repos by stars
        usort($allRepos, function ($a, $b) {
            return $b['stars'] <=> $a['stars'];
        });

        // ✅ Always return max 6
        return array_slice($allRepos, 0, 6);
    }

    function fetchProfile($username)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GITHUB_TOKEN'),
        ])->get("https://api.github.com/users/{$username}");

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }
}
