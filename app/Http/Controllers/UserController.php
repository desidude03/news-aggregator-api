<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\BaseController;
use App\Models\Articles;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function setPreferences(Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        $preferences = $request->validate([
            'sources' => 'array|nullable',
            'categories' => 'array|nullable',
            'authors' => 'array|nullable',
        ]);

        // Update user preferences
        $user->preferences()->updateOrCreate([], $preferences);

        return response()->json(['message' => 'Preferences updated successfully']);
    }

    public function getPreferences(Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        $preferences = $user->preferences()->first();

        return response()->json($preferences);
    }

    public function getPersonalizedFeed(Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        $preferences = $user->preferences()->first();

        $query = Articles::query();

        if ($preferences) {
            if ($preferences->sources) {
                $query->whereIn('source', $preferences->sources);
            }

            if ($preferences->categories) {
                $query->whereHas('category', function ($q) use ($preferences) {
                    $q->whereIn('name', $preferences->categories);
                });
            }

            if ($preferences->authors) {
                $query->whereHas('author', function ($q) use ($preferences) {
                    $q->whereIn('name', $preferences->authors);
                });
            }
        }

        $articles = $query->latest()->take(10)->get();

        return response()->json($articles);
    }
}