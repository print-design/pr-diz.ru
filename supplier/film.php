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
        </style>
    </head>
    <body>
        <?php
        include '../include/header_admin.php';
        ?>
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
                    <a href="create.php" title="Добавить поставщика" class="btn btn-dark">
                        <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить тип пленки
                    </a>
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