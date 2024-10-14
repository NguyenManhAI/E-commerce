<?php

require '/var/www/html/vendor/autoload.php'; // Đảm bảo bạn đã cài đặt thư viện elasticsearch-php bằng Composer

use Elastic\Elasticsearch\ClientBuilder;

// Tạo client Elasticsearch
$client = ClientBuilder::create()
    ->setHosts(['elasticsearch:9200']) // Sử dụng tên container để kết nối
    ->setBasicAuthentication('elastic', getenv('ELASTIC_PASSWORD')) // Thay 'elastic' bằng user nếu bạn đã tạo khác
    ->build();

// Tên index
$indexName = 'test_index';

// Kiểm tra xem index đã tồn tại chưa
if (!$client->indices()->exists(['index' => $indexName])) {
    // Tạo index
    $client->indices()->create([
        'index' => $indexName,
        'body' => [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 1,
            ],
            'mappings' => [
                'properties' => [
                    'title' => ['type' => 'text'],
                    'content' => ['type' => 'text'],
                ],
            ],
        ],
    ]);
    echo "Index created: $indexName\n";
} else {
    echo "Index already exists: $indexName\n";
}

// Thêm một document vào index
$doc = [
    'title' => 'Hello Elasticsearch',
    'content' => 'This is a test document.'
];

$response = $client->index([
    'index' => $indexName,
    'body' => $doc,
]);

echo "Document added with ID: " . $response['_id'] . "\n";

// Tìm kiếm document trong index
$searchParams = [
    'index' => $indexName,
    'body' => [
        'query' => [
            'match' => [
                'title' => 'Hello'
            ]
        ]
    ]
];

$searchResponse = $client->search($searchParams);
echo "Search results:\n";
foreach ($searchResponse['hits']['hits'] as $hit) {
    echo "ID: " . $hit['_id'] . " | Title: " . $hit['_source']['title'] . "\n";
}
