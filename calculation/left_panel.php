<?php
function GetSkiNameExt($param, $param_width) {
    switch ($param) {
        case SKI_STANDARD:
            return "Стандартные лыжи";
        case SKI_NONSTANDARD:
            return "Ширина $param_width мм";
        default :
            return 'Без лыж';
    }
}
?>
<div class="row">
    <div class="col-6">
        <table class="calculation-table">
            <tr><td colspan="2"><h2>О заказе</h2></td></tr>
            <tr><th>Заказчик</th><td><?=$calculation->customer ?></td></tr>
            <?php if(!empty($calculation->last_name) || !empty($calculation->first_name)): ?>
            <tr><th>Менеджер</th><td><?=$calculation->last_name.(empty($calculation->last_name) ? "" : " ").$calculation->first_name ?></td></tr>
            <?php endif; ?>
            <tr><th>Тип работы</th><td><?=WORK_TYPE_NAMES[$calculation->work_type_id] ?></td></tr>
            <?php if(!empty($calculation->quantity) && !empty($calculation->unit)): ?>
            <tr><th>Объем заказа</th><td><?= rtrim(rtrim(number_format($calculation->quantity, 2, ",", " "), "0"), ",") ?> <?=$calculation->unit == 'kg' ? "кг" : "шт" ?></td></tr>
            <?php endif; ?>
            <?php if(!empty($printings_number)): ?>
            <tr><th>Тиражей</th><td><?=$printings_number ?></td></tr>
            <?php endif; ?>
            
            <tr><td colspan="2" class="pt-4"><h2>Пленка</h2></td></tr>
            <tr><th colspan="2">Основная пленка</th></tr>
            <tr><td colspan="2"><?=$calculation->film_1 ?></td></tr>
            <tr>
                <td><?= number_format($calculation->thickness_1, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($calculation->density_1, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                <td><?= GetSkiNameExt($calculation->ski_1, $calculation->width_ski_1) ?></td>
            </tr>
            <?php if($calculation->customers_material_1 == 1): ?>
            <tr><td colspan="2">Сырьё заказчика</td></tr>
            <?php endif; ?>
            
            <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE && $calculation->laminations_number > 0): ?>
            <tr><th colspan="2">Ламинация 1<?= empty($calculation->laminator_id) ? "" : ($calculation->laminator_id == LAMINATOR_SOLVENT ? " (сольвент)" : " (бессольвент)") ?></th></tr>
            <tr><td colspan="2"><?=$calculation->film_2 ?></td></tr>
            <tr>
                <td><?= number_format($calculation->thickness_2, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($calculation->density_2, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                <td><?= GetSkiNameExt($calculation->ski_2, $calculation->width_ski_2) ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE && $calculation->laminations_number > 0 && $calculation->customers_material_2 == 1): ?>
            <tr><td colspan="2">Сырьё заказчика</td></tr>
            <?php endif; ?>
            
            <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE && $calculation->laminations_number > 1): ?>
            <tr><th colspan="2">Ламинация 2<?= empty($calculation->laminator_id) ? "" : ($calculation->laminator_id == LAMINATOR_SOLVENT ? " (сольвент)" : " (бессольвент)") ?></th></tr>
            <tr><td colspan="2"><?=$calculation->film_3 ?></td></tr>
            <tr>
                <td><?= number_format($calculation->thickness_3, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($calculation->density_3, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                <td><?= GetSkiNameExt($calculation->ski_3, $calculation->width_ski_3) ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE && $calculation->laminations_number > 1 && $calculation->customers_material_3 == 1): ?>
            <tr><td colspan="2">Сырьё заказчика</td></tr>
            <?php endif; ?>
        </table>
    </div>
    <div class="col-6">
        <table class="calculation-table">
            <tr><td colspan="2"><h2>Характеристики</h2></td></tr>
                <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
            <tr><th>Печатная машина</th><td><?=PRINTER_NAMES[$calculation->machine_id] ?></td></tr>
                <?php
                function GetInkWithCases($param) {
                    if($param < 1) {
                        return "красок";
                    }
                    elseif ($param < 2) {
                        return "краска";
                    }
                    elseif ($param < 5) {
                        return "краски";
                    }
                    else {
                        return "красок";
                    }
                }
                ?>
            <tr><th>Красочность</th><td><?=$calculation->ink_number ?>&nbsp;<?= GetInkWithCases($calculation->ink_number) ?></td></tr>
            <tr><th>Количество новых форм</th><td><?=$new_forms_number ?></td></tr>
                <?php endif; ?>
                <?php if(!empty($calculation->length)): ?>
            <tr><th>Длина этикетки</th><td><?= rtrim(rtrim(number_format($calculation->length, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                <?php endif; ?>
                <?php if(!empty($calculation->stream_width)): ?>
            <tr><th>Ширина ручья</th><td><?= rtrim(rtrim(number_format($calculation->stream_width, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                <?php endif; ?>
                <?php if(!empty($calculation->streams_number)): ?>
            <tr><th>Количество ручьев</th><td><?= $calculation->streams_number ?></td></tr>
                <?php endif; ?>
                <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
            <tr><th>Рапорт</th><td><?= rtrim(rtrim(number_format($calculation->raport, 3, ",", ""), "0"), ",") ?> мм</td></tr>
                <?php endif; ?>
                <?php if(!empty($calculation->number_in_raport)): ?>
            <tr><th>Количество этикеток в рапорте</th><td><?=$calculation->number_in_raport ?></td></tr>
                <?php endif; ?>
                <?php
                if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE):
                ?>
            <tr><th>Фактический зазор</th><td><?= rtrim(rtrim(number_format($calculation_result->gap, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                <?php endif; ?>
                <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE): ?>
            <tr><th>Количество ламинаций</th><td><?=$calculation->laminations_number == 0 ? "нет" : $calculation->laminations_number ?></td></tr>
                <?php endif; ?>
                <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE && $calculation->laminations_number > 0): ?>
            <tr><th>Ширина ламинирующего вала</th><td><?= $calculation->lamination_roller_width ?> мм</td></tr>
                <?php endif; ?>
            <tr><th>Доп. расходы с <?=(empty($calculation->unit) || $calculation->unit == 'kg' ? "кг" : "шт") ?></th><td><?=rtrim(rtrim(number_format($calculation->extra_expense, 2, ",", ""), "0"), ",") ?> руб</td></tr>
            <tr><th>Экосбор с 1 кг</th><td><?=DisplayNumber((($calculation->eco_price_1 * $calculation->GetCurrencyRate($calculation->eco_currency_1, $calculation->usd, $calculation->euro)) + ($calculation->eco_price_2 * $calculation->GetCurrencyRate($calculation->eco_currency_2, $calculation->usd, $calculation->euro)) + ($calculation->eco_price_3 * $calculation->GetCurrencyRate($calculation->eco_currency_3, $calculation->usd, $calculation->euro))) / ((empty($calculation->laminations_number) ? 0 : $calculation->laminations_number) + 1), 2) ?> руб</td></tr>
        </table>
    </div>
</div>
<?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
<p class="font-weight-bold mt-3">Красочность: <?=$calculation->ink_number." ".GetInkWithCases($calculation->ink_number) ?></p>
<?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
<p>Количество форм: Флинт <?=$calculation->cliches_count_flint ?>, Кодак <?=$calculation->cliches_count_kodak ?>, старых <?=$calculation->cliches_count_old ?></p>
<?php endif; ?>

<table class="table w-100">
    <tr>
        <th class="ink">Цветовая схема</th>
        <th class="ink">Цвет</th>
        <th class="ink">Запечатка</th>
        <?php if($calculation->work_type_id == WORK_TYPE_PRINT): ?>
        <th class="ink">Тип полимера</th>
        <th class="ink">Форма</th>
        <?php endif; ?>
    </tr>
    <?php
    for($i=1; $i<=$calculation->ink_number; $i++):
    $ink_var = "ink_$i";
    $color_var = "color_$i";
    $cmyk_var = "cmyk_$i";
    $lacquer_var = "lacquer_$i";
    $percent_var = "percent_$i";
    $cliche_var = "cliche_$i";
    ?>
    <tr>
        <td>
            <?php
            switch ($$ink_var) {
                case INK_CMYK:
                    echo "CMYK";
                    break;
                case INK_PANTON:
                    echo 'Пантон';
                    break;
                case INK_LACQUER:
                    echo 'Лак';
                    break;
                case INK_WHITE:
                    echo 'Белый';
                    break;
            }
            ?>
        </td>
        <td>
            <?php
            if($$ink_var == INK_CMYK) {
                echo $$cmyk_var;
            }
            elseif($$ink_var == INK_PANTON) {
                echo 'P'.$$color_var;
            }
            elseif($$ink_var == INK_LACQUER) {
                switch ($$lacquer_var) {
                    case LACQUER_GLOSSY:
                        echo 'глянцевый';
                        break;
                    case LACQUER_MATTE:
                        echo 'матовый';
                        break;
                }
            }
            ?>
        </td>
        <td><?=$$percent_var ?>%</td>
        <?php if($calculation->work_type_id == WORK_TYPE_PRINT): ?>
        <td>
            <?php
            switch ($$cliche_var) {
                case CLICHE_FLINT:
                    echo 'Флинт';
                    break;
                case CLICHE_KODAK;
                    echo 'Кодак';
                    break;
            }
            ?>
        </td>
        <td>
            <?php
            switch ($$cliche_var) {
                case CLICHE_OLD:
                    echo 'Старая';
                    break;
                default :
                    echo 'Новая';
                    break;
            }
            ?>
        </td>
        <?php endif; ?>
    </tr>
    <?php endfor; ?>
</table>
<div class="row">
    <div class="col-6"></div>
    <div class="col-6">
        <form method="post" class="form-inline">
            <div class="form-check">
                <label class="form-check-label text-nowrap mt-2">
                    <?php
                    $checked = $calculation->cliche_in_price == 1 ? " checked='checked'" : "";
                    ?>
                    <input type="checkbox" class="form-check-input" id="cliche_in_price" name="cliche_in_price" value="on"<?=$checked ?><?=$disabled_attr ?> onchange="javascript: if($(this).is(':checked')) { $('#customer_pays_for_cliche').prop('checked', true); } RecalculateByCliche();" />Включить ПФ в себестоимость
                    <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                    <input type="hidden" id="cliche_in_price_submit" name="cliche_in_price_submit" value="1" />
                    <input type="hidden" name="scroll" />
                </label>
            </div>
        </form>
    </div>
    <div class="col-6"></div>
    <div class="col-6">
        <form method="post" class="form-inline">
            <div class="form-check">
                <label class="form-check-label text-nowrap mt-2 mb-2">
                    <?php
                    $checked = $calculation->customer_pays_for_cliche == 1 ? " checked='checked'" : "";
                    ?>
                    <input type="checkbox" class="form-check-input" id="customer_pays_for_cliche" name="customer_pays_for_cliche" value="on"<?=$checked ?><?=$disabled_attr ?> onchange="javascript: if(!$(this).is(':checked')) { $('#cliche_in_price').prop('checked', false); } RecalculateByCliche();" />Заказчик платит за ПФ
                    <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                    <input type="hidden" id="customer_pays_for_cliche_submit" name="customer_pays_for_cliche_submit" value="1" />
                    <input type="hidden" name="scroll" />
                </label>
            </div>
        </form>
    </div>
</div>
<?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
<div class="row">
    <div class="col-6"></div>
    <div class="col-6" style="border-top: solid 2px lightgray;">
        <form method="post" class="form-inline">
            <div class="form-check">
                <label class="form-check-label text-nowrap mt-2">
                    <?php
                    $checked = $calculation->knife_in_price == 1 ? " checked='checked'" : "";
                    ?>
                    <input type="checkbox" class="form-check-input" id="knife_in_price" name="knife_in_price" value="on"<?=$checked ?><?=$disabled_attr ?> onchange="javascript: if($(this).is(':checked')) { $('#customer_pays_for_knife').prop('checked', true); } RecalculateByKnife();" />Включить нож в себестоимость
                    <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                    <input type="hidden" id="knife_in_price_submit" name="knife_in_price_submit" value="1" />
                    <input type="hidden" name="scroll" />
                </label>
            </div>
        </form>
    </div>
    <div class="col-6"></div>
    <div class="col-6">
        <form method="post" class="form-inline">
            <div class="form-check">
                <label class="form-check-label text-nowrap mt-2">
                    <?php
                    $checked = $calculation->customer_pays_for_knife == 1 ? " checked='checked'" : "";
                    ?>
                    <input type="checkbox" class="form-check-input" id="customer_pays_for_knife" name="customer_pays_for_knife" value="on"<?=$checked ?><?=$disabled_attr ?> onchange="javascript: if(!$(this).is(':checked')) { $('#knife_in_price').prop('checked', false); } RecalculateByKnife();" />Заказчик платит за нож
                    <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                    <input type="hidden" id="customer_pays_for_knife_submit" name="customer_pays_for_knife_submit" value="1" />
                    <input type="hidden" name="scroll" />
                </label>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>