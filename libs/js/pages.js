function Pages(params) {
    Pages.TYPE_PAGES = 1;
    Pages.TYPE_ALL = 2;

    this.layout = {
        root: null,
        content: null,
        bottom: null
    };

    this.params = {
        /**
         * Общее количество страниц
         */
        pagesCount: 0,

        /**
         * Текущая страница
         */
        page: 0,

        /**
         * Максимальное количество кнопок (примерно)
         */
        maxButtonsInLine: 6,

        /**
         * Слушатель изменения чего-либо
         */
        change: function(){},

        /**
         * Текущий тип показа (постраничный показ/все сразу)
         */
        type: Pages.TYPE_PAGES
    };

    /**
     * Установить сразу несколько параметров
     * @param params Параметры
     */
    this.setParams = function (params) {
        if (params == null || params === undefined || typeof(params) === undefined) {
            return;
        }
        var self = this;
        $.each(params, function (k, v) {
            self.params[k] = v;
        });
        if (this.shown) {
            this.draw();
        }
    };

    /**
     * Установить общее количество страниц
     * @param count количество
     * @returns {Pages}
     */
    this.setPagesCount = function (count) {
        this.params.pagesCount = count;
        this.draw();
        return this;
    };

    /**
     * Установить номер текущей страницы
     * @param index номер страницы
     * @returns {Pages}
     */
    this.setPage = function(index) {
        this.params.page = parseInt(index);
        this.draw();
        return this;
    };

    /**
     * Установить содержимое страницы
     * @param content Содержимое страницы
     * @returns {Pages}
     */
    this.setContent = function (content) {
        this.layout.content.empty();
        this.layout.content.append($(content));
        return this;
    };

    /**
     * Установить слушатель изменения страницы, типа показа (постраничный показ/все сразу) и т.д.
     * @param listener слушатель
     */
    this.setListener = function(listener) {
        this.params.change = listener;
    };

    /**
     * PRIVATE
     * Вызывает слушатель
     */
    this.changed = function() {
        this.params.change(this);
    };

    /**
     * Получить корневой элемент
     * @returns {HTMLObjectElement}
     */
    this.getRootLayout = function() {
        return this.layout.root;
    };

    /**
     * Получить текущий тип показа (постраничный показ/все сразу)
     * @returns {number}
     */
    this.getType = function() {
        return this.params.type;
    };

    /**
     * Получить текущую страницу.
     * @returns {number}
     */
    this.getPage = function() {
        return this.params.page;
    };

    /**
     * PRIVATE
     * Отрисовать все, что необходимо
     */
    this.draw = function() {
        var self = this;
        var pagesCount = this.params.pagesCount;
        var page;
        var maxButtons = this.params.maxButtonsInLine;

        if (pagesCount < 1) return;
        if (this.params.page < 1) this.params.page = 1;
        if (this.params.page > pagesCount) this.params.page = pagesCount;
        page = this.params.page;

        this.layout.bottom.empty();
        if (parseInt(this.params.type) === Pages.TYPE_PAGES) {
            this.layout.bottom.append(createSelectButtons(page, pagesCount, maxButtons));
        }
        this.layout.bottom.append(createTypeSelector(this.params.type));

        $(bottom).find('select').niceSelect();

        //**********
        function createTypeSelector(type) {
            //var select = $("<select class='input input-selector'>");
            var select = $("<select>");
            var optionPages = $("<option value='" + Pages.TYPE_PAGES + "'>Показать постранично</option>");
            var optionAll = $("<option value='" + Pages.TYPE_ALL + "'>Показать все</option>");
            select.append(optionPages);
            select.append(optionAll);
            select.val(type);
            select.change(function() {
                self.params.type = parseInt($(this).val());
                self.draw();
                self.changed();
            });
            //select.niceSelect();
            return select;
        }

        function createSelectButtons(page, count, max) {
            console.log(page, count, max);
            var container = $("<div class='flex-block flex-row flex-middle flex-center merge-input-border-radiuses' style='margin-right: 20px;'>");

            for (let i = page - 1; i > 0 && i >= page - (max / 2); i--) {
                let listener = function () {
                    self.setPage(i);
                    self.changed();
                };
                container.prepend(createSelectButton(i, listener));
            }
            if (page - (max / 2) > 1) {
                container.prepend(createSelectButton("<<", function() {
                    self.setPage(1);
                    self.changed();
                }));
            }

            let current = createSelectButton(page, function(){
                self.setPage(page);
                self.changed();
            });
            current.addClass('active');
            container.append(current);
            console.log("+1");

            for (let i = page + 1; i <= count && i <= page + (max / 2); i++) {
                let listener = function () {
                    self.setPage(i);
                    self.changed();
                };
                container.append(createSelectButton(i, listener));
                console.log("+1");
            }

            if (page + (max / 2) < count) {
                container.append(createSelectButton(">>", function() {
                    self.setPage(count);
                    self.changed();
                }));
            }

            function createSelectButton(text, listener) {
                //let button = $("<span class='component-pages-select-button'>" + text + "</span>");
                let button = $("<input type='button' class='input input-button' value='" + text + "'>");
                button.click(listener);
                return button;
            }

            return container;
        }
    };

    //Инициализация

    let root = $("<div class='component-pages-root content-indent'>");
    let content = $("<div class='component-pages-content'>");
    let bottom = $("<div class='component-pages-bottom flex-block flex-left flex-middle flex-wrap flex-row'>");

    root.append(content);
    root.append(bottom);

    this.layout.root = root;
    this.layout.content = content;
    this.layout.bottom = bottom;

    this.draw();
}