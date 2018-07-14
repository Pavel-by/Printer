<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/user.php";
require_once ROOT . "/class/php/shop.php";
require_once ROOT . "/class/php/script.php";

$user = new User();

if ( $user->getAccess() < $user::ACCESS_ADMIN ) {
    Script::close( "Недостаточно прав" );
}

if (!isset($_REQUEST['type'])) {
    Script::close("Не указан тип запроса");
}

$shop = new Shop();

switch ($_REQUEST['type']) {
    case "add":
        if ($shop->add($_REQUEST)) {
            Script::close("Успешно", true);
        } else {
            Script::close("Ошибка");
        }
        break;
    case "edit":
        if ($shop->edit($_REQUEST)) {
            Script::close("Успешно", true);
        } else {
            Script::close("Ошибка");
        }
        break;
    case "remove":
        if (!isset($_REQUEST['id'])) {
            Script::close("Не указан ID удаляемого элемента");
        }
        if ($shop->remove($_REQUEST['id'])) {
            Script::close("Успешно", true);
        } else {
            Script::close("Ошибка");
        }
        break;
    default:
        Script::close("Неизвестный тип запроса");
}
