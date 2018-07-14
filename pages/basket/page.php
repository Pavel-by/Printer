<div class="pre-content">
    <div class="limit-block">
        <div class="content-indent">
            <h2>Корзина</h2>
            <Divider></Divider>
            <form onsubmit="send(); return false;" id="form">
                <div id="basket-items" class="merge-input-border-radiuses-vertical"></div>
                <div>
                    <div class="flex-block flex-row flex-center flex-top">
                        <div id="blockOrder" class="contact-info-block">
                            <h3>Контактная информация</h3>
                            <p class="text-hint"><b>*</b> - поля, обязательные для заполнения</p>
                            <div class="flex-block flex-column flex-middle flex-left">
                                <div class="full-width input-block">
                                    <span class="input input-header">
                                        Номер телефона <b>*</b>
                                    </span>
                                    <input type="text" class="input input-text full-width" value=""
                                           placeholder="" name="phone">
                                    <span style="display: none;" class="input input-error">Это поле обязательно для заполнения</span>
                                </div>
                                <div class="full-width input-block">
                                    <span class="input input-header">
                                        Адрес эл. почты <b>*</b>
                                    </span>
                                    <input type="text" class="input input-text full-width" value=""
                                           placeholder="" name="email">
                                    <div style="display: none;" class="input input-error">
                                        <p>Убедитесь, что Вы правильно заполнили поле:</p>
                                        <ul>
                                            <li id="error-email-empty">Это поле не должно быть
                                                пустым
                                            </li>
                                            <li id="error-email-has-symbol">Должен присутствовать
                                                символ "@"
                                            </li>
                                            <li id="error-email-start-symbol">Символ "@" не должен
                                                стоять в начале
                                            </li>
                                            <li id="error-email-end-symbol">Символ "@" не должен
                                                стоять в конце
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="full-width input-block">
                                    <span class="input input-header">
                                        Имя
                                    </span>
                                    <input type="text" class="input input-text full-width" value=""
                                           placeholder="" name="name">
                                </div>
                            </div>
                            <div class="flex-block flex-middle flex-left flex-row">
                                <div class="merge-input-border-radiuses">
                                    <input type="submit" class="input input-submit"
                                           value="Заказать">
                                    <span class="input pre-input" id="total-price">0</span>
                                </div>
                                <img id="loading" class="loading"
                                     style="height: 20px; margin: 10px;"
                                     src="/images/settings-wheel.png">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
        const EMAIL_EMPTY = "error-email-empty";
        const EMAIL_HAS_NOT = "error-email-has-symbol";
        const EMAIL_START_SYMBOL = "error-email-start-symbol";
        const EMAIL_END_SYMBOL = "error-email-end-symbol";
        $(document).ready(function () {
            var phone = $("input[name=phone]");
            var email = $("input[name=email]");
            phone.mask("+7 (999) 999 99 99");
            phone.blur(function () {
                validateInputs(true, false);
            });
            phone.keyup(function () {
                if ($(this).hasClass('error')) {
                    validateInputs(true, false);
                }
            });
            email.blur(function () {
                validateInputs(false, true);
            });
            email.keyup(function () {
                if ($(this).hasClass('error')) {
                    validateInputs(false, true);
                }
            });
            var root = $("#basket-items");
            Basket.get(function (items) {
                $.each(items, function (index, item) {
                    root.append(createBasketItem(item));
                });
            });
            updateTotalPrice();
        });

        function updateTotalPrice() {
            var total = 0;
            $(".basket-item").each(function (index, elem) {
                var price = parseInt($(elem).attr('price'));
                var count = parseInt($(elem).find("input[name=count]").val());
                $(elem).find("p.basket-item-price").html(price * count);
                console.log(price, count);
                total += price * count;
            });
            if (total > 0) {
                unlockInputs();
                $("#total-price").html(total);
            } else {
                lockInputs();
                $("#total-price").html(total);
            }
        }

        function lockInputs() {
            $("#blockOrder").find("input").each(function (index, item) {
                item.disabled = true;
            });
        }

        function unlockInputs() {
            $("#blockOrder").find("input").each(function (index, item) {
                item.disabled = false;
            });
        }

        function validateInputs(showPhoneError, showEmailError) {
            if (!showPhoneError) showPhoneError = false;
            if (!showEmailError) showEmailError = false;

            var email = $("input[name=email]");
            var phone = $("input[name=phone]");
            var result = {
                email: true,
                phone: true
            };
            if (phone.val().length === 0 || phone.val().indexOf("_") >= 0) {
                result.phone = false;
            }
            else {
                phone.removeClass('error');
                phone.next('.input-error').hide(100);
            }

            var val = $(email).val();
            var pos = val.indexOf("@");
            if (pos === -1 || pos === val.length - 1 || pos === 0) {
                result.email = [];
                if (val.length === 0) {
                    result.email.push(EMAIL_EMPTY);
                }
                if (pos === -1) {
                    result.email.push(EMAIL_HAS_NOT);
                }
                if (pos === 0) {
                    result.email.push(EMAIL_START_SYMBOL);
                }
                if (pos === val.length - 1) {
                    result.email.push(EMAIL_END_SYMBOL);
                }
            }
            else {
                email.removeClass('error');
                email.next('.input-error').hide(100);
            }

            if (showPhoneError && result.phone !== true) {
                phone.addClass('error');
                phone.next('.input-error').show(100);
            }

            if (showEmailError && result.email !== true) {
                email.addClass('error');
                var emailError = email.next('.input-error');
                emailError.show(100);

                var temp = [EMAIL_HAS_NOT, EMAIL_EMPTY, EMAIL_END_SYMBOL, EMAIL_START_SYMBOL];
                $.each(temp, function (index, val) {
                    if ($.inArray(val, result.email) === -1)
                        emailError.find('#' + val).hide(100);
                    else {
                        emailError.find('#' + val).show(100);
                    }
                });
            }
            return result;
        }

        function send() {
            var enable = validateInputs(true, true);

            if (enable.email === true && enable.phone === true) {
                var email = $("input[name=email]");
                var phone = $("input[name=phone]");
                var name = $("input[name=name]");
                $('#loading').addClass('active');
                var data = new FormData();
                data.append("email", email.val());
                data.append("phone", phone.val());
                data.append("name", name.val());
                var temp = [];
                $("#basket-items").find('.basket-item').each(function (index, item) {
                    temp.push([
                        $(item).attr("id") + ":" + $(item).find("input[name=count]").val()
                    ]);
                });
                data.append("list", temp.join(','));
                $.ajax({
                    url: "/system/add-order.php",
                    type: 'post',
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: data,
                    success: function (d) {
                        $('#loading').removeClass('active');
                        if (!('error' in d)) {
                            Message.create({
                                header: "Спасибо!",
                                text: "Ваш заказ был успешно добавлен"
                            })
                        } else {
                            Message.create({header: "Ошибка", text: d.error})
                        }
                    },
                    error: function () {
                        $('#loading').removeClass('active');
                        Message.create({
                            header: "Ошибка",
                            text: "Фатальная ошибка. Не удалось обработать ответ сервера."
                        })
                    }
                });
            }
        }

        /**
         * Создать элемент корзины
         * @param {object} params
         */
        function createBasketItem(params) {
            var root = $("<div class='basket-item flex-block flex-between flex-stretch flex-row' price='" + params.price + "' id='" + params.id + "'>");

            var image;
            if (params.images.length > 0) {
                var imagesList = params.images.split(';');
                for (let i = 0; i < imagesList.length; i++) {
                    imagesList[i] = "/images/shop/small/" + imagesList[i];
                }
                var imageChanger = new ImageChanger(imagesList);
                image = imageChanger.getView();
                $(image).hover(function () {
                    imageChanger.start();
                }, function () {
                    imageChanger.stop();
                })
            } else {
                image = $("<img src=\"/images/image-close.png\" class=\"basket-item-image\">");
            }

            root.append(
                $("<a style='text-decoration: none;' href='/shop/" + params.id + "' class='flex-block flex-row flex-middle flex-left'>\n    <h3 class=\'basket-item-name\'>" + params.name + "</h3>\n    <p class=\'text-hint\'></p>\n</a>")
                    .prepend($("<div class='basket-item-pre-image'>").append(image))
            );
            root.append(
                $("<div class=\'input-block\'>\n    <span class=\'input-header input-block-item\'>Количество</span>\n</div>")
                    .append(
                        new NumberInput("count",
                            params.count,
                            function () {
                                updateTotalPrice();
                            }
                        ).addClass('input-block-item'))
                    .append("<p class='basket-item-price'>" + params.price + "</p>")
            );
            return root;
        }
    </script>
</div>