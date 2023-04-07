<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator', 'manager-senior'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Получение объекта
$sql = "select r.id role_id, r.plural role, e.id employee_id, e.first_name, e.last_name, e.phone, e.active "
        . "from plan_role r "
        . "left join plan_employee e on e.role_id = r.id "
        . "order by r.id, e.active desc, e.last_name";
$fetcher = new Fetcher($sql);
$roles = array();
while($row = $fetcher->Fetch()) {
    $role_id = $row['role_id'];
    if(!isset($roles[$role_id])) {
        $roles[$role_id] = array('name' => $row['role'], 'employees' => array());
    }
    
    $employee_id = $row['employee_id'];
    if(!isset($roles[$role_id]['employees'][$employee_id]) && !empty($row['first_name']) && !empty($row['last_name'])) {
        $roles[$role_id]['employees'][$employee_id] = array('first_name' => $row['first_name'], 'last_name' => $row['last_name'], 'phone' => $row['phone'], 'active' => $row['active']);
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            h2 {
                margin-top: 20px;
            }
            
            table.table tr th, table.table tr td {
                height: 55px;
            }
            
            .modal-content {
                border-radius: 20px;
            }
            
            .modal-header {
                border-bottom: 0;
                padding-bottom: 0;
            }
            
            .modal-footer {
                border-top: 0;
                padding-top: 0;
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_admin.php';
        ?>
        <div class="container-fluid">
            <?php
            include '../include/subheader_plan.php';
            
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div>
                    <h1>Сотрудники</h1>
                </div>
                <div>
                    <?php if(IsInRole(array('technologist', 'dev', 'administrator'))): ?>
                    <a href="plan_employees_create.php" class="btn btn-dark"><i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить сотрудника</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            $show_table_header = true;
            foreach($roles as $r_key => $role):
            ?>
            <h2 id="r_<?=$r_key ?>"><?=$role['name'] ?></h2>
            <table class="table">
                <?php if($show_table_header): ?>
                <tr>
                    <th style="border-top: 0; width: 35%;">Фамилия</th>
                    <th style="border-top: 0; width: 35%;">Имя</th>
                    <th style="border-top: 0;">Телефон</th>
                    <th style="border-top: 0; width: 80px;">Активный</th>
                </tr>
                <?php
                endif;
                $no_border_top = $show_table_header ? '' : " border-top: 0;";
                foreach($role['employees'] as $e_key => $employee):
                ?>
                <tr>
                    <td style="width: 35%;<?=$no_border_top ?>"><?=$employee['last_name'] ?></td>
                    <td style="width: 35%;<?=$no_border_top ?>"><?=$employee['first_name'] ?></td>
                    <td style="width: auto;<?=$no_border_top ?>"><?=$employee['phone'] ?></td>
                    <td class="text-right switch" style="width: 80px;<?=$no_border_top ?>">
                        <input type="checkbox" data-id="<?=$e_key ?>"<?=$employee['active'] ? " checked='checked'" : "" ?> />
                    </td>
                </tr>
                <?php
                $no_border_top = '';
                $show_table_header = false;
                endforeach;
                ?>
            </table>
            <?php
            endforeach;
            ?>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
    <script>
        // Активирование / деактивирование пользователя
        $(".switch input[type='checkbox']").change(function() {
            $.ajax({ url: "../ajax/plan_employee.php?id=" + $(this).attr('data-id') + "&active=" + $(this).is(':checked') })
                    .fail(function() {
                        alert('Ошибка при установке / снятии флага активности пользователя');
            });
        });
    </script>
</html>