<script type="text/javascript">

    const TYPE_UNCHECKED = 1;
    const TYPE_CHECKED = 2;
    const TYPE_DELETED = 3;

    $(document).ready(function () {
        $('select').niceSelect();
        $("#form-search").submit(function() {
            updateOrders();
            return false;
        });
        updateOrders();
    });

    function notifySelectedChanged() {
        var count = $(".selected-order").length;
        if (count > 0) {
            $("#edit-order-menu").addClass('active');
        } else {
            $("#edit-order-menu").removeClass('active');
        }
        console.log("SELECTED COUNT IS " + count);
    }

    function clearSelected() {
        $(".selected-order").removeClass("selected-order");
        notifySelectedChanged();
    }

    function setTypeToSelected(type) {
        var data = new FormData();
        var line = [];
        var selected = $('.selected-order');
        selected.each(function(index, item) {
            line.push($(item).find(".order-id").html());
        });
        data.append("id", line.join(" "));
        data.append("checked", type.toString());
        $.ajax({
            url: "/system/admin/orders.php?type=update",
            type: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            data: data,
            success: function (d) {
                if (!("error" in d)) {
                    updateOrders();
                } else {
                    Message.create({header: "Ошибка при попытке изменения данных", text: d.error});
                }
            },
            error: function () {
                Message.create({header: "Ошибка", text: "Ошибка при попытке изменения данных"});
            }
        });
    }

    function updateOrders() {
        var data = new FormData();
        $.each($("#form-search").serializeArray(), function(index, item) {
            switch (item.name) {
                case "checked":
                    if (item.value != 0) {
                        data.append(item.name, item.value);
                    }
                    break;
            }
        });
        $.ajax({
            url: "/system/admin/orders.php?type=get",
            type: "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            data: data,
            success: function (d) {
                if (!("error" in d)) {
                    var ordersBlock = $("#orders");
                    ordersBlock.empty();
                    for (let i = 0; i < d.orders.length; i++) {
                        ordersBlock.append(createOrder(d.orders[i]));
                    }
                    notifySelectedChanged();
                } else {
                    Message.create({header: "Ошибка при обновлении списка заказов", text: d.error});
                }
            },
            error: function () {
                Message.create({header: "Ошибка", text: "Ошибка при обновлении списка заказов"});
            }
        });

        function createOrder(order) {
            var image;
            switch (order.checked) {
                case 1:
                    image = "/images/not-checked.png";
                    break;
                case 2:
                    image = "/images/checked.png";
                    break;
                case 3:
                    image = "/images/deleted.png";
                    break;
                default:
                    image = "/images/question-green.png";
            }
            let root = $("<div class=\'flex-block flex-row flex-middle flex-left card\'>\n    <span class=\'order-id\'>" + order.id + "</span>\n    <div style=\'flex-basis: 50%;\'>\n        <p class=\'order-info-text\'><b>Имя: </b>" + order.name + "</p>\n        <p class=\'order-info-text\'><b>Email: </b>" + order.email + "</p>\n        <p class=\'order-info-text\'><b>Телефон: </b>" + order.phone + "</p>        \n    </div>\n    <div style=\'flex-basis: 50%;\'>\n        <p class=\'order-info-text\'><b>Цена: </b>" + order.price + "</p>\n        <p class=\'order-info-text\'><b>Дата: </b>"+ order.date +"</p>\n        <p class=\'order-info-text\'><a href=\'/files/orders/" + order.id + ".docx\'>Скачать подробную информацию</a></p>\n    </div>\n    <img src=\'" + image + "\' class=\'image-order-status\'>\n</div>");
            root.click(function() {
                $(this).toggleClass("selected-order");
                notifySelectedChanged();
            });
            return root;
        }
    }
</script>

<div class="edit-order-menu" id="edit-order-menu">
    <img class="menu-item" src="/images/deleted.png" onclick="setTypeToSelected(TYPE_DELETED);">
    <img class="menu-item" src="/images/not-checked.png" onclick="setTypeToSelected(TYPE_UNCHECKED);">
    <img class="menu-item" src="/images/checked.png" onclick="setTypeToSelected(TYPE_CHECKED);">
    <img class="menu-item" src="/images/image-close.png" onclick="clearSelected();">
</div>

<div class="limit-block">
    <div class="content-indent">
        <div class="center">
            <form class="inline-block" id="form-search">
                <div class="flex-block flex-row flex-middle flex-center">
                    <select name="checked">
                        <option value="0">Все</option>
                        <option value="1">Непроверенные</option>
                        <option value="2">Проверенные</option>
                        <option value="3">Удаленные</option>
                    </select>
                    <input type="submit" value="Обновить" class="input input-submit">
                </div>
            </form>
        </div>
        <div id="orders"></div>
    </div>
</div>