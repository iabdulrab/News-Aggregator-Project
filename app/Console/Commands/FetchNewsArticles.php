<?php

namespace App\Console\Commands;

use App\Services\NewsAggregatorService;
use Illuminate\Console\Command;

class FetchNewsArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch 
                            {--sources= : Comma-separated list of sources to fetch from}
                            {--from_date= : Fetch articles from this date (Y-m-d)}
                            {--to_date= : Fetch articles until this date (Y-m-d)}
                            {--search_query= : Search query}
                            {--article_category= : Category filter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news articles from configured sources';

    /**
     * Execute the console command.
     */
    public function handle(NewsAggregatorService $aggregatorService): int
    {
        $this->info('Starting to fetch news articles...');

        // Prepare parameters
        $params = array_filter([
            'from_date' => $this->option('from_date'),
            'to_date' => $this->option('to_date'),
            'search_query' => $this->option('search_query'),
            'article_category' => $this->option('article_category'),
        ]);

        // Fetch articles
        $sources = $this->option('sources');
        if ($sources) {
            $sourceKeys = array_map('trim', explode(',', $sources));
            $stats = $aggregatorService->fetchFromSpecificSources($sourceKeys, $params);
        } else {
            $stats = $aggregatorService->fetchAndStoreAllArticles($params);
        }

        // Display results
        $this->newLine();
        $this->info("Fetch completed!");
        $this->table(
            ['Source', 'Fetched', 'Stored'],
            collect($stats['sources'])->map(function ($data, $source) {
                return [
                    $source,
                    $data['fetched'] ?? 0,
                    $data['stored'] ?? 0,
                ];
            })->toArray()
        );

        $this->newLine();
        $this->info("Total: {$stats['total_fetched']} fetched, {$stats['total_stored']} stored");

        return Command::SUCCESS;
    }
}

