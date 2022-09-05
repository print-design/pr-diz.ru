<?php
include '../include/topscripts.php';
include './database_chinese.php';

const FILENAME = "words.txt";

$sql = "select line, word, transcription, translation from words";
$grabber = new GrabberChinese($sql);
$error_message = $grabber->error;
$result = $grabber->result;
$words = array();

foreach ($result as $item) {
    $words[$item['line']] = array('word' => $item['word'], 'transcription' => $item['transcription'], 'translation' => $item['translation']);
}
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
        $file = fopen(FILENAME, 'r');
        while($line = fgets($file)):
        if(array_key_exists($line, $words)):
        ?>
        <div class="row">
            <div class="col-3"></div>
        </div>
        <?php
        else:
        ?>
        <form method="post">
            <input type="hidden" name="scroll" />
            <input type="hidden" name="line" value="<?=$line ?>" />
            <div class="row">
                <div class="col-3"><input type="text" class="form-control clickable" name="word" value="<?=$line ?>" /></div>
                <div class="col-3"><input type="text" class="form-control clickable" name="transcription" /></div>
                <div class="col-3"><input type="text" class="form-control clickable1" name="translation" /></div>
                <div class="col-1"><button type="submit" class="btn btn-dark" name="word_submit">Добавить слово</button></div>
                <div class="col-1"><button type="submit" class="btn btn-dark" name="group_submit">Добавить раздел</button></div>
            </div>
        </form>
        <?php
        endif;
        endwhile;
        fclose($file);
        ?>
    </body>
    <script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
    <script src='<?=APPLICATION ?>/js/bootstrap.min.js'></script>
    <script>
        $('.clickable').click(function() {
            var textbox = $(this);
            var text = $(this).val();
            var selStart = textbox.prop('selectionStart');
            var textStart = text.substring(0, selStart).trim();
            var textEnd = text.substring(selStart).trim();
            $(this).val(textStart);
            $(this).parent().next().find('input').val(textEnd + ' ' + $(this).parent().next().find('input').val());
        });
        
        $('.clickable1').click(function() {
            var textbox = $(this);
            var text = $(this).val();
            var selStart = textbox.prop('selectionStart');
            var textStart = text.substring(0, selStart).trim();
            var textEnd = text.substring(selStart).trim();
            $(this).parent().prev().find('input').val($(this).parent().prev().find('input').val() + ' ' + textStart);
            $(this).val(textEnd);
        });
        
        // Прокрутка на прежнее место после отправки формы
        $(window).on("scroll", function(){
            $('input[name="scroll"]').val($(window).scrollTop());
        });
    
        <?php if(!empty($_REQUEST['scroll'])): ?>
        window.scrollTo(0, <?php echo intval($_REQUEST['scroll']); ?>);
        <?php endif; ?>
    </script>
</html>