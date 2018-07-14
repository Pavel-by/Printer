<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/categories.php";
require_once ROOT . "/class/php/script.php";
require_once ROOT . "/class/php/user.php";

$user = new User();

if ($user->getAccess() < User::ACCESS_ADMIN) {
    Script::close("Недостаточно прав");
}

if (!isset($_REQUEST['type'])) {
    Script::close("Не указан тип запроса");
}

$categoriesClass = new Categories();

switch ($_REQUEST['type']) {
    case "set":
        if (!isset($_REQUEST['categories'])) {
            Script::close("Не указан список категорий");
        }
        $categories = array();
        foreach (preg_split("/(;)/", $_REQUEST['categories']) as $line) {
            $temp = preg_split("/(:)/", $line);
            if (count($temp) !== 2) {
                Script::close("Неверное значение категорий");
            }
            $categories[] = array("name" => $temp[1], "english" => $temp[0]);
        }
        if ($categoriesClass->setCategories($categories)) {
            Script::close("Успешно", true);
        } else {
            Script::close("Ошибка при добавлении в базу данных");
        }
        break;
    default:
        Script::close("Неизвестный тип запроса");
}

