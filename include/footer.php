<div style="height: 50px;"></div>

<script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
<script src="<?=APPLICATION ?>/js/popper.min.js"></script>
<script src='<?=APPLICATION ?>/js/bootstrap.min.js'></script>
<script src="<?=APPLICATION ?>/js/jquery.maskedinput.min.js"></script>
<script src="<?=APPLICATION ?>/js/calculation.js?version=100"></script>

<?php
// Если в этом разделе есть find.php, то (помимо включения его в header.php)
// включаем также сюда footer_find.php
if(file_exists('find.php')) {
    include '../include/footer_find.php';
}
?>

<script src="<?=APPLICATION ?>/js/footer.js?v=<?=filemtime(__DIR__.'/../js/footer.js') ?>"></script>

<?php if(!empty($_REQUEST['scroll'])): ?>
<script>
    // Прокрутка на прежнее место после отправки формы
    window.scrollTo(0, <?= intval($_REQUEST['scroll']) ?>);
</script>
<?php endif; ?>
