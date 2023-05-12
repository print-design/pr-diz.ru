<div class="text-nowrap nav2">
    <?php
    $cutter_id = filter_input(INPUT_GET, 'id');
    $cut_header = '';
    if(array_key_exists($cutter_id, $cutter_names)) {
        $cut_header = "Резка &laquo;".$cutter_names[$cutter_id]."&raquo;";
    }
    else {
        $cut_header = "Резка $cutter_id";
    }
    
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