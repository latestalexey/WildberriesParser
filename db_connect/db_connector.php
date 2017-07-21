<?
/*
 * класс для подключения и работы с базой
 */

 class db_ORM{
    private static $db_name = 'WildberriesParser';
    private static $db_host = 'localhost';
    private static $db_login = 'root';
    private static $db_pass = '';
    
    // проверка подключения к субд
    public static function db_check_connection(){
        if(mysql_connect(self::$db_host, self::$db_login, self::$db_pass))
        {
            mysql_close();
            return true;
        }
        else return false;
    }
    
    //открытие подключения к базе
    private static function db_open_connection(){
        $connection = mysql_connect(self::$db_host, self::$db_login, self::$db_pass);
        return $connection;
    }
    
    //закрыть подключение к базе
    private static function db_close_connection(){
         mysql_close();
     }
    
    // проверка наличия базы
    private static function db_check_database(){
        if(self::db_open_connection())
        {
            if(mysql_select_db(self::$db_name))return true;   //база есть
            else return false;                                //базы нет
        }
        else die("Не могу подключиться к СУБД");
    }
    
    //создать базу
    private static function db_create_new_base(){
        $return = null;
        if(!self::db_check_database())
        {
            $sql = 'CREATE DATABASE '.self::$db_name;
            if (mysql_query($sql, self::db_open_connection()))$return = true;
            else $return = false;
        }
        else $return = true;   //база или есть или ее создали
        self::db_close_connection();
        
        return $return;
    }
    
    //создать таблицу
    private static function db_create_table($Table,$ItemWrite){
        $return = null;
        if(self::db_check_database()){
            if(mysql_query('SELECT id FROM '.$Table)){
                $return = true;
            }else{
                foreach($ItemWrite as $key=>$Item){
                    if(gettype($key)=='string')$type = "TEXT";
                    else $type = gettype($key);
                    $field[] = "`".$key."` ".$type;
                }
                $field = implode(",", $field);
                $query = "CREATE TABLE `".$Table."` (
                    `id` INT AUTO_INCREMENT,".$field.",
                    `Date_Time` DATE,
                    PRIMARY KEY(`id`)
                  )";
                if(mysql_query($query))$return = true;
                else $return = false;
            }
        }else{
            $return = false;
        }
        return $return;
    }
    
    //читать из базы, двумерный массив, ключи - имена таблиц, значения - имена полей
    public static function db_reading($arRead){
        if(self::db_open_connection()){
            foreach($arRead as $Table=>$ItemRead){
                foreach($ItemRead as $key=>$Item){
                    $select[] = "`".$Item.".".$Table."`";
                }
                $table[] = "`".$Table."`";
            }
            $select = implode(",", $select);    //строка полей
            $table = implode(",",$table);       //строка таблиц
            
            $query = "SELECT ".$select." FROM ".$table;                                     //составляем запрос к базе
            if($result_query = mysql_query($query)){                                        // делаем запрос к базе, если нечего читать вернем лож
                while ($line = mysql_fetch_array($result_query, MYSQL_ASSOC)) {             // составляем ответ
                    $result[] = $line;
                }
            }else return false;
            
        }else die('');
        self::db_close_connection();
        return $result;
    }
    
    //писать в базу трёхмерный массив, список значений для внесения, вторая вложенность: ключ - имя таблицы,третья вложенность ключ - имя поля, значение - значение.
    public static function db_write($arWrite){
        if(self::db_open_connection()){                     //если подключились к субд
            foreach ($arWrite as $key=>$arField){
                foreach($arField as $Table=>$ItemWrite){
                    if(self::db_create_new_base()){         //если есть или удалось создать базу
                        if(self::db_create_table($Table,$ItemWrite)){   //есть ли таблица или удалось создать
                            $fields = Array();
                            $value = Array();
                            foreach($ItemWrite as $field=>$Item){
                                $fields[] = "`".$field."`";
                                $value[] = "'".$Item."'";
                            }
                            $Field = implode(",", $fields);
                            $Value = implode(",", $value);

                            $query = "INSERT INTO `".$Table."` (".$Field.",`Date_Time`) "
                                . "VALUES (".$Value.", '".date('Y') . "." . date('m') . "." . date('d')."')";
                            if(mysql_query($query))return true;     //если удалось выполнить запрос на внесение
                        }else{
                            return false;
                        }
                    }else{
                        die('Не могу записать');
                    }
                }
            }  
            return true;
        }else die('Не могу записать');
    }
}