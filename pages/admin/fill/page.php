<script type="text/javascript">
    var DEFAULT_FORM;
    var selectImage = new SelectImage({
        dir: "/images/fill"
    });

    /**
     * Получить список картриджей
     * @param callback array (если запрос успешный) или null в случае ошибки
     */
    function getCartridgesList(callback) {
        $.ajax({
            url: '/system/admin/fill.php?type=get',
            type: 'post',
            dataType: 'json',
            data: {type: 'get'},
            success: function (d) {
                if ('error' in d) {
                    if (typeof (callback) === 'function') callback(null);
                } else {
                    if (typeof (callback) === 'function') callback(d.cartridges);
                }
            },
            error: function () {
                if (typeof (callback) === 'function') callback(null);
            }
        });
    }

    function updateCartridgesList() {
        getCartridgesList(function (d) {
            var root = $("#cartridges");
            root.empty();
            $.each(d, function (index, item) {
                var cartridge = createCartridge(item);
                cartridge.click(function () {
                    editCartridge(item);
                });
                root.append(cartridge);
            });
        });

        function createCartridge(item) {
            var images = item.images.split(';');
            var root = $("<div class='flex-block flex-row flex-middle flex-left cartridge'>");
            if (images.length > 0 && item.images.length > 0) {
                root.append("<img src='/images/fill/small/" + images[0] + "'>");
            }
            root.append("<div>\n    <p>\n        <b>Принтер: </b>\n        <span>" + item.printerName + "</span>\n    </p>\n    <p>\n        <b>Картридж: </b>\n        <span>" + item.cartridgeName + "</span>\n    </p>\n</div>")
            return root;
        }
    }

    function editCartridge(item) {
        var form = DEFAULT_FORM.clone(true, true);
        if (item.images.length > 0) {
            selectImage.setSelected(item.images.split(';'));
        } else {
            selectImage.setSelected([]);
        }
        form.prepend(selectImage.createWidget());
        form.find('[name=id]').val(item.id);
        form.find('[name=printerName]').val(item.printerName);
        form.find('[name=cartridgeName]').val(item.cartridgeName);
        form.find('[name=refillPrice]').val(item.refillPrice);
        form.find('[name=photoreceptorPrice]').val(item.photoreceptorPrice);
        form.find('[name=rakelPrice]').val(item.rakelPrice);
        form.find('[name=PCRPrice]').val(item.PCRPrice);
        form.find('[name=shellPrice]').val(item.shellPrice);
        form.find('[name=bladePrice]').val(item.bladePrice);
        form.find('[name=bushingPrice]').val(item.bushingPrice);
        form.css('display', 'inline-block');
        var message = new Message(
            {
                header: "Редактирование",
                custom: form,
                buttons: [
                    {
                        val: "Сохранить",
                        click: function () {
                            sendToServer(form, "edit", function(d, error) {
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
                                updateCartridgesList();
                            });
                        },
                        style: Message.STYLE_BRIGHT
                    },
                    {
                        val: "Удалить",
                        click: function() {
                            sendToServer(form, "remove", function(d) {
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
                                updateCartridgesList();
                            });
                        }
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

    function addCartridge() {
        var form = DEFAULT_FORM.clone(true, true);
        selectImage.setSelected([]);
        form.prepend(selectImage.createWidget());
        form.find('[name=id]').val("");
        form.find('[name=printerName]').val("");
        form.find('[name=cartridgeName]').val("");
        form.find('[name=refillPrice]').val("");
        form.find('[name=photoreceptorPrice]').val("");
        form.find('[name=rakelPrice]').val("");
        form.find('[name=PCRPrice]').val("");
        form.find('[name=shellPrice]').val("");
        form.find('[name=bladePrice]').val("");
        form.find('[name=bushingPrice]').val("");
        form.css('display', 'inline-block');
        var message = new Message(
            {
                header: "Добавить",
                custom: form,
                buttons: [
                    {
                        val: "Сохранить",
                        click: function () {
                            sendToServer(form, "add", function(d, error) {
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
                                updateCartridgesList();
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
        $.each($(form).serializeArray(), function(index, item) {
             data.append(item.name, item.value);
        });
        data.append('type', type);
        data.append('images', selectImage.getSelected().join(';'));
        $.ajax({
            url: "/system/admin/fill.php",
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

    function readImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $(input).parent().find('.load_image').attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }

    }

    function showEditForm(elem) {
        elem = $(elem);
        var form = $('#formEditItem');
        form.find('.before-input-file').html(form.find('.before-input-file').html());

        form.find('[name=id]').val(elem.attr('id'));
        form.find('img').attr('src', elem.find('img').attr('src'));
        form.find('[name=prevSrc]').val(elem.find('img').attr('src'));
        form.find('[name=printerName]').val(elem.find('.printerName').html());
        form.find('[name=cartridgeName]').val(elem.find('.cartridgeName').html());
        form.find('[name=refillPrice]').val(elem.find('.refillPrice').html());
        form.find('[name=photoreceptorPrice]').val(elem.find('.photoreceptorPrice').html());
        form.find('[name=rakelPrice]').val(elem.find('.rakelPrice').html());
        form.find('[name=PCRPrice]').val(elem.find('.PCRPrice').html());
        form.find('[name=shellPrice]').val(elem.find('.shellPrice').html());
        form.find('[name=bladePrice]').val(elem.find('.bladePrice').html());
        form.find('[name=bushingPrice]').val(elem.find('.bushingPrice').html());

        form.addClass('active');
    }

    function deleteItem(elem) {
        var id = $(elem).parent().find('[name=id]').val();
        var prevSrc = $(elem).parent().find('[name=prevSrc]').val();

        $.ajax({
            type: 'POST', // Тип запроса
            url: 'edit_cartridge.php?action=delete', // Скрипт обработчика
            data: {id: id, prevSrc: prevSrc}, // Данные которые мы передаем
            success: function (data) {
                getCartridges();
                $('#formEditItem').removeClass('active');
            },
            error: function (data) {
                console.log(data);
            }
        });
    }

    $(document).ready(function () {
        updateCartridgesList();

        DEFAULT_FORM = $("#form");

        $('#create_item_btn').click(function () {
            var formData = new FormData();
            formNewItem
            var form = $('#formNewItem');

            $.each($('#newItemImage')[0].files, function (i, file) {
                formData.append('file-' + i, file);
            });

            formData.append('printerName', form.find('[name=printerName]').val());
            formData.append('cartridgeName', form.find('[name=cartridgeName]').val());
            formData.append('refillPrice', form.find('[name=refillPrice]').val());
            formData.append('photoreceptorPrice', form.find('[name=photoreceptorPrice]').val());
            formData.append('rakelPrice', form.find('[name=rakelPrice]').val());
            formData.append('PCRPrice', form.find('[name=PCRPrice]').val());
            formData.append('shellPrice', form.find('[name=shellPrice]').val());
            formData.append('bladePrice', form.find('[name=bladePrice]').val());
            formData.append('bushingPrice', form.find('[name=bushingPrice]').val());

            $.ajax({
                type: 'POST', // Тип запроса
                url: 'save_cartridge.php', // Скрипт обработчика
                data: formData, // Данные которые мы передаем
                contentType: false,
                processData: false,
                cache: false,
                success: function (data) {
                    getCartridges();
                    clearNewItemForm();
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });

        $('#edit_item_btn').click(function () {
            var formData = new FormData();
            var form = $('#formEditItem');

            $.each($('#editItemImage')[0].files, function (i, file) {
                formData.append('file-' + i, file);
            });

            formData.append('prevSrc', form.find('[name=prevSrc]').val());
            formData.append('id', form.find('[name=id]').val());
            formData.append('printerName', form.find('[name=printerName]').val());
            formData.append('cartridgeName', form.find('[name=cartridgeName]').val());
            formData.append('refillPrice', form.find('[name=refillPrice]').val());
            formData.append('photoreceptorPrice', form.find('[name=photoreceptorPrice]').val());
            formData.append('rakelPrice', form.find('[name=rakelPrice]').val());
            formData.append('PCRPrice', form.find('[name=PCRPrice]').val());
            formData.append('shellPrice', form.find('[name=shellPrice]').val());
            formData.append('bladePrice', form.find('[name=bladePrice]').val());
            formData.append('bushingPrice', form.find('[name=bushingPrice]').val());

            $.ajax({
                type: 'POST', // Тип запроса
                url: 'edit_cartridge.php?action=edit', // Скрипт обработчика
                data: formData, // Данные которые мы передаем
                contentType: false,
                processData: false,
                cache: false,
                success: function (data) {
                    getCartridges();
                    $('#formEditItem').removeClass('active');
                },
                error: function (data) {
                    console.log(data);
                }
            });
        });
    });

    function showAddWindow() {
        var mes = new Message({
            header: "Добавить",
            custom: $("form[name=myForm]").clone().css({display: "block"}),
            buttons: [
                {
                    val: "Добавить",
                    style: Message.STYLE_BRIGHT,
                    click: function () {
                        alert("click");
                    }
                },
                {
                    val: "Отмена",
                    style: Message.STYLE_SIMPLE,
                    click: function () {
                        mes.hide();
                    }
                }
            ]
        });
        mes.show();
    }
</script>

<form id="form" style="display: none;">
    <div class="limit-block">
        <input type="hidden" name="id">
        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Название принтера</span>
            <input type="text" class="input input-text" name="printerName">
        </div>

        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Название картриджа</span>
            <input type="text" class="input input-text" name="cartridgeName">
        </div>

        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Цена: заправка</span>
            <input type="text" class="input input-text" name="refillPrice">
        </div>

        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Цена: фоторецептор</span>
            <input type="text" class="input input-text" name="photoreceptorPrice">
        </div>

        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Цена: ракель</span>
            <input type="text" class="input input-text" name="rakelPrice">
        </div>

        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Цена: вал первичного заряда</span>
            <input type="text" class="input input-text" name="PCRPrice">
        </div>

        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Цена: оболочка магнитного вала</span>
            <input type="text" class="input input-text" name="shellPrice">
        </div>

        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Цена: дозирующее лезвие магнитного вала</span>
            <input type="text" class="input input-text" name="bladePrice">
        </div>

        <div class="flex-block flex-column flex-left input-block">
            <span class="input input-header">Цена: бушинг магнитного вала</span>
            <input type="text" class="input input-text" name="bushingPrice">
        </div>
    </div>
</form>

<div class="limit-block">
    <div class="content-indent">
        <div class="card center">
            <input type="button" class="input input-submit" value="Добавить картридж"
                   onclick="addCartridge();">
        </div>
        <div id="cartridges">

        </div>
    </div>
</div>