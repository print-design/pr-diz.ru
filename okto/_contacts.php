<?php
include '../include/topscripts.php';

$me = GetUserId();
$sql = "select u.id, u.last_name, u.first_name, "
        . "(select count(id) from dialog where user_id_from = u.id and user_id_to = $me and viewed = 0) unviewed "
        . "from user u where u.active = 1 and u.id <> $me "
        . "order by u.last_name, u.first_name";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()):
?>
<button type="button" class="btn btn-light w-100 mt-1 mb-1 btn_contact" style="text-align: left;" data-id="<?=$row['id'] ?>">
    <div class="d-flex justify-content-between">
        <div><?=$row['last_name'].' '.$row['first_name'] ?></div>
        <div><?=($row['unviewed'] > 0 ? $row['unviewed'] : '') ?></div>
    </div>
</button>
<?php endwhile; ?>