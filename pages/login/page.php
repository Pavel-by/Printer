<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/user.php";

$user = new User();

?>

<div class="page-root">
    <div class="limit-block">
        <div class="content-indent center">
            <form class="login-form" id="form-login" onsubmit="send(); return false;">
                <h3>Авторизация</h3>
                <div class="input-block flex-block flex-column flex-left">
                    <span class="input input-header">Логин</span>
                    <input type="text" class="input input-text" name="login">
                </div>


                <div class="input-block flex-block flex-column flex-left">
                    <span class="input input-header">Пароль</span>
                    <input type="password" class="input input-text" name="password">
                </div>

                <div class="flex-block flex-row flex-left flex-middle">
                    <input type="submit" class="input input-submit" value="Войти">
                    <img src="/images/settings-wheel.png" style="height: 20px"
                         class="loading">
                </div>
                <span class="input input-error" id="error" style="display: none;"></span>

                <?php
                if ($user->getAccess() > 0) {
                    $name = $user->getUserName();
                    $s = "";
                    $s .= "<hr>";
                    $s .= "<a href='/admin' class='inline-block full-width' style='text-decoration: none;'>
    <div class='flex-block flex-row flex-between flex-middle login-block-authorised'>
        <p class='auth-name'>$name</p>
        <span class='auth-link'>Войти</span>
    </div>
</a>";
                    echo $s;
                }
                ?>

                <Divider></Divider>
                <div class="center">
                    <a class="login-small-link" href="/">Вернуться на главную</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    function send() {
        var error = $("#error");
        var data = new FormData($('#form-login')[0]);
        data.append("type", "sign-in");
        $.ajax({
            url: "/system/user.php",
            contentType: false,
            processData: false,
            dataType: "json",
            type: "post",
            data: data,
            success: function(d) {
                if (!("error" in d)) {
                    error.hide(100);
                    window.location.replace("/admin");
                } else {
                    error.html(d.error);
                    error.show();
                }
            }, error: function() {
                error.html("Фатальная ошибка");
                error.show();
            }
        });
    }
</script>