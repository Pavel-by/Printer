<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/cartridges.php";

$cartridges = new Cartridges();

$cartridgesList = $cartridges->getCartridges();

?>

<script type="text/javascript">
    var cartridges = $.parseJSON("<?php echo addslashes( json_encode( $cartridges->getCartridges() ) ); ?>");

    function openDetailBlock(elem) {
        var valute = " Р";
        console.log(elem.refillPrice);
        var featuresNotChecked = [
            {
                name: "Принтер",
                value: elem.printerName
            },
            {
                name: "Картридж",
                value: elem.cartridgeName
            },
            {
                name: "Заправка",
                value: elem.refillPrice.toString() + valute
            },
            {
                name: "Фоторецептор",
                value: elem.photoreceptorPrice.toString() + valute
            },
            {
                name: "Ракель",
                value: elem.rakelPrice.toString() + valute
            },
            {
                name: "Вал первичного заряда",
                value: elem.PCRPrice.toString() + valute
            },
            {
                name: "Оболочка магнитного вала",
                value: elem.shellPrice.toString() + valute
            },
            {
                name: "Дозирующее лезвие магнитного вала",
                value: elem.bladePrice.toString() + valute
            },
            {
                name: "Бушинг магнитного вала",
                value: elem.bushingPrice.toString() + valute
            }
        ];
        var featuresChecked = [];

        $.each(featuresNotChecked, function (index, elem) {
            if (elem.value !== "-" + valute && elem.value !== "-" && elem.value.length > 0) {
                featuresChecked.push(elem);
            }
        });
        new ProductCard({
            name: elem.cartridgeName,
            features: featuresChecked,
            featuresHeader: "Цены"
        }).show();
        /*var n = $('#detailBlock');
         n.find('img').attr('src', $(elem).find('img').attr('src'));
         n.find('.printerName').html($(elem).find('.printerName').html());
         n.find('.cartridgeName').html($(elem).find('.cartridgeName').html());
         n.find('.refillPrice').html($(elem).find('.refillPrice').html());
         n.find('.photoreceptorPrice').html($(elem).find('.photoreceptorPrice').html());
         n.find('.rakelPrice').html($(elem).find('.rakelPrice').html());
         n.find('.PCRPrice').html($(elem).find('.PCRPrice').html());
         n.find('.shellPrice').html($(elem).find('.shellPrice').html());
         n.find('.bladePrice').html($(elem).find('.bladePrice').html());
         n.find('.bushingPrice').html($(elem).find('.bushingPrice').html());
         n.addClass('active');*/
    }

    $(document).ready(function () {
        init();

        /*$('body').on('click', '.cartridge', function(){
         openDetailBlock($(this));
         });*/
        $('#blockDiv').click(function () {
            $('#detailBlock').removeClass('active');
        });

        $('.input-button').click(function () {
            updateSearch();
        });

        $('.input-text').keyup(function () {
            updateSearch();
        });
    });

    function updateSearch() {
        var one = $('#printerName');
        var val1 = one.val().toLowerCase();

        var two = $('#cartridgeName');
        var val2 = two.val().toLowerCase();
        console.log(val1);
        $('.cartridge').removeClass('hidden');
        $('#cartridges').find('.printerName').each(function (ind, elem) {
            elem = $(elem);
            if (elem.text().toLowerCase().indexOf(val1) === -1) {
                elem.closest('.cartridge').addClass('hidden');
            }
        });

        $('#cartridges').find('.cartridgeName').each(function (ind, elem) {
            if ($(elem).text().toLowerCase().indexOf(val2) == -1) {
                $(elem).closest('.cartridge').addClass('hidden');
            }
        });

        if ($(".cartridge").not(".hidden").length === 0) {
            $("#empty-search").removeClass("hidden");
        } else {
            $("#empty-search").addClass("hidden");
        }
    }

    function init() {
        var root = $("#cartridges");
        $.each(cartridges, function (index, item) {
            var cartridge = $("<div class='cartridge flex-block flex-row flex-middle flex-left'>");
            var image;
            if (item.images.length > 0) {
                var images = item.images.split(";");
                for (let i = 0; i < images.length; i++) {
                    images[i] = "/images/fill/" + images[i];
                }
                var imageChanger = new ImageChanger(images);
                image = imageChanger.getView();
                cartridge.hover(function () {
                    imageChanger.start();
                }, function () {
                    imageChanger.stop();
                })
            } else {
                image = $("<img src='/images/image-close.png'>");
            }
            cartridge.append($("<div class='pre-image'>").append(image))
                .append(
                    $("<div>")
                        .append($('<p class="printerName">' + item['printerName'] + '</p>'))
                        .append($('<p><b>Картридж: </b><span class="cartridgeName">' + item['cartridgeName'] + '</span></p>'))
                );
            cartridge.click(function () {
                openDetailBlock(item);
            });
            root.append(cartridge);
        });
    }
</script>

<div>
    <div class="pre-content">
        <div class="limit-block">
            <div class="content-indent">
                <h2>Заправка картриджей</h2>
                <Divider></Divider>
                <div class="flex-block flex-row flex-around flex-middle flex-wrap">
                    <div class="flex-block flex-center flex-middle flex-row">
                        <div class="merge-input-border-radiuses">
                            <input type="text" placeholder="Название принтера" id="printerName"
                                   class="input input-text">
                            <input type="text" placeholder="Название картриджа" id="cartridgeName"
                                   class="input input-text">
                            <input type="button" value="Найти" class="input input-button">
                        </div>
                    </div>
                </div>
                <Divider></Divider>

                <div id="cartridges" class="flex-stretch flex-block flex-row flex-wrap flex-center">
                    <p id="empty-search" class="hidden center text-hint">Не найдено ни одного картриджа</p>
                    <?php

                    /*foreach ( $cartridgesList as $rez ) {
                        $s = '<div class="cartridge">';
                        $s .= '<img src="'.$rez['image'].'">';
                        $s .= '<p class="printerName">' . $rez[ 'printerName' ] . '</p>';
                        $s .= '<p><b>Картридж: </b><span class="cartridgeName">' . $rez[ 'cartridgeName' ] . '</span></p>';
                        $s .= '<label class="hidden refillPrice">' . $rez[ 'refillPrice' ] . '</label>';
                        $s .= '<label class="hidden photoreceptorPrice">' . $rez[ 'photoreceptorPrice' ] . '</label>';
                        $s .= '<label class="hidden rakelPrice">' . $rez[ 'rakelPrice' ] . '</label>';
                        $s .= '<label class="hidden PCRPrice">' . $rez[ 'PCRPrice' ] . '</label>';
                        $s .= '<label class="hidden shellPrice">' . $rez[ 'shellPrice' ] . '</label>';
                        $s .= '<label class="hidden bladePrice">' . $rez[ 'bladePrice' ] . '</label>';
                        $s .= '<label class="hidden bushingPrice">' . $rez[ 'bushingPrice' ] . '</label>';
                        $s .= '<label class="hidden photoreceptorPrice">' . $rez[ 'photoreceptorPrice' ] . '</label>';
                        $s .= '<label class="hidden photoreceptorPrice">' . $rez[ 'photoreceptorPrice' ] . '</label>';
                        $s .= '<label class="hidden photoreceptorPrice">' . $rez[ 'photoreceptorPrice' ] . '</label>';
                        $s .= '</div>';
                        echo $s;
                    }*/
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>