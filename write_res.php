<?php
function a($list_menu_items, $link)
{
    # Фильтрация строк и вывод нужной информации
    /*while ($row = mysql_fetch_object($result)) {
        echo $row->name;
    }*/
    foreach ($list_menu_items as $key => $value) {
        $query ="INSERT INTO `Category`(`cat_name`, `cat_link`, `date`) "
            . "VALUES ('".$value['name']."','".$value['link']."','".date('d.m.Y')."')";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
    }
}