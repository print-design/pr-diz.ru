<?php
// Этот файл подключается из pack/details.php и buh/details.php.
// Ожидает уже заданными переменные: $id, $gross_weight, $pallet_count, $pallet_length,
// $pallet_width, $pallet_height, $pallet_shared_with_id, $gross_weight_valid, $pallet_count_valid,
// $pallet_length_valid, $pallet_width_valid, $pallet_height_valid

// Список заказов-кандидатов для раскрывающегося списка "В одном паллете с":
// в статусе "Готов к отгрузке", у которых уже заполнены все пять полей своими собственными
// значениями (то есть они сами ни с кем не связаны), и не сам текущий заказ
$pallet_shared_candidates = (new Grabber(
        "select c.id, c.name, cus.name customer from calculation c left join customer cus on c.customer_id = cus.id "
        . "where c.duplicate_status_id = ? and c.gross_weight is not null and c.pallet_count is not null "
        . "and c.pallet_length is not null and c.pallet_width is not null and c.pallet_height is not null "
        . "and c.pallet_shared_with_id is null and c.id != ? "
        . "order by c.name",
        [ORDER_STATUS_SHIP_READY, $id]
))->result;

$pallet_fields_disabled = !empty($pallet_shared_with_id);
?>
<div class="w-100">
    <form method="post">
        <input type="hidden" name="<?= CSRF_TOKEN ?>" value="<?= $_SESSION[CSRF_TOKEN] ?>" />
        <input type="hidden" name="id" value="<?=$id ?>" />
        <div class="form-group" style="width: 100%;">
            <label for="pallet_shared_with_id_<?=$id ?>" class="d-block">В одном паллете с</label>
            <select id="pallet_shared_with_id_<?=$id ?>" name="pallet_shared_with_id" class="form-control pallet-shared-with-select">
                <option value="">-- не выбрано --</option>
                <?php foreach($pallet_shared_candidates as $pallet_shared_candidate): ?>
                <option value="<?=$pallet_shared_candidate['id'] ?>"<?= $pallet_shared_with_id == $pallet_shared_candidate['id'] ? " selected='selected'" : "" ?>><?=htmlspecialchars($pallet_shared_candidate['name']) ?> (<?= htmlspecialchars($pallet_shared_candidate['customer']) ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="d-flex justify-content-between">
            <div class="form-group mr-1">
                <label for="gross_weight_<?=$id ?>">Вес брутто, кг</label>
                <input type="text" id="gross_weight_<?=$id ?>" name="gross_weight" class="form-control float-only float-format pallet-required-field<?=$gross_weight_valid ?>" value="<?= $pallet_fields_disabled ? '' : DisplayNumber($gross_weight, 0) ?>"<?= $pallet_fields_disabled ? '' : ' required="required"' ?> autocomplete="off" />
            </div>
            <div class="form-group mr-1 ml-1">
                <label for="pallet_count_<?=$id ?>">Кол-во паллетов</label>
                <input type="text" id="pallet_count_<?=$id ?>" name="pallet_count" class="form-control int-only pallet-required-field<?=$pallet_count_valid ?>" value="<?= $pallet_fields_disabled ? '' : $pallet_count ?>"<?= $pallet_fields_disabled ? '' : ' required="required"' ?> autocomplete="off" />
            </div>
            <div class="form-group mr-1 ml-1">
                <label for="pallet_length_<?=$id ?>">Длина, м</label>
                <input type="text" id="pallet_length_<?=$id ?>" name="pallet_length" class="form-control float-only float-format pallet-required-field<?=$pallet_length_valid ?>" value="<?= $pallet_fields_disabled ? '' : DisplayNumber($pallet_length, 2) ?>"<?= $pallet_fields_disabled ? '' : ' required="required"' ?> autocomplete="off" />
            </div>
            <div class="form-group mr-1 ml-1">
                <label for="pallet_width_<?=$id ?>">Ширина, м</label>
                <input type="text" id="pallet_width_<?=$id ?>" name="pallet_width" class="form-control float-only float-format pallet-required-field<?=$pallet_width_valid ?>" value="<?= $pallet_fields_disabled ? '' : DisplayNumber($pallet_width, 2) ?>"<?= $pallet_fields_disabled ? '' : ' required="required"' ?> autocomplete="off" />
            </div>
            <div class="form-group ml-1">
                <label for="pallet_height_<?=$id ?>">Высота, м</label>
                <input type="text" id="pallet_height_<?=$id ?>" name="pallet_height" class="form-control float-only float-format pallet-required-field<?=$pallet_height_valid ?>" value="<?= $pallet_fields_disabled ? '' : DisplayNumber($pallet_height, 2) ?>"<?= $pallet_fields_disabled ? '' : ' required="required"' ?> autocomplete="off" />
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button type="submit" name="save_pallet_data_submit" class="btn btn-outline-dark mb-3"><i class="fas fa-save mr-2"></i>Сохранить</button>
        </div>
    </form>
</div>
