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





$cat_products_url = getProductsUrlByCategoryUrl($current);
parseProducts($cat_products_url);




function parseProducts($cat_products_url) {
    foreach ($cat_products_url as $product_url) {
        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', $product_url);
        $body = $res->getBody();

        $doc = phpQuery::newDocument($body);



        $header = $doc->find('h1')->text();
        $qty = $doc->find('tr:eq(2) td:eq(1)')->text();
        $package = $doc->find('tr:eq(2) td:eq(2)')->text();
        $year = $doc->find('tr:eq(2) td:eq(3)')->text();
        $price = $doc->find('tr:eq(2) td:eq(4)')->text();
        $status = $doc->find('tr:eq(2) td:eq(5)')->text();
        $cat1 = $doc->find('.navig:eq(1)')->text();
        $cat2 = $doc->find('.navig:eq(2)')->text();
        $cat3 = $doc->find('.navig:eq(3)')->text();

        $img = $doc->find('img.mtov')->attr('src');

        $fp = fopen('csv/products.csv', 'w');

        fputcsv($fp, [$product_url, $header, $qty, $package, $year, $price, $status, $img, $cat1, $cat2, $cat3]);

    }
}




/**
 * Получили урлы продуктов из категорий
 */
function getProductsUrlByCategoryUrl($cat_url, $result = []) {
    $client = new GuzzleHttp\Client();
    $res = $client->request('GET', $cat_url);

    if ($res->getStatusCode() == 200) {
        $doc = phpQuery::newDocument($res->getBody());
        foreach ($doc->find('.kztov_case a.p_group') as $item) {
            $item = pq($item);
            $result[] = $item->attr('href');
        }

        if ($doc->find('.navigation a:contains("Далее")')->length) {
            $href = $doc->find('.navigation a:contains("Далее")')->attr('href');
            return getProductsUrlByCategoryUrl($href, $result);
        }
    }
    return $result;
}

