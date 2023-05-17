<?php
include '../include/topscripts.php';
include '../plan/roles.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator', 'manager-senior'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Получение объекта
$employees = array();
foreach(ROLES as $role) {
    $employees[$role] = array();
}

$sql = "select id, first_name, last_name, role_id, phone, active "
        . "from plan_employee "
        . "order by active desc, last_name, first_name";
$fetcher = new Fetcher($sql);
while($row = $fetcher->Fetch()) {
    array_push($employees[$row['role_id']], $row);
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
            foreach(ROLES as $role):
            ?>
            <h2 id="r_<?=$role ?>"><?=ROLE_PLURALS[$role] ?></h2>
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
                foreach($employees[$role] as $employee):
                ?>
                <tr>
                    <td style="width: 35%;<?=$no_border_top ?>"><?=$employee['last_name'] ?></td>
                    <td style="width: 35%;<?=$no_border_top ?>"><?=$employee['first_name'] ?></td>
                    <td style="width: auto;<?=$no_border_top ?>"><?=$employee['phone'] ?></td>
                    <td class="text-right switch" style="width: 80px;<?=$no_border_top ?>">
                        <input type="checkbox" data-id="<?=$employee['id'] ?>"<?=$employee['active'] ? " checked='checked'" : "" ?> />
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