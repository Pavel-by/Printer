/**
 * Карточка какого-либо продукта, "выпадает" сверху на весь экрын
 * @param {object} params Параметры. Список возможных параметров см. в комментарии к
 * ProductCard.params
 * @constructor
 */
function ProductCard(userParams) {
    this.drawn = false;
    this.shown = false;

    this.root = $("<div class='product-card-root'>");

    /**
     * Параметры краточки
     */
    params = {
        /**
         * Название товара
         */
        name: "",

        /**
         * Картинка
         */
        image: false,

        /**
         * Цена товара
         */
        price: false,

        /**
         * Можно ли добавлять в корзину. Если можно, то сюда ставится функция
         */
        basket: false,

        /**
         * Описание товара
         */
        description: false,

        /**
         * Характеристики товара
         *  [
         *      {
         *          name: "Название",
         *          value: "Значение хар-ки"
         *      }
         *  ]
         */
        features: false,

        /**
         * Заголовок перед таблицей характеристик
         */
        featuresHeader: "Характеристики"
    };

    var message = new Message({header: userParams.name, custom: $("<div>")});

    this.show = function () {
        this.draw();
        /*var self = this;
         this.draw();
         if (!this.shown){
         this.shown = true;
         $('body').append(this.root).ready(function() {
         self.root.addClass('active');
         });
         } else {
         self.root.addClass('active');
         }
         $('body').css("overflow", "hidden");*/
        message.show();
    };

    this.hide = function () {
        /*this.shown = false;
         this.root.remove();
         if ($(".product-card-root").length == 0) {
         $('body').css('overflow', 'auto');
         }*/
        message.hide();
    };

    this.setParams = function (newParams) {
        var self = this;
        $.each(newParams, function (k, v) {
            params[k] = v;
        });
        if (this.shown) {
            this.draw();
        }
        console.log(params);
        console.log("--------------------");
    };

    this.setParam = function (name, value) {
        params[name] = value;
        if (this.shown) {
            this.draw();
        }
    };

    this.draw = function () {
        var self = this;

        var root = getBase();

        var topBlock = $("<div class='product-card-top'>");

        if (params.image) {
            var image = getImageBase();
            $(image.image).attr('src', params.image);
            topBlock.append(image.root);
        }

        var basicInfo = getBasicInfoBase();
        topBlock.append(basicInfo.root);

        if (params.price !== false) {
            $(basicInfo.price).html(params.price);
            $(basicInfo.price).addClass('active');
        }
        if (params.basket !== false) {
            $(basicInfo.toBasket).addClass('active');
        }
        root.content.append(topBlock);

        if (params.description !== false) {
            root.content.append(getDescription(params.description));
        }

        if (params.features !== false) {
            root.content.append(getFeatures(params.features));
        }

        this.root.empty();
        this.root.append(root.root);
        this.content = root.content;
        message.setContent(this.content);
        console.log(root.content);

        this.drawn = true;

        /**
         * Возвращает необходимые элементы, образующие каркас элемента
         */
        function getBase() {
            var root = $("<div class='full-width'></div>");
            var content = $("<div class='limit-block product-card-content'></div>");
            //root.append(content);
            return {
                root: root,
                content: content
            }
        }

        function getImageBase() {
            var root = $("<div></div>");
            var image = $("<img class='product-card-image'>");
            root.append(image);
            return {
                root: root,
                image: image
            }
        }

        function getBasicInfoBase() {
            var root = $("<div></div>");
            var price = $("<span class='product-card-price'>");
            var toBasket = $("<span class='product-card-to-basket'>В корзину</span>");
            root.append($("<div>").append(price))
                .append($("<div>").append(toBasket));
            return {
                root: root,
                price: price,
                toBasket: toBasket
            }
        }

        function getDescription(descriptionString) {
            if (typeof(descriptionString) !== "undefined" && descriptionString !== false && descriptionString.length > 0) {
                return $("<div><h3>Описание</h3><p>" + descriptionString + "</p></div>");
            }
            return $("<div>");
        }

        function getFeatures(params) {
            var root = $("<div>");
            if (typeof(params.featuresHeader) !== "undefined" && params.featuresHeader !== false) {
                root.append($("<h3>" + params.featuresHeader + "</h3>"));
            }
            var table = $("<table class='product-card-features'>");
            root.append(table);
            $.each(params, function (index, elem) {
                console.log(elem);
                table.append(
                    $(
                        "<tr>" +
                        "<td>" + elem.name + "</td>" +
                        "<td>" + elem.value + "</td>" +
                        "</tr>"
                    )
                )
            });
            return root;
        }
    };

    this.setParams(userParams);
}