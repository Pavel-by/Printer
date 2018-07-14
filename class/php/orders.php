<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require ROOT . "/vendor/autoload.php";
require_once ROOT . "/server/db.php";
require_once ROOT . "/class/php/user.php";
require_once ROOT . "/class/php/log.php";
require_once ROOT . "/class/php/shop.php";

/**
 * Заказы пользователей
 * Class Orders
 */
class Orders
{
    const TAG = "Orders";
    private $user;

    const INITIAL_PARAMS = array(

        /**
         * ID заказа: одно число или несколько чисел через пробел
         */
        "id"      => "",

        /**
         * Имя заказчика
         */
        "name"    => "",

        /**
         * Email заказчика
         */
        "email"   => "",

        /**
         * Телефон заказчика
         */
        "phone"   => "",

        /**
         * Заказ в формате
         *  array(
         *      array(
         *          "id"    => ID_ТОВАРА,
         *          "count" => КОЛИЧЕСТВО_ТОВАРА
         *      ),
         *      array(...
         *  )
         */
        "list"    => array(),

        /**
         * Дата
         */
        "date"    => "",

        /**
         * Тип записи: 1 - непроверенный, 2 - проверенный, 3 - удаленный
         */
        "checked" => false );

    /**
     * Возможные параметры при получении списка заказов
     */
    const FILTER_PARAMS = array(

        /**
         * Минимальная стоимость заказа
         */
        "minPrice" => false,

        /**
         * Максимальная стоимость заказа
         */
        "maxPrice" => false,

        /**
         * Столбец, по которому производить сортировку. Возможные значения: id, name, email, phone,
         * price. Если необходимо указать порядок сортировки, через пробел необходимо указать тип:
         * ASC (по умолчанию) или DESC
         */
        "order"    => false );

    const AVAILABLE_ORDER_COLUMNS = array( "id", "name", "email", "phone", "price", "date" );

    const TYPE_STRING            = 0;
    const TYPE_INTEGER           = 1;
    const TYPE_ARRAY             = 2;
    const TYPE_DATE              = 3;
    const AVAILABLE_PARAMS_TYPES = array( "name"     => self::TYPE_STRING,
                                          "email"    => self::TYPE_STRING,
                                          "phone"    => self::TYPE_STRING,
                                          "list"     => self::TYPE_ARRAY,
                                          "minPrice" => self::TYPE_INTEGER,
                                          "maxPrice" => self::TYPE_INTEGER,
                                          "order"    => self::TYPE_STRING,
                                          "date"     => self::TYPE_DATE,
                                          "id"       => self::TYPE_STRING,
                                          "checked"  => self::TYPE_INTEGER );

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Получить список заказов
     *
     * @param array $userParams Массив - фильтр: возможные поля см. в Orders::INITIAL_PARAMS
     *
     * @return array|bool Массив заказов в формате INITIAL_PARAMS + поле price (общая цена), или
     *                          false в случае ошибки
     */
    public function get( $userParams )
    {
        if ( $this->user->getAccess() < User::ACCESS_ADMIN ) {
            return false;
        }
        $params = $this->validateParamsGet( $userParams );
        if ( !( $line = $this->createSTMTLineGet( $params ) ) ) {
            return false;
        }

        $mysqli = DB::connect();
        $stmt   = $mysqli->stmt_init();
        $stmt->prepare( $line );
        if ( $stmt->errno ) {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }

        $callParams = $this->createSTMTParamsGet( $params );
        if ( count( $callParams ) > 1 ) {
            call_user_func_array( array( $stmt, "bind_param" ), $this->refValues( $callParams ) );
            if ( $stmt->errno ) {
                Log::e( self::TAG, "SQL: " . $stmt->error );
                $stmt->close();
                return false;
            }
        }

        $stmt->execute();

        if ( $stmt->errno ) {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        $sql_res = $stmt->get_result();
        $res     = array();
        while ( $line = $sql_res->fetch_assoc() ) {
            $list = array();
            $mas  = preg_split( "/(,)/", $line[ 'list' ] );
            foreach ( $mas as $val ) {
                $temp   = preg_split( '/(:)/', $val );
                $list[] = array( "id" => $temp[ 0 ], "count" => $temp[ 1 ] );
            }
            $line[ 'list' ] = $list;
            $res[]          = $line;
        }
        return $res;
    }

    /**
     * Добавить заказ
     *
     * @param array $customParams Параметры заказа (допустимые см. в self::INITIAL_PARAMS)
     *
     * @return bool Успешно ли было добавление
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    public function add( $customParams = array() )
    {
        $mysqli = DB::connect();

        $params = array();
        foreach ( self::INITIAL_PARAMS as $key => $val ) {
            if ( isset( $customParams[ $key ] ) ) {
                $params[ $key ] = $customParams[ $key ];
            } else {
                $params[ $key ] = $val;
            }
        }

        if ( strlen( $params[ 'phone' ] ) === 0 or
            strlen( $params[ 'email' ] ) === 0 or
            count( $params[ 'list' ] ) === 0 ) {
            return false;
        }

        $list = (string)$this->createList( $params[ 'list' ] );

        if ( strlen( $list ) > 10000 or
            !( $params[ 'price' ] = $this->calculatePrice( $params[ 'list' ] ) ) ) {
            return false;
        }

        $stmt = $mysqli->stmt_init();
        $stmt->prepare( "INSERT INTO `orders`(`name`,`email`,`phone`,`list`, `price`,`checked`, `date`) VALUES(?,?,?,?,?,1, now())" );

        if ( $stmt->errno !== 0 ) {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        $stmt->bind_param( "ssssi", $params[ 'name' ], $params[ 'email' ], $params[ 'phone' ],
            $list, $params[ 'price' ] );
        if ( $stmt->errno !== 0 ) {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        $stmt->execute();
        if ( $stmt->errno === 0 ) {
            $params[ 'orderId' ] = $mysqli->insert_id;
            $stmt->close();
            $this->createFile( $params, ROOT . "/files/orders/" . $params[ 'orderId' ] . ".docx" );
            return true;
        } else {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
    }

    /**
     * Обновить информацию о заказе в базе данных
     *
     * @param array $userParams Список параметров; возможные см. в Orders::INITIAL_PARAMS. Если
     *                          какой-либо параметр не указан, он не будет изменен. Обязательно
     *                          поле "id", по нему определяется редактируемая запись. Если кроме
     *                          "id" больше не будет валидных полей, функция вернет false
     *
     * @return bool false (в случае ошибки) или true (при успешном выполнении операции)
     */
    public function update( $userParams )
    {
        if ( $this->user->getAccess() < User::ACCESS_ADMIN ) return false;
        if ( !isset( $userParams[ 'id' ] ) ) {
            return false;
        }
        $params = $this->validateParamsUpdate( $userParams );
        if ( ( $line = $this->createSTMTLineUpdate( $params ) ) === false ) {
            return false;
        }
        $sql  = DB::connect();
        $stmt = $sql->stmt_init();
        $stmt->prepare( $line );
        if ( $stmt->errno ) {
            Log::e( self::TAG, "SQL: " . $stmt->error );
            $stmt->close();
            return false;
        }
        call_user_func_array( array( $stmt, "bind_param" ),
            $this->refValues( $this->createSTMTParamsUpdate( $params ) ) );
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
     * Сформировать массив для вызова функции MYSQLI_STMT->bind_params(), т.к. функция вызывается
     * через call_user_func_array(), необходимо предварительно сформировать массив параметров для
     * нее.
     *
     * @param array $params Параметры (id, printerName и т.д.)
     *
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
            $s .= "s";
            $temp[ $i ] = $val;
            $i++;
        }
        $temp[ 0 ] = $s;
        return $temp;
    }

    /**
     * Создать строку для SQL запроса реактирования (вернее, для stmt метода prepare)
     *
     * @param $params
     *
     * @return bool|string
     */
    private function createSTMTLineUpdate( $params )
    {
        $line = "UPDATE `orders` SET ";
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
            if ( is_array( $params[ 'id' ] ) ) {
                for ( $i = 0; $i < count( $params[ 'id' ] ); $i++ ) {
                    $params[ 'id' ][ $i ] = "`id`=" . $params[ 'id' ][ $i ];
                }
                $line .= " WHERE (" . implode( " or ", $params[ 'id' ] ) . ")";
            } else {
                $line .= "`id`=" . $params[ 'id' ];
            }
            return $line;
        }
    }

    /**
     * Проверить параметры на соответствие допустимым, удалить лишние
     *
     * @param array $userParams Параметры, которые передал пользователь
     *
     * @return array Валидные параметры
     */
    private function validateParamsGet( $userParams )
    {
        $params = array();
        foreach ( $userParams as $key => $val ) {
            if ( ( isset( self::INITIAL_PARAMS[ $key ] ) or
                    isset( self::FILTER_PARAMS[ $key ] ) ) and $this->isParamValid( $key, $val ) )
                $params[ $key ] = $userParams[ $key ];
        }
        if ( isset( $params[ 'list' ] ) ) {
            $params[ 'list' ] = $this->createList( $params[ 'list' ] );
        }
        if ( isset( $params[ 'order' ] ) ) {
            $temp = preg_split( "/(\ )/", $params[ 'order' ] );
            $line = "";
            if ( !in_array( $temp[ 0 ], self::AVAILABLE_ORDER_COLUMNS ) ) {
                $line = false;
            } else {
                $line = $temp[ 0 ];
                if ( count( $temp ) > 1 and ( $temp[ 1 ] === "ASC" or $temp[ 1 ] === "DESC" ) ) {
                    $line .= " " . $temp[ 1 ];
                }
            }
            if ( $line === false ) {
                unset( $params[ 'order' ] );
            } else {
                $params[ 'order' ] = $line;
            }
        }
        if ( isset( $params[ 'id' ] ) ) {
            $params[ 'id' ] = preg_split( "/(\ )/", $params[ 'id' ] );
            foreach ( $params[ 'id' ] as $id ) {
                if ( !is_numeric( $id ) ) {
                    unset( $params[ 'id' ] );
                    break;
                }
            }
        }
        return $params;
    }

    private function validateParamsUpdate( $userParams )
    {
        $params = array();
        foreach ( $userParams as $key => $val ) {
            if ( isset( self::INITIAL_PARAMS[ $key ] ) and $this->isParamValid( $key, $val ) )
                $params[ $key ] = $userParams[ $key ];
        }
        if ( isset( $params[ 'list' ] ) ) {
            $params[ 'list' ] = $this->createList( $params[ 'list' ] );
        }
        if ( isset( $params[ 'id' ] ) ) {
            $params[ 'id' ] = preg_split( "/(\ )/", $params[ 'id' ] );
            foreach ( $params[ 'id' ] as $id ) {
                if ( !is_numeric( $id ) ) {
                    unset( $params[ 'id' ] );
                    break;
                }
            }
        }
        return $params;
    }

    private function isParamValid( $key, $value )
    {
        if ( !isset( self::AVAILABLE_PARAMS_TYPES[ $key ] ) ) {
            return false;
        }
        switch ( self::AVAILABLE_PARAMS_TYPES[ $key ] ) {
            case self::TYPE_STRING:
                return true;
            case self::TYPE_INTEGER:
                return is_numeric( $value );
            case self::TYPE_ARRAY:
                return is_array( $value );
            case self::TYPE_DATE:
                return ( strtotime( $value ) !== false );
        }
    }

    /**
     * Создать строку для SQL запроса получения записей (вернее, для stmt метода prepare)
     *
     * @param $params
     *
     * @return bool|string
     */
    private function createSTMTLineGet( $params )
    {
        $line = "SELECT * FROM `orders`";
        $temp = array();
        foreach ( $params as $key => $val ) {
            if ( $key === "order" ) continue;
            switch ( $key ) {
                case "minPrice":
                    $temp[] = "`price`>=?";
                    break;
                case "maxPrice":
                    $temp[] = "`price`<=?";
                    break;
                case "id":
                    if ( is_array( $val ) ) {
                        for ( $i = 0; $i < count( $val ); $i++ ) {
                            $val[ $i ] = "`id`=" . $val[ $i ];
                        }
                        $temp[] = "(" . implode( " or ", $val ) . ")";
                    } else {
                        $temp[] = "$key=$val";
                    }
                    break;
                default:
                    $temp[] = "$key=?";
                    break;
            }
        }
        if ( count( $temp ) == 0 ) {
            return $line;
        } else {
            $line .= " WHERE " . implode( " and ", $temp );
            if ( isset( $params[ 'order' ] ) ) $line .= " ORDER BY " . $params[ 'order' ];
            return $line;
        }
    }

    /**
     * Сформировать массив для вызова функции MYSQLI_STMT->bind_params(), т.к. функция вызывается
     * через call_user_func_array(), необходимо предварительно сформировать массив параметров для
     * нее.
     *
     * @param array $params Параметры
     *
     * @return array
     */
    private function createSTMTParamsGet( $params )
    {
        $s         = "";
        $temp      = array();
        $temp[ 0 ] = "";
        $i         = 1;
        foreach ( $params as $key => $val ) {
            if ( $key === "order" or $key === "id" ) {
                continue;
            }
            switch ( self::AVAILABLE_PARAMS_TYPES[ $key ] ) {
                case self::TYPE_INTEGER:
                    $s .= 'i';
                    break;
                default:
                    $s .= "s";
                    break;
            }
            $temp[ $i ] = $val;
            $i++;
        }
        $temp[ 0 ] = $s;
        return $temp;
    }

    /**
     * Создать текстовый файл с заказом
     *
     * @param array $params Параметры заказа (возможные см. в Orders::INITIAL_PARAMS
     * @param string path Путь, куда сохранить файл
     *
     * @throws \PhpOffice\PhpWord\Exception\Exception
     */
    private function createFile( $params, $path )
    {
        $file = new \PhpOffice\PhpWord\PhpWord();
        $file->setDefaultFontName( "Arial" );
        $file->setDefaultFontSize( 15 );

        $titleStyleFont      = array( "bold" => true, "size" => 22 );
        $titleStyleParagraph =
            array( "textAlignment" => \PhpOffice\PhpWord\SimpleType\TextAlignment::CENTER,
                   "alignment"     => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                   "indent"        => 0,
                   "spaceBefore"   => 50,
                   "spaceAfter"    => 50 );
        $textStyleParagraph  =
            array( "textAlignment" => \PhpOffice\PhpWord\SimpleType\TextAlignment::BASELINE,
                   "alignment"     => \PhpOffice\PhpWord\SimpleType\Jc::START,
                   "indent"        => 0.5,
                   "spaceBefore"   => 30,
                   "spaceAfter"    => 30 );
        $boldTextStyle       = array( "bold" => true );
        $tableStyle          =
            array( "width" => 100 * 50, "borderColor" => "000000", "borderSize" => 1 );
        $cellStyle           = array( "borderTopColor" => "000000", "borderTopSize" => 1 );
        $lineStyle           = array( "color" => "000000", "height" => 1, "width" => 1000 );

        $section = $file->addSection();

        $section->addText( "Заказ номер " . $params[ 'orderId' ], $titleStyleFont,
            $titleStyleParagraph );

        $textrun = $section->addTextRun( $textStyleParagraph );
        $textrun->addText( "Телефон: ", $boldTextStyle );
        $textrun->addText( $params[ 'phone' ] );

        $textrun = $section->addTextRun( $textStyleParagraph );
        $textrun->addText( "Email: ", $boldTextStyle );
        $textrun->addText( $params[ 'email' ] );

        $textrun = $section->addTextRun( $textStyleParagraph );
        $textrun->addText( "Имя: ", $boldTextStyle );
        $textrun->addText( $params[ 'name' ] );

        $section->addText( "Информация о заказе", $boldTextStyle, $titleStyleParagraph );

        $table = $section->addTable( $tableStyle );
        $table->getStyle()->setUnit( \PhpOffice\PhpWord\Style\Table::WIDTH_PERCENT );

        $table->addRow();
        $cell = $table->addCell();
        $cell->getStyle()->setStyleByArray( $cellStyle );
        $cell->addText( "Наименование", $boldTextStyle, $textStyleParagraph );

        $cell = $table->addCell();
        $cell->getStyle()->setStyleByArray( $cellStyle );
        $cell->addText( "Количество", $boldTextStyle, $textStyleParagraph );

        $cell = $table->addCell();
        $cell->getStyle()->setStyleByArray( $cellStyle );
        $cell->addText( "Цена (руб.)", $boldTextStyle, $textStyleParagraph );

        $mysqli = DB::connect();
        $stmt   = $mysqli->stmt_init();

        foreach ( $params[ 'list' ] as $item ) {
            $id = $item[ 'id' ];
            $stmt->prepare( "SELECT * FROM `shop` WHERE `id`=?" );
            $stmt->bind_param( "i", $id );
            $stmt->execute();
            if ( ( $row = $stmt->get_result()->fetch_assoc() ) !== false ) {
                $table->addRow();
                $cell = $table->addCell();
                $cell->getStyle()->setStyleByArray( $cellStyle );
                $cell->addText( $row[ 'name' ], array(), $textStyleParagraph );

                $cell = $table->addCell();
                $cell->getStyle()->setStyleByArray( $cellStyle );
                $cell->addText( $item[ 'count' ], array(), $textStyleParagraph );

                $cell = $table->addCell();
                $cell->getStyle()->setStyleByArray( $cellStyle );
                $cell->addText( $row[ 'price' ], array(), $textStyleParagraph );
            }
        }
        $stmt->close();

        $section->addText( "Общая сумма заказа: " . $this->calculatePrice( $params[ 'list' ] ),
            $boldTextStyle );

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter( $file, 'Word2007' );
        $objWriter->save( $path );
    }

    /**
     * Создать строку заказа для подстановки в SQL запрос
     *
     * @param array $order
     *
     * @return string
     */
    private function createList( $order )
    {
        $temp = array();
        foreach ( $order as $item ) {
            $temp[] = $item[ "id" ] . ":" . $item[ 'count' ];
        }
        return implode( ",", $temp );
    }

    private function calculatePrice( $order )
    {
        $shop = new Shop();
        $temp = array();
        foreach ( $order as $item ) {
            $temp[] = $item[ "id" ];
        }
        $items = $shop->find( array( "id" => $temp, "preview" => true ) );
        if ( $items !== false ) {
            $price = 0;
            foreach ( $items as $item ) {
                $price += $item[ 'price' ];
            }
            return $price;
        } else {
            return false;
        }
    }

    /**
     * Чет проблемы с call_user_function_array и bind_param... Приходится изворачиваться
     *
     * @param $arr
     *
     * @return array
     */
    private function refValues( $arr )
    {
        $refs = array();
        foreach ( $arr as $key => $value ) $refs[ $key ] = &$arr[ $key ];
        return $refs;
    }
}