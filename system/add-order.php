<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/orders.php";
require_once ROOT . "/class/php/script.php";

if (
    !isset( $_POST[ 'name' ] ) or
    !isset( $_POST[ 'email' ] ) or
    !isset( $_POST[ 'phone' ] ) or
    !isset( $_POST[ 'list' ] )
) {
    Script::close( "Невозможно добавить заказ." );
}

$name  = $_POST[ 'name' ];
$email = $_POST[ 'email' ];
$phone = $_POST[ 'phone' ];
$list  = array();
foreach ( preg_split( "/(,)/", $_POST[ 'list' ] ) as $s ) {
    $temp = preg_split( "/(:)/", $s );
    if ( count( $temp ) !== 2 ) {
        Script::close( "Ошибка при добавлении заказа. Неверный формат взодных данных." );
    }
    if ( (int)$temp[ 1 ] > 0 ) {
        $list[] = array(
            "id"    => $temp[ 0 ],
            "count" => $temp[ 1 ]
        );
    }
}
$orders = new Orders();
$params = array(
    "name"  => $name,
    "email" => $email,
    "phone" => $phone,
    "list"  => $list
);
if ( $orders->add( $params ) ) {
    Script::close( "Заказ был успешно добавлен", true );
} else {
    Script::close( "Ошибка при добавлении заказа" );
}

