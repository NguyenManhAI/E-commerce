<?php

namespace Bang\News\Helper;

use Bang\News\Model\RssReader;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool;

class Data extends AbstractHelper
{
    private const NEWS_URL = "https://vnexpress.net/rss/kinh-doanh.rss";
    private const ITEMS_PER_PAGE = 5;
    private const CACHE_KEY = 'vnexpress_news_cache';
    private const CACHE_LIFETIME = 3600; // Cache for 1 hour

    protected $newsList = [];
    protected $cache;
    protected $cacheKey;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Pool $cacheFrontendPool
    ) {
        parent::__construct($context);
        $this->cache = $cacheFrontendPool->get('default');
        $this->cacheKey = self::CACHE_KEY;
    }

    public function getNews($page = 1)
    {
        $allNews = $this->getCachedNews();
        
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

    protected function getCachedNews()
    {
        // Try to get news from cache
        $cachedData = $this->cache->load($this->cacheKey);
        
        if ($cachedData) {
            return unserialize($cachedData);
        }

        // If cache is empty or expired, load from RSS
        $allNews = RssReader::loadRss(self::NEWS_URL);
        
        // Save to cache
        $this->cache->save(
            serialize($allNews),
            $this->cacheKey,
            [],
            self::CACHE_LIFETIME
        );

        return $allNews;
    }
}