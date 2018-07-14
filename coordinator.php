<?php

define( 'ROOT', $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/coordinator.php";

$coordinator = new Coordinator();

$url = preg_split( "/(\?)/", $_SERVER[ 'REQUEST_URI' ] )[ 0 ];

if ( $coordinator->hasPage( $url ) ) {
    if ( $coordinator->hasAccess( $url ) ) {
        $coordinator->writePage( $url );
    } else {
        $coordinator->writePage( Coordinator::URL_401 );
    }
} else {
    $coordinator->writePage(Coordinator::URL_404);
}