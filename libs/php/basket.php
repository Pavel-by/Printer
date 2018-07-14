<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/basket.php";

$basket = new Basket();
$size   = $basket->size();
$items = $basket->get();
?>

<script type="text/javascript">
    var basketLabel = $('<span class="basket-label"></span>');
    var Basket = function () {
    };
    var preview = $("<div class='basket-preview passive'>");
    var previewShown = false;
    Basket.size = <?php echo $size; ?>;

    $(document).ready(function () {
        var root = $('#basket-place');
        if (root.length > 0) {
            var link = $('<a href="/basket" title="Корзина">');
            root.append(link);
            link.append($('<img class="basket-image" src="/images/basket-accent.png">'));
            link.append(basketLabel);
            root.append(preview);
            root.hover(function () {
                if (!previewShown) {
                    Basket.get(function (d) {
                        createPreview(d);
                        preview.removeClass('passive');
                        preview.addClass('active');

                    }, true);
                    previewShown = true;
                }
            }, function () {
                //$(preview).remove();
                preview.removeClass("active");
                preview.addClass('passive');
                previewShown = false;
            });
        }
        $('head').append(
            $('<link type="text/css" rel="stylesheet" href="/style/components/basket.css">')
        );
        Basket.draw();

        function createPreview(items) {
            console.log('create');
            var root = preview;
            root.empty();
            if (items.length > 0) {
                var button = $("<span class='input basket-preview-button'>Очистить</span>");
                button.click(function() {
                    Basket.clear(function() {
                        Basket.setSize(0);
                        console.log('before create');
                        createPreview([]);
                    });
                });
                root.append(button);
                for (let i = 0; i < 3 && i < items.length; i++) {
                    root.append(createLine(items[i].name, items[i].count, items[i].id));
                }
                if (items.length > 3) {
                    root.append(createLine("..."));
                }
                root.append($('<hr class="basket-preview-divider">'))
                button = $("<a href='/basket' class='input basket-preview-button'>К корзине</a>");
                root.append(button);
            } else {
                root.append(createLine("Корзина пуста"));
            }

            function createLine(text, count, id) {
                var root = $('<a href="#" class="input basket-preview-line">');
                var name = $('<span class="basket-preview-name">' + text + "</span>");
                root.append(name);
                if (id && typeof(id) !== undefined) {
                    root.attr('href', '/shop/' + id);
                } else {
                    root.addClass('passive');
                }
                if (count && typeof(count) !== undefined) {
                    var countBlock = $('<span class="basket-preview-count">' + count + "</span>");
                    root.append(countBlock);
                }
                return root;
            }

        }
    });


    Basket.setSize = function (size) {
        Basket.size = parseInt(size);
        Basket.draw();
    };

    Basket.draw = function () {
        var size = Basket.size;
        if (size > 0) {
            basketLabel.html(size);
            basketLabel.addClass('active');
        } else {
            basketLabel.html(size);
            basketLabel.removeClass('active');
        }
    };

    Basket.add = function (id, count) {
        Basket.changed = true;
        var data = new FormData();
        data.append('id', id);
        data.append('count', count);
        data.append('type', 'add');

        $.ajax({
            url: '/system/basket.php',
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: data,
            success: function (d) {
                if ('error' in d) {
                    alert('Ошибка при добавлении товара в корзину');
                } else if ('size' in d) {
                    Basket.setSize(d.size);
                }
            },
            error: function () {
                alert('Ошибка при добавлении товара в корзину');
            }
        });
    };

    Basket.remove = function (id) {
        Basket.changed = true;
        var data = new FormData();
        data.append('id', id);
        data.append('type', 'remove');

        $.ajax({
            url: '/system/basket.php',
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: data,
            success: function (d) {
                if ('error' in d) {
                    alert('Ошибка при удалении товара из корзины');
                } else if ('size' in d) {
                    Basket.setSize(d.size);
                }
            }
        });
    };

    Basket.clear = function (callback) {
        Basket.changed = true;
        var data = new FormData();
        data.append('type', 'clear');

        $.ajax({
            url: '/system/basket.php',
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: data,
            success: function (d) {
                if ('error' in d) {
                    alert('Ошибка при удалении товара из корзины');
                } else if ('size' in d) {
                    Basket.setSize(d.size);
                }
                if (typeof(callback) === "function") {
                    callback(d);
                }
            }
        });
    };

    Basket.changed = false;
    Basket.lastWasPreview = false;
    Basket.last = $.parseJSON("<?php echo addslashes(json_encode($items)); ?>");

    Basket.get = function (callback, preview) {
        if (!Basket.changed &&
            (preview === Basket.lastWasPreview || Basket.lastWasPreview === false)) {
            callback(Basket.last);
            return;
        }
        var data = new FormData();
        data.append('type', 'get');
        if (preview && typeof(preview) !== undefined && preview === true) {
            data.append('preview', "true");
        } else {
            preview = false;
        }

        $.ajax({
            url: '/system/basket.php',
            type: 'post',
            dataType: 'json',
            processData: false,
            contentType: false,
            data: data,
            success: function (d) {
                if (!('error' in d) && typeof(callback) === 'function') {
                    Basket.changed = false;
                    Basket.last = d;
                    Basket.lastWasPreview = preview;
                    callback(d);
                }
            },
            error: function (d) {
                alert('Фатальная ошибка');
            }
        });
    }
</script>
