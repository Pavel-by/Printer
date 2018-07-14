<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

class Image
{
    const SIZE_SMALL = array( 300, 300 );
    const SIZE_MEDIUM = array( 600, 600 );

    public function __construct()
    {
    }

    /**
     * Получить список названий картинок в директории
     * @param string $dir Директория, которую необходимо проверить (от корня сайта)
     * @return array Список файлов в директории
     */
    public static function get( $dir )
    {
        $dir = ROOT . "/" . trim($dir, "\/\\");
        if ( !is_dir( $dir ) ) {
            return array();
        }
        $allFiles = scandir( $dir );
        $files    = array();
        foreach ( $allFiles as $file ) {
            if ( $file == "." or $file == ".." or !is_file( "$dir/$file" ) ) {
                continue;
            }
            $files[] = $file;
        }
        return $files;
    }

    /**
     * Добавить картинку. В указанной директории будет создано 2 подпапки: small и medium,
     * содержащие уменьшенные копии исходной картинки в соответствии с размерами SIZE_SMALL и
     * SIZE_MEDIUM, а также исходная картинка.
     * @param string $directory    Директория, в которую вывести результаты (путь от корня сайта)
     * @param string $imagePath    Путь к исходной картинке
     * @param string $newImageName Новое имя изображения
     * @return bool Удалось ли выполнить операцию
     */
    public static function add( $directory, $imagePath, $newImageName )
    {
        if ( !is_file( $imagePath ) ) {
            return false;
        }

        $size = getimagesize( $imagePath );
        if ( !is_dir( ROOT . "/" . $directory or
            !is_dir( ROOT . "/" . $directory . "/small" ) or
            !is_dir( ROOT . "/" . $directory . "/medium" ) ) ) {
            @mkdir( ROOT . "/" . $directory );
            @mkdir( ROOT . "/" . $directory . "/small" );
            @mkdir( ROOT . "/" . $directory . "/medium" );
        }
        if ( $size[ 0 ] >= self::SIZE_SMALL[ 0 ] and $size[ 1 ] >= self::SIZE_SMALL[ 1 ] ) {
            self::resize(
                $imagePath,
                null,
                self::SIZE_SMALL[ 0 ],
                self::SIZE_SMALL[ 1 ],
                true,
                ROOT . "/$directory/small/$newImageName"
            );
        } else {
            copy( $imagePath, ROOT . "/$directory/small/$newImageName" );
        }

        if ( $size[ 0 ] >= self::SIZE_MEDIUM[ 0 ] and $size[ 1 ] >= self::SIZE_MEDIUM[ 1 ] ) {
            self::resize(
                $imagePath,
                null,
                self::SIZE_MEDIUM[ 0 ],
                self::SIZE_MEDIUM[ 1 ],
                true,
                ROOT . "/$directory/medium/$newImageName"
            );
        } else {
            copy( $imagePath, ROOT . "/$directory/medium/$newImageName" );
        }

        copy( $imagePath, ROOT . "/$directory/$newImageName" );
        return true;
    }

    /**
     * При изменении полученных размеров в соответствии с начальными пропорциями за отнову берется
     * высота
     * @param string $file             Начальный файл
     * @param null $string             Картинка в форме строки
     * @param int $width               Конечная ширина
     * @param int $height              Конечная высота
     * @param bool $proportional       Сохранять пропорции
     * @param string $output           Куда сохранять
     * @param bool $delete_original    Удалить оригинал
     * @param bool $use_linux_commands Использовать ли 'rm' при удалении исходной картинки
     * @param int $quality             Качество (от 1 до 100)
     * @return bool Удалась ли операция
     */
    public static function resize( $file,
                                   $string = null,
                                   $width = 0,
                                   $height = 0,
                                   $proportional = true,
                                   $output = 'file',
                                   $delete_original = false,
                                   $use_linux_commands = false,
                                   $quality = 100
    )
    {

        if ( $height <= 0 && $width <= 0 ) return false;
        if ( $file === null && $string === null ) return false;

        # Setting defaults and meta
        $info = $file !== null ? getimagesize( $file ) : getimagesizefromstring( $string );
        list( $width_old, $height_old ) = $info;
        $cropHeight = $cropWidth = 0;

        # Calculating proportionality
        if ( $proportional ) {
            if ( $width == 0 ) $factor = $height / $height_old;
            elseif ( $height == 0 ) $factor = $width / $width_old;
            else                    $factor = min( $width / $width_old, $height / $height_old );

            $final_width  = round( $width_old * $factor );
            $final_height = round( $height_old * $factor );
        } else {
            $final_width  = ( $width <= 0 ) ? $width_old : $width;
            $final_height = ( $height <= 0 ) ? $height_old : $height;
            $widthX       = $width_old / $width;
            $heightX      = $height_old / $height;

            $x          = min( $widthX, $heightX );
            $cropWidth  = ( $width_old - $width * $x ) / 2;
            $cropHeight = ( $height_old - $height * $x ) / 2;
        }

        # Loading image to memory according to type
        switch ( $info[ 2 ] ) {
            case IMAGETYPE_JPEG:
                $file !== null ? $image = imagecreatefromjpeg( $file ) : $image = imagecreatefromstring( $string );
                break;
            case IMAGETYPE_GIF:
                $file !== null ? $image = imagecreatefromgif( $file ) : $image = imagecreatefromstring( $string );
                break;
            case IMAGETYPE_PNG:
                $file !== null ? $image = imagecreatefrompng( $file ) : $image = imagecreatefromstring( $string );
                break;
            default:
                return false;
        }


        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor( $final_width, $final_height );
        if ( ( $info[ 2 ] == IMAGETYPE_GIF ) || ( $info[ 2 ] == IMAGETYPE_PNG ) ) {
            $transparency = imagecolortransparent( $image );
            $palletsize   = imagecolorstotal( $image );

            if ( $transparency >= 0 && $transparency < $palletsize ) {
                $transparent_color = imagecolorsforindex( $image, $transparency );
                $transparency      = imagecolorallocate( $image_resized, $transparent_color[ 'red' ], $transparent_color[ 'green' ], $transparent_color[ 'blue' ] );
                imagefill( $image_resized, 0, 0, $transparency );
                imagecolortransparent( $image_resized, $transparency );
            } elseif ( $info[ 2 ] == IMAGETYPE_PNG ) {
                imagealphablending( $image_resized, false );
                $color = imagecolorallocatealpha( $image_resized, 0, 0, 0, 127 );
                imagefill( $image_resized, 0, 0, $color );
                imagesavealpha( $image_resized, true );
            }
        }
        imagecopyresampled( $image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight );


        # Taking care of original, if needed
        if ( $delete_original ) {
            if ( $use_linux_commands ) exec( 'rm ' . $file );
            else @unlink( $file );
        }

        # Preparing a method of providing result
        switch ( strtolower( $output ) ) {
            case 'browser':
                $mime = image_type_to_mime_type( $info[ 2 ] );
                header( "Content-type: $mime" );
                $output = NULL;
                break;
            case 'file':
                $output = $file;
                break;
            case 'return':
                return $image_resized;
                break;
            default:
                break;
        }

        # Writing image according to type to the output destination and image quality
        switch ( $info[ 2 ] ) {
            case IMAGETYPE_GIF:
                imagegif( $image_resized, $output );
                break;
            case IMAGETYPE_JPEG:
                imagejpeg( $image_resized, $output, $quality );
                break;
            case IMAGETYPE_PNG:
                $quality = 9 - (int)( ( 0.9 * $quality ) / 10.0 );
                imagepng( $image_resized, $output, $quality );
                break;
            default:
                return false;
        }

        return true;
    }
}