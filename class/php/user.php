<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/server/db.php";
require_once ROOT . "/class/php/log.php";

class User
{
    private $values = array();
    const LOG_TAG = "User";

    const ACCESS_ADMIN = 5;

    const FIELD_USER_NAME = "username";
    const FIELD_ACCESS = "access";
    const FIELD_USER_KEY = "userkey";
    const FIELD_LOGIN = "login";
    const FIELD_PASSWORD = "password";

    const TYPE_NEW_USER = -1;

    public function __construct( $userkey = false )
    {
        if ( session_status() == PHP_SESSION_NONE ) session_start();
        if ( $userkey === false and isset( $_SESSION[ 'userkey' ] ) ) {
            $userkey = $_SESSION[ 'userkey' ];
        }
        $this->values[ self::FIELD_USER_KEY ] = $userkey;
        $this->download();
    }

    /**
     * Залогиниться. Заносит ключ в сессию
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function login( $login = null, $password = null )
    {
        if ( $login === null or $password === null ) {
            return false;
        }
        $password = hash( "sha512", $password );
        Log::d( self::LOG_TAG, "$login : $password" );
        $mysqli = DB::connect();
        $stmt   = $mysqli->stmt_init();
        $stmt->prepare( "SELECT `userkey` FROM `users` WHERE `login`=? and `password`=?" );
        if ( $stmt->errno !== 0 ) {
            Log::e( self::LOG_TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        $stmt->bind_param( "ss", $login, $password );
        if ( $stmt->errno !== 0 ) {
            Log::e( self::LOG_TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        $stmt->execute();
        $result = $stmt->get_result();
        //$stmt->store_result();
        if ( $stmt->errno !== 0 ) {
            Log::e( self::LOG_TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        } else if ( !$result or $result->num_rows === 0 ) {
            $stmt->close();
            return false;
        }
        $this->values[ self::FIELD_USER_KEY ] = $result->fetch_assoc()[ 'userkey' ];
        $_SESSION[ 'userkey' ]                = $this->values[ self::FIELD_USER_KEY ];
        $stmt->close();
        return $this->download();
    }

    /**
     * Разлогиниться
     */
    public function logout() {
        if (isset($_SESSION['userkey'])) {
            unset($_SESSION['userkey']);
        }
        session_destroy();
    }

    /**
     * Обновить (скачать из БД) данные в соответствии с имеющимся ключом
     * @return bool Удалось ли выполнить обновление
     */
    public function download()
    {
        if ( !$this->values[ self::FIELD_USER_KEY ] or
            $this->values[ self::FIELD_USER_KEY ] === self::TYPE_NEW_USER ) return false;

        $mysqli = DB::connect();
        $userkey = $this->getUserKey();

        $stmt = $mysqli->stmt_init();
        $stmt->prepare( "SELECT * FROM `users` WHERE `userkey`=?" );
        $stmt->bind_param( "s", $userkey );
        $stmt->execute();
        $result = $stmt->get_result();
        if ( $stmt->errno ) {
            Log::e(self::LOG_TAG, "SQL: " . $stmt->error);
            $stmt->close();
            return false;
        } else if (!$result or $result->num_rows == 0){
            $stmt->close();
            return false;
        }

        $this->values = $result->fetch_assoc();
        $stmt->close();

        return true;
    }

    /**
     * Загрузить данные на сервер. Если пользователь с таким ключом уже существует, информация
     * будет изменена, иначе просто будет добавлен новый пользователь. Можно выполнить только когда
     * все поля заполнены.
     * @return bool Удалось ли загрузить
     */
    public function upload()
    {
        if ( !$this->hasFullInfo() ) return false;

        $mysqli = DB::connect();

        $stmt = $mysqli->stmt_init();
        $stmt->prepare(
            "INSERT INTO `users` "
            . "SET `userkey`=?, `username`=?, `access`=?, `login`=?, `password`=? "
            . "ON DUPLICATE KEY UPDATE `username`=?, `access`=?, `login`=?, `password`=?"
        );
        $stmt->bind_param(
            "ssisssiss",
            $this->getUserKey(),
            $this->getUserName(),
            $this->getAccess(),
            $this->getLogin(),
            $this->getPassword(),
            $this->getUserName(),
            $this->getAccess(),
            $this->getLogin(),
            $this->getPassword()
        );
        $stmt->execute();
        return $stmt->errno === 0;
    }

    /**
     * @return array Массив данных пользователя
     */
    public function get()
    {
        return $this->values;
    }

    /**
     * @return bool|string
     */
    public function getUserKey()
    {
        return isset( $this->values[ self::FIELD_USER_KEY ] ) ?
            $this->values[ self::FIELD_USER_KEY ] : false;
    }

    /**
     * @return int
     */
    public function getAccess()
    {
        return isset( $this->values[ self::FIELD_ACCESS ] ) ?
            $this->values[ self::FIELD_ACCESS ] : 0;
    }

    /**
     * @return bool|string
     */
    public function getUserName()
    {
        return isset( $this->values[ self::FIELD_USER_NAME ] ) ?
            $this->values[ self::FIELD_USER_NAME ] : false;
    }

    /**
     * @return bool|string
     */
    public function getLogin()
    {
        return isset( $this->values[ self::FIELD_LOGIN ] ) ?
            $this->values[ self::FIELD_LOGIN ] : false;
    }

    /**
     * @return bool|string
     */
    public function getPassword()
    {
        return isset( $this->values[ self::FIELD_PASSWORD ] ) ?
            $this->values[ self::FIELD_PASSWORD ] : false;
    }

    /**
     * Установить сразу несколько параметров (вместо многократного вызова функций setUserKey,
     * setPassword и т.д.)
     * @param $arr array Ассоциативный массив "поле"=>"значение"
     */
    public function set( $arr )
    {
        foreach ( $arr as $key => $val ) {
            $this->values[ $key ] = $val;
        }
    }

    /**
     * @param $userKey string
     * @return $this
     */
    public function setUserKey( $userKey )
    {
        $this->values[ self::FIELD_USER_KEY ] = $userKey;
        return $this;
    }

    /**
     * @param $access int
     * @return $this
     */
    public function setAccess( $access )
    {
        $this->values[ self::FIELD_USER_KEY ] = $access;
        return $this;
    }

    /**
     * @param $userName string
     * @return $this
     */
    public function setUserName( $userName )
    {
        $this->values[ self::FIELD_USER_KEY ] = $userName;
        return $this;
    }

    /**
     * @param $login string
     * @return $this
     */
    public function setLogin( $login )
    {
        $this->values[ self::FIELD_LOGIN ] = $login;
        return $this;
    }

    /**
     * @param $password string
     * @return $this
     */
    public function setPassword( $password )
    {
        $this->values[ self::FIELD_PASSWORD ] = $password;
        return $this;
    }

    /**
     * @return bool Имеется ли полная информация о пользователе - заполнены ли все поля.
     */
    public function hasFullInfo()
    {
        if (
            !isset( $this->values[ self::FIELD_ACCESS ] ) or
            !isset( $this->values[ self::FIELD_USER_KEY ] ) or
            !isset( $this->values[ self::FIELD_USER_NAME ] ) or
            !isset( $this->values[ self::FIELD_LOGIN ] ) or
            !isset( $this->values[ self::FIELD_PASSWORD ] )
        ) {
            return false;
        }

        return true;
    }
}