SelectImage.INITIAL_PARAMS = {
    /**
     * Обязательный параметр: путь до директории на сервере, куда нужно сохранять файлы
     */
    dir: "",

    /**
     * Названия картинок (путь к ним будет строиться из значения dir)
     */
    images: []
};

SelectImage.IMAGE_ADD = "/images/image-add.png";

/**
 * Создать элемент выбора картинок.
 * @param userParams Параметры - возможные см. в SelectImage.INITIAL_PARAMS
 * @constructor
 */
function SelectImage(userParams) {
    var params = [];
    $.each(SelectImage.INITIAL_PARAMS, function (key, val) {
        params[key] = ((key in userParams) ? userParams[key] : val);
    });

    var message = new Message({
        header: "Выберите картинку",
        buttons: []
    });

    /**
     * Создать элемент выбора картинки (в окне выбора)
     * @param src Путь до картинки (от корня сайта)
     * @returns {jQuery|HTMLElement}
     */
    function createSelectImage(src) {
        var body = $("<div class='select-image-item'>");
        var img = $("<img class='passive'>");
        body.append(img);
        var image = new Image;
        image.onload = function () {
            img.attr("src", src);
            img.removeClass('passive');
        };
        image.src = src;
        return body;
    }

    function show(callback) {
        message.setContent($("<div class='center'><img src='/images/settings-wheel.png' style='width: 50px;' class='loading'></div>"));
        message.show();
        getImagesList(params.dir, function (d) {
            var selectedBlock = $("<div class=\'flex-block flex-row flex-left flex-top flex-wrap\'>");

            var addBlock = createSelectImage(SelectImage.IMAGE_ADD);
            var input = $("<input type='file' class='select-image-input' name='file' accept=\"image/*\">");
            input.click(function (e) {
                e.stopPropagation();
            });
            input.change(function () {
                var files = this.files;
                if (files.length > 0) {
                    loadImage(files[0], function (d) {
                        if (typeof(callback) === 'function') callback(d);
                        addSelectedImage(d);
                        close();
                    });
                }
            });
            addBlock.append(input);
            selectedBlock.append(addBlock);

            $.each(d, function (index, name) {
                var image = createSelectImage(params.dir + "/small/" + name);
                image.click(function () {
                    if (typeof(callback) === 'function') callback(name);
                    addSelectedImage(name);
                    close();
                });
                selectedBlock.append(image);
            });
            message.setContent(selectedBlock);
        });
    }

    function close() {
        message.hide();
    }

    /**
     * Получить список выбранных картинок
     * @returns {Array|HTMLCollectionOf<HTMLImageElement>}
     */
    this.getSelected = function() {
        return params.images;
    };

    /**
     * Установить новый список выбранных картинок
     * @param list Список в формате массива
     */
    this.setSelected = function(list) {
        params.images = list;
        notifyImagesListChanged();
    }

    /**
     * Загрузить новую картинку на сервер
     * @param file Файл (элемент списка input.files)
     * @param callback Функция, которая будет вызвана при успешном окончании загрузки. В нее будет
     *     передано имя файла на сервере или null в случае ошибки
     */
    function loadImage(file, callback) {
        var data = new FormData();
        data.append("image", file);
        data.append('type', 'add');
        data.append('dir', params.dir);

        $.ajax({
            url: "/system/admin/image.php",
            dataType: 'json',
            type: 'post',
            processData: false,
            contentType: false,
            data: data,
            success: function (d) {
                if ('error' in d) {
                    Message.create({header: "Ошибка", text: d.error});
                    if (typeof(callback) === 'function') callback(null);
                } else {
                    if (typeof(callback) === 'function') callback(d.files[0]);
                }
            },
            error: function () {
                Message.create({
                    header: "Ошибка",
                    text: "Фатальная ошибка. Не удалось загрузить файл на сервер."
                });
                if (typeof(callback) === 'function') callback(null);
            }
        });
    }

    /**
     * Получить список картинок на сервере
     * @param dir Директория, из которой нужно получить изображения
     * @param callback Функция, в которую будет передан массив названий картинок при успшном
     *     завершении или null в случае ошибки
     */
    function getImagesList(dir, callback) {
        $.ajax({
            url: "/system/admin/image.php",
            type: 'post',
            dataType: 'json',
            data: {type: 'get', dir: dir},
            success: function (d) {
                if ('error' in d) {
                    Message.create({header: "Ошибка", text: d.error});
                    if (typeof(callback) === 'function') callback(null);
                } else {
                    if (typeof(callback === 'function')) callback(d.images);
                }
            },
            error: function () {
                Message.create({
                    header: "Ошибка",
                    text: "Фатальная ошибка. Не удалось обработать ответ сервера."
                });
                if (typeof(callback) === 'function') callback(null);
            }
        });
    }

    /**
     * Список слушаьелей изменения списка картинок
     * @type {Array}
     */
    var imagesListChangedCallbacks = [];

    /**
     * Добавить слушатель изменения списка картинок
     * @param callback
     */
    function addImagesListChangedListener(callback) {
        imagesListChangedCallbacks.push(callback);
    }

    /**
     * Уведомить, что список выбранных картинок был изменен
     */
    function notifyImagesListChanged() {
        $.each(imagesListChangedCallbacks, function (index, item) {
            item();
        });
    }

    /**
     * Удалить картинку из списка выюранных
     * @param name
     */
    function removeSelectedImage(name) {
        removeFromArray(params.images, name);
        notifyImagesListChanged();
    }

    /**
     * Добавить картинку в список выбранного
     * @param src
     */
    function addSelectedImage(src) {
        for (var i = 0; i < params.images.length; i++) {
            if (src === params.images[i]) {
                return;
            }
        }
        params.images.push(src);
        notifyImagesListChanged();
    }

    /**
     * Открыть окно выбора
     * @param callback
     */
    this.show = function (callback) {
        show(callback);
    };

    /**
     * Закрыть окно выбора
     */
    this.close = function () {
        close();
    };

    /**
     * Создать виджет. Он будет синхронизироваться с SelectImage, из которого был создан
     * @returns {jQuery|HTMLElement}
     */
    this.createWidget = function () {
        var root = $("<div class='flex-block flex-row flex-wrap flex-top flex-left'>");

        function update() {
            root.empty();
            $.each(params.images, function (index, name) {
                let block = createSelectedBlock(params.dir + "/" + name);
                block.click(function () {
                    removeSelectedImage(name);
                });
                root.append(block);
            });

            var addBlock = createSelectedBlock("/images/image-add.png");
            addBlock.find('img.close').remove();
            addBlock.click(function () {
                show();
            });
            root.append(addBlock);
        }

        addImagesListChangedListener(function () {
            update();
        });

        update();

        return root;

        function createSelectedBlock(src) {
            var body = $("<div class='selected-image-block'>");
            var img = $("<img class='passive'>");
            body.append(img);
            var image = new Image;
            image.onload = function () {
                img.attr("src", src);
                img.removeClass('passive');
            };
            image.src = src;
            var closeImage = $("<img src='/images/image-close.png' class='close'>");
            body.append(closeImage);
            return body;
        }
    };

    function removeFromArray(arr) {
        var what, a = arguments, L = a.length, ax;
        while (L > 1 && arr.length) {
            what = a[--L];
            while ((ax = arr.indexOf(what)) !== -1) {
                arr.splice(ax, 1);
            }
        }
        return arr;
    }
}

$('head').append("<link type='text/css' rel=stylesheet href='/style/components/select-image.css?time=1'>");