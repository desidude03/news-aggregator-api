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

        // if ($category) {
        //     $query->whereHas('category', function ($q) use ($category) {
        //         $q->where('name', $category);
        //     });
        // }

        if ($source) {
            $query->where('source_name', $source);
        }

        $articles = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json($articles);
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
    public function store($id)
    {
        $article = Articles::findOrFail($id);

        return response()->json($article);
    }

    /**
     * Display the specified resource.
     */
    public function show(Articles $articles)
    {
        //
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
