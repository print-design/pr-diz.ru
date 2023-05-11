<div class="text-nowrap nav2">
    <?php
    $laminate_header = '';
    $sql = "select id, name from laminator order by id";
    $fetcher = new Fetcher($sql);
    while($row = $fetcher->Fetch()):
        $laminator_class = filter_input(INPUT_GET, 'id') == $row['id'] ? ' active' : '';
    if(filter_input(INPUT_GET, 'id') == $row['id']) {
        $laminate_header = $row['name'];
    }
    ?>
    <a href="<?= BuildQuery('id', $row['id']) ?>" class="mr-4<?=$laminator_class ?>"><?=$row['name'] ?></a>
    <?php endwhile; ?>
</div>
<hr />