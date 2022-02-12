<?php

namespace App\Console\Commands;

use App\Models\ScrapedNews;
use App\Services\ArticleScraperService;
use Illuminate\Console\Command;

class scrapeNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape news from https://news.ycombinator.com/news';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function getPageUrl(int $page): string
    {
        return "https://news.ycombinator.com/news?p={$page}";
    }

    public function handle(ArticleScraperService $articleScraperService)
    {
        $page = 1;
        do {
            $this->info('Fetching articles from page ' . $page);

            $extractedArticles = $articleScraperService->getArticlesFromUrl($this->getPageUrl($page));
            $extractedArticles->each(function($article){

                $this->info('Updating article ' . $article['scrapedId']);

                ScrapedNews::updateOrCreate(
                    [
                        'scrapedId' => $article['scrapedId'],
                    ],
                    [
                        'title' => $article['title'],
                        'link' => $article['link'],
                        'points' => $article['points'],
                        'created_date' => $article['created_date']
                    ]
                );
            });

            $page++;
        } while ($extractedArticles->count() > 0);
    }
}
