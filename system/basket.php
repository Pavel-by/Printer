<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/basket.php";

$type  = false;
$id    = false;
$count = 1;

if ( isset( $_POST[ 'type' ] ) ) {
    $type = $_POST[ 'type' ];
}

if ( isset( $_POST[ 'id' ] ) and is_numeric( $_POST[ 'id' ] ) ) {
    $id = $_POST[ 'id' ];
}

if ( isset( $_POST[ 'count' ] ) and is_numeric( $_POST[ 'count' ] ) ) {
    $count = $_POST[ 'count' ];
}

if ( $type !== false ) {
    $basket = new Basket();
    switch ( $type ) {
        case 'get':
            $preview = false;
            if (isset($_POST['preview']) and ($_POST['preview'] === true or $_POST['preview'] === "true")) {
                $preview = true;
            }
            echo json_encode( $basket->get($preview) );
            break;

        case 'add':
            if ( $id !== false ) {
                $basket->add( $id, $count );
                echo json_encode( array(
                    "success" => true,
                    "size" => $basket->size()
                ) );
            } else {
                echo json_encode( array(
                    "error" => "Has not `ID`"
                ) );
            }
            break;

        case 'remove':
            if ( $id !== false ) {
                $basket->remove( $id );
                echo json_encode( array(
                    "success" => true,
                    "size" => $basket->size()
                ) );
            } else {
                echo json_encode( array(
                    "error" => "Has not `ID`"
                ) );
            }
            break;

        case 'clear':
            $basket->clear();
            echo json_encode(array(
                'size' => $basket->size()
            ));
            break;

        case 'size':
            echo json_encode(array(
                'size' => $basket->size()
            ));
            break;

        default:
            echo json_encode( array(
                "error" => "Unknown `type` value"
            ) );
    }
} else {
    echo json_encode( array(
        "error" => "Has not `type`"
    ) );
}