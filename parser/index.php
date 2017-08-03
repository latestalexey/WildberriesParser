<?php
require_once '../vendor/autoload.php';
require_once '../db_connect/db_connector.php';
require_once '../Parser.php';

$main_url = 'https://www.wildberries.ru';   //адрес магазина
$page_get_request = '?page=';               //добавочный адрес страница
$page_size = '&pagesize=40';               //выводить по 200 товаров на страницу
$fullArray = Array();                       //объявляем массив для данных
$max_connet = 24;

$Category = new category;
$subCategory = new subcategory;

$fullArray = $Category->category;
$subcat = $subCategory->subcategory;

foreach ($fullArray as $key => $value) {
    foreach ($subcat as $k => $v) {
        if ($value["id"] == $v["cat_id"])
            $fullArray[$key]["subcategory"][] = $v;
    }
}
unset($Category, $subCategory);

//составляем пакеты ссылок для парсинга
foreach ($fullArray as $key => $value) {
    foreach ($value["subcategory"] as $k => $v) {                                      //по всем подкатегориям
        $maxPage = Parser::get_inf_of_count_item($main_url . $v["subcat_link"] . $page_get_request . "1" . $page_size);  //парсим количество страниц в подкатегории
        for ($thisPage = 1, $i = 0; $maxPage >= $thisPage; $i++, $thisPage++) {            //цикл по созданию пакетов

            $urls[$i] = $main_url . $v["subcat_link"] . $page_get_request . $thisPage . $page_size; //пакет ссылок для передачи для загрузки

            if ($thisPage % $max_connet == 0) {
                $i = 0;
                //$data = Parser::pars_page($urls);                                       //передача пакета
                product::write();
                /*внесение в базу*/
                unset($urls, $data);
                $urls = Array();
            }

            if (($maxPage - ($thisPage - ($i))) == count($urls)) {
                $i = 0;
                //$data = Parser::pars_page($urls);                                       //передача пакета
                /*внесение в базу*/
                unset($urls, $data);
                $urls = Array();
            }
        }
    }
}

//xprint( $fullArray );  //получаем массив категорий


/*
get_categiry($main_url);                             //получам категории и подкатегории в переменную

$urls = Array();                            //список первых страниц каждой подкатегории
foreach ($list_menu_items as $catkey => $catvalue) {
    foreach ($catvalue['subcategories'] as $subcatkey => $subcatvalue) {
        $urls[count($urls)] = $main_url.$subcatvalue['link'];
    }
}

//$urls = array_chunk($urls, 20);            //разбили массив категорий на пакеты

/*foreach ($urls as $key => $value) {        // спарсили каждый пакет
    $htmls[$key] = multyrequest($value);
}*//*
    mysql_close();
    for($j=0;$j<100;$j++)
    {
    $res='';



    $a=0;//количество проходов
    $start = microtime(true);//начало отсчета времени работы скрипта
    //foreach ($urls as $key => $url) {
        $ex=true;
        $corent_page = 1;                                   //текущая страница
        $htmls = Array();                                   //массив с ответами
        while($ex)
        {
            $pages_array = Array();                         //массив страниц

            for($i=$corent_page;$i<$corent_page+$max_connet;$i++)    //собрали массив страниц для парсинга
            {
                if($i!=1)
                    $pages_array[count($pages_array)] = $urls[0].$page_get_request.$i;
                else
                    $pages_array[count($pages_array)] = $urls[0];
            }

            $corent_page += $max_connet;

            while(isset($pages_array))
            {
                $htmls_tmp = multyrequest($pages_array);            //получили ответ со страницами
                unset($pages_array);
                foreach ($htmls_tmp as $key => $html) {
                    if((strpos($html['head'], 'TP/1.1 200 OK'))==FALSE)
                    {
                        unset($htmls_tmp[$key]);
                        $pages_array[count($pages_array)] = $key;
                        //$ex=false;
                    }

                    //xprint($html['head']);
                }
                //xprint($pages_array);

                $htmls += $htmls_tmp;
                $a++;
            }


            if($corent_page>1000)$ex=false;

        }
        */
//}
//xprint($a);
//xprint($htmls);
/*$time = microtime(true) - $start;//сохраняем время работы скрипта

$res ="('".$max_connet."','".$time."','".$a."','1','100','1000')";
mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
mysql_select_db('test') or die('Не могу выбрать базу данных');
$query = "INSERT INTO `max_connect`(`max_con`,`time_to_con`,`count_step`,`con_time_out`,`usleep`,`count_page`) "
                . "VALUES ".$res;
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
mysql_close();
}*/
/*mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
mysql_select_db('test') or die('Не могу выбрать базу данных');
$query = "INSERT INTO `max_connect`(`max_con`,`time_to_con`,`count_step`,`con_time_out`,`usleep`,`count_page`) "
                . "VALUES".$res;
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
mysql_close();*/
//printf('Чтение подкатегорий завершено через %.4F сек.</br>', $time);//вывводим время работы скрипта