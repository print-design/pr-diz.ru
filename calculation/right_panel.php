<div id="calculation">
    <h1>Расчет</h1>
    <div class="d-table w-100">
        <div class="d-table-row">
            <div class="d-table-cell pb-2 pt-2">
                <div style="font-size: x-small;">Наценка</div>
                10%
            </div>
            <div class="d-table-cell pb-2 pt-2 pl-3" style="color: gray; border: solid 1px gray; border-radius: 10px;">
                <div style="font-size: x-small;">Курс евро</div>
                93
            </div>
        </div>
        <div class="d-table-row">
            <div class="d-table-cell pb-2 pt-2">
                Себестоимость
                <div class="font-weight-bold" style="font-size: large;">1 200 000 руб.</div>
            </div>
            <div class="d-table-cell pb-2 pt-2 pl-3">
                За 1 кг
                <div class="font-weight-bold" style="font-size: large;">765 руб.</div>
            </div>
        </div>
        <div class="d-table-row">
            <div class="d-table-cell pb-2 pt-2">
                Отгрузочная стоимость
                <div class="font-weight-bold" style="font-size: large;">800 000 руб.</div>
            </div>
            <div class="d-table-cell pb-2 pt-2 pl-3">
                За 1 кг
                <div class="font-weight-bold" style="font-size: large;">978 руб.</div>
            </div>
        </div>
    </div>
        <?php
        if(!empty($lamination1_brand_name) || !empty($lamination2_brand_name)):
        ?>
    <button type="button" class="btn btn-light" id="show_costs" onclick="javascript: ShowCosts();"><i class="fa fa-chevron-down"></i>&nbsp;Показать расходы</button>
    <div id="costs" class="d-none">
        <button type="button" class="btn btn-light" id="hide_costs" onclick="javascript: HideCosts();"><i class="fa fa-chevron-up"></i>&nbsp;Скрыть расходы</button>
        <div class="d-table w-100">
            <div class="d-table-row">
                <div class="d-table-cell pb-2 pt-2">
                    Отходы
                    <div class="font-weight-bold" style="font-size: large;">1 280 руб.</div>
                </div>
                <div class="d-table-cell pb-2 pt-2 pl-3">
                    <br />
                    <div class="font-weight-bold" style="font-size: large;">4,5 кг.</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-2 pt-2">
                    Клей
                    <div class="font-weight-bold" style="font-size: large;">800 000 руб.</div>
                </div>
                <div class="d-table-cell pb-2 pt-2 pl-3">
                    <br />
                    <div class="font-weight-bold" style="font-size: large;">1,0 кг.</div>
                </div>
            </div>
            <div class="d-table-row">
                <div class="d-table-cell pb-2 pt-2">
                    Работа ламинатора
                    <div class="font-weight-bold">230 руб.</div>
                </div>
                <div class="d-table-cell pb-2 pt-2 pl-3">
                    <br />
                    <div class="font-weight-bold" style="font-size: large;">1,5 ч.</div>
                </div>
            </div>
        </div>
    </div>
        <?php
        endif;
        ?>
    <form method="post">
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