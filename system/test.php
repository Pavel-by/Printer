<?php
    //echo hash('sha512', 'pohjuava');
$arr = preg_split("/(\?)/", $_SERVER['REQUEST_URI']);
var_dump($arr);
