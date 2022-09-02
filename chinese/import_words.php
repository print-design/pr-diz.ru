<?php
include '../include/topscripts.php';

const FILENAME = "words.txt";
?>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <link href="<?=APPLICATION ?>/css/select2.min.css" rel="stylesheet"/>
    </head>
    <body>
        <h1>Импорт</h1>
        <?php
        $words = fopen(FILENAME, 'r');
        while($line = fgets($words)):
        ?>
        <form method="post">
            <input type="hidden" name="scroll" />
            <div class="row">
                <div class="col-3"><input type="text" class="form-control mr-5" name="word" value="<?=$line ?>" /></div>
                <div class="col-3"><input type="text" class="form-control" name="transcription" /></div>
                <div class="col-3"><input type="text" class="form-control" name="translation" /></div>
                <div class="col-1"><button type="submit" class="btn btn-dark" name="word_submit">Добавить слово</button></div>
                <div class="col-1"><button type="submit" class="btn btn-dark" name="group_submit">Добавить раздел</button></div>
            </div>
        </form>
        <?php
        endwhile;
        fclose($words);
        ?>
    </body>
    <script>
        // Прокрутка на прежнее место после отправки формы
        $(window).on("scroll", function(){
            $('input[name="scroll"]').val($(window).scrollTop());
        });
    
        <?php if(!empty($_REQUEST['scroll'])): ?>
        window.scrollTo(0, <?php echo intval($_REQUEST['scroll']); ?>);
        <?php endif; ?>
    </script>
</html>