<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/server/db.php";

class Categories
{
    const LOG_TAG = "categories";

    private $fileName;

    public function __construct()
    {
        $this->fileName = ROOT . "/libs/json/categories.json";
    }

    /**
     * Получить список категорий
     * @return array Массив категорий в формате
     *               array(
     *                  array(
     *                      "id": "<ID категории>",
     *                      "name": "<читабельное название>",
     *                      "english": "<название на английском>"
     *                  ),
     *                  ...
     *               )
     */
    public function getCategories()
    {
        $mysqli = DB::connect();
        $temp   = $mysqli->query( "SELECT * FROM `shop-categories`" );
        $res    = array();
        while ( $row = $temp->fetch_assoc() ) {
            $res[] = $row;
        }
        return $res;
    }

    /**
     * Добавить новую категорию
     * @param string $name Название категории (на русском языке)
     * @return bool Удалось ли добавить категорию
     */
    public function addCategory( $name )
    {
        $mysqli = DB::connect();
        $stmt   = $mysqli->stmt_init();
        $stmt->prepare( "INSERT INTO `shop-categories`(`name`) VALUES(?)" );
        $stmt->bind_param( "s", $name );
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
     * Удалить категорию
     * @param string|int $id ID категории
     * @return bool
     */
    public function removeCategory( $id )
    {
        $mysqli = DB::connect();
        $stmt   = $mysqli->stmt_init();
        $stmt->prepare( "DELETE FROM `shop-categories` WHERE `id`=?" );
        $stmt->bind_param( "i", $id );
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
     * Установить новые категории
     * @param array $categories Список категорий (формат см. в описании к getCategories())
     * @return bool Успешно ли выполнена операция
     */
    public function setCategories( $categories )
    {
        $mysqli = DB::connect();
        $mysqli->query( "TRUNCATE `shop-categories`" );
        if ( $mysqli->errno ) {
            Log::e( self::LOG_TAG, "SQL: " . $mysqli->error );
            $mysqli->close();
            return false;
        }
        $stmt = $mysqli->stmt_init();
        if (($line = $this->createSQLLineSet($categories)) === false) {
            $stmt->close();
            return false;
        }
        $stmt->prepare($line);
        if ($stmt->errno) {
            Log::e(self::LOG_TAG, "SQL: " . $stmt->error);
            $stmt->close();
            return false;
        }
        call_user_func_array(
            array($stmt, "bind_param"),
            $this->createSTMTParamsSet($categories)
        );
        if ($stmt->errno) {
            Log::e(self::LOG_TAG, "SQL: " . $stmt->error);
            $stmt->close();
            return false;
        }
        $stmt->execute();
        if ($stmt->errno) {
            Log::e(self::LOG_TAG, "SQL: " . $stmt->error);
            $stmt->close();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Проверить категорию на наличие по английскому названию. Если такой нет, возвращает категорию
     * по умолчанию (ее английское название)
     * @param string $name Английское название категории
     * @return string Действительное название категории
     */
    public function validate( $name )
    {
        $categories = $this->getCategories();
        foreach ( $categories as $category ) {
            if ( $category[ 'english' ] === $name ) {
                return $name;
            }
        }
        return $categories[ 0 ][ 'english' ];
    }

    /**
     * Получить массив параметров категории по ее английскому названию
     * @param string $value Английское наименование категории
     * @return array|bool Массив параметров категории или false в
     */
    public function getCategoryByEnglish($value) {
        $mysqli = DB::connect();
        $stmt = $mysqli->stmt_init();
        $stmt->prepare("SELECT * FROM `shop-categories` WHERE `english`=?");
        $stmt->bind_param("s", $value);
        if ($stmt->errno) {
            Log::e(self::LOG_TAG, "SQL: " . $stmt->error);
            $stmt->close();
            return false;
        }
        $stmt->execute();

        if ($stmt->errno) {
            Log::e(self::LOG_TAG, "SQL: " . $stmt->error);
            $stmt->close();
            return false;
        } else {
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                return false;
            }
            return $result->fetch_assoc();
        }
    }

    /**
     * Создать строку добавления записей для вставки в SQL запрос (вернее, для вставки в запрос
     * stmt::prepare)
     * @param array $categories Список категорий (формат см. в комментарии к getCategories())
     * @return bool|string Строка для вставки
     */
    private function createSQLLineSet( $categories )
    {
        $line = "INSERT INTO `shop-categories`(`name`,`english`) VALUES (?,?)";
        if ( count( $categories ) === 0 ) {
            return false;
        }
        for ( $i = 1; $i < count( $categories ); $i++ ) {
            $line .= ", (?, ?)";
        }
        return $line;
    }

    /**
     * Создать параметры для подстановки в функцию call_user_func_array при добавлении записей в
     * таблицу через STMT
     * @param array $categories Список категорий (формат см. в описании к getCategories)
     * @return array Массив для подстановки в функцию
     */
    private function createSTMTParamsSet( $categories )
    {
        $res      = array();
        $res[ 0 ] = "";
        $i        = 1;
        foreach ( $categories as $category ) {
            $res[0] .= "ss";
            $res[ $i ] = $category[ 'name' ];
            $i++;
            $res[ $i ] = $category[ 'english' ];
            $i++;
        }
        $temp = array();
        foreach ($res as $key=>$val) {
            $temp[$key] = &$res[$key];
        }
        return $res;
    }
}