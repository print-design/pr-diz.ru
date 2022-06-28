<?php
include '../include/topscripts.php';

$id = filter_input(INPUT_GET, 'id');

if(!empty($id)):
$sql = "select c.name, c.person, c.phone, c.extension, c.email, u.id user_id, u.last_name, u.first_name "
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
        <td class="pb-3">
            <div id="customer_card_person" class="d-flex justify-content-between">
                <div id="customer_card_person_value"><?=$row['person'] ?></div>
                <div><a href="javascript: void(0);" onclick="EditCustomerPerson();"><img src="../images/icons/edit1.svg" title="Редактировать" /></a></div>
            </div>
            <div id="customer_card_person_edit" class="d-none justify-content-between">
                <div><input type="text" class="form-control" id="customer_card_person_input" value="<?=$row['person'] ?>" /></div>
                <div>
                    <a class="btn btn-outline-dark" onclick="CancelCustomerPerson();" href="javascript: void(0);"><i class="fas fa-undo"></i></a>
                    <a class="btn btn-dark" href="javascript: void(0);">OK</a>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="pr-3 pb-3">Номер телефона:</td>
        <td class="pb-3">
            <div id="customer_card_phone" class="d-flex justify-content-between">
                <div id="customer_card_phone_value"><?=$row['phone'].(empty($row['extension']) ? "" : " (доп. ".$row['extension'].")") ?></div>
                <div><a href="javascript: void(0);" onclick="EditCustomerPhone();"><img src="../images/icons/edit1.svg" title="Редактировать" /></a></div>
            </div>
            <div id="customer_card_phone_edit" class="d-none justify-content-between">
                <div><input type="tel" class="form-control" id="customer_card_phone_input" value="<?=$row['phone'].(empty($row['extension']) ? "" : " (доп. ".$row['extension'].")") ?>" /></div>
                <div>
                    <a class="btn btn-outline-dark" onclick="CancelCustomerPhone()();" href="javascript: void(0);"><i class="fas fa-undo"></i></a>
                    <a class="btn btn-dark" href="javascript: void(0);">OK</a>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="pr-3 pb-3">E-mail:</td>
        <td class="pb-3">
            <div id="customer_card_email" class="d-flex justify-content-between">
                <div id="customer_card_email_value"><?=$row['email'] ?></div>
                <div><a href="javascript: void(0);" onclick="EditCustomerEmail();"><img src="../images/icons/edit1.svg" title="Редактировать" /></a></div>
            </div>
            <div id="customer_card_email_edit" class="d-none justify-content-between">
                <div><input type="text" class="form-control" id="customer_card_email_input" value="<?=$row['email'] ?>" /></div>
                <div>
                    <a class="btn btn-outline-dark" onclick="CancelCustomerEmail();" href="javascript: void(0);"><i class="fas fa-undo"></i></a>
                    <a class="btn btn-dark" href="javascript: void(0);">OK</a>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="pr-3 pb-3">Менеджер:</td>
        <td class="pb-3">
            <div id="customer_card_manager" class="d-flex justify-content-between">
                <div id="customer_card_manager_value" data-id='<?=$row['user_id'] ?>'><?=$row['last_name'].' '.$row['first_name'] ?></div>
                <div><a href="javascript: void(0);" onclick="EditCustomerManager();"><img src="../images/icons/edit1.svg" title="Редактировать" /></a></div>
            </div>
            <div id="customer_card_manager_edit" class="d-none justify-content-between">
                <div>
                    <select class="form-control" id="customer_card_manager_select">
                        <?php
                        $u_sql = "select u.id, u.last_name, u.first_name from user u inner join role r on u.role_id = r.id where r.name = 'manager' order by u.last_name, u.first_name";
                        $u_fetcher = new Fetcher($u_sql);
                        while ($u_row = $u_fetcher->Fetch()):
                            $manager_selected = $row['user_id'] == $u_row['id'] ? " selected='selected'" : "";
                        ?>
                        <option value="<?=$u_row['id'] ?>"<?=$manager_selected ?>><?=$u_row['last_name'].' '.$u_row['first_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <a class="btn btn-outline-dark" onclick="CancelCustomerManager();" href="javascript: void(0);"><i class="fas fa-undo"></i></a>
                    <a class="btn btn-dark" href="javascript: void(0);">OK</a>
                </div>
            </div>
        </td>
    </tr>
</table>
<?php
endif;
endif;
?>
<button type="button" class="close" data-dismiss='modal' style="position: absolute; right: 34px; top: 34px; z-index: 2000;"><img src="../images/icons/close_modal_red.svg" /></button>
<script>
    function EditCustomerPerson() {
        $('#customer_card_person').removeClass('d-flex');
        $('#customer_card_person').addClass('d-none');
        $('#customer_card_person_edit').removeClass('d-none');
        $('#customer_card_person_edit').addClass('d-flex');
    }
    
    function CancelCustomerPerson() {
        $('#customer_card_person_input').val($('#customer_card_person_value').text());
        $('#customer_card_person_edit').removeClass('d-flex');
        $('#customer_card_person_edit').addClass('d-none');
        $('#customer_card_person').removeClass('d-none');
        $('#customer_card_person').addClass('d-flex');
    }
    
    function EditCustomerPhone() {
        $('#customer_card_phone').removeClass('d-flex');
        $('#customer_card_phone').addClass('d-none');
        $('#customer_card_phone_edit').removeClass('d-none');
        $('#customer_card_phone_edit').addClass('d-flex');
    }
    
    function CancelCustomerPhone() {
        $('#customer_card_phone_input').val($('#customer_card_phone_value').text());
        $('#customer_card_phone_edit').removeClass('d-flex');
        $('#customer_card_phone_edit').addClass('d-none');
        $('#customer_card_phone').removeClass('d-none');
        $('#customer_card_phone').addClass('d-flex');
    }
    
    function EditCustomerEmail() {
        $('#customer_card_email').removeClass('d-flex');
        $('#customer_card_email').addClass('d-none');
        $('#customer_card_email_edit').removeClass('d-none');
        $('#customer_card_email_edit').addClass('d-flex');
    }
    
    function CancelCustomerEmail() {
        $('#customer_card_email_input').val($('#customer_card_email_value').text());
        $('#customer_card_email_edit').removeClass('d-flex');
        $('#customer_card_email_edit').addClass('d-none');
        $('#customer_card_email').removeClass('d-none');
        $('#customer_card_email').addClass('d-flex');
    }
    
    function EditCustomerManager() {
        $('#customer_card_manager').removeClass('d-flex');
        $('#customer_card_manager').addClass('d-none');
        $('#customer_card_manager_edit').removeClass('d-none');
        $('#customer_card_manager_edit').addClass('d-flex');
    }
    
    function CancelCustomerManager() {
        $('#customer_card_manager_select').val($('#customer_card_manager_value').attr('data-id'));
        $('#customer_card_manager_edit').removeClass('d-flex');
        $('#customer_card_manager_edit').addClass('d-none');
        $('#customer_card_manager').removeClass('d-none');
        $('#customer_card_manager').addClass('d-flex');
    }
</script>