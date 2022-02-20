<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\ArticleScraperService;
use App\Models\ScrapedNews;
use Tests\TestCase;

class ArticlesScraperFeatureTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_command_fetches_articles_until_an_empty_page()
    {
        //setup
        $testArticle= [
            'scrapedId'=>123,
            'title'=>'ArticlesTitle',
            'link'=>'ArticlesLink',
            'points'=>321,
            'created_date'=>Carbon::now()
        ];

        $articlesScraperServicesMock = $this->mock(ArticleScraperService::class);
        $articlesScraperServicesMock
            ->expects('getArticlesFromUrl')
            ->with('https://news.ycombinator.com/news?p=1')
            ->andReturn(collect([$testArticle]));

        $articlesScraperServicesMock
            ->expects('getArticlesFromUrl')
            ->with('https://news.ycombinator.com/news?p=2')
            ->andReturn(collect([]));



        //action
        $command = $this->artisan('scrape:news');

        //assertions
        $command->expectsOutput('Fetching articles from page 1');
        $command->expectsOutput('Updating article '. $testArticle['scrapedId']);
        $command->expectsOutput('Fetching articles from page 2');
        $command->doesntExpectOutput('Fetching articles from page 3');

    }
    public function test_command_saves_fetched_articles_in_database()
    {
        //setup
        $testArticle= [
            'scrapedId'=>123,
            'title'=>'ArticlesTitle',
            'link'=>'ArticlesLink',
            'points'=>321,
            'created_date'=>Carbon::now()
        ];

        $articlesScraperServicesMock = $this->mock(ArticleScraperService::class)->makePartial();
        $articlesScraperServicesMock
            ->expects('getArticlesFromUrl')
            ->with('https://news.ycombinator.com/news?p=1')
            ->andReturn(collect([$testArticle]));

        $articlesScraperServicesMock
            ->expects('getArticlesFromUrl')
            ->with('https://news.ycombinator.com/news?p=2')
            ->andReturn(collect([]));

        //action
        $command = $this->artisan('scrape:news');

        //assertions
        $command->expectsOutput('Fetching articles from page 1');
        $command->expectsOutput('Updating article '. $testArticle['scrapedId']);

        $data = ScrapedNews::query()->where('scrapedId', $testArticle['scrapedId'])->first();

        $this->assertModelExists($data);

    }
    public function test_command_updates_fetched_articles()
    {
        //setup
        $testArticle= [
            'scrapedId'=>123,
            'title'=>'ArticlesTitle',
            'link'=>'ArticlesLink',
            'points'=>321,
            'created_date'=>Carbon::now()
        ];

        $articleForUpdate = ScrapedNews::query()->where('scrapedId', $testArticle['scrapedId'])->first();

        $articleForUpdate->points = 322;

        $articleForUpdate->save();

        $articlesScraperServicesMock = $this->mock(ArticleScraperService::class)->makePartial();
        $articlesScraperServicesMock
            ->expects('getArticlesFromUrl')
            ->with('https://news.ycombinator.com/news?p=1')
            ->andReturn(collect([$testArticle]));

        $articlesScraperServicesMock
            ->expects('getArticlesFromUrl')
            ->with('https://news.ycombinator.com/news?p=2')
            ->andReturn(collect([]));

        //action
        $command = $this->artisan('scrape:news');

        //assertions
        $command->expectsOutput('Fetching articles from page 1');
        $command->expectsOutput('Updating article '. $testArticle['scrapedId']);

        $this->assertEquals(322, (ScrapedNews::query()->where('scrapedId', $testArticle['scrapedId'])->first())->points);

    }
}
