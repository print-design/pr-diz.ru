<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');

if(!empty($id)):
$sql = "select c.name, c.person, c.phone, c.extension, c.email, u.last_name, u.first_name "
        . "from customer c "
        . "inner join user u on c.manager_id = u.id "
        . "where c.id = $id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()):
?>
<h2><?=$row['name'] ?></h2>
<table class="w-100 mt-3">
    <tr>
        <td class="pr-3 pb-3">Имя представителя:</td>
        <td class="pb-3"><?=$row['person'] ?></td>
    </tr>
    <tr>
        <td class="pr-3 pb-3">Номер телефона:</td>
        <td class="pb-3"><?=$row['phone'].(empty($row['extension']) ? "" : " (доп. ".$row['extension'].")") ?></td>
    </tr>
    <tr>
        <td class="pr-3 pb-3">E-mail:</td>
        <td class="pb-3"><?=$row['email'] ?></td>
    </tr>
    <tr>
        <td class="pr-3 pb-3">Менеджер:</td>
        <td class="pb-3"><?=$row['last_name'].' '.$row['first_name'] ?></td>
    </tr>
</table>
<?php
endif;
endif;
?>
<button type="button" class="close" data-dismiss='modal' style="position: absolute; right: 34px; top: 34px; z-index: 2000;"><img src="../images/icons/close_modal_red.svg" /></button>