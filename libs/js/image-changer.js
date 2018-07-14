/**
 * Простейший листатель картинок
 * @param {array} links Массив ссылок на картинки
 * @constructor
 */
function ImageChanger(links) {
    if (links.length === 0) {
        links.push("/images/image-close.png");
    }
    var image = $("<img src='" + links[0] + "'>");

    var enabled = false;
    var index = 0;
    var timing = 2000;
    var timeout = null;

    function next() {
        index++;
        if (index >= links.length) {
            index = 0;
        }
        image.attr('src', links[index]);
        if (enabled) {
            timeout = setTimeout(next, timing);
        }
    }

    /**
     * Начать листание картинок
     */
    this.start = function () {
        enabled = true;
        if (timeout !== null) {
            clearTimeout(timeout);
        }
        timeout = setTimeout(next, timing / 2);
    };

    /**
     * Остановить листание картинок
     */
    this.stop = function () {
        enabled = false;
        if (timeout !== null) {
            clearTimeout(timeout);
        }
        timeout = null;
    };

    /**
     * Получить объект картинки, на которой меняются изображения
     * @returns {HTMLImageElement}
     */
    this.getView = function () {
        return image[0];
    }
}