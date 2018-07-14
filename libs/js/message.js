Message.STYLE_SIMPLE = 1;
Message.STYLE_BRIGHT = 2;

Message.create = function(params) {
    (new Message(params)).show();
};

function Message(userParams) {
    var params = {
        header: "Сообщение",
        text: "",
        custom: null,
        buttons: [
            {
                val: "Закрыть",
                click: function() {
                    hide();
                },
                style: Message.STYLE_BRIGHT
            }
        ]
    };
    $.each(userParams, function(key, val) {
         params[key] = val;
    });

    var elementRoot = $("<div class='message-root'>")
        .on("click", function() {
            if ($(event.target).hasClass("message-root")) hide();
        });
    var elementBody = $("<div class='message-body'>");
        /*.click(function(e) {
            e.stopPropagation();
        });*/
    var elementHeader = $("<p class='message-header'>" + params.header + "</p>");
    var elementContent = $("<div class='message-content'>");
    var elementButtonsContainer = $("<div class='flex-block flex-row flex-right'>");
    elementRoot.append(elementBody);
    elementBody.append(elementHeader)
        .append(elementContent)
        .append(elementButtonsContainer);
    var elem;
    for (let i = 0; i < params.buttons.length; i++) {
        elem = params.buttons[i];
        if ('style' in elem && elem.style === Message.STYLE_BRIGHT) {
            var button = $("<input type='button' class='input input-submit' value='" + elem.val + "'>");
        } else {
            var button = $("<input type='button' class='input input-button' value='" + elem.val + "'>");
        }
        button.click(elem.click);
        elementButtonsContainer.append(button);
    }
    if (params.custom !== null) {
        if (typeof(params.custom) === "string") {
            elementContent.html("<p>" + params.custom + "</p>");
        } else {
            elementContent.append($(params.custom));
        }
    } else {
        elementContent.append($("<p>" + params.text + "</p>"));
    }

    this.setContent = function(content) {
        setContent(content);
    };

    this.setHeader = function(header) {
        setHeader(header);
    };

    this.show = function() {
        show()
    };

    this.hide = function() {
        hide();
    };

    function setContent(content) {
        elementContent.empty();
        if (typeof(content) === "string") {
            elementContent.html(content);
        } else {
            elementContent.append($(content));
        }
    }
    function setHeader(header) {
        elementHeader.empty();
        if (typeof(content) === "string") {
            elementHeader.html(header);
        } else {
            elementHeader.append($(header));
        }
    }
    function show() {
        let body = $("body");
        body.append(elementRoot);
        body.ready(function() {
            elementRoot.addClass("active");
            if (params.buttons.length === 1) {
                button.focus();
            }
        });
        body.css('overflow', 'hidden');
    }
    function hide() {
        elementRoot.remove();
        if ($('body').find('.message-root.active').length === 0) {
            $('body').css('overflow', 'auto');
        }
    }
}

$("head").append("<link type='text/css' rel='stylesheet' href='/style/components/message.css'>");