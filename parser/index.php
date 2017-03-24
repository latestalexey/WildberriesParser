<?php 
require_once '../vendor/autoload.php';
require_once '../db_connect.php';      //подключиться к базе
require_once '../write_res.php';       //выполнение запросов к базе
require_once 'multy_query.php';       //параллельные запросы

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

$urls = array_chunk($urls, 20);            //разбили массив категорий на пакеты

foreach ($urls as $key => $value) {        // спарсили каждый пакет
    $htmls[$key] = multyrequest($value);
    
}
xprint();