<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Articles;
use Carbon\Carbon;

class NewsAPICron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:newsApi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = env('NEWSAPI_API_KEY'); // Make sure you update api key in your env
        if (!$apiKey) {
            $this->error('API key is not set in the environment file.');
            return 1;
        }
        // List of news API endpoints
        $newsApis = [
            'https://newsapi.org/v2/top-headlines?country=us&apiKey='.$apiKey,
            'https://newsapi.org/v2/everything?q=bitcoin&apiKey='.$apiKey,
        ];

        foreach ($newsApis as $apiUrl) {
            $response = Http::get($apiUrl);

            if ($response->successful()) {
                $articles = $response->json('articles');

                foreach ($articles as $articleData) {
                    // Store or update the article in the database
                    Articles::updateOrCreate(
                        [
                            'title' => $articleData['title'],
                            'source_name' => $articleData['source']['name'],
                        ],
                        [
                            'author' => $articleData['author'] ?? null,
                            'description' => $articleData['description'] ?? null,
                            'url' => $articleData['url'],
                            'url_to_image' => $articleData['urlToImage'] ?? null,
                            'published_at' => Carbon::parse($articleData['publishedAt']),
                            'content' => $articleData['content'] ?? null,
                            'category_id' => rand(1, 6),

                        ]
                    );
                }

                $this->info('Articles fetched and updated successfully from ' . $apiUrl);
            } else {
                $this->error('Failed to fetch articles from ' . $apiUrl);
            }
        }

        return 0;
    }
}
