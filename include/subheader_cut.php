<div class="text-nowrap nav2">
    <?php
    $cut_header = '';
    $cutter_id = filter_input(INPUT_GET, 'id');
    $cutter_name = '';
    
    foreach ($cutters as $cutter):
    if(array_key_exists($cutter, $cutter_names)) {
        $cutter_name = "Резка &laquo;".$cutter_names[$cutter]."&raquo;";
    }
    else {
        $cutter_name = "Резка $cutter";
    }
    
    $cutter_class = $cutter_id == $cutter ? ' active' : '';
    ?>
    <a href="<?= BuildQuery('id', $cutter) ?>" class="mr-4<?=$cutter_class ?>"><?=$cutter_name ?></a>
    <?php endforeach; ?>
</div>
<hr />