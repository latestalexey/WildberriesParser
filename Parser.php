<?
/*
 * класс для работы со страницей, парсинг
 */

class Parser{
    function __construct(){
        $this->multi = curl_multi_init();
        $this->handles = Array();
        return $this;
    }
    
    function __destruct() {
        if (is_resource($this->multi)) {
            curl_multi_close($this->multi);
        }
    }
    
    private static function request($urls){
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
            curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36');

            curl_multi_add_handle($this->multi, $ch);
            $this->handles[$url] = $ch;
        }
        $active = null;
        do {
            $mrc = curl_multi_exec($this->multi, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($this->multi) == -1) {
                usleep(100);
            }
            do {
                $mrc = curl_multi_exec($this->multi, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }

        foreach ($this->handles as $key => $channel) {
            $tmp = curl_multi_getcontent($channel);
            $response[] = $tmp;
            curl_multi_remove_handle($this->multi, $channel);
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
    
    public static function get_categiry($main_url){
        $list_category = null;
        
        /*--------------- запросы на категории -------------------*/
        //двумерный массив, ключи - имена таблиц, значения - имена полей
        $arRead = array(
            "Category"=>array(
                "cat_id",
                "cat_name",
                "cat_link",
            )
        );
        
        //если база не пустая, вытащили инфу из нее, иначе парсим
        if($list_category = db_ORM::db_reading($arRead)){
            return $list_category;
        }else{
            if($list_category = self::pars_category($main_url)){
                db_ORM::db_write($arWrite);
                return $list_category;
            }
            else
                return false;
        }
        /*--------------- канец запросов на категории -------------------*/

        /*--------------- запросы на подкатегории -----------------------*/
        /*$result = null;
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
        }*/
        /*--------------- канец запросов на подкатегории -------------------*/
        
        return db_ORM::db_check_connection();
    }
}