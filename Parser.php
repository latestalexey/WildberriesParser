<?
/*
 * класс для работы со страницей, парсинг
 */

class Parser{
    protected static $agents = array(
        0 => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0; SV1)',
        1 => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)',
        2 => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
        3 => 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727)',
        4 => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)',
        5 => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.6) Gecko/2009011913 Firefox/3.0.6',
        6 => 'Mozilla/5.0 (Windows NT 5.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
        7 => 'Mozilla/5.0 (Windows NT 6.1; rv:5.0) Gecko/20100101 Firefox/5.0',
        8 => 'Opera/9.63 (Windows NT 5.1; U; en)',
        9 => 'Opera/9.80 (Windows NT 5.1; U; en) Presto/2.9.168 Version/11.50',
        10 => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Version/3.1.2 Safari/525.21',
        11 => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/1.0.154.48 Safari/525.19',
        12 => 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1b3pre) Gecko/20090217 Shiretoko/3.1b3pre',
        13 => 'Mozilla/5.0 (compatible; Konqueror/3.5; Linux) KHTML/3.5.10 (like Gecko)',
        14 => 'Opera/9.63 (X11; Linux i686; U; en) Presto/2.1.1',
        15 => 'Lynx/2.8.6rel.5 libwww-FM/2.14 SSL-MM/1.4.1 OpenSSL/0.9.7e-p1',
        16 => 'Links (2.2; FreeBSD 7.0-RC1 i386; 195x65)',
        17 => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.0.6) Gecko/2009011912 Firefox/3.0.6',
        18 => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:5.0.1) Gecko/20100101 Firefox/5.0.1',
        29 => 'Opera/9.62 (Macintosh; Intel Mac OS X; U; en) Presto/2.1.1',
        20 => 'Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; en) Presto/2.9.168 Version/11.50',
        21 => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.122 Safari/534.30',
        22 => 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; en-us) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1',
        23 => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50',
    );
    protected static $handles = Array();

    private static function request($urls){
        $multi = curl_multi_init();
        $response = null;
        foreach ($urls as $key => $url) 
        {
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_USERAGENT, self::$agents[$key]);

            curl_multi_add_handle($multi, $ch);
            self::$handles[$url] = $ch;
        }
        $active = null;
        do {
            $mrc = curl_multi_exec($multi, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($multi) == -1) {
                usleep(100);
            }
            do {
                $mrc = curl_multi_exec($multi, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }

        foreach (self::$handles as $key => $channel) {
            $tmp = curl_multi_getcontent($channel);
            $response[] = $tmp;
            curl_multi_remove_handle($multi, $channel);
        }
        return $response;
    }
    
    // парсим категории
    public static function pars_category($main_url){
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
    return $list_menu_items_tmp;
    /*-------------------------------------------------------------------*/
    }

    public static function pars_subcategory($list_url)
    {
        /*-------------------получаем список подкатегорий--------------------*/
        foreach ($list_url as $key => $value) {
            $urls[] = $value['cat_link'];
        }
        $list_menu_items_tmp = array();
        foreach (self::request($urls) as $key => $value) {                      //пробегаем по всем категриям

            phpQuery::newDocument($value);                              //инициализируем класс для страницыкатегории

            $list_submenu_item_dom = pq('ul.maincatalog-list-1')->children('li:not(.j-all-menu-item)'); //получаем список подкатегорий

            foreach ($list_submenu_item_dom as $keys => $val) {                                         //парсим список подкатегорий
                $li_submenu = pq($val)->children('a');

                if ((bool)strripos($li_submenu->attr('href'), 'aspx') == false) {
                    $count = count($list_menu_items_tmp);
                    $list_menu_items_tmp[$count]['cat_id'] = $key + 1;
                    $list_menu_items_tmp[$count]['name'] = $li_submenu->html();
                    $list_menu_items_tmp[$count]['link'] = $li_submenu->attr('href');
                }
            }
            phpQuery::unloadDocuments();      //убиваем класс для страницы категории освобождаем место
        } //получили список категорий с сайта
        /*-------------------------------------------------------------------*/
        $listSubcat = array();
        $listSubcat[0] = $list_menu_items_tmp[0];
        foreach ($list_menu_items_tmp as $key => $value) {
            $flag = 0;
            foreach ($listSubcat as $k => $v) {
                if (substr_count($v['link'], $value['link']) > 0) {
                    $flag++;
                }
            }
            if ($flag == 0) {
                $listSubcat[] = $value;
            }
        }
        return $listSubcat;
    }

    //получаем количеситво страниц для парсинга
    public static function get_inf_of_count_item($url)
    {
        /*--------------------------получаем информацию о страницах для парсинга--------------------*/

        $html_temp = file_get_contents($url);   //для каждой подкатегории нужно развернуть страницу и получить из нее данные
        phpQuery::newDocument($html_temp);                                  //создаем класс для этой страницы

        //$list_menu_items[$key]['subcategories'][$cat_key]['count_product'] = pq('.total.many>span:not(.active)')->text();//выдираем количество продуктов подкатегории
        foreach (pq('.pager-bottom .pager .pageToInsert')->children('a') as $k => $v) {//получаем количество страниц
            if ($k + 2 == pq('.pager-bottom .pager .pageToInsert')->children('a')->count())
                $count_page = pq($v)->html();
        }

        phpQuery::unloadDocuments();//убиваем класс страницы подкатегории

        return $count_page;
        /*------------------------------------------------------------------------------------------*/
    }

    //парсим страницу
    public static function pars_page($urls)
    {
        $list_menu_items = Array();
        /*--------------------------------парсим страницы--------------------------------------------*/
        foreach (self::request($urls) as $key => $value) {
            phpQuery::newDocument($value);//создаем класс для этой страницы
            foreach (pq('.catalog_main_table .ref_goods_n_p') as $q => $qq) //проходим по всем товарам на страницу
            {
                $id = pq($qq)->children('.l_class')->attr('id');    //вытаскиваем идентификатор товара
                $link = pq($qq)->attr('href');                      //сылку на товар

                if (pq($qq)->children('.price')->children('ins')->html() == '')   //цену на товар, новую и старую. если есть
                {
                    $price_old = preg_replace("/[^0-9]/", '', pq($qq)->children('.price')->text());
                    $price_new = '';
                } else {
                    $price_old = preg_replace("/[^0-9]/", '', pq($qq)->children('.price')->children('ins')->text());
                    $price_new = preg_replace("/[^0-9]/", '', pq($qq)->children('.price')->children('del')->text());
                }

                $list_menu_items[$q]['id'] = $id;
                $list_menu_items[$q]['link'] = $link;
                $list_menu_items[$q]['price_old'] = $price_old;
                $list_menu_items[$q]['price_new'] = $price_new;
            }
            phpQuery::unloadDocuments();
        }
        return $list_menu_items;
        /*foreach ($list_menu_items as $key => $category) {//проходим по всем категориям
            foreach ($category['subcategories'] as $cat_key => $subcategory) {//проходим по всем подкатегориям
                for ($i = 1; $i <=$subcategory['count_page']; $i++)
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
        /*}
        phpQuery::unloadDocuments();
    }
}
}*/
        /*-------------------------------------------------------------------------------------------*/
    }
}