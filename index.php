<?php

require_once 'vendor/autoload.php';
$start = microtime(true);//начало отсчета времени работы скрипта
$main_url = 'https://www.wildberries.ru';//адрес магазина
$page_get_request = '?page=';//добавочный адрес страница
$html = file_get_contents($main_url);//получаем главную страницу
phpQuery::newDocument($html);//инициализация класса для главной страницы

$list_menu_item_dom = pq('ul.topmenus')->children('li:not(.divider)'
        . ':not(.submenuless)'
        . ':not(.row-divider)'
        . ':not(.promo-offer)'
        . ':not(.brands)'
        . ':not(.certificate)'); // получаем список категорий
$list_menu_items = Array();//объявляем массив для данных

/*-----------------получаем список категорий-------------------------*/
foreach ($list_menu_item_dom as $key => $value) {

    $li = pq($value)->children('a');//вытаскиваем элемент "ссылка"

    if ($li->html() !== '' && $li->attr('href') !== '') {   //если ссылка и имя не пустые
        $list_menu_items[$key]['name'] = $li->html();       // то записываем название категории
        $list_menu_items[$key]['link'] = $li->attr('href'); // и ссылку на ее страницу
    }
}
phpQuery::unloadDocuments();//убиваем класс для главной страницы, освобождаем место
$time = microtime(true) - $start;//сохраняем время работы скрипта
printf('Чтение категорий завершено через %.4F сек.</br>', $time);//вывводим время работы скрипта
/*-------------------------------------------------------------------*/


/*-------------------получаем список подкатегорий--------------------*/
foreach ($list_menu_items as $key => $value) {//пробегаем по всем категриям
    $html_temp = file_get_contents($value['link']);//загружаем страницу категории
    phpQuery::newDocument($html_temp);//инициализируем класс для страницыкатегории

    $list_submenu_item_dom = pq('ul.maincatalog-list-1')->children('li:not(.j-all-menu-item)');//получаем список подкатегорий
    foreach ($list_submenu_item_dom as $keys => $val) {//парсим список подкатегорий
        $li_submenu = pq($val)->children('a');
        $list_menu_items[$key]['subcategories'][$keys]['name'] = $li_submenu->html();
        $list_menu_items[$key]['subcategories'][$keys]['link'] = $li_submenu->attr('href');
    }
    phpQuery::unloadDocuments();//убиваем класс для страницы категории освобождаем место
}
$time = microtime(true) - $start;//сохраняем время работы скрипта
printf('Чтение подкатегорий завершено через %.4F сек.</br>', $time);//вывводим время работы скрипта
/*-------------------------------------------------------------------*/

xprint($list_menu_items);

/*
 * $html_temp_sub = file_get_contents($main_url . $list_menu_items[$key]['subcategories'][$keys]['link']); 
  phpQuery::newDocument($html_temp_sub);
        $list_menu_items[$key]['subcategories'][$keys]['count_product'] = pq('.total.many>span:not(.active)')->text();
  foreach (pq('.pager-bottom .pager .pageToInsert')->children('a') as $k => $v) {
            if ($k + 2 == pq('.pager-bottom .pager .pageToInsert')->children('a')->count())
                $list_menu_items[$key]['subcategories'][$keys]['count_page'] = pq($v)->html();
        }
        phpQuery::unloadDocuments();
        
        for ($i = 1; $i <= $list_menu_items[$key]['subcategories'][$keys]['count_page']; $i++) 
        {
            phpQuery::newDocument(file_get_contents($main_url.$list_menu_items[$key]['subcategories'][$keys]['link'].$page_get_request.$i));
            foreach (pq('.catalog_main_table .ref_goods_n_p') as $q => $qq) 
            {
                $id = pq($qq)->children('.l_class')->attr('id');
                $link = pq($qq)->attr('href');
                $price = pq($qq)->children('.price')->text();
                $list_menu_items[$key]['subcategories'][$keys]['items'][$q]['id'] = $id;
                $list_menu_items[$key]['subcategories'][$keys]['items'][$q]['link'] = $link;
                $list_menu_items[$key]['subcategories'][$keys]['items'][$q]['price'] = preg_replace("/[^0-9]/", '', $price);
            }
            phpQuery::unloadDocuments();
        }*/
?>