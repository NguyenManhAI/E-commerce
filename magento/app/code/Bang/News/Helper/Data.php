<?php

namespace Bang\News\Helper;

use Bang\News\Model\RssReader;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
    private const newsUrl = "https://vnexpress.net/rss/kinh-doanh.rss";
    private const ITEMS_PER_PAGE = 5;
    protected $newsList = [];

    public function getNews($page = 1) {
        $this->newsList = [];
        $allNews = RssReader::loadRss(self::newsUrl);
        
        // Calculate pagination
        $totalItems = count($allNews);
        $totalPages = ceil($totalItems / self::ITEMS_PER_PAGE);
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * self::ITEMS_PER_PAGE;
        
        // Get items for current page
        $this->newsList = array_slice($allNews, $offset, self::ITEMS_PER_PAGE);
        
        return [
            'items' => $this->newsList,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ];
    }
}