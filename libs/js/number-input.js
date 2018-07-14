/**
 * Числовое поле ввода со стрелочками "+" и "-"
 * @param name Имя поля
 * @constructor
 */
function NumberInput(name, init, onChange) {
    if (!init || typeof(init) === undefined) {
        init = "0";
    } else {
        init = validateInt(init);
    }
    var root = $('<div class="merge-input-border-radiuses">');
    var left = $('<span class="input input-button">-</span>');
    var input = $('<input name="' + name + '" type="text" value="' + init + '" class="input input-text" style="box-sizing: content-box; width: 40px; padding-right: 0; padding-left: 0; text-align: center;">');
    var right = $('<span class="input input-button">+</span>');
    root.append(left);
    root.append(input);
    root.append(right);

    left.click(function () {
        let val = parseInt(validateInt(input.val())) - 1;
        input.val(validateInt(val));
        changed(val);
    });

    right.click(function () {
        let val = parseInt(validateInt(input.val())) + 1;
        input.val(validateInt(val));
        changed(val);
    });

    $(input).keyup(function (d) {
        var val = validateInt($(input).val());
        $(this).val(val);
        changed(val);
    });
    return root;

    function validateInt(string) {
        let val = string.toString().match(/([0-9])*/ig).join('');
        if (val.length === 0) {
            val = "0";
        }
        val = parseInt(val);

        if (val < 0) {
            val = 0;
        }
        if (val > 1000000) {
            val = 1000000;
        }
        return val;
    }

    function changed(val) {
        if (onChange && typeof(onChange) === 'function') {
            onChange(val);
        }
    }
}