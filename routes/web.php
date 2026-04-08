<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

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

/**
 * API route to handle usernames
 */
Route::post('/merge', function (Request $request) {

    $usernames = explode(',', $request->usernames);

    $usersData = [];
    $profiles = [];

    foreach ($usernames as $username) {
        $username = trim($username);
        if (!$username) continue;

        $data = fetchContributions($username);
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

    $merged = mergeContributions($usersData);

    return response()->json([
        'profiles' => $profiles,
        'merged' => $merged
    ]);
});
