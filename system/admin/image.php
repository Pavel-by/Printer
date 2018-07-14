<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/image.php";
require_once ROOT . "/class/php/user.php";
require_once ROOT . "/class/php/script.php";

$user = new User();
if ( $user->getAccess() < User::ACCESS_ADMIN ) {
    Script::close( "Недостаточно прав" );
}

if ( !isset( $_REQUEST[ 'type' ] ) ) {
    Script::close( "Не указан тип запроса." );
}

$type = $_POST[ 'type' ];

switch ( $type ) {
    case "add":
        if ( !isset( $_REQUEST[ 'dir' ] ) or !preg_match( "/(^\/?images\/)/", $_REQUEST[ 'dir' ] ) ) {
            Script::close( "Не указана директория сохранения или она имеет неверный формат" );
        }

        $saveDir = trim($_REQUEST[ 'dir' ], "\\\/");
        $time    = time();
        $i       = 0;
        $result  = array();

        foreach ( $_FILES as $file ) {
            $temp = preg_split( "/(\.)/", $file[ 'name' ] );
            $name = "$time-$i." . $temp[ count( $temp ) - 1 ];
            if ( Image::add( $saveDir, $file[ 'tmp_name' ], $name ) ) {
                $result[] = $name;
            };
        }
        Script::close( "Успешно", true, array( "files" => $result ) );
        break;
    case 'get':
        if ( !isset( $_REQUEST[ 'dir' ] ) or !preg_match( "/(^\/?images\/)/", $_REQUEST[ 'dir' ] ) ) {
            Script::close( "Не указана директория сохранения или она имеет неверный формат" );
        }
        $dir = $_REQUEST[ 'dir' ];
        Script::close( "Успешно", true, array( "images" => Image::get( $dir ) ) );
        break;
    default:
        Script::close( "Неизвестный тип запроса" );
}


