<?php

# Соединение
mysql_connect('localhost', 'root', '') or die('Could not connect: ' . mysql_error());
 
# Выбор базы данных
mysql_select_db('WildberriesParser') or die('Не могу выбрать базу данных');
