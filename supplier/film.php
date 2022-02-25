<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <link href="<?=APPLICATION ?>/css/select2.min.css" rel="stylesheet"/>
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
        <div id="create_film_brand_variation" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <p class="font-weight-bold" style="font-size: x-large;">Добавить пленку</p>
                            <button type="button" class="close create_film_brand_variation_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <select class="form-control" name="film_brand" id="film_brand">
                                    <option value="" hidden="hidden">Марка пленки</option>
                                    <option>HDPL</option>
                                    <option>HMIL.M</option>
                                    <option>HOHL</option>
                                    <option>HWHL</option>
                                    <option>LOBA</option>
                                    <option disabled="disabled">  </option>
                                    <option value="+">+&nbsp;Новая марка</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" name="thickness" class="form-control int-only" placeholder="Толщина" />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" name="weight" class="form-control float-only" placeholder="Удельный вес" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-dark" id="create_film_variation_submit" name="create_film_variation_submit">Добавить</button>
                            <button type="button" class="btn btn-light create_film_brand_variation_dismiss" data-dismiss="modal">Отменить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="create_film_brand" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <p class="font-weight-bold" style="font-size: x-large;">Введите марку пленки</p>
                            <button type="button" class="close create_film_brand_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="film_brand" id="film_brand" class="form-control" placeholder="Марка пленки" />
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-dark" id="create_film_brand" name="create_film_brand">Добавить</button>
                            <button type="button" class="btn btn-light create_film_brand_dismiss" data-dismiss="modal">Отменить</button>
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
                    <button class="btn btn-dark" data-toggle="modal" data-target="#create_film_brand_variation">
                        <i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить тип пленки
                    </button>
                </div>
            </div>
            <h2>HGPL</h2>
            <table class="table table-hover">
                <tr>
                    <th width="50%" style="border-top: 0;">Название пленки</th>
                    <th style="border-top: 0;">Толщина</th>
                    <th style="border-top: 0;">Удельный вес</th>
                </tr>
                <tr>
                    <td>HGPL</td>
                    <td>15 мкм</td>
                    <td>13.65 г/м<sup>2</sup></td>
                </tr>
                <tr>
                    <td>HGPL</td>
                    <td>15 мкм</td>
                    <td>13.65 г/м<sup>2</sup></td>
                </tr>
                <tr>
                    <td>HGPL</td>
                    <td>15 мкм</td>
                    <td>13.65 г/м<sup>2</sup></td>
                </tr>
                <tr>
                    <td>HGPL</td>
                    <td>15 мкм</td>
                    <td>13.65 г/м<sup>2</sup></td>
                </tr>
            </table>
            <h2>HMIL.ML</h2>
            <table class="table table-hover">
                <tr>
                    <td width="50%" style="border-top: 0;">HMIL.ML</td>
                    <td style="border-top: 0;">15 мкм</td>
                    <td style="border-top: 0;">27.3 г/м<sup>2</sup></td>
                </tr>
                <tr>
                    <td>HMIL.ML</td>
                    <td>15 мкм</td>
                    <td>27.3 г/м<sup>2</sup></td>
                </tr>
                <tr>
                    <td>HMIL.ML</td>
                    <td>15 мкм</td>
                    <td>27.3 г/м<sup>2</sup></td>
                </tr>
                <tr>
                    <td>HMIL.ML</td>
                    <td>15 мкм</td>
                    <td>27.3 г/м<sup>2</sup></td>
                </tr>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('#film_brand').change(function() {
                if($(this).val() == '+') {
                    $('#create_film_brand_variation').modal('hide');
                }
            });
            
            $('#create_film_brand').on('hidden.bs.modal', function() {
                $('#film_brand').val('');
                $('#create_film_brand_variation').modal('show');
            });
            
            $('#create_film_brand_variation').on('hidden.bs.modal', function() {
                if($('#film_brand').val() == '+') {
                    $('#create_film_brand').modal('show');
                }
            });
            
            $('#create_film_brand').on('shown.bs.modal', function() {
                $('input:text:visible:first').focus();
            });
        </script>
    </body>
</html>