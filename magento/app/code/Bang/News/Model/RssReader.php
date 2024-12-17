<?php

namespace Bang\News\Model;

use Exception;

class RssReader {
    // Hàm để tải RSS feed và phân tích dữ liệu
    public static function loadRss(string $rssUrl ) {
        $rssContent = file_get_contents($rssUrl);
        if (!$rssContent) {
            throw new Exception("Không thể tải RSS từ URL: " . $rssUrl);
        }

        // Phân tích nội dung RSS (XML)
        $rss = simplexml_load_string($rssContent);
        $rssItems = [];
        if (!$rss) {
            throw new Exception("Không thể phân tích RSS XML.");
        }

        // Duyệt qua tất cả các mục (items) trong RSS
        foreach ($rss->channel->item as $item) {
            $rssItem = new NewsItem();
            $rssItem->setTitle((string) $item->title);
            $rssItem->setLink((string) $item->link);
            $rssItem->setDescription((string) $item->description);
            // Lấy ảnh từ description nếu có
            preg_match('/<img[^>]+src="([^"]+)"/', (string) $item->description, $matches);
            if (!empty($matches[1])) {
                $rssItem->setImageUrl($matches[1]);
            }

            // Thêm đối tượng vào mảng các bài viết
            $rssItems[] = $rssItem;
        }
        return $rssItems;
    }
}
