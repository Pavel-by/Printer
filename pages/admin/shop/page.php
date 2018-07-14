<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/categories.php";

$categoriesClass = new Categories();

$categories = $categoriesClass->getCategories();

?>

<script type="text/javascript">
    var DEFAULT_FORM;
    var selectImage = new SelectImage({
        dir: "/images/shop"
    });
    var categories = $.parseJSON("<?php echo addslashes( json_encode( $categories ) ); ?>");

    /**
     * Получить список картриджей
     * @param callback array (если запрос успешный) или null в случае ошибки
     */
    function getProductsList(callback) {
        var data = new FormData($("#search-form")[0]);
        data.append("type", "get")
        $.ajax({
            url: '/system/get-shop-items.php',
            type: 'post',
            dataType: 'json',
            contentType: false,
            processData: false,
            data: data,
            success: function (d) {
                if ('error' in d) {
                    if (typeof (callback) === 'function') callback(null);
                } else {
                    if (typeof (callback) === 'function') callback(d.items);
                }
            },
            error: function () {
                if (typeof (callback) === 'function') callback(null);
            }
        });
    }

    function updateProductsList() {
        getProductsList(function (d) {
            var root = $("#cartridges");
            root.empty();
            $.each(d, function (index, item) {
                var cartridge = createProduct(item);
                cartridge.click(function () {
                    editProduct(item);
                });
                root.append(cartridge);
            });
        });

        function createProduct(item) {
            var images = item.images.split(';');
            var root = $("<div class='flex-block flex-row flex-middle flex-left cartridge'>");
            if (images.length > 0 && item.images.length > 0) {
                root.append("<img src='/images/shop/small/" + images[0] + "'>");
            }
            root.append("<div>\n    <p>\n        <b>Название: </b>\n        <span>" + item.name + "</span>\n    </p>\n    <p>\n        <b>Цена: </b>\n        <span>" + item.price + "</span>\n    </p>\n</div>")
            return root;
        }
    }

    function editProduct(item) {
        var form = DEFAULT_FORM.clone(true, true);
        if (item.images.length > 0) {
            selectImage.setSelected(item.images.split(';'));
        } else {
            selectImage.setSelected([]);
        }
        console.log(item.category);
        form.prepend(selectImage.createWidget());
        form.find('[name=id]').val(item.id);
        form.find('[name=name]').val(item.name);
        form.find('[name=price]').val(item.price);
        form.find('[name=category]').val(item.category.toString()).niceSelect();
        form.find('[name=description]').val(item.description);
        form.css('display', 'inline-block');
        var message = new Message(
            {
                header: "Редактирование",
                custom: form,
                buttons: [
                    {
                        val: "Сохранить",
                        click: function () {
                            sendToServer(form, "edit", function (d, error) {
                                message.hide();
                                if (d === null) {
                                    var mesData = {
                                        header: "Ошибка"
                                    };
                                    if (error !== null) {
                                        mesData.text = error;
                                    } else {
                                        mesData.text = "Фатальная ошибка. Не удалось обработать ответ сервера.";
                                    }
                                    Message.create(mesData);
                                }
                                updateProductsList();
                            });
                        },
                        style: Message.STYLE_BRIGHT
                    },
                    {
                        val: "Удалить",
                        click: function () {
                            sendToServer(form, "remove", function (d) {
                                message.hide();
                                if (d === null) {
                                    var mesData = {
                                        header: "Ошибка"
                                    };
                                    if (error !== null) {
                                        mesData.text = error;
                                    } else {
                                        mesData.text = "Фатальная ошибка. Не удалось обработать ответ сервера.";
                                    }
                                    Message.create(mesData);
                                }
                                updateProductsList();
                            });
                        }
                    },
                    {
                        val: "Закрыть",
                        click: function () {
                            message.hide();
                        }
                    }
                ]
            });
        message.show();
    }

    function addProduct() {
        var form = DEFAULT_FORM.clone(true, true);
        selectImage.setSelected([]);
        form.prepend(selectImage.createWidget());
        form.find('[name=id]').val("");
        form.find('[name=name]').val("");
        form.find('[name=price]').val("");
        form.find('[name=category]').val("cartridges").niceSelect();
        form.find('[name=description]').val("");
        form.css('display', 'inline-block');
        var message = new Message(
            {
                header: "Добавить",
                custom: form,
                buttons: [
                    {
                        val: "Сохранить",
                        click: function () {
                            sendToServer(form, "add", function (d, error) {
                                message.hide();
                                if (d === null) {
                                    var mesData = {
                                        header: "Ошибка"
                                    };
                                    if (error !== null) {
                                        mesData.text = error;
                                    } else {
                                        mesData.text = "Фатальная ошибка. Не удалось обработать ответ сервера.";
                                    }
                                    Message.create(mesData);
                                }
                                updateProductsList();
                            });
                        },
                        style: Message.STYLE_BRIGHT
                    },
                    {
                        val: "Зактыть",
                        click: function () {
                            message.hide();
                        }
                    }
                ]
            });
        message.show();
    }

    function changeCategories() {
        var root = $("<div class=\'flex-block flex-column flex-center\'>\n    <p>Необходимо учитывать, что </p>\n</div>");
        var categoriesBlock = $("<div class='flex-block flex-column flex-center'>");
        root.append(categoriesBlock);

        $.each(categories, function (index, item) {
            categoriesBlock.append(createLine(item.english, item.name));
        });

        var addButton = $("<input class='input input-button' value='Добавить' type='button'>")
            .click(function () {
                categoriesBlock.append(createLine("", ""))
            });
        root.append($("<div>").append(addButton));

        var message = new Message({
            header: "Изменить категории",
            custom: root,
            buttons: [
                {
                    val: "Сохранить",
                    click: function () {
                        if (validateFields()) {
                            var data = new FormData();
                            var s = [];
                            $.each(categoriesBlock.children(), function (index, elem) {
                                s.push($(elem).find('[name=english]').val() + ":" + $(elem).find('[name=name]').val());
                            });
                            data.append("categories", s.join(';'));
                            data.append("type", "set");
                            $.ajax({
                                url: "/system/admin/categories.php",
                                type: "post",
                                dataType: "json",
                                processData: false,
                                contentType: false,
                                data: data,
                                success: function(d) {
                                    if ('error' in d) {
                                        Message.create({header: "Ошибка", text: d.error});
                                    } else {
                                        document.location.reload();
                                    }
                                    message.hide();
                                },
                                error: function() {
                                    Message.create({header: "Ошибка", text: "Фатальная ошибка. Не удалось обработать ответ сервера."});
                                }
                            });
                        }
                    },
                    style: Message.STYLE_BRIGHT
                },
                {
                    val: "Закрыть",
                    click: function () {
                        message.hide();
                    }
                }
            ]
        });
        message.show();

        function validateFields() {
            var validEnglishCodes = [];
            var illegalChars = [";", ":"];
            var ok = true;
            for (let i = 65; i <= 90; i++) {
                validEnglishCodes.push(i);
            }
            for (let i = 97; i <= 122; i++) {
                validEnglishCodes.push(i);
            }
            root.find('input').each(function (index, item) {
                var field = $(item);
                var val = field.val();
                if (!field.is('[name]')) {
                    return;
                }

                if (field.attr('name') === "english" && val.length > 0) {
                    for (let i = 0; i < val.length; i++) {
                        if (validEnglishCodes.indexOf(val.charCodeAt(i)) < 0) {
                            field.addClass('error');
                            ok = false;
                            return;
                        }
                    }
                    field.removeClass('error');
                } else if (val.length === 0) {
                    ok = false;
                    field.addClass('error');
                } else {
                    field.removeClass('error');
                }
            });
            return ok;
        }

        function createLine(eng, name) {
            var root = $("<div class=\'merge-input-border-radiuses center\'>\n    <input type=\'text\' class=\'input input-text\' placeholder=\'На английском\' name=\'english\' value=\'" + eng + "\'>\n    <input type=\'text\' class=\'input input-text\' placeholder=\'Название\' name=\'name\' value=\'" + name + "\'>\n</div>");
            var closeButton = $("<span class='input input-button'>Удалить</span>")
                .click(function () {
                    root.remove();
                });
            root.append(closeButton);
            return root;
        }
    }

    /**
     * Отослать данные на сервер.
     * @param form Форма, содержащая всю необходимую информацию в input (кроме типа запроса)
     * @param type Тип запроса (add, edit, remove и т.д.)
     * @param callback Функция, которая будет вызвана по окончании запроса на сервер. В случае
     * успешного запроса возвращает ответ сервера, иначе -
     * callback(null,<Описание ошибки: если описания нет, то null>)
     */
    function sendToServer(form, type, callback) {
        var data = new FormData();
        $.each($(form).serializeArray(), function (index, item) {
            data.append(item.name, item.value);
        });
        data.append('type', type);
        data.append('images', selectImage.getSelected().join(';'));
        $.ajax({
            url: "/system/admin/shop.php",
            type: "post",
            dataType: "json",
            processData: false,
            contentType: false,
            data: data,
            success: function (d) {
                if ('error' in d) {
                    if (typeof(callback) === 'function') callback(null, d.error);
                } else {
                    if (typeof(callback) === 'function') callback(d);
                }
            },
            error: function () {
                if (typeof(callback) === 'function') callback(null, null);
            }
        });
    }

    $(document).ready(function () {
        updateProductsList();

        DEFAULT_FORM = $("#form");

        $('#search-select').niceSelect();
    });
</script>

<form id="form" style="display: none;">
    <div class="limit-block">
        <input type="hidden" name="id">
        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Название</span>
            <input type="text" class="input input-text" name="name">
        </div>

        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Цена</span>
            <input type="text" class="input input-text" name="price">
        </div>

        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Категория</span>
            <select name="category">
                <?php

                foreach ( $categories as $category ) {
                    echo "<option value='" . $category[ 'id' ] . "'>" . $category[ 'name' ] . "</option>";
                }

                ?>
            </select>
        </div>

        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Описание</span>
            <textarea class="input input-text" name="description"></textarea>
        </div>
    </div>
</form>

<div class="limit-block">
    <div class="content-indent">
        <div class="card center">
            <input type="button" class="input input-submit" value="Добавить товар"
                   onclick="addProduct();">
            <input type="button" class="input input-submit" value="Изменить список категорий"
                   onclick="changeCategories();">
        </div>
        <div class="center">
            <form id="search-form" class="flex-block flex-row flex-center flex-top"
                  onsubmit="updateProductsList(); return false;">
                <div class="merge-input-border-radiuses inline-block">
                    <select name="category" class="input" id="search-select">
                        <option value="">Все</option>
                        <?php

                        foreach ( $categories as $category ) {
                            echo "<option value='" . $category[ 'id' ] . "'>" . $category[ 'name' ] . "</option>";
                        }

                        ?>
                    </select>
                    <input name="name" class="input input-text" type="text" placeholder="Название">
                    <input value="Найти" class="input input-submit" type="submit">
                </div>

            </form>
        </div>
        <div id="cartridges">

        </div>
    </div>
</div>