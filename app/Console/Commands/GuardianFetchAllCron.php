<?php

namespace App\Console\Commands;

use App\Models\Articles; // Replace with your actual model name
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class GuardianFetchAllCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guardian:fetchAll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from the Guardian API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $apiKey = env('GUARDIAN_API_KEY'); // Make sure you update api key in your env
         $apiKey = 'test';
        if (!$apiKey) {
            $this->error('Guardian API key is not set in the environment file.');
            return 1;
        }

        $url = 'https://content.guardianapis.com/search';
        $params = [
            'api-key' => $apiKey,
            'show-fields' => 'headline,body,thumbnail', // Adjust fields as needed
            'page' => 1, // Initial page number
        ];
        $constructedUrl = $url . '?' . http_build_query($params);
        $apiUrl = Http::get($constructedUrl);

        $allArticles = [];

        do {
            $response = Http::get($url, $params);

            if ($response->successful()) {
                $data = $response->json()['response']['results'];
                $allArticles = array_merge($allArticles, $data);

                // Check if there are more pages to fetch
                $currentPage = $response->json()['response']['currentPage'];
                $totalPages = $response->json()['response']['pages'];
                $params['page'] = $currentPage + 1;
            } else {
                $this->error('Failed to fetch articles from the Guardian API.'.$apiUrl);
                break;
            }
        } while ($currentPage < $totalPages);

        // Process and save articles
        foreach ($allArticles as $articleData) {
            $article = Articles::updateOrCreate(
                [
                    'url' => $articleData['id'], // Use unique identifier from Guardian API
                ],
                [
                    'title' => $articleData['fields']['headline'],
                    'author' => $articleData['fields']['byline'] ?? null, // Handle potential missing author
                    'description' => $articleData['webTitle'] ?? null, // Adjust based on API response
                    'url' => $articleData['id'], // Use unique identifier from Guardian API
                    'url_to_image' => $articleData['fields']['thumbnail'] ?? null, // Handle potential missing image
                    'published_at' => Carbon::parse($articleData['webPublicationDate']),
                    'content' => strip_tags($articleData['fields']['body']), // Extract content and remove HTML tags
                    'source_name' => 'The Guardian',
                    'category_id' => $randomNumber = rand(1, 3),
                ]
            );
        }

        $this->info('Articles fetched and updated successfully.'.$apiUrl);

        return 0;
    }
}
