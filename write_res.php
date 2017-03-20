<?php
function a($list_menu_items, $link)
{
    foreach ($list_menu_items as $key => $value) {
        $query ="INSERT INTO `Category`(`cat_name`, `cat_link`, `date`) "
            . "VALUES ('".$value['name']."','".$value['link']."','".date('Y').'.'.date('m').'.'.date('d')."')";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    }
}

function get_time_of_last_check()       //получает дату обновления категории и подкатегории
{
    $query ="SELECT `date` FROM `Category` GROUP BY `date`";
    $result = '';
        
        $result_q = mysql_query($query) or die('Query failed: ' . mysql_error());
        
        while ($line = mysql_fetch_array($result_q, MYSQL_ASSOC)) {
            $result = $line['date'];
        }
    
    return $result;
}

function pars_category()                                                //парсим категории
{
    global $main_url, $list_menu_items,$start;
    
    /*-----------------получаем список категорий-------------------------*/
    $html = file_get_contents($main_url);                               //получаем главную страницу
    phpQuery::newDocument($html);                                       //инициализация класса для главной страницы

    $list_menu_item_dom = pq('ul.topmenus')->children('li:not(.divider)'
            . ':not(.submenuless)'
            . ':not(.row-divider)'
            . ':not(.promo-offer)'
            . ':not(.brands)'
            . ':not(.certificate)');                                    // получаем список категорий
    foreach ($list_menu_item_dom as $key => $value) {
        $li = pq($value)->children('a');                                //вытаскиваем элемент "ссылка"

        if ($li->html() !== '' && $li->attr('href') !== '') {           //если ссылка и имя не пустые
            $list_menu_items[$key]['name'] = $li->html();               // то записываем название категории
            $list_menu_items[$key]['link'] = $li->attr('href');         // и ссылку на ее страницу
        }
    }
    phpQuery::unloadDocuments();                                        //убиваем класс для главной страницы, освобождаем место
    $time = microtime(true) - $start;                                   //сохраняем время работы скрипта
    //printf('Чтение категорий завершено через %.4F сек.</br>', $time);   //вывводим время работы скрипта
    /*-------------------------------------------------------------------*/
}