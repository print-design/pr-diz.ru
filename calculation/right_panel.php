<?php
$calculation_class = "";
                        
if(isset($create_calculation_submit_class) && empty($create_calculation_submit_class)) {
    $calculation_class = " class='d-none'";    
}
?>
<div id="calculation"<?=$calculation_class ?>>
    <h1>Расчет</h1>
    <form method="post">
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell w-50">
                    <div class="p-2 w-75" style="border: solid 1px lightgray; border-radius: 10px; height: 80px;">
                        <div style="font-size: x-small;">Наценка</div>
                            <?php
                            $extracharge_disabled = " disabled='disabled'";
                            if($status_id == 1) {
                                $extracharge_disabled = "";
                            }
                            ?>
                        <input type="text" id="extracharge" name="extracharge" class="int-only mt-1" style="width: 50px;" value="<?=$extracharge ?>"<?=$extracharge_disabled ?> /> %
                    </div>
                </div>
                <div class="d-table-cell w-50">
                    <div class="p-2 w-75" style="color: gray; border: solid 1px gray; border-radius: 10px; height: 80px;">
                        <div style="font-size: x-small;">Курс евро</div>
                        93
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <h2>Себестоимость</h2>
        </div>
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell pb-2 pt-2 w-50">
                    <div style="font-size: small;">Себестоимость</div>
                    <div class="font-weight-bold" style="font-size: large;">1&nbsp;&nbsp;200&nbsp;&nbsp;000&nbsp;&nbsp;<i class="fas fa-ruble-sign" style="font-size: medium;"></i></div>
                </div>
                <div class="d-table-cell pb-2 pt-2 pl-3">
                    <div style="font-size: small;">Себестоимость, 1 <span class="unit_name"><?=(empty($unit) || $unit == 'kg' ? "кг" : "шт") ?></span></div>
                    <div class="font-weight-bold" style="font-size: large;">765&nbsp;&nbsp;<i class="fas fa-ruble-sign" style="font-size: medium;"></i></div>
                </div>
            </div>
            <?php if($work_type_id == 2): ?>
            <div class="d-table-row">
                <div class="d-table-cell pb-2 pt-2">
                    <div style="font-size: small;">Себестоимость форм</div>
                    <div class="font-weight-bold" style="font-size: large;">800&nbsp;&nbsp;000&nbsp;&nbsp;<i class="fas fa-ruble-sign" style="font-size: medium;"></i></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="mt-3">
            <h2>Отгрузочная стоимость</h2>
        </div>
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell pb-2 pt-2 w-50">
                    <div style="font-size: small;">Отгрузочная стоимость</div>
                    <div class="font-weight-bold" style="font-size: large;">800&nbsp;&nbsp;000&nbsp;&nbsp;<i class="fas fa-ruble-sign" style="font-size: medium;"></i></div>
                </div>
                <div class="d-table-cell pb-2 pt-2 pl-3">
                    <div style="font-size: small;">Отгрузочная стоимость, 1 <span class="unit_name"><?=(empty($unit) || $unit == 'kg' ? "кг" : "шт") ?></span></div>
                    <div class="font-weight-bold" style="font-size: large;">978&nbsp;&nbsp;<i class="fas fa-ruble-sign" style="font-size: medium;"></i></div>
                </div>
            </div>
        </div>
        <?php
        if(!empty($lamination1_brand_name) || !empty($lamination2_brand_name) || $work_type_id == 2):
        ?>
        <button type="button" class="btn btn-light" id="show_costs" onclick="javascript: ShowCosts();"><i class="fa fa-chevron-down"></i>&nbsp;Показать расходы</button>
        <div id="costs" class="d-none">
            <button type="button" class="btn btn-light" id="hide_costs" onclick="javascript: HideCosts();"><i class="fa fa-chevron-up"></i>&nbsp;Скрыть расходы</button>
            <div class="d-table w-100">
                <div class="d-table-row">
                    <div class="d-table-cell pb-2 pt-2 w-50">
                        <div style="font-size: small;">Отходы</div>
                        <div class="font-weight-bold" style="font-size: large;">1&nbsp;&nbsp;280&nbsp;&nbsp;<i class="fas fa-ruble-sign" style="font-size: medium;"></i></div>
                    </div>
                    <div class="d-table-cell pb-2 pt-2 pl-3 w-50">
                        <br />
                        <div class="font-weight-bold" style="font-size: large;">4,5 кг</div>
                    </div>
                </div>
                <?php if($work_type_id == 2): ?>
                <div class="d-table-row">
                    <div class="d-table-cell pb-2 pt-2">
                        <div style="font-size: small;">Краска</div>
                        <div class="font-weight-bold" style="font-size: large;">17&nbsp;&nbsp;500&nbsp;&nbsp;<i class="fas fa-ruble-sign" style="font-size: medium;"></i></div>
                    </div>
                    <div class="d-table-cell pb-2 pt-2 pl-3">
                        <br />
                        <div class="font-weight-bold" style="font-size: large;">17,5 кг</div>
                    </div>
                </div>
                <?php
                endif;
                if(!empty($lamination1_brand_name) || !empty($lamination2_brand_name)):
                ?>
                <div class="d-table-row">
                    <div class="d-table-cell pb-2 pt-2">
                        <div style="font-size: small;">Клей</div>
                        <div class="font-weight-bold" style="font-size: large;">800&nbsp;&nbsp;000&nbsp;&nbsp;<i class="fas fa-ruble-sign" style="font-size: medium;"></i></div>
                    </div>
                    <div class="d-table-cell pb-2 pt-2 pl-3">
                        <br />
                        <div class="font-weight-bold" style="font-size: large;">1,0 кг</div>
                    </div>
                </div>
                <?php
                endif;
                if($work_type_id == 2):
                ?>
                <div class="d-table-row">
                    <div class="d-table-cell pb-2 pt-2">
                        <div style="font-size: small;">Печать тиража</div>
                        <div class="font-weight-bold" style="font-size: large;">470&nbsp;&nbsp;500&nbsp;&nbsp;<i class="fas fa-ruble-sign" style="font-size: medium;"></i></div>
                    </div>
                    <div class="d-table-cell pb-2 pt-2 pl-3">
                        <br />
                        <div class="font-weight-bold" style="font-size: large;">6 часов 30 минут</div>
                    </div>
                </div>
                <?php
                endif;
                if(!empty($lamination1_brand_name) || !empty($lamination2_brand_name)):
                ?>
                <div class="d-table-row">
                    <div class="d-table-cell pb-2 pt-2">
                        <div style="font-size: small;">Работа ламинатора</div>
                        <div class="font-weight-bold">230 руб.</div>
                    </div>
                    <div class="d-table-cell pb-2 pt-2 pl-3">
                        <br />
                        <div class="font-weight-bold" style="font-size: large;">1,5 ч</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        endif;
        ?>
        <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
        <input type="hidden" id="change_status_submit" name="change_status_submit" />
            <?php if($status_id == 1): ?>
        <button type="submit" id="status_id" name="status_id" value="2" class="btn btn-outline-dark w-75 mt-3">Отправить КП</button>
            <?php elseif($status_id == 2): ?>
        <button type="submit" id="status_id" name="status_id" value="3" class="btn btn-outline-dark w-75 mt-3">Отправить в работу</button>
            <?php elseif ($status_id == 4): ?>
        <button type="submit" id="status_id" name="status_id" value="6" class="btn btn-outline-dark w-75 mt-3">Составить тех. карту</button>
            <?php endif; ?>
    </form>
</div>