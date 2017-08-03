<?php

class subcategory extends ORM
{
    public function __construct()
    {
        parent::__construct();
        $tName = "subcategory";
        parent::readTable($tName);
        if (empty(self::$resReading)) {           //таблица либо пустая, либо ее нет
            unset(self::$resReading);           //удаляем переменную чтения

            self::db_create_table($tName);           //создаем таблицу для категорий
            self::$category = self::readTable('category');      //получаем категории из базы
            self::$category = self::$resReading;
            unset(self::$resReading);
            self::$subcategory = Parser::pars_subcategory(self::$category); //парсим подкатегории
            if (self::$subcategory) { //если спарсили
                $value = array();
                foreach (self::$subcategory as $key => $val) {
                    $value[] = "'" . $val["cat_id"] . "'," . "'" . $val["name"] . "'," . "'" . $val["link"] . "'";
                }
                $field = "`cat_id`, `subcat_name`, `subcat_link`";
                self::$query = array(
                    $tName => array(
                        $field => $value
                    )
                );
                parent::db_write();             //пишем в таблицу
            }
        } else {
            self::$subcategory = self::$resReading;
        }
        unset(self::$resReading);
        unset(self::$tName);
    }

    //создать таблицу
    private function db_create_table($tName)
    {
        if (parent::db_check_database()) {
            $query = "CREATE TABLE `" . self::$db_name . "`.`" . $tName . "` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `cat_id` INT NOT NULL,
                `subcat_name` VARCHAR(255) NOT NULL,
                `subcat_link` VARCHAR(255) NOT NULL,
                PRIMARY KEY(`id`)
              ) ENGINE = InnoDB";
            if (mysql_query($query)) return true;
            else return false;
        } else {
            return false;
        }
    }
}