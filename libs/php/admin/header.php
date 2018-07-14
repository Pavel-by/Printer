<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/user.php";

$user = new User();

?>
<div class="limit-block">
    <div class="content-indent">
        <div class="flex-block flex-row flex-between flex-middle">
            <a href="/" class="top-link">В основную часть сайта</a>
            <div class="flex-block flex-row flex-right flex-middle">
                <p class="top-username"><?php echo $user->getUserName(); ?></p>
                <a href="/system/user.php?type=sign-out" class="top-link">Выйти</a>
            </div>
        </div>
        <div class="flex-block flex-row flex-center flex-middle">
            <a class="admin-menu-item" href="/admin/fill">Заправка</a>
            <a class="admin-menu-item" href="/admin/shop">Магазин</a>
            <a class="admin-menu-item" href="/admin/orders">Заказы</a>
        </div>
        <hr>
    </div>
</div>