<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\BaseController;
use App\Models\Articles;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;

class UserController extends BaseController
{
  /**
 * @group User Preferences
 *
 * Set user preferences for sources, categories, and authors.
 *
 * @bodyParam sources string[]|null The list of preferred sources. Must be an array. Example: ["TechCrunch", "Wired"]
 * @bodyParam categories string[]|null The list of preferred categories. Must be an array. Example: ["Technology", "Science"]
 * @bodyParam authors string[]|null The list of preferred authors. Must be an array. Example: ["John Doe", "Jane Smith"]
 *
 * @header Authorization string The Bearer token used for authentication. Example: Bearer <token>
 *
 * @response 200 {
 *   "message": "Preferences updated successfully"
 * }
 * @response 422 {
 *   "message": "The sources field must be an array.",
 *   "errors": {
 *     "sources": [
 *       "The sources field must be an array."
 *     ]
 *   }
 * }
 */
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

    /**
     * @group User Preferences
     *
     * Get the current user preferences.
     *
     * @header Authorization string The Bearer token used for authentication. Example: Bearer <token>
     *
     * @response 200 {
     *   "sources": ["TechCrunch", "Wired"],
     *   "categories": ["Technology", "Science"],
     *   "authors": ["John Doe", "Jane Smith"]
     * }
     */
    public function getPreferences(Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        $preferences = $user->preferences()->first();

        return response()->json($preferences);
    }

    /**
     * @group Personalized Feed
     *
     * Get the personalized feed of articles based on user preferences.
     *
     * @header Authorization string The Bearer token used for authentication. Example: Bearer <token>
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Article Title",
     *       "content": "Article content here.",
     *       "published_at": "2024-01-01T00:00:00Z",
     *       "source_name": "TechCrunch",
     *       "category": {
     *         "name": "Technology"
     *       },
     *       "author": {
     *         "name": "John Doe"
     *       }
     *     }
     *   ]
     * }
     * 
     * @response 404 {
     *   "message": "No personalized feed available"
     * }
     */
    public function getPersonalizedFeed(Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        $preferences = $user->preferences()->first();

        $query = Articles::query();

        if ($preferences) {
            if ($preferences->sources) {
                $query->whereIn('source_name', $preferences->sources);
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

        // dd($query->toSql());

        return response()->json($articles);
    }
}