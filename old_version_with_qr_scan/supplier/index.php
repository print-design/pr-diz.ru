<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка создания поставщика
$form_valid = true;
$supplier_insert_id = null;

if(null !== filter_input(INPUT_POST, 'create_supplier_submit')) {
    $name = filter_input(INPUT_POST, 'name');
    
    if(empty($name)) {
        $error_message = "Не указано название поставщика";
        $form_valid = false;
    }
    
    $name = addslashes($name);
    $sql = "select count(id) from supplier where name = '$name'";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        if($row[0] != 0) {
            $error_message = "Такой поставщик уже есть";
            $form_valid = false;
        }
    }
    
    if($form_valid) {
        $sql = "insert into supplier(name) values('$name')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $supplier_insert_id = $executer->insert_id;
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
        <div id="create_supplier" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <p class="font-weight-bold" style="font-size: x-large;">Поставщик</p>
                            <button type="button" class="close create_supplier_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="name" id="name" class="form-control" placeholder="Поставщик" required="required" />
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-dark" id="create_supplier_submit" name="create_supplier_submit">Добавить</button>
                            <button type="button" class="btn btn-light create_supplier_dismiss" data-dismiss="modal">Отменить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-0">
                    <h1>Поставщики</h1>
                </div>
                <div class="pt-1">
                    <button class="btn btn-dark" data-toggle="modal" data-target="#create_supplier">
                        <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить поставщика
                    </button>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="border-top: 0;">Название поставщика</th>
                        <th style="border-top: 0;">Пленки</th>
                        <th style="border-top: 0;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select s.id, s.name, "
                            . "(select count(id) from film where id in (select film_id from film_variation where id in (select film_variation_id from supplier_film_variation where supplier_id = s.id))) count, "
                            . "(select name from film where id in (select film_id from film_variation where id in (select film_variation_id from supplier_film_variation where supplier_id = s.id)) order by name limit 1) first "
                            . "from supplier s order by s.name";
                    $fetcher = new Fetcher($sql);
                    
                    while($row = $fetcher->Fetch()):
                        $id = $row['id'];
                        $name = htmlentities($row['name']);
                        $count = $row['count'];
                        $first = $row['first'];
                        $products = '';
                        if($first != null) {
                            $products = htmlentities($first);
                            
                            if($count > 1) {
                                $products .= " и еще ".(intval($count) - 1);
                            }
                        }
                    ?>
                    <tr id="s_<?=$id ?>">
                        <td><?=$name ?></td>
                        <td><?=$products ?></td>
                        <td class="text-right"><a href="edit.php?id=<?=$id ?>" title="Редактировать"><img src="../images/icons/edit1.svg" /></a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('#create_supplier').on('shown.bs.modal', function() {
                $('input:text:visible:first').focus();
            });
            
            $('#create_supplier').on('hidden.bs.modal', function() {
                $('input#name').val('');
            });
            
            <?php if(null !== filter_input(INPUT_POST, 'create_supplier_submit') && empty($error_message) && !empty($supplier_insert_id)): ?>
            window.scrollTo(0, $('#s_<?= $supplier_insert_id ?>').offset().top - $('#topmost').height());
            <?php endif; ?>
        </script>
    </body>
</html>