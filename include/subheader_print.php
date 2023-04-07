<div class="text-nowrap nav2">
    <?php
    $print_header = '';
    $machine = '';
    $sql = "select id, shortname, name from machine order by position";
    $fetcher = new Fetcher($sql);
    while($row = $fetcher->Fetch()):
        $machine_class = filter_input(INPUT_GET, 'id') == $row['id'] ? ' active' : '';
    if(filter_input(INPUT_GET, 'id') == $row['id']) {
        $print_header = $row['name'];
        $machine = $row['shortname'];
    }
    ?>
    <a href="<?= BuildQuery('id', $row['id']) ?>" class="mr-4<?=$machine_class ?>"><?=$row['name'] ?></a>
    <?php endwhile; ?>
</div>
<hr />