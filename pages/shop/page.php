<?php

if ( !defined( "ROOT" ) ) define( "ROOT", $_SERVER[ 'DOCUMENT_ROOT' ] );

require_once ROOT . "/class/php/categories.php";

?>

<div class="pre-content">
    <script type="text/javascript">
        var INITIAL = {
            pagesCount: 10
        }
    </script>
    <div class="limit-block">
        <div class="content-indent">
            <h2>Магазин</h2>
            <Divider></Divider>
            <div class="flex-block flex-row flex-top flex-left">
                <div class="left-menu-container">
                    <div class="merge-input-border-radiuses-vertical">
                        <?php
                        $link = preg_split(
                            "/([\\\\\/])/",
                            trim(preg_split( "/(\?)/", $_SERVER[ 'REQUEST_URI' ] )[ 0 ], "\\\/")
                        );
                        $last = $link[ count( $link ) - 1 ];
                        $categories = new Categories();

                        if (!is_numeric($last)) {
                            $last = $categories->validate($last);
                        }

                        $all = $categories->getCategories();
                        foreach ($all as $category) {
                            $nameEn = $category['english'];
                            $name = $category['name'];
                            if ($nameEn === $last) {
                                echo "<a href=\"/shop/$nameEn\" class=\"input input-menu-item active\">$name</a>";
                            } else {
                                echo "<a href=\"/shop/$nameEn\" class=\"input input-menu-item\">$name</a>";
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="full-width">
                    <?php
                    if ( is_numeric( $last ) ) {
                        include( 'product.php' );
                    } else {
                        include( 'list.php' );
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>