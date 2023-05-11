<div class="text-nowrap nav2">
    <?php
    $laminate_header = '';
    $laminator_id = filter_input(INPUT_GET, 'id');
    
    foreach($laminators as $laminator):
        $laminator_class = $laminator_id == $laminator ? ' active' : '';
    ?>
    <a href="<?= BuildQuery('id', $laminator) ?>" class="mr-4<?=$laminator_class ?>"><?=$laminator_names[$laminator] ?></a>
    <?php endforeach; ?>
</div>
<hr />