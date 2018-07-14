<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/user.php";
require_once ROOT . "/class/php/cartridges.php";
require_once ROOT . "/class/php/script.php";

$user = new User();

if ( $user->getAccess() < $user::ACCESS_ADMIN ) {
    Script::close( "Недостаточно прав" );
}

if ( !isset( $_REQUEST[ 'type' ] ) ) {
    Script::close( "Не указан тип запроса" );
}

$cartridges = new Cartridges();


switch ( $_REQUEST[ 'type' ] ) {
    case "add":
        if ( $cartridges->add( $_REQUEST ) ) {
            Script::close( "Успешно", true );
        } else {
            Script::close( "Ошибка" );
        }
        break;
    case "edit":
        if ( $cartridges->edit( $_REQUEST ) ) {
            Script::close( "Успешно", true );
        } else {
            Script::close( "Ошибка" );
        }
        break;
    case "remove":
        if ( !isset( $_REQUEST[ 'id' ] ) or $cartridges->remove( $_REQUEST[ "id" ] ) ) {
            Script::close( "Успешно", true );
        } else {
            Script::close( "Ошибка" );
        }
        break;
    case "get":
        Script::close( "Успешно", true,
            array( "cartridges" => $cartridges->getCartridges() ) );
        break;
    default:
        Script::close( "Неизвестный тип запроса " . $_REQUEST[ 'type' ] );
}