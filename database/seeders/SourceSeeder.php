<?php

namespace Database\Seeders;

use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            [
                'key' => 'newsapi',
                'name' => 'NewsAPI',
                'base_url' => 'https://newsapi.org/v2',
                'meta' => [
                    'description' => 'NewsAPI aggregates headlines from over 80,000 worldwide sources',
                    'website' => 'https://newsapi.org'
                ]
            ],
            [
                'key' => 'guardian',
                'name' => 'The Guardian',
                'base_url' => 'https://content.guardianapis.com',
                'meta' => [
                    'description' => 'News and opinions from The Guardian',
                    'website' => 'https://www.theguardian.com'
                ]
            ],
            [
                'key' => 'nytimes',
                'name' => 'The New York Times',
                'base_url' => 'https://api.nytimes.com/svc/search/v2',
                'meta' => [
                    'description' => 'Breaking news, analysis, and opinion from The New York Times',
                    'website' => 'https://www.nytimes.com'
                ]
            ],
        ];

        foreach ($sources as $source) {
            Source::updateOrCreate(
                ['key' => $source['key']],
                $source
            );
        }
    }
}

