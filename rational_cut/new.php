<?php
include '../include/topscripts.php';

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$brand_name_valid = '';
$thickness_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'rational_cut_submit')) {
    $brand_name = filter_input(INPUT_POST, 'brand_name');
    if(empty($brand_name)) {
        $brand_name_valid = ISINVALID;
        $form_valid = true;
    }
    
    $thickness = filter_input(INPUT_POST, 'thickness');
    if(empty($thickness)) {
        $thickness_valid = ISINVALID;
        $form_valid = true;
    }
    
    $targets = array();
    $i = 0;
    while (null !== filter_input(INPUT_POST, 'width_'.(++$i)) && null !== filter_input(INPUT_POST, 'length_'.$i)) {
        $target = array();
        $target['width'] = filter_input(INPUT_POST, 'width_'.$i);
        $target['length'] = filter_input(INPUT_POST, 'length_'.$i);
        array_push($targets, $target);
    }
    
    if(count($targets) == 0) {
        $error_message = "Укажите хотя бы два ручья";
        $form_valid = false;
    }
    
    if($form_valid) {
        $brand_name = addslashes($brand_name);
        $sql = "insert into rational_cut(brand_name, thickness) values('$brand_name', $thickness)";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $rational_cut_id = $executer->insert_id;
        $rational_cut_stage_id = null;
        
        if(empty($error_message) && !empty($rational_cut_id)) {
            $sql = "insert into rational_cut_stage (rational_cut_id) values($rational_cut_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            $rational_cut_stage_id = $executer->insert_id;
            
            if(empty($error_message) && !empty($rational_cut_stage_id)) {
                foreach ($targets as $target) {
                    $width = $target['width'];
                    $length = $target['length'];
                    $sql = "insert into rational_cut_stage_stream (rational_cut_stage_id, width, length) values($rational_cut_stage_id, $width, $length)";
                    $executer = new Executer($sql);
                    $error_message = $executer->error;
                }
            }
        }
        
        if(empty($error_message) && !empty($rational_cut_stage_id)) {
            header("Location: stage.php?id=$rational_cut_stage_id");
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        include 'style.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_analytics.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <a class="btn btn-outline-dark backlink" href="<?=APPLICATION ?>/rational_cut/">К списку</a>
            <h1 class="mb-4">Новый раскрой</h1>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <div class="form-group">
                            <label for="brand_name">Марка плёнки</label>
                            <select id="brand_name" name="brand_name" class="form-control" required="required">
                                <option value="" hidden="hidden">...</option>
                                    <?php
                                    $sql = "select distinct trim(name) name from film_brand order by name";
                                    $fetcher = new Fetcher($sql);
                                    while ($row = $fetcher->Fetch()):
                                    $selected = '';
                                    if(filter_input(INPUT_POST, 'brand_name') == $row[0]) {
                                        $selected = " selected='selected'";
                                    }
                                    ?>
                                <option<?=$selected ?>><?=$row[0] ?></option>
                                    <?php
                                    endwhile;
                                    ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="thickness">Толщина</label>
                            <select id="thickness" name="thickness" class="form-control" required="required">
                                <option value="" hidden="hidden">...</option>
                                <?php
                                if(null !== filter_input(INPUT_POST, 'brand_name')):
                                    $brand_name = filter_input(INPUT_POST, 'brand_name');
                                    $sql = "select distinct fbv.thickness, fbv.weight from film_brand_variation fbv inner join film_brand fb on fbv.film_brand_id = fb.id where fb.name='$brand_name' order by thickness";
                                    $fetcher = new Fetcher($sql);
                                    while ($row = $fetcher->Fetch()):
                                        $thickness = $row['thickness'];
                                        $weight = $row['weight'];
                                        $selected = '';
                                        if(filter_input(INPUT_POST, 'thickness') == $thickness) {
                                            $selected = " selected='selected'";
                                        }
                                        ?>
                                <option value='<?=$thickness ?>'<?=$selected ?>><?=$thickness ?> мкм <?=$weight ?> г/м<sup>2</sup></option>
                                        <?php
                                    endwhile;
                                endif;
                                ?>
                            </select>
                        </div>
                        <?php
                        $i = 0;
                        while (++$i == 1 || (null != filter_input(INPUT_POST, 'width_'.$i) && null != filter_input(INPUT_POST, 'length_'.$i))):
                            ?>
                        <div class="row">
                            <div class="col-5">
                                <div class="form-group">
                                    <label for="width_<?=$i ?>">Ширина, мм</label>
                                    <input type="text" id="width_<?=$i ?>" name="width_<?=$i ?>" class="form-control" required="required" value="<?= filter_input(INPUT_POST, 'width_'.$i) ?>" />
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group">
                                    <label for="length_<?=$i ?>">Длина, м</label>
                                    <input type="text" id="length_<?=$i ?>" name="length_<?=$i ?>" class="form-control" required="required" value="<?= filter_input(INPUT_POST, 'length_'.$i) ?>" />
                                </div>
                            </div>
                            <di class="col-2">
                                <?php
                                $class_d_none = "";
                                if(null !== filter_input(INPUT_POST, 'width_'.($i + 1)) && null !== filter_input(INPUT_POST, 'length_'.($i + 1))) {
                                    $class_d_none = " d-none";
                                }
                                ?>
                                <button type="button" data-i="<?=($i + 1) ?>" class="btn btn-outline-dark mt-4 btn_add<?=$class_d_none ?>"><i class="fas fa-plus"></i></button>
                            </di>
                        </div>
                            <?php
                            endwhile;
                            ?>
                        <div class="form-group mt-4">
                            <button type="submit" id="rational_cut_submit" name="rational_cut_submit" class="btn btn-dark w-50">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
    <?php
    include '../include/footer.php';
    ?>
    <script>
        $('#brand_name').change(function() {
            if($(this).val() == "") {
                $('#thickness').html("<option value=''>Выберите толщину</option>");
            }
            else {
                $.ajax({ url: "../ajax/thickness.php?brand_name=" + $(this).val() })
                        .done(function(data) {
                            $('#thickness').html(data);
                        })
                        .fail(function() {
                            alert('Ошибка при выборе марки пленки');
                        });
            }
        });
        
        function BtnAdd() {
            $('.btn_add').click(function() {
                $(this).parent().parent().after('<div class="row">' +
                        '<div class="col-5">' + 
                        '<div class="form-group">' +
                        '<label for="width_' + $(this).attr('data-i') + '">Ширина, мм</label>' +
                        '<input type="text" id="width_' + $(this).attr('data-i') + '" name="width_' + $(this).attr('data-i') + '" class="form-control" required="required" value="" />' +
                        '</div>' +
                        '</div>' +
                        '<div class="col-5">' +
                        '<div class="form-group">' +
                        '<label for="length_' + $(this).attr('data-i') + '">Длина, м</label>' +
                        '<input type="text" id="length_' + $(this).attr('data-i') + '" name="length_' + $(this).attr('data-i') + '" class="form-control" required="required" value="" />' +
                        '</div>' +
                        '</div>' +
                        '<di class="col-2">' +
                        '<button type="button" data-i="' + (parseInt($(this).attr('data-i')) + 1) + '" class="btn btn-outline-dark mt-4 btn_add"><i class="fas fa-plus"></i></button>' +
                        '</di>' +
                        '</div>');
                $(this).addClass('d-none');
                BtnAdd();
            });
        }
        
        BtnAdd();
    </script>
</html>