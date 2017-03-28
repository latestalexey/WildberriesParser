<?php

function xprint($param, $title = 'Отладочная информация') {
    ini_set('xdebug.var_display_max_depth', 50);
    ini_set('xdebug.var_display_max_chidren', 25600);
    ini_set('xdebug.var_display_max_data', 999999999);
    if (PHP_SAPI == 'cli') {
        echo "\n--------------[$title]---------------\n";
        echo var_dump($param);
        echo "\n-------------------------------------\n";
    } else {
        ?>
        <style>
            .xprint-wrapper{
                padding: 10px;
                margin-bottom: 25px;
                color: black;
                position: relative;
                top: 10px;
                border: 1px solid gray;
                font-family: InputMono, monospace;
            }
        </style>
        <div class="xprint-wrapper">
            <div class="xprint-title">
                <?= $title ?>
            </div>
            <pre style="color: #000;">
                <?= htmlspecialchars(var_dump($param)) ?>
            </pre>
        </div><?php
    }
}

function xd($var, $title = NULL) {
    xprint($var, $title);
    die();
}
