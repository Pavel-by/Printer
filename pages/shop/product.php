<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/shop.php";

$shop = new Shop();
if ( ( $res = $shop->find( array( 'id' => $last ) ) ) !== false ) {
    if ( count( $res ) > 0 ) {
        printProductCard( $res[ 0 ] );
    } else {
        print404();
    }
} else {
    print404();
}

function print404()
{
    echo "<div>
        <h3>Ошибка</h3>
        <p>Товар не был найден.</p>
    </div>";
}

function printProductCard( $item )
{
    $id = $item['id'];
    $name        = $item[ 'name' ];
    $description = $item[ 'description' ];
    $price       = $item[ 'price' ];
    if ( strlen( $item[ 'images' ] ) > 0 ) {
        $imageBig       = "/images/shop/" . preg_split( "/(;)/", $item[ 'images' ] )[ 0 ];
        $smallImages = "";
        foreach ( preg_split( "/(;)/", $item[ 'images' ] ) as $image ) {
            $smallImages .= "<a href='/images/shop/$image' class='product-image product-image-small' data-lcl-thumb=\"/images/shop/" . $image . "\">
    <img src='/images/shop/small/" . $image . "'>
</a>";
        }
    } else {
        $imageBig       = "/images/image-close.png";
        $smallImages = "";
    }

    echo "<div>
    <h2 style='padding-top: 0; margin-top: 0;'>$name</h2>
    <div class='flex-block flex-row flex-top flex-left'>
        <div style='flex-basis: 50%;' class='flex-block flex-column flex-center'>
            <img class='product-image-main' src='$imageBig'>
            <div class='flex-block flex-row flex-top flex-left flex-wrap'>$smallImages</div>
        </div>
        <div style='flex-basis: 50%;'>
            <div>
                <span class='product-price'>$price</span>
            </div>
            <div>
                <span class='input input-light' onclick='Basket.add($id, 1);'>В корзину</span>    
            </div>
            <p class='product-description'>$description</p>
        </div>
    </div>
</div>";
}

?>

<script type="text/javascript">
    $(document).ready(function() {
        Gallery('.product-image');
    })
</script>
