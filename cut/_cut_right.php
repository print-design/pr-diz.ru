<?php
// Печать: лицевая, оборотная
const SIDE_FRONT = 1;
const SIDE_BACK = 2;

// Бирки: Принт-Дизайн, безликие
const LABEL_PRINT_DESIGN = 1;
const LABEL_FACELESS = 2;

// Упаковка: паллетированная, россыпью, европаллет, коробки
const PACKAGE_PALLETED = 1;
const PACKAGE_BULK = 2;
const PACKAGE_EUROPALLET = 3;
const PACKAGE_BOXES = 4;

// Отходы
$waste1 = "";
$waste2 = "";
$waste3 = "";
$waste = "";

if(in_array($calculation->film_1, WASTE_PRESS_FILMS)) {
    $waste1 = WASTE_PRESS;
}
elseif($calculation->film_1 == WASTE_PAPER_FILM) {
    $waste1 = WASTE_PAPER;
}
elseif(empty ($calculation->film_1)) {
    $waste1 = "";
}
else {
    $waste1 = WASTE_KAGAT;
}

if(in_array($calculation->film_2, WASTE_PRESS_FILMS)) {
    $waste2 = WASTE_PRESS;
}
elseif ($calculation->film_2 == WASTE_PAPER_FILM) {
    $waste2 = WASTE_PAPER;
}
elseif(empty ($calculation->film_2)) {
    $waste2 = "";
}
else {
    $waste2 = WASTE_KAGAT;
}

if(in_array($calculation->film_3, WASTE_PRESS_FILMS)) {
    $waste3 = WASTE_PRESS;
}
elseif($calculation->film_3 == WASTE_PAPER_FILM) {
    $waste3 = WASTE_PAPER;
}
elseif(empty ($calculation->film_3)) {
    $waste3 = "";
}
else {
    $waste3 = WASTE_KAGAT;
}

$waste = $waste1;
if(!empty($waste2) && $waste2 != $waste1) $waste = WASTE_KAGAT;
if(!empty($waste3) && $waste3 != $waste2) $waste = WASTE_KAGAT;
?>
<div class="cutter_info">
    <div class="subtitle">Хар-ки</div>
    <div class="subtitle">ИНФОРМАЦИЯ ПО ПЕЧАТИ</div>
    <table>
        <tr>
            <td><?=(empty($calculation->machine_id) ? '' : ' '.PRINTER_NAMES[$calculation->machine_id].' ') ?>Марка мат-ла</td>
            <td><?=$calculation->film_1 ?></td>
        </tr>
        <tr>
            <td>Толщина</td>
            <td>
                <?php
                if(!empty($calculation->thickness_1)) {
                    echo DisplayNumber(floatval($calculation->thickness_1), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(DisplayNumber(floatval($calculation->density_1), 2), "0").' г/м<sup>2</sup>';
                }
                else {
                    echo "0 мкм&nbsp;&ndash;&nbsp;0 г/м<sup>2</sup>";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Ширина мат-ла</td>
            <td><?= DisplayNumber(floatval($calculation_result->width_1), 0) ?> мм</td>
        </tr>
        <tr>
            <td>Метраж на тираж</td>
            <td><?= DisplayNumber(floatval($calculation_result->length_pure_1), 0) ?> м</td>
        </tr>
        <tr>
            <td>Печать</td>
            <td>
                <?php
                switch ($calculation_result->side) {
                    case SIDE_FRONT:
                        echo 'Лицевая';
                        break;
                    case SIDE_BACK:
                        echo 'Оборотная';
                        break;
                    default :
                        echo "Ждем данные";
                        break;
                    }
                ?>
            </td>
        </tr>
        <tr>
            <td>Длина этикетки</td>
            <td><?= rtrim(rtrim(DisplayNumber(floatval($calculation->length), 2), "0"), ",").(empty($calculation->length) ? "" : " мм") ?></td>
        </tr>
        <tr>
            <td>Кол-во ручьёв</td>
            <td><?=$calculation->streams_number ?></td>
        </tr>
        <tr>
            <td>Красочность</td>
            <td><?=$calculation->ink_number ?> кр.</td>
        </tr>
    </table>
    <div class="subtitle">ИНФОРМАЦИЯ ПО ЛАМИНАЦИИ 1</div>
    <table>
        <tr>
            <td>Кол-во ламинаций</td>
            <td><?= empty($calculation->laminations_number) ? "нет" : $calculation->laminations_number ?></td>
        </tr>
        <tr>
            <td>Марка пленки</td>
            <td><?= $calculation->film_2 ?></td>
        </tr>
        <tr>
            <td>Толщина</td>
            <td>
                <?php
                if(!empty($calculation->thickness_2)) {
                    echo DisplayNumber(floatval($calculation->thickness_2), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(DisplayNumber(floatval($calculation->density_2), 2), "0").' г/м<sup>2</sup>';
                }
                else {
                    echo "0 мкм&nbsp;&ndash;&nbsp;0 г/м<sup>2</sup>";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Ширина мат-ла</td>
            <td><?= DisplayNumber(floatval($calculation_result->width_2), 0) ?> мм</td>
        </tr>
    </table>
    <div class="subtitle">ИНФОРМАЦИЯ ПО ЛАМИНАЦИИ 2</div>
    <table>
        <tr>
            <td>Марка пленки</td>
            <td><?= $calculation->film_3 ?></td>
        </tr>
        <tr>
            <td>Толщина</td>
            <td>
                <?php
                if(!empty($calculation->thickness_3)) {
                    echo DisplayNumber(floatval($calculation->thickness_3), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(DisplayNumber(floatval($calculation->density_3), 2), "0").' г/м<sup>2</sup>';
                }
                else {
                    echo "0 мкм&nbsp;&ndash;&nbsp;0 г/м<sup>2</sup>";
                }
                ?>
            </td>
        </tr>
    </table>
    <div class="subtitle">ИНФОРМАЦИЯ ДЛЯ РЕЗЧИКА</div>
    <table>
        <tr>
            <td>Объем заказа</td>
            <td><?= DisplayNumber(intval($calculation->quantity), 0) ?> <?=$calculation->unit == 'kg' ? 'кг' : 'шт' ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= DisplayNumber(floatval($calculation_result->length_pure_1), 0) ?> м</td>
        </tr>
        <tr>
            <td>Отгрузка в</td>
            <td><?=$calculation->unit == 'kg' ? 'Кг' : 'Шт' ?></td>
        </tr>
        <tr>
            <td><?=$calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? "Обр. шир. / Гор. зазор" : "Обрезная ширина" ?></td>
            <td>
                <?php
                if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                    if(empty($calculation->data_gap->gap_stream)) {
                        echo DisplayNumber(intval($calculation->stream_width), 0)." мм";
                    }
                    else {
                        echo DisplayNumber(floatval($calculation->stream_width) + floatval($calculation->data_gap->gap_stream), 2)." / ".DisplayNumber(floatval($calculation->data_gap->gap_stream), 2)." мм";
                    }
                }
                else {
                    echo DisplayNumber(intval($calculation->stream_width), 0)." мм";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Намотка до</td>
            <td>
                <?php
                if(empty($calculation_result->winding)) {
                    echo 'Ждем данные';
                }
                elseif(empty ($calculation_result->winding_unit)) {
                    echo 'Нет данных по кг/мм/м/шт';
                }
                elseif($calculation_result->winding_unit == 'pc') {
                    if(empty($calculation->length)) {
                        echo 'Нет данных по длине этикетки';
                    }
                    else {
                        echo DisplayNumber(floatval($calculation_result->winding) * floatval($calculation->length) / 1000, 0);
                    }
                }
                else {
                    echo DisplayNumber(floatval($calculation_result->winding), 0);
                }                    
                        
                switch ($calculation_result->winding_unit) {
                    case 'kg':
                        echo " кг";
                        break;
                    case 'mm':
                        echo " мм";
                        break;
                    case 'm':
                        echo " м";
                        break;
                    case 'pc':
                        echo " м";
                        break;
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Прим. метраж намотки</td>
            <td>
                <?php
                /* 1) Если намотка до =«кг», то Примерный метраж = (намотка до *1000*1000)/((уд вес пленка 1 + уд вес пленка 2 + уд вес пленка 3)*обрезная ширина))
                * 1) Если намотка до =«кг», то Примерный метраж = (намотка до *1000*1000)/((уд вес пленка 1 + уд вес пленка 2 + уд вес пленка 3)*обрезная ширина))-200
                * 2) Если намотка до = «мм» , то значение = "Нет"
                * 3) Если намотка до = «м», то значение = "Нет"
                * 4) Если намотка до = «шт» , то значение = "Нет" */
                if(empty($calculation_result->winding) || empty($calculation_result->winding_unit)) {
                    echo 'Ждем данные';
                }
                elseif(empty ($calculation->density_1)) {
                    echo 'Нет данных по уд. весу пленки';
                }
                elseif(empty ($calculation_result->width_1)) {
                    echo 'Нет данных по ширине мат-ла';
                }
                elseif($calculation_result->winding_unit == 'kg') {
                    echo DisplayNumber((floatval($calculation_result->winding) * 1000 * 1000) / ((floatval($calculation->density_1) + ($calculation->density_2 === null ? 0 : floatval($calculation->density_2)) + ($calculation->density_3 === null ? 0 : floatval($calculation->density_3))) * floatval($calculation->stream_width)) - 200, 0)." м";
                }
                else {
                    echo 'Нет';
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Шпуля</td>
            <td><?= empty($calculation_result->spool) ? "Ждем данные" : $calculation_result->spool." мм" ?></td>
        </tr>
        <tr>
            <td>Этикеток в 1 м. пог.</td>
            <td>
                <?php
                if(empty($calculation->length)) {
                    echo "";
                }
                elseif($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                    // Делаем новый расчёт (необходимо для получения параметра "количество этикеток в рапорте чистое")
                    echo DisplayNumber(floatval($calculation->number_in_raport_pure) / floatval($calculation->raport) * 1000.0, 4);
                }
                else {
                    echo DisplayNumber(1 / floatval($calculation->length) * 1000, 4);
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Бирки</td>
            <td>
                <?php
                switch ($calculation_result->labels) {
                    case LABEL_PRINT_DESIGN:
                        echo "Принт-Дизайн";
                        break;
                    case LABEL_FACELESS:
                        echo "Безликие";
                        break;
                    default :
                        echo "Ждем данные";
                        break;
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Склейки</td>
            <td>Помечать</td>
        </tr>
        <tr>
            <td>Отходы</td>
            <td><?=$waste ?></td>
        </tr>
        <tr>
            <td>Упаковка</td>
            <td>
                <?php
                switch ($calculation_result->package) {
                    case PACKAGE_PALLETED:
                        echo "Паллетирование";
                        break;
                    case PACKAGE_BULK:
                        echo "Россыпью";
                        break;
                    case PACKAGE_EUROPALLET:
                        echo "Европаллет";
                        break;
                    case PACKAGE_BOXES:
                        echo "Коробки";
                        break;
                    default :
                        echo "Ждем данные";
                        break;
                }
                ?>
            </td>
        </tr>
    </table>
</div>