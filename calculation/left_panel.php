<div class="row">
    <div class="col-6">
        <table class="calculation-table">
            <tr><td colspan="2"><h2>О заказе</h2></td></tr>
            <tr><th>Заказчик</th><td><?=$customer ?></td></tr>
                <?php if(!empty($last_name) || !empty($first_name)): ?>
            <tr><th>Менеджер</th><td><?=$last_name.(empty($last_name) ? "" : " ").$first_name ?></td></tr>
                <?php endif; ?>
            <tr><th>Тип работы</th><td><?=$work_type ?></td></tr>
                <?php if(!empty($quantity) && !empty($unit)): ?>
            <tr><th>Объем заказа</th><td><?= rtrim(rtrim(number_format($quantity, 2, ",", " "), "0"), ",") ?> <?=$unit == 'kg' ? "кг" : "шт" ?></td></tr>
                <?php endif; ?>
                <?php if(!empty($printings_number)): ?>
            <tr><th>Тиражей</th><td><?=$printings_number ?></td></tr>
                <?php endif; ?>
            <tr><td colspan="2" class="pt-4"><h2>Пленка</h2></td></tr>
            <tr><th colspan="2">Основная пленка</th></tr>
                <?php if(empty($film_name)): ?>
            <tr><td colspan="2"><?=$individual_film_name ?></td></tr>
            <tr>
                <td><?= number_format($individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                <td><?= GetSkiNameExt($ski, $width_ski) ?></td>
            </tr>
                <?php else: ?>
            <tr><td colspan="2"><?=$film_name ?></td></tr>
            <tr>
                <td><?= number_format($thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                <td><?= GetSkiNameExt($ski, $width_ski) ?></td>
            </tr>
                <?php endif; ?>
                <?php if($customers_material == 1): ?>
            <tr><td colspan="2">Сырьё заказчика</td></tr>
                <?php endif; ?>
                <?php if(!empty($lamination1_individual_film_name)): ?>
            <tr><th colspan="2">Ламинация 1</th></tr>
            <tr><td colspan="2"><?=$lamination1_individual_film_name ?></td></tr>
            <tr>
                <td><?= number_format($lamination1_individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination1_individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                <td><?= GetSkiNameExt($lamination1_ski, $lamination1_width_ski) ?></td>
            </tr>
                <?php elseif(!empty($lamination1_film_name)): ?>
            <tr><th colspan="2">Ламинация 1<?= empty($laminator_id) ? "" : ($laminator_id == CalculationBase::SOLVENT_YES ? " (сольвент)" : " (бессольвент)") ?></th></tr>
            <tr><td colspan="2"><?=$lamination1_film_name ?></td></tr>
            <tr>
                <td><?= number_format($lamination1_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination1_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                <td><?= GetSkiNameExt($lamination1_ski, $lamination1_width_ski) ?></td>
            </tr>
                <?php endif; ?>
                <?php if((!empty($lamination1_individual_film_name) || !empty($lamination1_film_name)) && $lamination1_customers_material == 1): ?>
            <tr><td colspan="2">Сырьё заказчика</td></tr>
                <?php endif; ?>
                <?php if(!empty($lamination2_individual_film_name)): ?>
            <tr><th colspan="2">Ламинация 2</th></tr>
            <tr><td colspan="2"><?=$lamination2_individual_film_name ?></td></tr>
            <tr>
                <td><?= number_format($lamination2_individual_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_individual_density, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                <td><?= GetSkiNameExt($lamination2_ski, $lamination2_width_ski) ?></td>
            </tr>
                <?php elseif(!empty($lamination2_film_name)): ?>
            <tr><th colspan="2">Ламинация 2</th></tr>
            <tr><td colspan="2"><?=$lamination2_film_name ?></td></tr>
            <tr>
                <td><?= number_format($lamination2_thickness, 0, ",", " ") ?> мкм &ndash; <span class="text-nowrap"><?= rtrim(rtrim(number_format($lamination2_weight, 2, ",", " "), "0"), ",") ?> г/м<sup>2</sup></span></td>
                <td><?= GetSkiNameExt($lamination2_ski, $lamination2_width_ski) ?></td>
            </tr>
                <?php endif; ?>
                <?php if((!empty($lamination2_individual_film_name) || !empty($lamination2_film_name)) && $lamination2_customers_material == 1): ?>
            <tr><td colspan="2">Сырьё заказчика</td></tr>
                <?php endif; ?>
        </table>
    </div>
    <div class="col-6">
        <table class="calculation-table">
            <tr><td colspan="2"><h2>Характеристики</h2></td></tr>
                <?php if($work_type_id != CalculationBase::WORK_TYPE_NOPRINT): ?>
            <tr><th>Печатная машина</th><td><?=$machine ?></td></tr>
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
            <tr><th>Красочность</th><td><?=$ink_number ?>&nbsp;<?= GetInkWithCases($ink_number) ?></td></tr>
            <tr><th>Количество новых форм</th><td><?=$new_forms_number ?></td></tr>
                <?php endif; ?>
                <?php if(!empty($length)): ?>
            <tr><th>Длина этикетки</th><td><?= rtrim(rtrim(number_format($length, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                <?php endif; ?>
                <?php if(!empty($stream_width)): ?>
            <tr><th>Ширина ручья</th><td><?= rtrim(rtrim(number_format($stream_width, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                <?php endif; ?>
                <?php if(!empty($streams_number)): ?>
            <tr><th>Количество ручьев</th><td><?= $streams_number ?></td></tr>
                <?php endif; ?>
                <?php if($work_type_id != CalculationBase::WORK_TYPE_NOPRINT): ?>
            <tr><th>Рапорт</th><td><?= rtrim(rtrim(number_format($raport, 3, ",", ""), "0"), ",") ?> мм</td></tr>
                <?php endif; ?>
                <?php if(!empty($number_in_raport)): ?>
            <tr><th>Количество этикеток в рапорте</th><td><?=$number_in_raport ?></td></tr>
                <?php endif; ?>
                <?php
                if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE):
                ?>
            <tr><th>Фактический зазор</th><td><?= rtrim(rtrim(number_format($gap, 2, ",", ""), "0"), ",") ?> мм</td></tr>
                <?php endif; ?>
                <?php
                $lamination = "нет";
                if(!empty($lamination1_film_name) || !empty($lamination1_individual_film_name)) $lamination = "1";
                if(!empty($lamination2_film_name) || !empty($lamination2_individual_film_name)) $lamination = "2";
                ?>
                <?php if($work_type_id != CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
            <tr><th>Количество ламинаций</th><td><?=$lamination ?></td></tr>
                <?php endif; ?>
                <?php if(!empty($lamination1_individual_film_name) || !empty($lamination1_film_name)): ?>
            <tr><th>Ширина ламинирующего вала</th><td><?= $lamination_roller_width ?> мм</td></tr>
                <?php endif; ?>
                <?php
                function GetSkiNameExt($param, $param_width) {
                    switch ($param) {
                        case CalculationBase::STANDARD_SKI:
                            return "Стандартные лыжи";
                        case CalculationBase::NONSTANDARD_SKI:
                            return "Ширина $param_width мм";
                        default :
                            return 'Без лыж';
                    }
                }
                ?>
        </table>
    </div>
</div>
<?php
if($work_type_id != CalculationBase::WORK_TYPE_NOPRINT):

// Стоимость форм
$cliche_data = null;

$sql = "select flint_price, flint_currency, kodak_price, kodak_currency, scotch_price, scotch_currency "
        . "from norm_cliche where date <= '$date' order by id desc limit 1";
$fetcher = new Fetcher($sql);

if($row = $fetcher->Fetch()) {
    $cliche_data = new DataCliche($row['flint_price'], $row['flint_currency'], $row['kodak_price'], $row['kodak_currency'], $row['scotch_price'], $row['scotch_currency']);
}

// Высота форм
$cliche_height = $raport + 20;

// Ширина форм
$cliche_width = ($streams_number * $stream_width + 20) + ((!empty($ski) && $ski == CalculationBase::NO_SKI) ? 0 : 20);

// Площадь форм
$cliche_area = $cliche_height * $cliche_width / 100;
?>
<p class="font-weight-bold mt-3">Красочность: <?=$ink_number." ".GetInkWithCases($ink_number) ?></p>
<?php if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE): ?>
<p>Количество форм: Флинт <?=$cliches_count_flint ?>, Кодак <?=$cliches_count_kodak ?>, старых <?=$cliches_count_old ?></p>
<?php endif; ?>

<table class="table w-100">
    <tr>
        <th class="ink">Цветовая схема</th>
        <th class="ink">Цвет</th>
        <th class="ink">Запечатка</th>
        <?php if($work_type_id == CalculationBase::WORK_TYPE_PRINT): ?>
        <th class="ink">Тип полимера</th>
        <th class="ink">Форма</th>
        <?php endif; ?>
        <th class="ink">Себестоимость</th>
    </tr>
    <?php
    for($i=1; $i<=$ink_number; $i++):
    $ink_var = "ink_$i";
    $color_var = "color_$i";
    $cmyk_var = "cmyk_$i";
    $percent_var = "percent_$i";
    $cliche_var = "cliche_$i";
    ?>
    <tr>
        <td>
            <?php
            switch ($$ink_var) {
                case 'cmyk':
                    echo "CMYK";
                    break;
                case 'panton':
                    echo 'Пантон';
                    break;
                case 'lacquer':
                    echo 'Лак';
                    break;
                case  'white':
                    echo 'Белый';
                    break;
            }
            ?>
        </td>
        <td>
            <?php
            if($$ink_var == "cmyk") {
                echo $$cmyk_var;
            }
            elseif($$ink_var == "panton") {
                echo 'P'.$$color_var;
            }
            ?>
        </td>
        <td><?=$$percent_var ?>%</td>
        <?php if($work_type_id == CalculationBase::WORK_TYPE_PRINT): ?>
        <td>
            <?php
            switch ($$cliche_var) {
                case CalculationBase::FLINT:
                    echo 'Флинт';
                    break;
                case CalculationBase::KODAK;
                    echo 'Кодак';
                    break;
            }
            ?>
        </td>
        <td>
            <?php
            switch ($$cliche_var) {
                case CalculationBase::OLD:
                    echo 'Старая';
                    break;
                default :
                    echo 'Новая';
                    break;
            }
            ?>
        </td>
        <?php endif; ?>
        <td class="text-nowrap">
            <?php
            switch ($$cliche_var) {
                case CalculationBase::OLD:
                    echo '0 ₽';
                    break;
                case CalculationBase::FLINT:
                    echo CalculationBase::Display($cliche_area * $cliche_data->flint_price * CalculationBase::GetCurrencyRate($cliche_data->flint_currency, $usd, $euro), 2)." ₽";
                    break;
                case CalculationBase::KODAK:
                    echo CalculationBase::Display($cliche_area * $cliche_data->kodak_price * CalculationBase::GetCurrencyRate($cliche_data->kodak_currency, $usd, $euro), 2)." ₽";
                    break;
            }
            ?>
        </td>
    </tr>
    <?php endfor; ?>
</table>
<div class="row">
    <div class="col-6"></div>
    <div class="col-6">
        <form method="post" class="form-inline">
            <div class="form-check">
                <label class="form-check-label text-nowrap" style="line-height: 25px;">
                    <?php
                    $checked = $cliche_in_price == 1 ? " checked='checked'" : "";
                    ?>
                    <input type="checkbox" class="form-check-input" id="cliche_in_price" name="cliche_in_price" value="on"<?=$checked ?><?=DISABLED_ATTR ?> onchange="javascript: this.form.submit();">Включить ПФ в себестоимость
                    <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                    <input type="hidden" id="cliche_in_price_submit" name="cliche_in_price_submit" value="1" />
                    <input type="hidden" name="scroll" />
                </label>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>