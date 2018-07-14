<?php
$host          = 'localhost';
$user          = 'root';
$password_link = '';
$db_name       = 'printer';
//$link=mysqli_connect($host,$user,$password_link,$db_name) or die('error');

$mysqli = new mysqli( $host, $user, $password_link, $db_name );

class DB
{
    private static $host = 'localhost';
    private static $user = 'root';
    private static $password_link = '';
    private static $db_name = 'printer';

    private static $mysqli = false;

    public static function connect()
    {
        if ( self::$mysqli ) return self::$mysqli;

        self::$mysqli = new mysqli(
            self::$host,
            self::$user,
            self::$password_link,
            self::$db_name );
        self::$mysqli->query("SET NAMES 'utf8';");
        self::$mysqli->query("SET CHARACTER SET 'utf8';");
        return self::$mysqli;
    }
}
