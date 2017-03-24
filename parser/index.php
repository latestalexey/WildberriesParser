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
        $urls[count($urls)] = $main_url.$subcatvalue['link'];
    }
}

//$urls = array_chunk($urls, 20);            //разбили массив категорий на пакеты

/*foreach ($urls as $key => $value) {        // спарсили каждый пакет
    $htmls[$key] = multyrequest($value);
}*/


foreach ($urls as $key => $url) {
    $ex=true;
    while($ex) 
    {
        $pages_array = Array();                         //массив страниц
        $htmls = Array();                               //массив с ответами
        $corent_page = 1;                               //текущая страница
        for($i=$corent_page;$i<$corent_page+20;$i++)    //собрали массив страниц для парсинга
        {
            if($i!=1)
                $pages_array[count($pages_array)] = $main_url.$subcatvalue['link'].$page_get_request.$i;
            else
                $pages_array[count($pages_array)] = $main_url.$subcatvalue['link'];
        }
        
        $htmls = multyrequest($pages_array);            //солучили ответ со страницами
        
        $ex=false;
    }
    xd($htmls);
}
