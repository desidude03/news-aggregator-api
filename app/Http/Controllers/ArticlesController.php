<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArticlesRequest;
use App\Http\Requests\UpdateArticlesRequest;
use App\Models\Articles;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

class ArticlesController extends BaseController
{
    /**
     * Display a listing of the resource.
     */

     /**
     * @group Articles
     *
     * Display a listing of the resource.
     *
     * @queryParam per_page int The number of results per page. Defaults to 10. Example: 10
     * @queryParam page int The page number. Defaults to 1. Example: 1
     * @queryParam keyword string Search keyword for title or content. Example: Sport
     * @queryParam start_date string The start date for filtering articles. Format: YYYY-MM-DD. Example: 2024-01-01
     * @queryParam end_date string The end date for filtering articles. Format: YYYY-MM-DD. Example: 2024-12-31
     * @queryParam category string The category name to filter articles by. Example: Technology
     * @queryParam source string The source name to filter articles by. Example: TechCrunch
     *
     * @header Authorization string The Bearer token used for authentication. Example: Bearer <your token>
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
     *       }
     *     }
     *   ],
     *   "meta": {
     *     "total": 100,
     *     "per_page": 10,
     *     "current_page": 1,
     *     "last_page": 10
     *   }
     * }
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 10); 
        $page = $request->query('page', 1);
        $keyword = $request->query('keyword');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $category = $request->query('category');
        $source = $request->query('source');

        $query = Articles::query();
        // Search filters
        if ($keyword) {
            $query->where('title', 'like', "%{$keyword}%")
                  ->orWhere('content', 'like', "%{$keyword}%");
        }

        if ($startDate) {
            $query->whereDate('published_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('published_at', '<=', $endDate);
        }

        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        if ($source) {
            $query->where('source_name', $source);
        }

        $articles = $query->paginate($perPage, ['*'], 'page', $page);

        $response = $articles->toArray();
        unset($response['links']); // Remove 'links'

        return response()->json($response);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Articles $articles)
    {
        //
    }

    /**
     * Display the specified resource.
     */

    /**
     * @group Articles
     *
     * Display the specified resource.
     *
     * @urlParam id int The ID of the article. Example: 1
     *
     * @response 200 {
     *   "id": 1,
     *   "title": "Article Title",
     *   "content": "Article content here.",
     *   "published_at": "2024-01-01T00:00:00Z",
     *   "source_name": "TechCrunch",
     *   "category": {
     *     "name": "Technology"
     *   }
     * }
     * 
     * @response 404 {
     *   "message": "Resource not found"
     * }
     */
    public function show($id)
    {
        $article = Articles::findOrFail($id);

        return response()->json($article);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Articles $articles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticlesRequest $request, Articles $articles)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Articles $articles)
    {
        //
    }
}
