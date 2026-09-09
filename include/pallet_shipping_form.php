<?php
// Этот файл подключается из pack/details.php и buh/details.php.
// Ожидает уже заданными переменные: $id, $gross_weight, $pallet_count, $pallet_length,
// $pallet_width, $pallet_height, $gross_weight_valid, $pallet_count_valid,
// $pallet_length_valid, $pallet_width_valid, $pallet_height_valid
?>
<div class="w-100">
    <form method="post">
        <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
        <input type="hidden" name="id" value="<?=$id ?>" />
        <input type="hidden" name="scroll" />
        <div class="d-flex justify-content-between">
            <div class="form-group mr-1">
                <label for="gross_weight_<?=$id ?>">Вес брутто, кг</label>
                <input type="text" id="gross_weight_<?=$id ?>" name="gross_weight" class="form-control float-only float-format<?=$gross_weight_valid ?>" value="<?= DisplayNumber($gross_weight, 0) ?>" required="required" autocomplete="off" />
            </div>
            <div class="form-group mr-1 ml-1">
                <label for="pallet_count_<?=$id ?>">Кол-во паллетов</label>
                <input type="text" id="pallet_count_<?=$id ?>" name="pallet_count" class="form-control int-only<?=$pallet_count_valid ?>" value="<?=$pallet_count ?>" required="required" autocomplete="off" />
            </div>
            <div class="form-group mr-1 ml-1">
                <label for="pallet_length_<?=$id ?>">Длина, м</label>
                <input type="text" id="pallet_length_<?=$id ?>" name="pallet_length" class="form-control float-only float-format<?=$pallet_length_valid ?>" value="<?= DisplayNumber($pallet_length, 2) ?>" required="required" autocomplete="off" />
            </div>
            <div class="form-group mr-1 ml-1">
                <label for="pallet_width_<?=$id ?>">Ширина, м</label>
                <input type="text" id="pallet_width_<?=$id ?>" name="pallet_width" class="form-control float-only float-format<?=$pallet_width_valid ?>" value="<?= DisplayNumber($pallet_width, 2) ?>" required="required" autocomplete="off" />
            </div>
            <div class="form-group ml-1">
                <label for="pallet_height_<?=$id ?>">Высота, м</label>
                <input type="text" id="pallet_height_<?=$id ?>" name="pallet_height" class="form-control float-only float-format<?=$pallet_height_valid ?>" value="<?= DisplayNumber($pallet_height, 2) ?>" required="required" autocomplete="off" />
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <?php if($gross_weight !== null && $pallet_count !== null && $pallet_length !== null && $pallet_width !== null && $pallet_height !== null): ?>
            <button type="submit" name="clear_pallet_data_submit" class="btn btn-outline-danger mb-3 mr-2" onclick="return confirm('Вы уверены, что хотите очистить данные отгружаемого паллета?');"><i class="fas fa-times mr-2"></i>Очистить</button>
            <?php endif; ?>
            <button type="submit" name="save_pallet_data_submit" class="btn btn-outline-dark mb-3"><i class="fas fa-save mr-2"></i>Сохранить</button>
        </div>
    </form>
</div>
