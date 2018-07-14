<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once 'shop.php';

class Basket
{
    public function __construct()
    {
        if ( session_status() == PHP_SESSION_NONE ) session_start();
        if ( !isset( $_SESSION[ 'basket' ] ) ) {
            $_SESSION[ 'basket' ] = array();
        }
    }

    /**
     * Добавить товар в корзину
     * @param int $id    ID товара
     * @param int $count Количество
     */
    public function add( $id, $count )
    {
        $id    = (string)$id;
        $count = (int)$count;
        if ( isset( $_SESSION[ 'basket' ][ $id ] ) ) {
            $_SESSION[ 'basket' ][ $id ][ 'count' ] += $count;
        }else {
            $_SESSION[ 'basket' ][ $id ] = array(
                "id"    => $id,
                "count" => $count
            );
        }
    }

    /**
     * Удалить товар из корзины
     * @param int $id ID товара
     */
    public function remove( $id )
    {
        $id = (int)$id;
        if ( isset( $_SESSION[ 'basket' ][ $id ] ) ) {
            unset( $_SESSION[ 'basket' ][ $id ] );
        }
    }

    /**
     * Очистить корзину
     */
    public function clear()
    {
        $_SESSION[ 'basket' ]  = array();
    }

    /**
     * Получить массив товаров. Возвращает все данные о товарах, которые есть в таблице
     * @param boolean $preview Нужно ли выдать сокращенную версию: id, name, count, price
     * @return array
     */
    public function get( $preview = false )
    {
        $ids   = array();
        $count = array();
        foreach ( $_SESSION[ 'basket' ] as $item ) {
            $ids[]                  = $item[ 'id' ];
            $count[ $item[ 'id' ] ] = $item[ 'count' ];
        }

        $shop   = new Shop();
        $params = array(
            'id' => $ids
        );
        if ( $preview === true ) {
            $params[ 'preview' ] = true;
        }
        if ( count( $ids ) > 0 and $res = $shop->find( $params ) ) {
            foreach ( $res as $key => $item ) {
                $res[ $key ][ 'count' ] = $count[ $item[ 'id' ] ];
            }
            return $res;
        } else {
            return array();
        }
    }

    /**
     * @return int Количество элементов в корзине
     */
    public function size()
    {
        return count( $_SESSION[ 'basket' ] );
    }
}