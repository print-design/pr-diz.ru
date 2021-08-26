<?php
include '../include/topscripts.php';
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
        include 'header.php';
        include '../include/pager_top.php';
        ?>
        <div class="container-fluid">
            <h1 class="mb-4">Рациональный раскрой</h1>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <div class="form-group">
                            <label for="brand_name">Марка плёнки</label>
                            <select id="brand_name" name="brand_name" class="form-control" required="required">
                                <option value="" hidden="hidden">...</option>
                                    <?php
                                    $sql = "select distinct name from film_brand order by name";
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
                            <button type="submit" id="rational_cut_submit" name="rational_cut_submit" class="btn btn-dark w-50">Рассчитать</button>
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