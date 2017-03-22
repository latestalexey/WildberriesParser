<?php 
require_once '../vendor/autoload.php';
require_once '../db_connect.php';      //подключиться к базе
require_once '../write_res.php';       //выполнение запросов к базе

$start = microtime(true);                   //начало отсчета времени работы скрипта
 
$main_url = 'https://www.wildberries.ru';   //адрес магазина
$page_get_request = '?page=';               //добавочный адрес страница
$page_size = '&pagesize=200';               //выводить по 200 товаров на страницу
$list_menu_items = Array();                 //объявляем массив для данных


//get_categiry();                             //получам категории и подкатегории в переменную

//сначала мы должны получить количество товаров и страниц для запуска цикла парсинга
//в дальнейшем эта функция будет следить за количеством товара и страниц во время парсинга
//get_inf_of_count_item();
?><pre><?
/*foreach ($list_menu_items as $cat_key => $cat_value) {
    foreach ($cat_value["subcategories"] as $subcat_key => $subcat_value) {
        //pars_page();
        echo  ($subcat_value['name'].'</br>');
    }
}*/
$a=1;

while($c = curl_init('https://www.wildberries.ru/catalog/obuv/dlya-novorozhdennyh?page='.$a)){
    //phpQuery::newDocument($html_temp);
    
 


    

    //curl_exec($c);
    //phpQuery::unloadDocuments();
    $a++;
}
//var_dump($a);

?></pre>