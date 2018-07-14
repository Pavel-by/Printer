<?php

/**
 * Просто для удобства: через него можно закрыть сессию (текущий запрос) с ошибкой
 * (чтобы каждай раз не писать echo json_encode(dfldk); exit();)
 * Class Script
 */
class Script
{
    public function __construct()
    {
    }

    /**
     * Завершить скрипт
     * @param string $message    Сообщение ошибки
     * @param bool $success      Успешно ли был завершен скрипт
     * @param array $successData Дополнительная информация, которая также будет выведена
     */
    public static function close( $message, $success = false, $successData = array() )
    {
        $res = array();
        foreach ( $successData as $key => $val ) {
            $res[ $key ] = $val;
        }
        if ( $success ) {
            $res[ 'success' ] = true;
            $res[ 'message' ] = $message;
        } else {
            $res[ 'error' ] = $message;
        }
        echo json_encode( $res );
        exit();
    }
}