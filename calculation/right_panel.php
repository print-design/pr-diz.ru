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
                <div class="d-table-cell" style="width: 33%;">
                    <div class="row">
                        <div class="col-6">
                            <div class="p-2" style="color: gray; border: solid 1px lightgray; border-radius: 10px; height: 60px; width: 100px;">
                                <div class="text-nowrap" style="font-size: x-small;">Наценка</div>
                                <?php if($status_id == 1): ?>
                                <div class="input-group">
                                    <input type="text" id="extracharge" name="extracharge" class="int-only" style="width: 30px; height: 25px;" value="<?=$extracharge ?>" />
                                    <div class="input-group-append" style="height: 25px;">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <?php else: ?>
                                <span class="text-nowrap"><?=$extracharge ?>%</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2" style="color: gray; border: solid 1px gray; border-radius: 10px; height: 60px; width: 100px;">
                                <div class="text-nowrap" style="font-size: x-small;">Курс евро</div>
                                    <?php
                                    $sql = "select euro from currency order by id desc limit 1";
                                    $fetcher = new Fetcher($sql);
                                    if($row = $fetcher->Fetch()) {
                                        echo number_format($row[0], 2, ',', ' ');
                                    }
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-cell"></div>
            </div>
        </div>
        <div class="mt-3">
            <h2>Стоимость</h2>
        </div>
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell pb-2 pt-2" style="width: 33%;">
                    <h3>Себестоимость</h3>
                    <div>Себестоимость</div>
                    <div class="value mb-2">860 000 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">765 &#8381; за <?=(empty($unit) || $unit == 'kg' ? "кг" : "шт") ?></span></div>
                    <?php if($work_type_id == 2): ?>
                    <div>Себестоимость форм</div>
                    <div class="value mb-2">800 000 &#8381;</div>
                    <?php endif; ?>
                </div>
                <div class="d-table-cell pb-2 pt-2 pl-3" style="width: 33%;">
                    <h3>Отгрузочная стоимость</h3>
                    <div>Отгрузочная стоимость</div>
                    <div class="value">1 200 000 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">236 &#8381; за <?=(empty($unit) || $unit == 'kg' ? "кг" : "шт") ?></span></div>
                </div>
                <div class="d-table-cell"></div>
            </div>
        </div>
        <div class="mt-3">
            <h2>Материалы</h2>
        </div>
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell pb-2 pt-2" style="width: 33%;">
                    <h3>Основная пленка</h3>
                    <div>Закупочная стоимость</div>
                    <div class="value mb-2">800 000 &#8381;</div>
                    <div>Минимальная ширина</div>
                    <div class="value mb-2">800 000 мм</div>
                    <div>Масса без приладки</div>
                    <div class="value mb-2">7 000 кг&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">172 000 м</span></div>
                    <div>Масса с приладкой</div>
                    <div class="value mb-2">8 000 кг&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">192 000 м</span></div>
                </div>
                <div class="d-table-cell pb-2 pt-2 pl-3" style="width: 33%;">
                    <?php if(!empty($lamination1_brand_name)): ?>
                    <h3>Ламинация 1</h3>
                    <div>Закупочная стоимость</div>
                    <div class="value mb-2">800 000 &#8381;</div>
                    <div>Минимальная ширина</div>
                    <div class="value mb-2">800 000 мм</div>
                    <div>Масса без приладки</div>
                    <div class="value mb-2">7 000 кг&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">172 000 м</span></div>
                    <div>Масса с приладкой</div>
                    <div class="value mb-2">8 000 кг&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">192 000 м</span></div>
                    <?php endif; ?>
                </div>
                <div class="d-table-cell pb-2 pt-2 pl-3" style="width: 33%;">
                    <?php if(!empty($lamination2_brand_name)): ?>
                    <h3>Ламинация 2</h3>
                    <div>Закупочная стоимость</div>
                    <div class="value mb-2">800 000 &#8381;</div>
                    <div>Минимальная ширина</div>
                    <div class="value mb-2">800 000 мм</div>
                    <div>Масса без приладки</div>
                    <div class="value mb-2">7 000 кг&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">172 000 м</span></div>
                    <div>Масса с приладкой</div>
                    <div class="value mb-2">8 000 кг&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">192 000 м</span></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        if(!empty($lamination1_brand_name) || !empty($lamination2_brand_name) || $work_type_id == 2):
        ?>
        <button type="button" class="btn btn-light" id="show_costs" onclick="javascript: ShowCosts();"><i class="fa fa-chevron-down"></i>&nbsp;Показать расходы</button>
        <div id="costs" class="d-none">
            <button type="button" class="btn btn-light" id="hide_costs" onclick="javascript: HideCosts();"><i class="fa fa-chevron-up"></i>&nbsp;Скрыть расходы</button>
            <h2 class="mt-2">Расходы</h2>
            <div class="d-table w-100">
                <div class="d-table-row">
                    <div class="d-table-cell pb-2 pt-2" style="width: 33%;">
                        <div>Отходы</div>
                        <div class="value mb-2">1 280 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">4,5 кг</span></div>
                        <?php if($work_type_id == 2): ?>
                        <div>Краска</div>
                        <div class="value mb-2">17 500 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">17,5 кг</span></div>
                        <?php
                        endif;
                        if(!empty($lamination1_brand_name) || !empty($lamination2_brand_name)):
                        ?>
                        <div>Клей</div>
                        <div class="value mb-2">800 000 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">1,0 кг</span></div>
                        <?php
                        endif;
                        if($work_type_id == 2):
                        ?>
                        <div>Печать тиража</div>
                        <div class="value mb-2">470 500 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">6 ч. 30 мин.</span></div>
                        <?php
                        endif;
                        if(!empty($lamination1_brand_name) || !empty($lamination2_brand_name)):
                        ?>
                        <div>Работа ламинатора</div>
                        <div class="value mb-2">1 500 &#8381;&nbsp;&nbsp;&nbsp;<span style="font-weight: normal;">3 часа</span></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-table-cell"></div>
                </div>
            </div>
        </div>
        <?php
        endif;
        ?>
        <div style="clear:both"></div>
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell" style="width: 33%;">
                    <input type="hidden" id="id" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                    <input type="hidden" id="change_status_submit" name="change_status_submit" />
                        <?php if($status_id == 1): ?>
                    <button type="submit" id="status_id" name="status_id" value="2" class="btn btn-outline-dark w-100 mt-3">Отправить КП</button>
                        <?php elseif($status_id == 2): ?>
                    <button type="submit" id="status_id" name="status_id" value="3" class="btn btn-outline-dark w-100 mt-3">Отправить в работу</button>
                        <?php elseif ($status_id == 4): ?>
                    <button type="submit" id="status_id" name="status_id" value="6" class="btn btn-outline-dark w-100 mt-3">Составить тех. карту</button>
                        <?php endif; ?>
                </div>
                <div class="d-table-cell"></div>
            </div>
        </div>
    </form>
</div>