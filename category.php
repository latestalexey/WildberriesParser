<?

class category extends ORM
{
    //public function __construct() {
    public static function sfg()
    {
        $tName = "category";
        parent::readTable($tName);
        if (empty(self::resReading)) {           //таблица либо пустая, либо ее нет
            unset(self::resReading);             //удаляем переменную чтения
            self::db_create_table($tName);           //создаем таблицу для категорий
            self::$category = Parser::pars_category('https://www.wildberries.ru');      //парсим категории
            if (self::$category) {                //если парсинг удался
                //создаем параметр для записи в таблицу
                $value = array();
                foreach (self::$category as $key => $val) {
                    $value[] = "'" . $val["name"] . "'," . "'" . $val["link"] . "'";
                }
                $field = "`cat_name`,`cat_link`";
                self::$query = array(
                    $tName => array(
                        $field => $value
                    )
                );
                parent::db_write();             //пишем в таблицу
            }
        } else {
            self::$category = self::$resReading;
        }
        unset(self::$resReading);
    }

    //создать таблицу
    private function db_create_table($tName)
    {
        if (parent::db_check_database()) {
            $query = "CREATE TABLE `" . self::$db_name . "`.`" . $tName . "` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `cat_name` VARCHAR(255) NOT NULL,
                `cat_link` VARCHAR(255) NOT NULL,
                PRIMARY KEY(`id`)
              ) ENGINE = InnoDB";
            if (mysql_query($query)) return true;
            else return false;
        } else {
            return false;
        }
    }
}
