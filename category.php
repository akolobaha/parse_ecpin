<?php
header('Content-type: text/html; charset=utf-8');

require __DIR__ . '/vendor/autoload.php';

$url = 'http://www.ecpin.ru/';


$content = file_get_contents($url);
$doc = phpQuery::newDocument($content);

$fp = fopen('csv/categories.csv', 'w');

// Получаем все ссылки с главной страницы (входят все категории)
foreach ($doc->find('a') as $item) {
    $item = pq($item);
    $href = $item->attr('href');
    $text = $item->text();
    if ($href && $text) {
        fputcsv($fp, [$href]);
    }
}

fclose($fp);

