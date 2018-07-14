<form id="search" class="search-form" onsubmit="updateFromForm(); return false;">
    <div class="content-indent">
        <div class="flex-block flex-row flex-middle flex-left">
            <div class="merge-input-border-radiuses" style="margin: 15px; margin-top: 0;">
                <input type="text" name="name" class="input input-text" placeholder="Название">
            </div>

            <div class="merge-input-border-radiuses" style="margin: 15px; margin-top: 0;">
                <select class="input input-selector" id="search-order-column">
                    <option value="price" selected>Сортировать по цене</option>
                    <option value="name">Сортировать по названию</option>
                </select>
                <select class="input input-selector" id="search-order-type">
                    <option value="ASC" selected>по возрастанию</option>
                    <option value="DESC">по убыванию</option>
                </select>
            </div>
            <input type="submit" value="Найти" class="input input-submit" style="margin: 15px; margin-top: 0;">
        </div>
    </div>
    <input type="hidden" name="category" value="<?php echo $categories->getCategoryByEnglish($last)['id']; ?>">
    <input type="hidden" name="page" value="1">
</form>
<div id="pages-block"></div>

<script type="text/javascript">
    var pages;
    var searchForm;
    var pageSize = 16;
    var params = [];

    $(document).ready(function () {
        pages = new Pages();
        searchForm = $("#search");
        pages.setListener(function (p) {
            console.log(p.getType());
            if (p.getType() === Pages.TYPE_PAGES) {
                setPage(p.getPage());
            } else {
                setPage(0);
            }
        });
        $("#pages-block").append(pages.getRootLayout());
        $("select").niceSelect();
        initParams();
        update();
    });

    /**
     * Устанавливает, по сути, все: контент, количество страниц, текущую страницу
     * @param items
     */
    function setContent(items) {
        var content = $("<div class='flex-block flex-row flex-stretch flex-wrap flex-left full-width'>");
        $.each(items.items, function (index, item) {
            content.append(createCard(item));
        });
        pages.setContent(content);
        pages.setPagesCount((items.total - 1) / pageSize + 1);
        pages.setPage(params['page']);

        function createCard(item) {
            //var link = $("<a href='shop/" + item.id + "' class='inline-block'>");
            //var root = $("<div class='item' id='" + item.id + "'>");
            var root = $("<a href='/shop/" + item.id + "' class='item flex-block flex-row flex-bottom' id='" + item.id + "'>");
            var image;
            if (item.images.length > 0) {
                var images = item.images.split(';');
                for (let i = 0; i < images.length; i++) {
                    images[i] = "/images/shop/" + images[i];
                }
                var imageChanger = new ImageChanger(images);
                image = imageChanger.getView();
                root.hover(function () {
                    imageChanger.start();
                }, function () {
                    imageChanger.stop();
                })
            } else {
                image = $("<img src='/images/image-close.png'>")
            }
            var name = $("<p class='item-name'>" + item.name + "</p>");
            var price = $('<p class="item-price">' + item.price + ' Р</p>');
            var toBasket = $('<span class="input input-button full-width center">В корзину</span>');
            toBasket.click(function () {
                Basket.add($(this).parent().attr('id'), 1);
                return false;
            });
            root.append($("<div class='pre-image'>").append(image));
            root.append(name);
            root.append(price);
            root.append(toBasket);
            //link.append(root);
            return root;
        }
    }

    function setPage(index) {
        console.log('set page  ' + index);
        searchForm.find('[name=page]').val(index);
        params['page'] = index;
        update();
    }

    /**
     * Обновить данные: берет данные из формы (название товара). Устанавливается номер страницы: 1
     */
    function updateFromForm() {
        params['page'] = 1;
        params['name'] = searchForm.find('input[name=name]').val();
        update();
    }

    /**
     * Обновить текущую страницу с товарами
     * @param callback
     */
    function update(callback) {
        var data = new FormData();
        data.append('name', params['name']);
        if (params['type'] === Pages.TYPE_ALL) {
            params['page'] = 0;
        }
        data.append('page', params['page']);
        data.append('pageSize', pageSize.toString());
        data.append('category', params['category']);

        $.ajax({
            url: "/system/get-shop-items.php",
            dataType: 'json',
            data: data,
            type: 'post',
            processData: false,
            contentType: false,
            success: function (d) {
                if ('error' in d && d.error !== false) {
                    alert(d.error);
                } else {
                    setContent(d);
                }

                if (typeof(callback) === 'function') {
                    callback();
                }
            },
            error: function () {
                alert("Ошибка");
            }
        });
    }

    /**
     * Начальная инициализация параметров (берутся значения из формы)
     */
    function initParams() {
        searchForm.find('input').not('[type=button]').each(function (index, elem) {
            params[$(elem).attr('name')] = $(elem).val();
        });
    }
</script>