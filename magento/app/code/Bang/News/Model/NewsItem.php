<?php

namespace Bang\News\Model;

class NewsItem {
    private $title;
    private $link;
    private $description;
    private $imageUrl;
    private $anchorUrl; // URL của thẻ <a>
    private $textDescription; // Văn bản mô tả

    // Setters and Getters cho các thuộc tính
    public function setTitle($title) {
        $this->title = $title;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setLink($link) {
        $this->link = $link;
    }

    public function getLink() {
        return $this->link;
    }

    public function setDescription($description) {
        $this->description = $description;
        // Tách phần tử <a>, <img> và văn bản
        $this->extractContent($description);
    }

    public function getDescription() {
        return $this->description;
    }

    public function setImageUrl($imageUrl) {
        $this->imageUrl = $imageUrl;
    }

    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function getAnchorUrl() {
        return $this->anchorUrl;
    }

    public function getTextDescription() {
        return $this->textDescription;
    }

    // Tách các phần tử <a>, <img> và văn bản từ mô tả
    private function extractContent($description) {
        // Tìm và lấy URL của thẻ <a>
        preg_match('/<a href="([^"]+)"/', $description, $matches);
        if (isset($matches[1])) {
            $this->anchorUrl = $matches[1];
        }

        // Tìm và lấy URL của thẻ <img>
        preg_match('/<img[^>]+src="([^"]+)"/', $description, $imgMatches);
        if (isset($imgMatches[1])) {
            $this->imageUrl = $imgMatches[1];
        }

        // Lấy phần văn bản mô tả (loại bỏ tất cả thẻ HTML)
        $this->textDescription = strip_tags($description);
    }

    public function __tostring() {
        return "title=". $this->title .", link=". $this->link . ", description=". $this->textDescription .", imageurl=". $this->imageUrl
        . ", anchorUrl=" . $this->anchorUrl;
    }
}