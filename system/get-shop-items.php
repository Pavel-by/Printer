<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/shop.php";
require_once ROOT . "/class/php/log.php";
require_once ROOT . "/class/php/categories.php";

$shop            = new Shop();
$categoriesClass = new Categories();

$searchParams = array();
if ( isset( $_POST[ 'category' ] ) ) {
    $searchParams[ 'category' ] = $_POST[ 'category' ];

}

if ( isset( $_POST[ 'name' ] ) ) {
    $searchParams[ 'name' ] = $_POST[ 'name' ];
}

if ( isset( $_POST[ 'id' ] ) ) {
    $searchParams[ 'id' ] = $_POST[ 'id' ];
}

if ( isset( $_POST[ 'page' ] ) and is_numeric( $_POST[ 'page' ] ) and (int)$_POST[ 'page' ] > 0
    and isset( $_POST[ 'pageSize' ] ) and is_numeric( $_POST[ 'pageSize' ] )
    and (int)$_POST[ 'pageSize' ] > 0 ) {
    $page                   = (int)$_POST[ 'page' ];
    $pageSize               = $_POST[ 'pageSize' ];
    $searchParams[ 'from' ] = ( $page - 1 ) * $pageSize;
    $searchParams[ 'to' ]   = $page * $pageSize;
}

$result = array(
    'items' => false,
    'total' => false
);
if ( ( $lines = $shop->find( $searchParams ) ) !== false ) {
    $result[ 'items' ]       = $lines;
    $searchParams[ 'count' ] = true;
    if ( ( $count = $shop->find( $searchParams ) ) !== false ) {
        $result[ 'total' ] = $count;
    }
}

if ( $result[ 'items' ] !== false and $result[ 'total' ] !== false ) {
    echo json_encode( $result );
} else {
    echo json_encode( array(
        "error" => "Ошибка при получении данных"
    ) );
}