<div id="left_bar">
    <a href="<?=APPLICATION ?>/" class="left_bar_item logo" title="На главную" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/logo.svg" /></a>
    <?php if(LoggedIn()): ?>
    <a href="javascript: void(0);" class="left_bar_item"><img src="<?=APPLICATION ?>/images/nav_clock.svg" /></a>
    <a href="<?=APPLICATION ?>/pallet/" class="left_bar_item" title="Склад" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_sklad.svg" /></a>
    <a href="<?=APPLICATION ?>/grafik/comiflex.php" class="left_bar_item" title="График" data-toggle="tooltip" data-placement="right"><img src="<?=APPLICATION ?>/images/nav_grafik.svg" /></a>
    <?php endif; ?>
</div>