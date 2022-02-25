<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

function OrderLink($param) {
    if(array_key_exists('order', $_REQUEST) && $_REQUEST['order'] == $param) {
        echo "<strong><i class='fas fa-arrow-down' style='color: black; font-size: small;'></i></strong>";
    }
    else {
        echo "<a class='gray' href='".BuildQueryAddRemove("order", $param, "page")."' style='font-size: x-small;'><i class='fas fa-arrow-down'></i></a>";
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
        <div id="create_film_variation" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <p class="font-weight-bold" style="font-size: x-large;">Добавить пленку</p>
                            <button type="button" class="close user_change_password_dismiss" data-dismiss="modal"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <select name="film_brand" class="form-control">
                                    <option value="" hidden="hidden">Марка пленки</option>
                                    <option>HDPL</option>
                                    <option>HMIL.M</option>
                                    <option>HOHL</option>
                                    <option>HWHL</option>
                                    <option>LOBA</option>
                                    <option>+&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Новая марка</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" name="thickness" class="form-control" placeholder="Толщина" />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" name="weight" class="form-control" placeholder="Удельный вес" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-dark" id="create_film_variation_submit" name="create_film_variation_submit">Добавить</button>
                            <button type="button" class="btn btn-light user_change_password_dismiss" data-dismiss="modal">Отменить</button>
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
                    <h1>Пленка</h1>
                </div>
                <div class="p-0">
                    <button class="btn btn-dark" data-toggle="modal" data-target="#create_film_variation">
                        <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить тип пленки
                    </button>
                </div>
            </div>
            <h2>HGPL</h2>
            <table class="table table-hover">
                <tr>
                    <th width="30%" style="border-top: 0;">Название пленки&nbsp;&nbsp;<?= OrderLink('id') ?></th>
                    <th width="30%" style="border-top: 0;">Тип&nbsp;&nbsp;<?= OrderLink('id') ?></th>
                    <th style="border-top: 0;">Толщина&nbsp;&nbsp;<?= OrderLink('id') ?></th>
                    <th style="border-top: 0;">Удельный вес&nbsp;&nbsp;<?= OrderLink('id') ?></th>
                    <th style="border-top: 0;"></th>
                </tr>
                <tr>
                    <td>HGPL</td>
                    <td>Прозрачка</td>
                    <td>15 мкм</td>
                    <td>13.65 г/м<sup>2</sup></td>
                    <td class="text-right pr-4"><img src="../images/icons/trash2.svg" /></td>
                </tr>
                <tr>
                    <td>HGPL</td>
                    <td>Прозрачка</td>
                    <td>15 мкм</td>
                    <td>13.65 г/м<sup>2</sup></td>
                    <td class="text-right pr-4"><img src="../images/icons/trash2.svg" /></td>
                </tr>
                <tr>
                    <td>HGPL</td>
                    <td>Прозрачка</td>
                    <td>15 мкм</td>
                    <td>13.65 г/м<sup>2</sup></td>
                    <td class="text-right pr-4"><img src="../images/icons/trash2.svg" /></td>
                </tr>
                <tr>
                    <td>HGPL</td>
                    <td>Прозрачка</td>
                    <td>15 мкм</td>
                    <td>13.65 г/м<sup>2</sup></td>
                    <td class="text-right pr-4"><img src="../images/icons/trash2.svg" /></td>
                </tr>
            </table>
            <h2>HMIL.ML</h2>
            <table class="table table-hover">
                <tr>
                    <td width="30%" style="border-top: 0;">HMIL.ML</td>
                    <td width="30%" style="border-top: 0;"></td>
                    <td style="border-top: 0;">15 мкм</td>
                    <td style="border-top: 0;">27.3 г/м<sup>2</sup></td>
                    <td class="text-right pr-4" style="border-top: 0;"><img src="../images/icons/trash2.svg" /></td>
                </tr>
                <tr>
                    <td>HMIL.ML</td>
                    <td></td>
                    <td>15 мкм</td>
                    <td>27.3 г/м<sup>2</sup></td>
                    <td class="text-right pr-4"><img src="../images/icons/trash2.svg" /></td>
                </tr>
                <tr>
                    <td>HMIL.ML</td>
                    <td></td>
                    <td>15 мкм</td>
                    <td>27.3 г/м<sup>2</sup></td>
                    <td class="text-right pr-4"><img src="../images/icons/trash2.svg" /></td>
                </tr>
                <tr>
                    <td>HMIL.ML</td>
                    <td></td>
                    <td>15 мкм</td>
                    <td>27.3 г/м<sup>2</sup></td>
                    <td class="text-right pr-4"><img src="../images/icons/trash2.svg" /></td>
                </tr>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>