<?php
header('Content-type: text/html; charset=utf-8');

require __DIR__ . '/vendor/autoload.php';


$items = file('csv/categories.csv');

$fp = fopen('csv/categories.csv', 'w');


$current = array_pop($items);


// Записываем обратно в файл, за исключением одного
foreach ($items as $item) {
    $item = trim($item);
    fputcsv($fp, [$item]);
}

fclose($fp);


if (file_exists('csv/test.csv')) {
    $results = file('csv/test.csv');
} else {
    $results = [];
};


// Типа парсим дальше. И записываем в следующий файл
array_push($results, $current);

$fp = fopen('csv/test.csv', 'w');
foreach ($results as $result) {
    $result = trim($result);
    fputcsv($fp, [$result]);
}

fclose($fp);

$link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
        "https" : "http") . "://" . $_SERVER['HTTP_HOST'] .
    $_SERVER['REQUEST_URI'];

header("Location: $link");



