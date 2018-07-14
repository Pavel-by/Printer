<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/server/db.php";
require_once ROOT . "/class/php/user.php";
require_once ROOT . "/class/php/log.php";

class Cartridges
{
    const LOG_TAG = "Cartridges";

    private $user;

    const CARTRIDGE_PARAMS = array(
        "id"                 => false,
        "images"             => false,
        "printerName"        => false,
        "cartridgeName"      => false,
        "refillPrice"        => false,
        "photoreceptorPrice" => false,
        "rakelPrice"         => false,
        "PCRPrice"           => false,
        "shellPrice"         => false,
        "bladePrice"         => false,
        "bushingPrice"       => false
    );

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Получить список картриджей
     * @return array
     */
    public function getCartridges()
    {
        $mysqli = DB::connect();

        $stmt = $mysqli->stmt_init();
        $stmt->prepare( "SELECT * FROM `cartridges` ORDER BY `id` DESC" );
        $stmt->execute();

        $rez = array();
        if ( $stmt->errno == 0 ) {
            $rezSQL = $stmt->get_result();
            while ( $s = $rezSQL->fetch_assoc() ) {
                $rez[] = $s;
            }
        }

        return $rez;
    }

    /**
     * Добавить новый картридж
     * @param array $userParams Параметры картрилда (список возможных см. в
     *                          Cartridges::CARTRIDGE_PARAMS
     * @return bool
     */
    public function add( $userParams = array() )
    {
        if ( $this->user->getAccess() < User::ACCESS_ADMIN ) {
            return false;
        }
        $params = $this->createParams( $userParams );

        $mysqli = DB::connect();
        $stmt   = $mysqli->stmt_init();
        $stmt->prepare( "INSERT INTO `cartridges`(`images`,`printerName`,`cartridgeName`,`refillPrice`,`photoreceptorPrice`,`rakelPrice`,`PCRPrice`,`shellPrice`,`bladePrice`,`bushingPrice`) VALUES(?,?,?,?,?,?,?,?,?,?)" );
        if ( $stmt->errno ) {
            Log::e( self::LOG_TAG, "SQL: " . $stmt->error );
            return false;
        }
        $stmt->bind_param( "ssssssssss",
            $params[ 'images' ],
            $params[ 'printerName' ],
            $params[ 'cartridgeName' ],
            $params[ 'refillPrice' ],
            $params[ 'photoreceptorPrice' ],
            $params[ 'rakelPrice' ],
            $params[ 'PCRPrice' ],
            $params[ 'shellPrice' ],
            $params[ 'bladePrice' ],
            $params[ 'bushingPrice' ]
        );
        if ( $stmt->errno ) {
            Log::e( self::LOG_TAG, "SQL: " . $stmt->error );
            return false;
        }
        $stmt->execute();
        if ( $stmt->errno ) {
            Log::e( self::LOG_TAG, "SQL: " . $stmt->error );
            return false;
        }
        return true;
    }

    /**
     * Редактировать картридж.
     * @param array $userParams Параметры картрилда (список возможных см. в
     *                          Cartridges::CARTRIDGE_PARAMS). ОБЯЗАТЕЛЬНО ПОЛЕ 'id'
     * @return bool
     */
    public function edit( $userParams = array() )
    {
        if ( $this->user->getAccess() < User::ACCESS_ADMIN or !isset( $userParams[ 'id' ] ) ) {
            return false;
        }
        $params = $this->createParams( $userParams );
        $mysqli = DB::connect();
        $stmt   = $mysqli->stmt_init();
        $line   = $this->createSQLLineUpdate( $params );
        if ( !$line ) {
            return false;
        }
        $stmt->prepare( $line );
        if ( $stmt->errno ) {
            Log::e( self::LOG_TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        call_user_func_array(
            array( $stmt, 'bind_param' ),
            $this->refValues( $this->createSTMTParamsUpdate( $params ) )
        );
        if ( $stmt->errno ) {
            Log::e( self::LOG_TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        $stmt->execute();

        if ( $stmt->errno ) {
            Log::e( self::LOG_TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        } else {
            $stmt->close();
            return true;
        }
    }

    /**
     * Удалить картридж
     * @param string|int $id ID картриджа
     * @return bool
     */
    public function remove( $id )
    {
        if ( $this->user->getAccess() < User::ACCESS_ADMIN ) {
            return false;
        }
        $mysqli = DB::connect();
        $stmt   = $mysqli->stmt_init();
        $stmt->prepare( "DELETE FROM `cartridges` WHERE `id`=?" );
        $stmt->bind_param( "i", $id );
        if ( $stmt->errno ) {
            Log::e( self::LOG_TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        $stmt->execute();
        if ( $stmt->errno ) {
            Log::e( self::LOG_TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        } else {
            $stmt->close();
            return true;
        }
    }

    /**
     * Создать строку для SQL запроса при обновлении данных
     * @param array $params Параметры (id, printerName и т.д.)
     * @return bool|string
     */
    private function createSQLLineUpdate( $params )
    {
        $line = "UPDATE `cartridges` SET ";
        $temp = array();
        foreach ( $params as $key => $val ) {
            if ( $key === "id" ) {
                continue;
            }
            $temp[] = "$key=?";
        }
        if ( count( $temp ) == 0 ) {
            return false;
        } else {
            $line .= implode( ", ", $temp );
            $line .= " WHERE `id`=?";
            return $line;
        }
    }

    /**
     * Сформировать массив для вызова функции MYSQLI_STMT->bind_params(), т.к. функция вызывается
     * через call_user_func_array(), необходимо предварительно сформировать массив параметров для
     * нее.
     * @param array $params Параметры (id, printerName и т.д.)
     * @return array
     */
    private function createSTMTParamsUpdate( $params )
    {
        $s         = "";
        $temp      = array();
        $temp[ 0 ] = "";
        $i         = 1;
        foreach ( $params as $key => $val ) {
            if ( $val === false or $key === "id" ) {
                continue;
            }
            $s          .= "s";
            $temp[ $i ] = $val;
            $i++;
        }
        $temp[ 0 ]  = $s . "i";
        $temp[ $i ] = $params[ 'id' ];
        return $temp;
    }

    /**
     * Создать параметры из начального списка и заполнить их значениями из пользовательских
     * параметров
     * @param array $userParams Пользовательские параметры
     * @return array
     */
    private function createParams( $userParams )
    {
        $params = array();
        foreach ( self::CARTRIDGE_PARAMS as $key => $val ) {
            $params[ $key ] = isset( $userParams[ $key ] ) ?
                $userParams[ $key ] : self::CARTRIDGE_PARAMS[ $key ];
        }
        return $params;
    }

    /**
     * Чет проблемы с call_user_function_array и bind_param... Приходится изворачиваться
     * @param $arr
     * @return array
     */
    private function refValues( $arr )
    {
        $refs = array();
        foreach ( $arr as $key => $value )
            $refs[ $key ] = &$arr[ $key ];
        return $refs;
    }
}