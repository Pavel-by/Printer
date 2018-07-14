<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/server/db.php";
require_once ROOT . "/class/php/user.php";

class Coordinator
{
    private $pages;
    private $user;
    const URL_404 = "404";
    const URL_401 = "401";

    public function __construct()
    {
        if ( session_status() == PHP_SESSION_NONE ) session_start();
        $this->pages = json_decode( file_get_contents( ROOT . "/server/json/pages.json" ), false );
        $this->user  = new User();
    }

    /**
     * Определить, есть ли страница в общем списке
     * @param $url Адрес страницы
     * @return bool Имеется ли такая страница
     */
    public function hasPage( $url )
    {
        if ( $this->getPageInfo( $url ) ) {
            return true;
        }
        return false;
    }

    /**
     * Получить информацию о странице (url, directory, access)
     * @param String $url Адрес страницы
     * @return bool|object Объект страницы или false
     */
    public function getPageInfo( $url )
    {
        foreach ( $this->pages as $page ) {
            if ( preg_match( $page->url, $url ) ) {
                return $page;
            }
        }
        return false;
    }

    public function hasAccess( $url )
    {
        if ( !$page = $this->getPageInfo( $url ) ) return false;
        if ( isset( $page->access ) ) {
            return ((int) ($this->user->getAccess()) >= (int) ($page->access));
        }
        return true;
    }

    public function writePage( $url )
    {
        $page = $this->getPageInfo( $url );

        if (isset($page->redirect)) {
            header("Location:" . $page->redirect);
            return true;
        }

        $pageDir  = ROOT . "/pages/" . $page->dir;
        $xml  = new SimpleXMLElement(
            file_get_contents(
                $pageDir . "/index.php"
            )
        );
        echo "<html>";
        echo "<head>";
        echo '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
        echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />";
        echo "<link rel=\"shortcut icon\" href=\"/images/icon1.png\" type=\"image/x-icon\">";
        echo "<link rel=\"StyleSheet\" type=\"text/css\" href=\"/style/components/select.css?" . filemtime("style/components/select.css") . "\">";
        echo "<script type='text/javascript'>" . file_get_contents("libs/js/jquery.js") . "</script>";
        echo "<script type='text/javascript'>" . file_get_contents("libs/js/select.js") . "</script>";
        echo "<script type='text/javascript'>" . file_get_contents("libs/js/to-top.js") . "</script>";

        foreach ( $xml->head->children() as $k => $v ) {
            switch ( $k ) {
                case "css":
                    if (!is_file(ROOT . "/style/$v")) break;
                    echo '<link rel="StyleSheet" type="text/css" href="/style/' .
                        $v . "?time=" . filemtime( ROOT . "/style/$v" ) .
                        '">';
                    break;
                case "jslib":
                    if (!is_file("libs/js/" . $v)) break;
                    echo '<script language="JavaScript" src="'
                        . "/libs/js/" . $v . "?time=" . filemtime("libs/js/" . $v) .
                        '"></script>';
                    break;
                case "js":
                    if (!is_file("$pageDir/$v")) break;
                    echo '<script language="JavaScript">'
                    . file_get_contents("$pageDir/$v")
                    . '</script>';
                    break;
                case "title":
                    echo "<title>" . $v . "</title>";
                    break;
                default:
                    echo "<" . $k . " ";
                    foreach ( $v->attributes() as $k1 => $v1 ) if ( !is_numeric( $k1 ) ) echo $k1 . "='" . $v1 . "' ";
                    echo ">" . $v . "</" . $k . ">";
                    break;
            }
        }
        echo "</head>";

        echo "<body>";
        foreach ( $xml->body->children() as $k => $v ) {
            switch ( $k ) {
                case "phplib":
                    if (!is_file(ROOT . '/libs/php/' . $v)) break;
                    include( ROOT . '/libs/php/' . $v );
                    break;
                case "php":
                    if (!is_file($pageDir . '/' . $v)) break;
                    include( $pageDir . '/' . $v );
                    break;
                default:
                    echo "<" . $k . " ";
                    foreach ( $v->attributes() as $k1 => $v1 ) if ( !is_numeric( $k1 ) ) echo $k1 . "='" . $v1 . "' ";
                    echo ">" . $v . "</" . $k . ">";
                    break;
            }
        }
        echo "</body>";
        echo "</html>";
    }
}