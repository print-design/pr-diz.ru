<?php
include '../include/topscripts.php';

// Если не задано значение id, перенаправляем на список
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/pallet/');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <div style="margin-left: 30px;">
            <div style="margin-bottom: 20px; margin-top: 30px;">
                <a href="<?=APPLICATION ?>/pallet/pallet.php?id=<?=$id ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
            </div>
            <embed src="pdf.php?id=<?=$id ?>" type="application/pdf" width="100%" height="500px">
        </div>
    </body>
</html>