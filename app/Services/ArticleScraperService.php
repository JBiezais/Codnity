<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PHPHtmlParser\Dom;

class ArticleScraperService {

    public function getArticlesFromUrl(string $url): Collection
    {
        $dom = (new Dom())->loadFromUrl($url);

        $articles = $dom->find('.athing');
        return collect($articles)->map(function(Dom\Node\HtmlNode $article){

            $articleTitleWithLink = $article->find('.titlelink')[0];
            [$points, $createdDate] = $this->getPointsAndDateFromArticle($article);

            return [
                'scrapedId' => $article->id,
                'title' => $articleTitleWithLink->text,
                'link' => $articleTitleWithLink->href,
                'points' => $points,
                'created_date' => $createdDate
            ];
        });
    }

    private function getPointsAndDateFromArticle(Dom\Node\HtmlNode $article):array
    {
        $articleLinkSubtext = $article->nextSibling();
        if ($scoreTextElement = $articleLinkSubtext->find('.score')[0] ?? null){
            $points = explode(' ', $scoreTextElement->innerText)[0];
        } else {
            $points = 0;
        }
        $createdDate = Carbon::parse($articleLinkSubtext->find('.age')[0]->title);

        return [$points, $createdDate];
    }

}
