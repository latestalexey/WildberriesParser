<?php
function get_categiry() { //получаем категории из базы 
    global $main_url, $list_menu_items;

    /*--------------- запросы на категории -------------------*/
    $query_to_get_categoru = "SELECT `cat_id`,`cat_name`,`cat_link` FROM `Category`"; //составляем запрос к базе
    $result_query = mysql_query($query_to_get_categoru) or die('Query failed: ' . mysql_error()); // делаем запрос к базе
    while ($line = mysql_fetch_array($result_query, MYSQL_ASSOC)) {// составляем ответ
        $result[count($result)] = $line;
    }
    if (count($result) != 0) {   //база не пуста, сохраняем категории в переменную
        foreach ($result as $key => $value) {
            $list_menu_items[$key]['id'] = $value['cat_id'];
            $list_menu_items[$key]['name'] = $value['cat_name'];
            $list_menu_items[$key]['link'] = $value['cat_link'];
        };
    } else {
        pars_category();   //база пуста, парсим, сохраняем категории в базу и переменную
    }
    /*--------------- канец запросов на категории -------------------*/
    
    /*--------------- запросы на подкатегории -----------------------*/
    $result = null;
    $query_to_get_subcategoru = "SELECT `cat_id`,`subcat_id`,`subcat_name`,`subcat_link` FROM `Subcategory`"; //составляем запрос к базе
    $result_query = mysql_query($query_to_get_subcategoru) or die('Query failed: ' . mysql_error()); // делаем запрос к базе
    while ($line = mysql_fetch_array($result_query, MYSQL_ASSOC)) {// составляем ответ
        $result[count($result)] = $line;
    }
    if (count($result) != 0) {   //база не пуста, сохраняем категории в переменную
        foreach ($list_menu_items as $cat_key=>$category) {
            foreach ($result as $key => $value) {
                if($category['id']==$value['cat_id'])
                {
                    $list_menu_items[$cat_key]['subcategories'][$key]['subcat_id'] = $value['subcat_id'];
                    $list_menu_items[$cat_key]['subcategories'][$key]['name'] = $value['subcat_name'];
                    $list_menu_items[$cat_key]['subcategories'][$key]['link'] = $value['subcat_link'];
                }
            }
        }
    } else {
        pars_subcategory();   //база пуста, парсим, сохраняем категории в базу и переменную
    }
    /*--------------- канец запросов на подкатегории -------------------*/
}

function pars_category()                                                //парсим категории
{
    global $main_url, $list_menu_items;
    
    /*-----------------получаем список категорий-------------------------*/
    $b = curl_init($main_url);
    curl_setopt($b,CURLOPT_CONNECTTIMEOUT,10);
    curl_setopt($b,CURLOPT_HEADER,true);
    curl_setopt($b,CURLOPT_RETURNTRANSFER,true);
    $html = curl_exec($b);                                              //получаем главную страницу
    curl_close($b);
    phpQuery::newDocument($html);                                       //инициализация класса для главной страницы

    $list_menu_item_dom = pq('ul.topmenus')->children('li:not(.divider)'
            . ':not(.submenuless)'
            . ':not(.row-divider)'
            . ':not(.promo-offer)'
            . ':not(.brands)'
            . ':not(.certificate)');                                    // получаем список категорий
    foreach ($list_menu_item_dom as $key => $value) {
        $li = pq($value)->children('a');                                //вытаскиваем элемент "ссылка"
            
        if ($li->html() !== '' && $li->attr('href') !== '' && (bool)strripos( $li->attr('href'),'aspx')==false) 
        {           //если ссылка и имя не пустые
            $list_menu_items_tmp[$key]['name'] = $li->html();               // то записываем название категории
            $list_menu_items_tmp[$key]['link'] = $li->attr('href');         // и ссылку на ее страницу
        }
    }
    phpQuery::unloadDocuments();                                        //убиваем класс для главной страницы, освобождаем место
    
    foreach ($list_menu_items_tmp as $key => $value) {//вносим категории в базу
        $query = "INSERT INTO `Category`(`cat_name`, `cat_link`, `date`) "
                . "VALUES ('" . $value['name'] . "','" . $value['link'] . "','" . date('Y') . '.' . date('m') . '.' . date('d') . "')";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    }
    
    $result = Array();
    $query_to_get_categoru = "SELECT `cat_id`,`cat_name`,`cat_link` FROM `Category`"; //составляем запрос к базе
    $result_query = mysql_query($query_to_get_categoru) or die('Query failed: ' . mysql_error()); // делаем запрос к базе
    while ($line = mysql_fetch_array($result_query, MYSQL_ASSOC)) {// составляем ответ
        $result[count($result)] = $line;
    }
    foreach ($result as $key => $value) {
        $list_menu_items[$key]['id'] = $value['cat_id'];
        $list_menu_items[$key]['name'] = $value['cat_name'];
        $list_menu_items[$key]['link'] = $value['cat_link'];
    };
    
    /*-------------------------------------------------------------------*/
}


function pars_subcategory()
{
    global $main_url, $list_menu_items;
    /*-------------------получаем список подкатегорий--------------------*/
    $list_menu_items_tmp = null;
    foreach ($list_menu_items as $key => $value) {                      //пробегаем по всем категриям
                 
        $b = curl_init($value['link']);                                 //загружаем страницу категории
        curl_setopt($b,CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($b,CURLOPT_HEADER,true);
        curl_setopt($b,CURLOPT_RETURNTRANSFER,true);
        $html_temp = curl_exec($b);                                              //получаем главную страницу
        curl_close($b);
        
        phpQuery::newDocument($html_temp);                              //инициализируем класс для страницыкатегории

        $list_submenu_item_dom = pq('ul.maincatalog-list-1')->children('li:not(.j-all-menu-item)'); //получаем список подкатегорий
        foreach ($list_submenu_item_dom as $keys => $val) {                                         //парсим список подкатегорий
            $li_submenu = pq($val)->children('a');
            
            if((bool)strripos( $li_submenu->attr('href'),'aspx')==false)
            {
                $list_menu_items_tmp[$key][$keys]['cat_id'] = $value['id'];
                $list_menu_items_tmp[$key][$keys]['name'] = $li_submenu->html();
                $list_menu_items_tmp[$key][$keys]['link'] = $li_submenu->attr('href');
            }
        }
        phpQuery::unloadDocuments();      //убиваем класс для страницы категории освобождаем место
    } //получили список категорий с сайта
    
    foreach ($list_menu_items_tmp as $k => $val) { //внесли все категории в базу
        foreach ($val as $key => $value) {//вносим подкатегории в базу
            $query = "INSERT INTO `Subcategory`(`cat_id`, `subcat_name`, `subcat_link`, `date`) "
                    . "VALUES ('" . $value['cat_id'] . "', '" 
                    . $value['name'] . "','" 
                    . $value['link'] . "','" 
                    . date('Y') . '.' 
                    . date('m') . '.' 
                    . date('d') . "')";
            $result = mysql_query($query);
        }
    }
    
    $result = Array(); //вытащили все падкатегории из базы с id категории и подкатегории
    $query_to_get_subcategoru = "SELECT `cat_id`, `subcat_id`,`subcat_name`,`subcat_link` FROM `Subcategory`"; //составляем запрос к базе
    $result_query = mysql_query($query_to_get_subcategoru) or die('Query failed: ' . mysql_error()); // делаем запрос к базе
    while ($line = mysql_fetch_array($result_query, MYSQL_ASSOC)) {// составляем ответ
        $result[count($result)] = $line;
    }
    foreach ($list_menu_items as $key => $value) {
        foreach ($result as $k => $v) {
            if($value['id']==$v['cat_id'])
            {
                $list_menu_items[$key]['subcategories'][$k]['subcat_id'] = $v['subcat_id'];
                $list_menu_items[$key]['subcategories'][$k]['name'] = $v['subcat_name'];
                $list_menu_items[$key]['subcategories'][$k]['link'] = $v['subcat_link'];
            }
        }
    };
    /*-------------------------------------------------------------------*/
}

function get_inf_of_count_item()
{
/*--------------------------получаем информацию о страницах для парсинга--------------------*/

    foreach ($list_menu_items as $key => $category) {                           //проходим по всем категориям
        foreach ($category['subcategories'] as $cat_key => $subcategory) {      //проходим по всем подкатегориям
            $html_temp = file_get_contents($main_url . $subcategory['link']);   //для каждой подкатегории нужно развернуть страницу и получить из нее данные
            phpQuery::newDocument($html_temp);                                  //создаем класс для этой страницы

            $list_menu_items[$key]['subcategories'][$cat_key]['count_product'] = pq('.total.many>span:not(.active)')->text();//выдираем количество продуктов подкатегории
            foreach (pq('.pager-bottom .pager .pageToInsert')->children('a') as $k => $v) {//получаем количество страниц
                if ($k + 2 == pq('.pager-bottom .pager .pageToInsert')->children('a')->count())
                    $list_menu_items[$key]['subcategories'][$cat_key]['count_page'] = pq($v)->html();
            }

            phpQuery::unloadDocuments();//убиваем класс страницы подкатегории
        }
    }

/*------------------------------------------------------------------------------------------*/
}

function pars_page($subcat)
{
    /*--------------------------------парсим страницы--------------------------------------------*/
global $main_url, $list_menu_items,$page_get_request;

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

                if(pq($qq)->children('.price')->children('ins')->html()=='')   //цену на товар, новую и старую. если есть
                {
                    $price_old = preg_replace("/[^0-9]/", '',pq($qq)->children('.price')->text());
                    $price_new = '';
                }
                else
                {
                    $price_old = preg_replace("/[^0-9]/", '',pq($qq)->children('.price')->children('ins')->text());
                    $price_new = preg_replace("/[^0-9]/", '',pq($qq)->children('.price')->children('del')->text());
                }

                /*$list_menu_items[$key]['subcategories'][$cat_key]['items'][$q]['id'] = $id;
                $list_menu_items[$key]['subcategories'][$cat_key]['items'][$q]['link'] = $link;
                $list_menu_items[$key]['subcategories'][$cat_key]['items'][$q]['price_old'] = $price_old;
                $list_menu_items[$key]['subcategories'][$cat_key]['items'][$q]['price_new'] = $price_new;*/
            }
            phpQuery::unloadDocuments();
        }
    }
}
/*-------------------------------------------------------------------------------------------*/
}