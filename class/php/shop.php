<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/server/db.php";
require_once ROOT . "/class/php/log.php";
require_once ROOT . "/class/php/user.php";

class Shop
{
    const TAG = "Shop";

    private $params;

    private $user;

    /**
     * ---Используется в подготовке параметров к SQL запросу
     * значение поля будет вставлено как строка
     */
    const TYPE_STRING = 1;

    /**
     * ---Используется в подготовке параметров к SQL запросу
     * значение поля будет вставлено как число
     */
    const TYPE_INT = 2;

    /**
     * ---Используется в подготовке параметров к SQL запросу
     * значение будет вставлено в SQL запрос как часть запроса - название поля не требуется
     */
    const TYPE_CUSTOM_STRING = 3;

    /**
     * ---Используется в подготовке параметров к SQL запросу
     * Вот и костыли полезли))
     * Параметр, в котором указано значение для сортировки
     */
    const TYPE_ORDER = 4;

    /**
     * @var string Начало sql запроса
     */
    private $SQL_START = "SELECT * FROM `shop`";

    /**
     * @var string Начало SQL запроса для поиска количества элементов
     */
    private $SQL_START_COUNT = "SELECT COUNT(*) as 'count' FROM `shop`";

    private $SQL_START_PREVIEW = "SELECT `id`,`name`,`price` FROM `shop`";

    const SEARCH_PARAMS = array(
        /**
         * Название товара. Строка или false
         */
        "name"     => false,

        /**
         * ID товара. Число, array или false
         */
        "id"       => false,

        /**
         * Категория товара. Строка или false
         */
        "category" => false,

        /**
         * С какой строки возвращать результаты. Число или false
         */
        "from"     => false,

        /**
         * По какую строку. Число или false
         */
        "to"       => false,

        /**
         * Вернуть только количество найденных элементов. При таком поиске не учитываются параметры
         * "from" и "to". Boolean
         */
        "count"    => false,

        /**
         * Нужно ли выдать сокращенную версию: id, name, price. Boolean
         */
        "preview"  => false,

        /**
         * Столбец, по которому производить сортировку. Возможные значения: id, name, email, phone,
         * price. Если необходимо указать порядок сортировки, через пробел необходимо указать тип:
         * ASC (по умолчанию) или DESC
         */
        "order" => false
    );

    const AVAILABLE_ORDER_COLUMNS = array("id", "name", "price");

    /**
     * Параметры продукта
     */
    const PRODUCT_PARAMS = array(
        "id"          => false,
        "name"        => false,
        "price"       => false,
        "category"    => false,
        "images"      => false,
        "description" => false
    );

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Найти товары в базе данных.
     * @param array $params Параметры для поиска. Возможные параметры см. в описании SEARCH_PARAMS
     * @return array|bool Массив товаров. Возвращает все индексы из базы даннхи
     */
    public function find( $params = [] )
    {
        $this->rewriteParams( $params );
        $mysqli = DB::connect();
        $stmt   = $mysqli->stmt_init();

        $pre = $this->createPreParamsToSQL();

        $stmt->prepare( $this->getSQLSearchLine( $pre ) );

        if ($stmt->errno) {
            log::e(self::TAG, $stmt->error);
            $stmt->close();
            return false;
        }

        $paramsToBind = $this->refValues( $this->getParamsToSQL( $pre ) );

        if ( count( $paramsToBind ) > 1 ) {
            call_user_func_array(
                array( $stmt, 'bind_param' ),
                $paramsToBind
            );
        }
        $stmt->execute();

        if ( $stmt->errno === 0 ) {
            if ( $this->isSearchCount() ) {
                $sql_res = $stmt->get_result();
                return (int)$sql_res->fetch_assoc()[ 'count' ];
            } else {
                $sql_res = $stmt->get_result();
                $res     = array();

                while ( $row = $sql_res->fetch_assoc() ) {
                    $res[] = $row;
                }
                $stmt->close();
                return $res;
            }
        } else {
            Log::e( "Shop", "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
    }

    /**
     * Добавить новый продука в магазин
     * @param array $userParams Параметры продукта (список возможных см. в Shop::PRODUCT_PARAMS
     * @return bool true, если элемент был успешно добавлен, и false в случае ошибки
     */
    public function add( $userParams )
    {
        if ( $this->user->getAccess() < User::ACCESS_ADMIN ) {
            return false;
        }
        $params = $this->createProductParams( $userParams );
        $mysqli = DB::connect();
        $stmt   = $mysqli->stmt_init();
        $stmt->prepare( "INSERT INTO `shop` (`name`,`price`,`category`,`images`,`description`) VALUES(?,?,?,?,?)" );
        $stmt->bind_param(
            "sssss",
            $params[ 'name' ],
            $params[ 'price' ],
            $params[ 'category' ],
            $params[ 'images' ],
            $params[ 'description' ]
        );
        if ( $stmt->errno ) {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        $stmt->execute();
        if ( $stmt->errno ) {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        } else {
            return true;
        }
    }

    /**
     * Редактировать продукт в магазине
     * @param array $userParams Параметры продукта (список возможных см. в Shop::PRODUCT_PARAMS
     * @return bool true, если элемент был успешно редактирован, и false в случае ошибки
     */
    public function edit($userParams) {
        if ( $this->user->getAccess() < User::ACCESS_ADMIN ) {
            return false;
        }
        $params = $this->createProductParams( $userParams );
        $mysqli = DB::connect();
        $stmt = $mysqli->stmt_init();
        if (($line = $this->createSTMTLineUpdate($params)) === false) {
            return false;
        }
        $stmt->prepare($line);
        if ( $stmt->errno ) {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        call_user_func_array(
            array( $stmt, 'bind_param' ),
            $this->refValues( $this->createSTMTParamsUpdate( $params ) )
        );
        if ( $stmt->errno ) {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        $stmt->execute();

        if ( $stmt->errno ) {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        else {
            return true;
        }
    }

    /**
     * Удалить прдукт
     * @param string|int $id ID элемента
     * @return bool
     */
    public function remove( $id )
    {
        if ( $this->user->getAccess() < User::ACCESS_ADMIN ) {
            return false;
        }
        $mysqli = DB::connect();
        $stmt   = $mysqli->stmt_init();
        $stmt->prepare( "DELETE FROM `shop` WHERE `id`=?" );
        $stmt->bind_param( "i", $id );
        if ( $stmt->errno ) {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        $stmt->execute();
        if ( $stmt->errno ) {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        } else {
            $stmt->close();
            return true;
        }
    }

    /**
     * Создать строку для SQL запроса зерактирования (вернее, для stmt метода prepare)
     * @param $params
     * @return bool|string
     */
    private function createSTMTLineUpdate($params) {
        $line = "UPDATE `shop` SET ";
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
    private function createProductParams( $userParams )
    {
        $params = array();
        foreach ( self::PRODUCT_PARAMS as $key => $val ) {
            $params[ $key ] = isset( $userParams[ $key ] ) ?
                $userParams[ $key ] : self::PRODUCT_PARAMS[ $key ];
        }
        return $params;
    }

    /**
     * Сформировать массив для вызова функции MYSQLI_STMT->bind_params(), т.к. функция вызывается
     * через call_user_func_array(), необходимо предварительно сформировать массив параметров для
     * нее.
     *
     * @param array $params Массив параметров, которые необходимо использовать при поиске
     * @return array
     */
    private function getParamsToSQL( $params )
    {
        $arr  = array();
        $temp = "";
        foreach ( $params as $param ) {
            switch ( $param[ 'type' ] ) {
                case self::TYPE_STRING:
                    $temp .= "s";
                    break;
                case self::TYPE_INT:
                    $temp .= "i";
                    break;
            }
        }
        $arr[] = $temp;
        foreach ( $params as $param ) {
            if ( $param[ 'type' ] == self::TYPE_STRING ) {
                $arr[] = "%" . $param[ 'value' ] . "%";
            } else if ( $param[ 'type' ] == self::TYPE_INT ) {
                $arr[] = $param[ 'value' ];
            }
        }
        return $arr;
    }

    /**
     * Получить строку для SQL запроса
     * @param array $params
     * @return string
     */
    private function getSQLSearchLine( $params )
    {
        $temp = array();
        $sortLine = " ORDER BY `id` DESC ";
        foreach ( $params as $param ) {
            switch ( $param[ 'type' ] ) {
                case self::TYPE_STRING:
                    $temp[] = "`" . $param[ 'field' ] . "` LIKE ?";
                    break;
                case self::TYPE_INT:
                    $temp[] = "`" . $param[ 'field' ] . "`=?";
                    break;
                case self::TYPE_CUSTOM_STRING:
                    $temp[] = $param[ 'value' ];
                    break;
                case self::TYPE_ORDER:
                    $sortLine = " ORDER BY `" . $param['value']['column'] . "` " . $param['value']['type'];
            }
        }

        if ( $this->isSearchCount() ) {
            $sql_start = $this->SQL_START_COUNT;
        } else if ( $this->isPreview() ) {
            $sql_start = $this->SQL_START_PREVIEW;
        } else {
            $sql_start = $this->SQL_START;
        }

        if ( count( $temp ) > 0 ) {
            $result = $sql_start . " WHERE " . implode( ' and ', $temp );
        } else {
            $result = $sql_start;
        }

        $result .= $sortLine;

        if ( !$this->isSearchCount() and
            is_numeric( $this->params[ 'from' ] ) and is_numeric( $this->params[ 'to' ] ) ) {
            $from   = $this->params[ 'from' ];
            $to     = $this->params[ 'to' ];
            $result .= " LIMIT $from, $to";
        }

        return $result;
    }

    /**
     * Ищем ли мы сейчас число строк
     * @return bool
     */
    private function isSearchCount()
    {
        return isset( $this->params[ 'count' ] ) and $this->params[ 'count' ] === true;
    }

    private function isPreview()
    {
        return $this->params[ 'preview' ];
    }

    /**
     * Возвращает массив формата
     *  array(
     *      array(
     *          'field': 'name',
     *          'type': <тип>,
     *          'value': 'тут_одно_слово'
     *      ),
     *      ...
     *  )
     * @return array
     */
    private function createPreParamsToSQL()
    {
        $arr = array();
        if ( $this->params[ 'name' ] != false ) {
            foreach ( preg_split( '/( )/', $this->params[ 'name' ] ) as $val ) {
                $arr[] = array(
                    'field' => 'name',
                    'type'  => self::TYPE_STRING,
                    'value' => $val
                );
            }
        }
        if ( $this->params[ 'category' ] != false ) {
            $arr[] = array(
                'field' => 'category',
                'type'  => self::TYPE_STRING,
                'value' => $this->params[ 'category' ]
            );
        }
        if ( $this->params[ 'id' ] != false ) {
            if ( is_array( $this->params[ 'id' ] ) ) {
                $s = array();
                foreach ( $this->params[ 'id' ] as $val ) {
                    if ( is_numeric( $val ) ) {
                        $s[] = "`id`=$val";
                    }
                }
                $arr[] = array(
                    'type'  => self::TYPE_CUSTOM_STRING,
                    'value' => "(" . implode( ' or ', $s ) . ")"
                );
            } else {
                $arr[] = array(
                    'field' => 'id',
                    'type'  => self::TYPE_INT,
                    'value' => $this->params[ 'id' ]
                );
            }
        }
        $arr[] = array (
            'type' =>self::TYPE_ORDER,
            'value' => $this->params['order']
        );
        return $arr;
    }

    /**
     * Полностью обновляет параметры. Если в $params есть значения с соответствующими ключами,
     * заносит их в параметры
     *
     * @param array $userParams Список параметров
     */
    private function rewriteParams( $userParams = [] )
    {
        $this->params = array();
        foreach ( self::SEARCH_PARAMS as $key => $val ) {
            $this->params[ $key ] = $val;
        }
        foreach ( $userParams as $key => $val ) {
            if ( array_key_exists( $key, $this->params ) ) {
                $this->params[ $key ] = $val;
            }
        }

        $temp = preg_split( "/(\ )/", $this->params[ 'order' ] );
        $order = array();
        if ( !in_array( $temp[ 0 ], self::AVAILABLE_ORDER_COLUMNS ) ) {
            $order = array("column"=>"id", "type"=>"ASC");
        } else {
            $order['column'] = $temp[ 0 ];
            if ( count( $temp ) > 1 and ( $temp[ 1 ] === "ASC" or $temp[ 1 ] === "DESC" ) ) {
                $order['type'] = $temp[ 1 ];
            } else {
                $order['type'] = "ASC";
            }
        }
        $this->params['order'] = $order;
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