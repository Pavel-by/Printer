<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/user.php";
require_once ROOT . "/class/php/script.php";
require_once ROOT . "/class/php/orders.php";

$user = new User();
$orders = new Orders();

if ($user->getAccess() < User::ACCESS_ADMIN) {
    Script::close("Недостаточно прав");
}

if (!isset($_REQUEST['type'])) {
    Script::close("Не указан тип запроса");
}
switch ($_REQUEST['type']) {
    case "get":
        if (($result = $orders->get($_REQUEST)) !== false) {
            Script::close("Успешно", true, array( "orders" => $result));
        } else {
            Script::close("(системная ошибка) Ошибка при попытке получения списка заказов");
        }
    case "update":
        if (!isset($_REQUEST['id'])){
            Script::close("(системная ошибка) Для редактирования заказа нужно указать его id");
        }
        if ($orders->update($_REQUEST)) {
            Script::close("Успешно", true);
        } else {
            Script::close("Ошибка при попытке изменения записи");
        }
}
Script::close("Ошибка");