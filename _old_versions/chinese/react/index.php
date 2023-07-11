<?php
include '../../include/topscripts.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            .container {
                font-size: x-large;
            }
        </style>
        <script>
            window.dataservice="<?=APPLICATION ?>/chinese/word.php";
        </script>
        <script defer="defer" src="main.dcfb7812.js"></script>
    </head>
    <body>
        <noscript>You need to enable JavaScript to run this app.</noscript>
        <div class="container" id="container"></div>
    </body>
</html>