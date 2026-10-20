<script>
    // Параметры заказа для проверки рулонов (используются в js/cut_validate.js)
    var CUT_PARAMS = {
        spool: <?= (float)$calculation_result->spool ?>,
        thickness_1: <?= (float)$calculation->thickness_1 ?>,
        thickness_2: <?= (float)$calculation->thickness_2 ?>,
        thickness_3: <?= (float)$calculation->thickness_3 ?>
    };
</script>
<script src="<?=APPLICATION ?>/js/cut_validate.js?v=<?=filemtime(__DIR__.'/../js/cut_validate.js') ?>"></script>
