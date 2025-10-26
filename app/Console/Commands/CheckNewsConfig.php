<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckNewsConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:check-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check news aggregator API configuration';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking News Aggregator Configuration...');
        $this->newLine();

        $allConfigured = true;

        // Check NewsAPI
        $newsApiKey = config('services.newsapi.api_key');
        if (empty($newsApiKey)) {
            $this->error('❌ NewsAPI: NOT CONFIGURED');
            $this->line('   Set NEWSAPI_KEY in .env file');
            $this->line('   Get key at: https://newsapi.org/register');
            $allConfigured = false;
        } else {
            $this->info('✓ NewsAPI: CONFIGURED');
        }

        $this->newLine();

        // Check Guardian API
        $guardianKey = config('services.guardian_api.api_key');
        if (empty($guardianKey)) {
            $this->error('❌ Guardian API: NOT CONFIGURED');
            $this->line('   Set GUARDIAN_API_KEY in .env file');
            $this->line('   Get key at: https://open-platform.theguardian.com/access/');
            $allConfigured = false;
        } else {
            $this->info('✓ Guardian API: CONFIGURED');
        }

        $this->newLine();

        // Check NYT API
        $nytKey = config('services.nyt_api.api_key');
        if (empty($nytKey)) {
            $this->error('❌ New York Times API: NOT CONFIGURED');
            $this->line('   Set NYT_API_KEY in .env file');
            $this->line('   Get key at: https://developer.nytimes.com/get-started');
            $allConfigured = false;
        } else {
            $this->info('✓ New York Times API: CONFIGURED');
        }

        $this->newLine();

        if ($allConfigured) {
            $this->info('🎉 All API keys are configured! You can now run: php artisan news:fetch');
            return Command::SUCCESS;
        } else {
            $this->warn('⚠️  Some API keys are missing. Please configure them in your .env file.');
            $this->newLine();
            $this->info('Example .env configuration:');
            $this->line('NEWSAPI_KEY=your_newsapi_key_here');
            $this->line('GUARDIAN_API_KEY=your_guardian_key_here');
            $this->line('NYT_API_KEY=your_nyt_key_here');
            return Command::FAILURE;
        }
    }
}

