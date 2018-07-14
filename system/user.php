<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/user.php";
require_once ROOT . "/class/php/script.php";

$user = new User();

if ( !isset( $_REQUEST[ 'type' ] ) ) {
    Script::close( "Ошибка при получении данных на сервере: не указан тип запроса" );
}

$type = $_REQUEST[ 'type' ];

switch ( $type ) {
    case "sign-in":
        if ( isset( $_POST[ 'login' ] ) and isset( $_POST[ 'password' ] ) and
            $user->login( $_POST[ 'login' ], $_POST[ 'password' ] ) ) {
            Script::close( "Успешно", true );
        } else {
            Script::close( "Неверный логин или пароль" );
        }
        break;
    case "sign-out":
        $user->logout();
        header("Location:/");
        break;
    default:
        Script::close( "Ошибка при обработке данных на сервере: неизвестный тип запроса ($type)" );
}

Script::close( "Неизвестная ошибка" );