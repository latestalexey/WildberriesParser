<?php

class product extends ORM
{

    public static function write()
    {
        parent::__construct();
        $tName = "product";
        parent::readTable($tName, 1);
        /*if(empty(parent::resReading)) {           //таблица либо пустая, либо ее нет
            unset(parent::resReading);           //удаляем переменную чтения
            self::db_create_table($tName);           //создаем таблицу для товаров
        }*/
    }

    //создать таблицу
    private function db_create_table($tName)
    {
        if (parent::db_check_database()) {
            $query = "CREATE TABLE `" . self::$db_name . "`.`" . $tName . "` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `product_id` VARCHAR(255) NOT NULL,
                `link` VARCHAR(255) NOT NULL,
                `prise_old` INT,
                `prise_new`INT,
                `data_frome`DATE,
                `data_to`DATE,
                PRIMARY KEY(`id`)
              ) ENGINE = InnoDB";
            if (mysql_query($query)) return true;
            else return false;
        } else {
            return false;
        }
    }
}