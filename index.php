<?php
set_time_limit (50000000);
require_once 'vendor/autoload.php';
$start = microtime(true);//начало отсчета времени работы скрипта
$main_url = 'http://www.garantgroup.com';//адрес магазина
//$page_get_request = '?page=';//добавочный адрес страница
$html = file_get_contents($main_url.'/katalog/');//получаем главную страницу
phpQuery::newDocument($html);//инициализация класса для главной страницы

$list_menu_item_dom = pq('#partnersBySHCG')->children('a'); // получаем список категорий
$list_menu_items = Array();//объявляем массив для данных



/*-----------------получаем список категорий-------------------------*/
foreach ($list_menu_item_dom as $key => $value) {

    $li = pq($value);//вытаскиваем элемент "ссылка"

    //if ($li->html() !== '' && $li->attr('href') !== '') {   //если ссылка и имя не пустые
        $list_menu_items[$key]['name'] = $li->attr('title');       // то записываем название категории 
        $list_menu_items[$key]['link'] = $li->attr('href'); // и ссылку на ее страницу
        $list_menu_items[$key]['count_page'] = 1;
    //}
}
phpQuery::unloadDocuments();//убиваем класс для главной страницы, освобождаем место
//$time = microtime(true) - $start;//сохраняем время работы скрипта
//printf('Чтение категорий завершено через %.4F сек.</br>', $time);//вывводим время работы скрипта


/*-------------------------------------------------------------------*/


/*-------------------получаем список подкатегорий--------------------*/
$list_items = Array();
foreach ($list_menu_items as $key => $value) {//пробегаем по всем категриям
    $page = 1;
    while($page<=$list_menu_items[$key]['count_page']){
        $html_temp = file_get_contents($main_url.$value['link'].'&PAGEN_1='.$page.'&SIZEN_1=10');//загружаем страницу категории
        phpQuery::newDocument($html_temp);//инициализируем класс для страницыкатегории

        $list_submenu_item_dom = pq('#itemsBySHCG')->children('.itemblock');//получаем список подкатегорий


        $list_submenu_item_dom_count = pq('#itemsBySHCG .bx_pagination_page ul')->children('li');

        //xd($list_submenu_item_dom_count->html());
        foreach ($list_submenu_item_dom_count as $k => $v) {
            $li_submenu = pq($v)->children('a');

            if(empty($list_menu_items[$key]['count_page']) || $list_menu_items[$key]['count_page']<(int)$li_submenu->html()){
                $list_menu_items[$key]['count_page'] = $li_submenu->html();
            }
        }

        foreach ($list_submenu_item_dom as $keys => $val) {//парсим список подкатегорий
            $li_submenu = pq($val)->children('a');
            //$list_menu_items[$key]['products'][$keys]['name'] = $li_submenu->html();
            $list_menu_items[$key]['products'][]['link'] = $li_submenu->attr('href');
        }
        phpQuery::unloadDocuments();//убиваем класс для страницы категории освобождаем место
        $page++;
    }
}
//xd($list_menu_items);
foreach ($list_menu_items as $key => $value) {
    mkdir("garantgroup/".str_replace("/companies/?companies=", "",$value['link']), 0777);
    foreach ($value['products'] as $k => $v) {
        $html_temp = file_get_contents($main_url.$v['link']);//загружаем страницу категории
        phpQuery::newDocument($html_temp);//инициализируем класс для страницыкатегории
        if(!strripos(pq('.itemblock .prodImg')->attr('href'),'garant150.jpg')){
            //$list_menu_items[$key]['products'][$k]['img_url'] = pq('.itemblock .prodImg')->attr('href');
            //$list_menu_items[$key]['products'][$k]['img_name'] = preg_replace('/\/.*\//','',pq('.itemblock .prodImg')->attr('href'));
        
            
            $url = $main_url.pq('.itemblock .prodImg')->attr('href');
            $path = "garantgroup/".str_replace("/companies/?companies=", "",$value['link'])."/".preg_replace('/\/.*\//','',pq('.itemblock .prodImg')->attr('href'));
            $list_menu_items[$key]['products'][$k]['img'] = $path;
            file_put_contents($path, file_get_contents($url));
        }
        
        $p = pq('.itemblock p');
        foreach ($p as $kp => $vp) {
            if($kp==1){
                $list_menu_items[$key]['products'][$k]['art_number'] = str_replace("Код товара: ","",pq($vp)->html());
            }
        }
        $list_menu_items[$key]['products'][$k]['description'] = pq('#tabs-1')->html();
        phpQuery::unloadDocuments();//убиваем класс для страницы категории освобождаем место
        
        
        mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
        mysql_select_db('test') or die('Не могу выбрать базу данных');
        $query = "INSERT INTO `garantgroup`(`art_number`,`description`,`image`) "
                        . "VALUES `art_number`=".$list_menu_items[$key]['products'][$k]['art_number'].",`description`=".$list_menu_items[$key]['products'][$k]['description'].",`image`=".$list_menu_items[$key]['products'][$k]['img'];
                $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        mysql_close();
    }
    //xd($list_menu_items);
}
/*
mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
mysql_select_db('test') or die('Не могу выбрать базу данных');
$query = "INSERT INTO `garantgroup`(`art_number`,`description`,`image`) "
                . "VALUES ".$res;
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
mysql_close();*/




$time = microtime(true) - $start;//сохраняем время работы скрипта
printf('Чтение подкатегорий завершено через %.4F сек.</br>', $time);//вывводим время работы скрипта*/
/*-------------------------------------------------------------------*/
/*
$list_item =Array();
$count_page = 1;
$file_string = '';
/*--------------------------собираем список карточек--------------------*//*
for($i=1;$i<=$count_page;$i++)
{
    
    /********собираем максимальную страницу**********/
   /* $html_temp = file_get_contents($main_url.$i); //для каждой подкатегории нужно развернуть страницу и получить из нее данные
    phpQuery::newDocument($html_temp);//создаем класс для этой страницы
    foreach (pq('.toolbar-bottom .pages.gen-direction-arrows1 li:not(.next):not(.previous):not(.current)')->children('a') as $k => $v) {//получаем количество страниц
        if((int)(pq($v)->html())>=$count_page)
        $count_page = (int)(pq($v)->html());
    }
    /**************************************************/
  /*  
    foreach (pq('.product-name')->children('a') as $k => $v) {//получаем количество страниц
        $list_item[count($list_item)]['link'] = pq($v)->attr('href');
    }
    phpQuery::unloadDocuments();//убиваем класс страницы подкатегории
}
mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
mysql_select_db('WildberriesParser') or die('Не могу выбрать базу данных');
foreach ($list_item as $key => $value) {
    $h = file_get_contents($list_item[$key]['link']); 
    phpQuery::newDocument($h);
    //$list_item[$key]['article'] = pq('.sku .value')->text();
    //$list_item[$key]['description'] = pq('.panel .std')->html();
    
    //$file_string .= (string)(pq('.sku .value')->text()).';'.trim ((string)(pq('.panel .std')->text())," \t\n\r\0\x0B").';';
    
    //$current = file_get_contents('NETGEAR.csv');
    //$current .= pq('.sku .value')->text().';'.pq('.panel .std')->html().';\n';
    //file_put_contents('NETGEAR.csv',$current);
    //$val_query.='("'.pq('.sku .value')->text().'","'.addslashes(/*pq('.panel .std')->html/*$a.$b).'")';
    
    $query = "INSERT INTO `D-Link`(`article`, `description`) "
            . "VALUES ('".pq('.sku .value')->text()."','".addslashes(pq('.panel .std')->html())."')";
    $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    usleep(300);
    phpQuery::unloadDocuments();
}

xprint(count($list_item));
$time = microtime(true) - $start;
printf('Чтение подкатегорий завершено через %.4F сек.</br>', $time);
//xprint($list_item);
//foreach ($list_menu_items as $key => $category) {//проходим по всем категориям
    //foreach ($category['subcategories'] as $cat_key => $subcategory) {//проходим по всем подкатегориям
        /*$html_temp = file_get_contents($main_url.'1'); //для каждой подкатегории нужно развернуть страницу и получить из нее данные
        phpQuery::newDocument($html_temp);//создаем класс для этой страницы
        
        //$list_menu_items[$key]['subcategories'][$cat_key]['count_product'] = pq('.total.many>span:not(.active)')->text();//выдираем количество продуктов подкатегории
        foreach (pq('.toolbar-bottom .pages.gen-direction-arrows1 li')->children('a') as $k => $v) {//получаем количество страниц
            //if ($k + 2 == pq('.pager-bottom .pager .pageToInsert')->children('a')->count())
                $count_page[$k] = pq($v)->html();
        }
//        $count_page = pq('.toolbar-bottom .pages.gen-direction-arrows1')->html();
        phpQuery::unloadDocuments();//убиваем класс страницы подкатегории
    //}*/
//}
/*------------------------------------------------------------------------------------------*/


/*--------------------------------парсим страницы--------------------------------------------*/
/*
foreach ($list_menu_items as $key => $category) {//проходим по всем категориям
    foreach ($category['subcategories'] as $cat_key => $subcategory) {//проходим по всем подкатегориям
        for ($i = 1; $i <= $subcategory['count_page']; $i++) 
        {
            $html_temp = file_get_contents($main_url . $subcategory['link'].$page_get_request.$i); //для каждой подкатегории нужно развернуть страницу и получить из нее данные
            phpQuery::newDocument($html_temp);//создаем класс для этой страницы            
            foreach (pq('.catalog_main_table .ref_goods_n_p') as $q => $qq) //проходим по всем товарам на страницу
            {
                $id = pq($qq)->children('.l_class')->attr('id');    //вытаскиваем идентификатор товара
                $link = pq($qq)->attr('href');                      //сылку на товар
                if(!pq($qq)->children('.price')->children('ins'))   //цену на товар, новую и старую. если есть
                {
                    $price_old = preg_replace("/[^0-9]/", '',pq($qq)->children('.price')->text());
                    $price_new = '';
                }
                else
                {
                    $price_old = preg_replace("/[^0-9]/", '',pq($qq)->children('.price ins')->text());
                    $price_new = preg_replace("/[^0-9]/", '',pq($qq)->children('.price del')->text());
                }
                
                $list_menu_items[$key]['subcategories'][$cat_key]['items'][$q]['id'] = $id;
                $list_menu_items[$key]['subcategories'][$cat_key]['items'][$q]['link'] = $link;
                $list_menu_items[$key]['subcategories'][$cat_key]['items'][$q]['price_old'] = $price_old;
                $list_menu_items[$key]['subcategories'][$cat_key]['items'][$q]['price_new'] = $price_new;
            }
            phpQuery::unloadDocuments();
        }
    }
}
$time = microtime(true) - $start;//сохраняем время работы скрипта
printf('Чтение информации о товарах завершено через %.4F сек.</br>', $time);//вывводим время работы скрипта
xprint($list_menu_items);*/
/*-------------------------------------------------------------------------------------------*/

?>