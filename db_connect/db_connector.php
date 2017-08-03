<?
/*
 * класс для подключения и работы с базой
 */

class ORM
{
    public function __construct()
    {
        /*
    public static $this->db_name = 'WildberriesParser';
    public static $this->host = 'localhost';
    public static $this->login = 'root';
    public static $this->pass = '';
         */
    }

    public static $db_name = 'WildberriesParser';
    public static $host = 'localhost';
    public static $login = 'root';
    public static $pass = '';

    protected function db_open_connection()
    {
        $connection = mysql_connect(self::$host, self::$login, self::$pass);
        return $connection;
    }
    
    //закрыть подключение к базе
    protected function db_close_connection()
    {
         mysql_close();
     }
    
    // проверка наличия базы
    protected function db_check_database()
    {
        if(self::db_open_connection())
        {
            if (mysql_select_db(self::db_name)) return true;   //база есть
            else return false;                                //базы нет
        }
        else die("Не могу подключиться к СУБД");
    }
    
    //создать базу
    protected function db_create_new_base()
    {
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

    //писать в таблицу
    /*protected function db_write(){
        if(self::db_open_connection()){                     //если подключились к субд
            foreach (self::$query as $Table=>$arFields){
                foreach($arFields as $field=>$ItemWrite){
                    foreach($ItemWrite as $key=>$Item){
                        $query = "INSERT INTO  `".$this->db_name."`.`".$Table."`(".$field.") "
                        . "VALUES (".$Item.")";
                        mysql_query($query);     //если удалось выполнить запрос на внесение
                    }
                }
            }  
            return true;
        }else die('Не могу записать');
    }*/

    //читать из таблицы
    protected function readTable($tName, $limit = false)
    {
        if ($limit) $limitString = " LIMIT " . $limit;
        if (self::db_open_connection($tName)) {
            $query = "SELECT * FROM `" . self::$db_name . "`.`" . $tName . "`" . $limitString;               //составляем запрос к базе
            if($result_query = mysql_query($query)){                                        // делаем запрос к базе, если нечего читать вернем лож
                while ($line = mysql_fetch_array($result_query, MYSQL_ASSOC)) {             // составляем ответ
                    $result[] = $line;
                }
                self::$resReading = $result;
            }else return false;
        }else die('');
        self::db_close_connection();
    }
}