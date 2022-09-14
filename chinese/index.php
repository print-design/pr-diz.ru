<?php
include '../include/topscripts.php';
include './database_chinese.php';
?>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            .container {
                font-size: x-large;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="text-center">
                <a class="btn btn-dark mt-2" href="./">Новое слово</a>
            </div>
            <hr />
            <?php
            $sql = "select word, transcription, translation from words order by rand() limit 1";
            $fetcher = new FetcherChinese($sql);
            if($row = $fetcher->Fetch()):
            ?>
            <h1><?=$row['word'] ?></h1>
            <div id="showtranscription" class="mt-4"><a class="btn btn-outline-dark" href="javascript:void(0);" id="btn_showtranscription">Показать транскрипцию</a></div>
            <div id="transcription" class="mt-4 d-none"><?=$row['transcription'] ?></div>
            <div id="showtranslation" class="mt-4"><a class="btn btn-outline-dark" href="javascript:void(0);" id="btn_showtranslation">Показать перевод</a></div>
            <div id="translation" class="mt-4 d-none"><?=$row['translation'] ?></div>
            <?php
            endif;
            ?>
        </div>
    </body>
    <script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
    <script src='<?=APPLICATION ?>/js/bootstrap.min.js'></script>
    <script>
        $('#btn_showtranscription').click(function() {
            $('#showtranscription').addClass('d-none');
            $('#transcription').removeClass('d-none');
        });
        
        $('#btn_showtranslation').click(function() {
            $('#showtranslation').addClass('d-none');
            $('#translation').removeClass('d-none');
        });
    </script>
</html>