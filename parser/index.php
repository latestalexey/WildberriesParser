<?php 
require_once '../vendor/autoload.php';
require_once '../db_connect.php';      //подключиться к базе
require_once '../write_res.php';       //выполнение запросов к базе

$start = microtime(true);                   //начало отсчета времени работы скрипта
 
$main_url = 'https://www.wildberries.ru';   //адрес магазина
$page_get_request = '?page=';               //добавочный адрес страница
$page_size = '&pagesize=200';               //выводить по 200 товаров на страницу
$list_menu_items = Array();                 //объявляем массив для данных


get_categiry();                             //получам категории и подкатегории в переменную

$urls = Array();                            //список первых страниц каждой подкатегории
foreach ($list_menu_items as $catkey => $catvalue) {
    foreach ($catvalue['subcategories'] as $subcatkey => $subcatvalue) {
        $urls[count($urls)] = 'https://www.wildberries.ru'.$subcatvalue['link'].$page_get_request.'1';
    }
}

$multi = curl_multi_init();
$handles = Array();
foreach ($urls as $key => $url) 
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    
    curl_multi_add_handle($multi, $ch);
    $handles[$url] = $ch;
}

$active = null;
do {
    $mrc = curl_multi_exec($multi, $active);
} while ($mrc == CURLM_CALL_MULTI_PERFORM);

while ($active && $mrc == CURLM_OK) {
    if (curl_multi_select($multi) == -1) {
        usleep(100);
    }
    do {
        $mrc = curl_multi_exec($multi, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
}

foreach ($handles as $key => $channel) {
    $html[$channel] = curl_multi_getcontent($channel);
    xprint($key);
    
    curl_multi_remove_handle($multi, $channel);
}
curl_multi_close($multi);
xprint($html);