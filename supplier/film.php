<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка создания марки плёнки
$film_insert_id = null;

if(null !== filter_input(INPUT_POST, 'create_film_submit')) {
    $name = filter_input(INPUT_POST, 'name');
    
    if(!empty($name)) {
        $name = addslashes($name);
        $sql = "insert into film(name) values('$name')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $film_insert_id = $executer->insert_id;
    }
}

// Обработка создания вариации плёнки
if(null !== filter_input(INPUT_POST, 'create_film_variation_submit')) {
    $film_id = filter_input(INPUT_POST, 'film_id');
    $thickness = filter_input(INPUT_POST, 'thickness');
    $weight = filter_input(INPUT_POST, 'weight');
    
    if(!empty($film_id) && !empty($thickness) && !empty($weight)) {
        $sql = "insert into film_variation(film_id, thickness, weight) values($film_id, $thickness, $weight)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
}

// Получение объекта
$sql = "select f.id film_id, f.name film, fv.id film_variation_id, fv.thickness, fv.weight "
        . "from film f "
        . "left join film_variation fv on fv.film_id = f.id "
        . "order by f.name, fv.thickness, fv.weight";
$fetcher = new Fetcher($sql);
$films = array();
while($row = $fetcher->Fetch()) {
    $film_id = $row['film_id'];
    if(!isset($films[$film_id])) {
        $films[$film_id] = array('name' => $row['film'], 'film_variations' => array());
    }
    
    $film_variation_id = $row['film_variation_id'];
    if(!isset($films[$film_id]['film_variations'][$film_variation_id]) && !empty($row['thickness']) && !empty($row['weight'])) {
        $films[$film_id]['film_variations'][$film_variation_id] = array('thickness' => $row['thickness'], 'weight' => $row['weight']);
    }
}

//----------------------------
// DELETE !!!
$sql = "select distinct fb.id film_brand_id, fb.name film_brand, fbv.id film_brand_variation_id, fbv.thickness, fbv.weight "
        . "from film_brand fb inner join film_brand_variation fbv on fbv.film_brand_id = fb.id "
        . "where fb.id in (select min(id) from film_brand where name = fb.name group by name) "
        . "order by fb.name, fbv.thickness, fbv.weight";
$fetcher = new Fetcher($sql);
$film_brands = array();
while($row = $fetcher->Fetch()) {
    $film_brand_id = $row['film_brand_id'];
    if(!isset($film_brands[$film_brand_id])) {
        $film_brands[$film_brand_id] = array('name' => $row['film_brand'], 'film_brand_variations' => array());
    }
    
    $film_brand_variation_id = $row['film_brand_variation_id'];
    if(!isset($film_brands[$film_brand_id]['film_brand_variations'][$film_brand_variation_id])) {
        $film_brands[$film_brand_id]['film_brand_variations'][$film_brand_variation_id] = array('thickness' => $row['thickness'], 'weight' => $row['weight']);
    }
}
//-------------------------------------
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
        <div id="create_film_variation" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <p class="font-weight-bold" style="font-size: x-large;">Добавить пленку</p>
                            <button type="button" class="close create_film_variation_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <select class="form-control" name="film_id" id="film_id" required="required">
                                    <option value="" hidden="hidden">Марка пленки</option>
                                    <?php foreach($films as $f_key => $film): ?>
                                    <option value="<?=$f_key ?>"><?=$film['name'] ?></option>
                                    <?php endforeach; ?>
                                    <option disabled="disabled">  </option>
                                    <option value="+">+&nbsp;Новая марка</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" id="thickness" name="thickness" class="form-control int-only" placeholder="Толщина" required="required" />
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" id="weight" name="weight" class="form-control float-only" placeholder="Удельный вес" required="required" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-dark" id="create_film_variation_submit" name="create_film_variation_submit">Добавить</button>
                            <button type="button" class="btn btn-light create_film_variation_dismiss" data-dismiss="modal">Отменить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="create_film" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <p class="font-weight-bold" style="font-size: x-large;">Введите марку пленки</p>
                            <button type="button" class="close create_film_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" name="name" id="name" class="form-control" placeholder="Марка пленки" required="required" />
                            </div>
                        </div>
                        <div class="modal-footer" style="justify-content: flex-start;">
                            <button type="submit" class="btn btn-dark" id="create_film_submit" name="create_film_submit">Добавить</button>
                            <button type="button" class="btn btn-light create_film_dismiss" data-dismiss="modal">Отменить</button>
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
            <?php
            $show_table_header = true;
            foreach($films as $f_key => $film):
            ?>
            <h2 id="f_<?=$f_key ?>"><?=$film['name'] ?></h2>
            <table class="table table-hover">
                <?php if($show_table_header): ?>
                <tr>
                    <th width="50%" style="border-top: 0;">Название пленки</th>
                    <th style="border-top: 0;">Толщина</th>
                    <th style="border-top: 0;">Удельный вес</th>
                </tr>
                <?php
                $show_table_header = false;
                endif;
                $no_border_top = $show_table_header ? '' : " style='border-top: 0;'";
                foreach($film['film_variations'] as $fv_key => $film_variation):
                ?>
                <tr>
                    <td width="50%"<?=$no_border_top ?>><?=$film['name'] ?></td>
                    <td<?=$no_border_top ?>><?=$film_variation['thickness'] ?> мкм</td>
                    <td<?=$no_border_top ?>><?=$film_variation['weight'] ?> г/м<sup>2</sup></td>
                </tr>
                <?php
                $no_border_top = '';
                endforeach;
                ?>
            </table>
            <?php
            endforeach;
            ?>
            <?php
            //-------------------------
            // DELETE !!!!!
            echo "<hr />";
            $show_table_header = true;
            foreach(array_keys($film_brands) as $key):
            ?>
            <h2 id="fb_<?=$key ?>"><?=$film_brands[$key]['name'] ?></h2>
            <table class="table table-hover">
                <?php if($show_table_header): ?>
                <tr>
                    <th width="50%" style="border-top: 0;">Название пленки</th>
                    <th style="border-top: 0;">Толщина</th>
                    <th style="border-top: 0;">Удельный вес</th>
                </tr>
                <?php
                endif;
                $no_border_top = $show_table_header ? '' : " style = 'border-top: 0;'";
                foreach(array_keys($film_brands[$key]['film_brand_variations']) as $fbv_key):
                ?>
                <tr id="fbv_<?=$fbv_key ?>">
                    <td width="50%"<?=$no_border_top ?>><?=$film_brands[$key]['name'] ?></td>
                    <td<?=$no_border_top ?>><?=$film_brands[$key]['film_brand_variations'][$fbv_key]['thickness'] ?> мкм</td>
                    <td<?=$no_border_top ?>><?=$film_brands[$key]['film_brand_variations'][$fbv_key]['weight'] ?> г/м<sup>2</sup></td>
                </tr>
                <?php
                $no_border_top = '';
                endforeach;
                ?>
            </table>
            <?php
            $show_table_header = false;
            endforeach;
            //--------------------------------
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('select#film_id').change(function() {
                if($(this).val() == '+') {
                    $('#create_film_variation').modal('hide');
                }
            });
            
            $('#create_film').on('hidden.bs.modal', function() {
                $('input#name').val('');
                $('#create_film_variation').modal('show');
            });
            
            $('#create_film_variation').on('hidden.bs.modal', function() {
                if($('select#film_id').val() == '+') {
                    $('#create_film').modal('show');
                }
                
                $('select#film_id').val('');
                $('input#thickness').val('');
                $('input#weight').val('');
            });
            
            $('#create_film').on('shown.bs.modal', function() {
                $('input:text:visible:first').focus();
            });
            
            <?php
            if(null !== filter_input(INPUT_POST, 'create_film_submit')):
                if(!empty($film_insert_id)):
                ?>
                $('select#film_id').val(<?=$film_insert_id ?>);
                <?php endif; ?>
            $('#create_film_variation').modal('show');
            <?php endif; ?>
            
            <?php if(null !== filter_input(INPUT_POST, 'film_id')): ?>
            window.scrollTo(0, $('#f_<?= filter_input(INPUT_POST, 'film_id') ?>').offset().top - $('#topmost').height());
            <?php endif; ?>
        </script>
    </body>
</html>