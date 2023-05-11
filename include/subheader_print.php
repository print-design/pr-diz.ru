<div class="text-nowrap nav2">
    <?php
    $print_header = '';
    $printer_id = filter_input(INPUT_GET, 'id');
    
    foreach($printers as $printer):
        $printer_class = $printer_id == $printer ? ' active' : '';
    ?>
    <a href="<?= BuildQuery('id', $printer) ?>" class="mr-4<?=$printer_class ?>"><?=$printer_names[$printer] ?></a>
    <?php endforeach; ?>
</div>
<hr />