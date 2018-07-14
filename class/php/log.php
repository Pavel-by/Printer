<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

class Log
{
    const FILE_D = ROOT . "/logs/log-d.txt";
    const FILE_E = ROOT . "/logs/log-e.txt";
    const MAX_TAG_LEN = 16;

    public static function d( $tag, $text )
    {
        $f = fopen( self::FILE_D, 'a' );
        $s = sprintf(
            "%20s: %" . self::MAX_TAG_LEN . "s: ",
            date( "Y-m-d H:i:s" ),
            $tag
        ) . $text . "\n";
        fwrite($f, $s);
        fclose($f);
    }

    public static function e( $tag, $text )
    {
        $f = fopen( self::FILE_E, 'a' );
        $s = sprintf(
                "%20s: %" . self::MAX_TAG_LEN . "s: ",
                date( "Y-m-d H:i:s" ),
                $tag
            ) . $text . "\n";
        fwrite($f, $s);
        fclose($f);
    }
}