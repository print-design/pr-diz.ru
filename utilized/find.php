<?php
if(LoggedIn()):
$find_class = " d-none";
$group_class = "";
$append_class = "";
$string_class = " d-none";
$placeholder = "Поиск по складу";
if(filter_input(INPUT_GET, "find") != '') {
    $find_class = " w-100";
    $group_class = " w-100";
    $append_class = " d-none";
    $string_class = "";
    $placeholder = "";
}
?>
<button type="button" class="btn btn-link nav-link ml-auto<?=$append_class ?>" id="find-append" style="border-top-right-radius: 5px; border-bottom-right-radius: 5px; color: black;"><i class="fas fa-search"></i></button>
<form class="form-inline ml-auto mr-3<?=$find_class ?>" method="get" id="find-form" action="<?=APPLICATION.'/utilized/' ?>">
    <div class="input-group<?=$group_class ?>" id="find-group">
        <input type="text" class="form-control no-latin" id="find" name="find" placeholder="<?=$placeholder ?>" />
        <div class="input-group-append">
            <button type="submit" class="btn btn-outline-dark form-control" id="find-submit" style="border-top-right-radius: 5px; border-bottom-right-radius: 5px;">Найти</button>
        </div>
        <div class="position-absolute px-2 align-text-bottom <?=$string_class ?>" style="top: 3px; left: 5px; bottom: 3px; background-color: gray; color: white; border-radius: 4px; padding-top: .4rem;">
        <?= filter_input(INPUT_GET, "find") ?>
            &nbsp;&nbsp;
            <a href="<?=APPLICATION.'/utilized/' ?>"><i class="fas fa-times" style="color: white;"></i></a>
        </div>
    </div>
</form>
<?php
else:
    echo "<div class='ml-auto'></div>";
endif;
?>