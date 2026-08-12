<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if(!empty($id)):
$sql = "select c.date, c.name, c.person, c.phone, c.extension, c.email, u.id user_id, u.last_name, u.first_name "
        . "from customer c "
        . "inner join user u on c.manager_id = u.id "
        . "where c.id = ?";
$fetcher = new Fetcher($sql, [$id]);
if($row = $fetcher->Fetch()):
?>
<h2><?=$row['name'] ?></h2>
<table class="w-100 mt-3">
    <tr>
        <td class="pb-3">Дата регистрации:</td>
        <td class="pb-3"><?=DateTime::createFromFormat('Y-m-d H:i:s', $row['date'])->format('d.m.Y') ?></td>
    </tr>
    <tr>
        <td class="pb-3">Имя представителя:</td>
        <td class="pb-3"><?=$row['person'] ?></td>
    </tr>
    <tr>
        <td class="pb-3">Номер телефона:</td>
        <td class="pb-3"><?=$row['phone'].(empty($row['extension']) ? "" : " (доп. ".$row['extension'].")") ?>
    </tr>
    <tr>
        <td class="pb-3">E-mail:</td>
        <td class="pb-3"><?=$row['email'] ?></td>
    </tr>
    <tr>
        <td class="pb-3">Менеджер:</td>
        <td class="pb-3"><?=$row['last_name'].' '.$row['first_name'] ?></td>
    </tr>
</table>
<?php
endif;
endif;
?>
<button type="button" class="close" data-dismiss='modal' style="position: absolute; right: 34px; top: 34px; z-index: 2000;"><img src="../images/icons/close_modal_red.svg" /></button>