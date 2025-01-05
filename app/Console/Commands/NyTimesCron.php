<?php

namespace App\Console\Commands;

use App\Models\Articles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class NyTimesCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:nyTimes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from the New York Times API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = env('NYTIMES_API_KEY'); // Make sure you update api key in your env
        if (!$apiKey) {
            $this->error('NyTimes API key is not set in the environment file.');
            return 1;
        }

            $apiUrl = 'https://api.nytimes.com/svc/topstories/v2/world.json?api-key=' . $apiKey;
            $response = Http::get($apiUrl);

            if ($response->successful()) {
                $data = $response->json()['results'];

                foreach ($data as $articleData) {
                    $article = Articles::updateOrCreate(
                        [ 
                            'title' => $articleData['title'],
                            'author' => $articleData['author'] ?? null, // Handle potential missing author
                            'description' => $articleData['abstract'], 
                            'url' => $articleData['url'],
                            'url_to_image' => $articleData['multimedia'][0]['url'] ?? null, // Example for handling image URL
                            'published_at' => $articleData['published_date'],
                            'content' => $articleData['abstract'], // You might need to adjust based on API response
                            'source_name' => 'New York Times', 
                            'category_id' => $randomNumber = rand(1, 3),
                        ]
                    );
                }

                $this->info('Articles fetched and updated successfully.'.$apiUrl);
            } else {
                $this->error('Failed to fetch articles from the New York Times API.'.$apiUrl);
            }

        return 0;
    }
}
