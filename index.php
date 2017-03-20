<?php 
require_once 'vendor/autoload.php';
require_once 'db_connect.php';      //подключиться к базе
require_once 'write_res.php';       //выполнение запросов к базе

$start = microtime(true);                   //начало отсчета времени работы скрипта
 
$main_url = 'https://www.wildberries.ru';   //адрес магазина
$page_get_request = '?page=';               //добавочный адрес страница
$list_menu_items = Array();                 //объявляем массив для данных


if((date('Y').'.'.(date('m')+1).'.'.date('d'))<get_time_of_last_check()) //если прошел месяц, парсим категории
  pars_category();
else                                        // если не пошел месяц, то получаем сразу подкатегории из базы
    {
        
    }


//a($list_menu_items,$link);

/*-------------------получаем список подкатегорий--------------------*/
//foreach ($list_menu_items as $key => $value) {                      //пробегаем по всем категриям
//    $html_temp = file_get_contents($value['link']);                 //загружаем страницу категории
//    phpQuery::newDocument($html_temp);                              //инициализируем класс для страницыкатегории
//
//    $list_submenu_item_dom = pq('ul.maincatalog-list-1')->children('li:not(.j-all-menu-item)'); //получаем список подкатегорий
//    foreach ($list_submenu_item_dom as $keys => $val) {                                         //парсим список подкатегорий
//        $li_submenu = pq($val)->children('a');
//        $list_menu_items[$key]['subcategories'][$keys]['name'] = $li_submenu->html();
//        $list_menu_items[$key]['subcategories'][$keys]['link'] = $li_submenu->attr('href');
//    }
//    phpQuery::unloadDocuments();                                    //убиваем класс для страницы категории освобождаем место
//}
//$time = microtime(true) - $start;                                   //сохраняем время работы скрипта
//printf('Чтение подкатегорий завершено через %.4F сек.</br>', $time);//вывводим время работы скрипта
/*-------------------------------------------------------------------*/
//
//
///*--------------------------получаем информацию о страницах для парсинга--------------------*/
//
//foreach ($list_menu_items as $key => $category) {                           //проходим по всем категориям
//    foreach ($category['subcategories'] as $cat_key => $subcategory) {      //проходим по всем подкатегориям
//        $html_temp = file_get_contents($main_url . $subcategory['link']);   //для каждой подкатегории нужно развернуть страницу и получить из нее данные
//        phpQuery::newDocument($html_temp);                                  //создаем класс для этой страницы
//        
//        $list_menu_items[$key]['subcategories'][$cat_key]['count_product'] = pq('.total.many>span:not(.active)')->text();//выдираем количество продуктов подкатегории
//        foreach (pq('.pager-bottom .pager .pageToInsert')->children('a') as $k => $v) {//получаем количество страниц
//            if ($k + 2 == pq('.pager-bottom .pager .pageToInsert')->children('a')->count())
//                $list_menu_items[$key]['subcategories'][$cat_key]['count_page'] = pq($v)->html();
//        }
//        
//        phpQuery::unloadDocuments();//убиваем класс страницы подкатегории
//    }
//}
//$time = microtime(true) - $start;//сохраняем время работы скрипта
//printf('Чтение информации со страниц подкатегорий завершено через %.4F сек.</br>', $time);//вывводим время работы скрипта
///*------------------------------------------------------------------------------------------*/
//
//
///*--------------------------------парсим страницы--------------------------------------------*/
//
//foreach ($list_menu_items as $key => $category) {//проходим по всем категориям
//    foreach ($category['subcategories'] as $cat_key => $subcategory) {//проходим по всем подкатегориям
//        for ($i = 1; $i <=$subcategory['count_page']; $i++) 
//        {
//            $html_temp = file_get_contents($main_url . $subcategory['link'].$page_get_request.$i); //для каждой подкатегории нужно развернуть страницу и получить из нее данные
//            phpQuery::newDocument($html_temp);//создаем класс для этой страницы            
//            foreach (pq('.catalog_main_table .ref_goods_n_p') as $q => $qq) //проходим по всем товарам на страницу
//            {
//                $id = pq($qq)->children('.l_class')->attr('id');    //вытаскиваем идентификатор товара
//                $link = pq($qq)->attr('href');                      //сылку на товар
//                  
//                if(pq($qq)->children('.price')->children('ins')->html()=='')   //цену на товар, новую и старую. если есть
//                {
//                    $price_old = preg_replace("/[^0-9]/", '',pq($qq)->children('.price')->text());
//                    $price_new = '';
//                }
//                else
//                {
//                    $price_old = preg_replace("/[^0-9]/", '',pq($qq)->children('.price')->children('ins')->text());
//                    $price_new = preg_replace("/[^0-9]/", '',pq($qq)->children('.price')->children('del')->text());
//                }
//                
//                $list_menu_items[$key]['subcategories'][$cat_key]['items'][$q]['id'] = $id;
//                $list_menu_items[$key]['subcategories'][$cat_key]['items'][$q]['link'] = $link;
//                $list_menu_items[$key]['subcategories'][$cat_key]['items'][$q]['price_old'] = $price_old;
//                $list_menu_items[$key]['subcategories'][$cat_key]['items'][$q]['price_new'] = $price_new;
//            }
//            phpQuery::unloadDocuments();
//        }
//    }
//}
//$time = microtime(true) - $start;//сохраняем время работы скрипта
//printf('Чтение информации о товарах завершено через %.4F сек.</br>', $time);//вывводим время работы скрипта
////xprint($list_menu_items);
///*-------------------------------------------------------------------------------------------*/

?>