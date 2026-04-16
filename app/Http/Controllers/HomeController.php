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

        foreach ($usernames as $username) {
            $username = trim($username);
            if (!$username) continue;

            $data = $this->fetchContributions($username);
            $usersData[] = $data;

            if (isset($data['data']['user'])) {
                $profiles[] = [
                    'name' => $data['data']['user']['name'],
                    'avatar' => $data['data']['user']['avatarUrl'],
                    'url' => $data['data']['user']['url'],
                    'username' => $username
                ];
            }
        }

        $merged = $this->mergeContributions($usersData);

        $repos = $this->fetchRepositories($usernames);

        return response()->json([
            'profiles' => $profiles,
            'merged' => $merged,
            'repos' => $repos // ✅ NEW
        ]);
    }

    /**
     * Fetch contributions from GitHub
     */
    function fetchContributions($username) {
        $query = '
        query($username:String!) {
            user(login: $username) {
                name
                avatarUrl
                url
                contributionsCollection {
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
            'variables' => ['username' => $username]
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
}
