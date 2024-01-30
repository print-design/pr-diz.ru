<?php
class CalculationResult {
    // Результаты вычислений
    public $usd, $euro;
    public $cost, $cost_per_unit, $shipping_cost, $shipping_cost_per_unit, $income, $income_per_unit;
    public $cliche_cost, $shipping_cliche_cost, $income_cliche;
    public $knife_cost, $shipping_knife_cost, $income_knife;
    public $total_weight_dirty;
    public $film_cost_1, $film_cost_per_unit_1, $width_1, $weight_pure_1, $length_pure_1, $weight_dirty_1, $length_dirty_1;
    public $film_cost_2, $film_cost_per_unit_2, $width_2, $weight_pure_2, $length_pure_2, $weight_dirty_2, $length_dirty_2;
    public $film_cost_3, $film_cost_per_unit_3, $width_3, $weight_pure_3, $length_pure_3, $weight_dirty_3, $length_dirty_3;
    public $film_waste_cost_1, $film_waste_weight_1, $ink_cost, $ink_weight, $work_cost_1, $work_time_1;
    public $film_waste_cost_2, $film_waste_weight_2, $glue_cost_2, $glue_expense_2, $work_cost_2, $work_time_2;
    public $film_waste_cost_3, $film_waste_weight_3, $glue_cost_3, $glue_expense_3, $work_cost_3, $work_time_3;
    public $gap, $priladka_printing;
    
    // Данные тех. карты
    public $techmap_id, $techmap_date, $supplier_id, $side, $winding, $winding_unit, $spool, $labels, $package, $photolabel, $roll_type, $comment;
    public $supplier;
    
    // Печать: лицевая, оборотная
    public const SIDE_FRONT = 1;
    public const SIDE_BACK = 2;
    
    // Бирки: Принт-Дизайн, безликие
    public const LABEL_PRINT_DESIGN = 1;
    public const LABEL_FACELESS = 2;
    
    // Упаковка: паллетированная, россыпью, европаллет, коробки
    public const PACKAGE_PALLETED = 1;
    public const PACKAGE_BULK = 2;
    public const PACKAGE_EUROPALLET = 3;
    public const PACKAGE_BOXES = 4;
        
    // Фотометка
    public const PHOTOLABEL_LEFT = "left";
    public const PHOTOLABEL_RIGHT = "right";
    public const PHOTOLABEL_BOTH = "both";
    public const PHOTOLABEL_NONE = "none";
    
    // Данные получены из другой тех. карты
    public const FROM_OTHER_TECHMAP = "from_other_techmap";
    
    // Конструктор
    public function __construct($usd, $euro,
            $cost, $cost_per_unit, $shipping_cost, $shipping_cost_per_unit, $income, $income_per_unit,
            $cliche_cost, $shipping_cliche_cost, $income_cliche,
            $knife_cost, $shipping_knife_cost, $income_knife,
            $total_weight_dirty,
            $film_cost_1, $film_cost_per_unit_1, $width_1, $weight_pure_1, $length_pure_1, $weight_dirty_1, $length_dirty_1,
            $film_cost_2, $film_cost_per_unit_2, $width_2, $weight_pure_2, $length_pure_2, $weight_dirty_2, $length_dirty_2,
            $film_cost_3, $film_cost_per_unit_3, $width_3, $weight_pure_3, $length_pure_3, $weight_dirty_3, $length_dirty_3,
            $film_waste_cost_1, $film_waste_weight_1, $ink_cost, $ink_weight, $work_cost_1, $work_time_1,
            $film_waste_cost_2, $film_waste_weight_2, $glue_cost_2, $glue_expense_2, $work_cost_2, $work_time_2,
            $film_waste_cost_3, $film_waste_weight_3, $glue_cost_3, $glue_expense_3, $work_cost_3, $work_time_3,
            $gap, $priladka_printing, 
            $techmap_id, $techmap_date, $supplier_id, $side, $winding, $winding_unit, $spool, $labels, $package, $photolabel, $roll_type, $comment, 
            $supplier) {
        $this->usd = $usd; // Курс доллара
        $this->euro = $euro; // Курс евро
        
        $this->cost = $cost; // Себестоимость
        $this->cost_per_unit = $cost_per_unit; // Себестоимость за единицу
        $this->shipping_cost = $shipping_cost; // Отгрузочная стоимость
        $this->shipping_cost_per_unit = $shipping_cost_per_unit; // Отгрузочная стоимость за единицу
        $this->income = $income; // Прибыль
        $this->income_per_unit = $income_per_unit; // Прибыль за единицу
        
        $this->cliche_cost = $cliche_cost; // Себестоимость форм
        $this->shipping_cliche_cost = $shipping_cliche_cost; // Отгрузочная стоимость форм
        $this->income_cliche = $income_cliche; // Прибыль ПФ
        
        $this->knife_cost = $knife_cost; // Себестоимость ножа
        $this->shipping_knife_cost = $shipping_knife_cost; // Отгрузочная стоимость ножа
        $this->income_knife = $income_knife; // Прибыль с ножа
        
        $this->total_weight_dirty = $total_weight_dirty; // Общая масса с приладкой
        
        $this->film_cost_1 = $film_cost_1; // Общая стоимость вссех материалов 1
        $this->film_cost_per_unit_1 = $film_cost_per_unit_1; // Масса с приладкой на 1 кг 1
        $this->width_1 = $width_1; // Ширина материала кратная 5, мм 1
        $this->weight_pure_1 = $weight_pure_1; // Масса плёнки чистая, кг 1
        $this->length_pure_1 = $length_pure_1; // Длина плёнки чистая, м 1
        $this->weight_dirty_1 = $weight_dirty_1; // Масса плёнки грязная, кг 1
        $this->length_dirty_1 = $length_dirty_1; // Длина плёнки грязная, кг 1
        
        $this->film_cost_2 = $film_cost_2; // Общая стоимость вссех материалов 2
        $this->film_cost_per_unit_2 = $film_cost_per_unit_2; // Масса с приладкой на 1 кг 2
        $this->width_2 = $width_2; // Ширина материала кратная 5, мм 2
        $this->weight_pure_2 = $weight_pure_2; // Масса плёнки чистая, кг 2
        $this->length_pure_2 = $length_pure_2; // Длина плёнки чистая, м 2
        $this->weight_dirty_2 = $weight_dirty_2; // Масса плёнки грязная, кг 2
        $this->length_dirty_2 = $length_dirty_2; // Длина плёнки грязная, кг 2
        
        $this->film_cost_3 = $film_cost_3; // Общая стоимость вссех материалов 3
        $this->film_cost_per_unit_3 = $film_cost_per_unit_3; // Масса с приладкой на 1 кг 3
        $this->width_3 = $width_3; // Ширина материала кратная 5, мм 3
        $this->weight_pure_3 = $weight_pure_3; // Масса плёнки чистая, кг 3
        $this->length_pure_3 = $length_pure_3; // Длина плёнки чистая, м 3
        $this->weight_dirty_3 = $weight_dirty_3; // Масса плёнки грязная, кг 3
        $this->length_dirty_3 = $length_dirty_3; // Длина плёнки грязная, кг 3
        
        $this->film_waste_cost_1 = $film_waste_cost_1; // Отходы, стоимость 1
        $this->film_waste_weight_1 = $film_waste_weight_1; // Отходы, масса 1
        $this->ink_cost = $ink_cost; // Стоимость красок 1
        $this->ink_weight = $ink_weight; // Масса краски 1
        $this->work_cost_1 = $work_cost_1; // Общая стоимость трудозатрат 1
        $this->work_time_1 = $work_time_1; // Общее время трудозатрат 1
        
        $this->film_waste_cost_2 = $film_waste_cost_2; // Отходы, стоимость 2
        $this->film_waste_weight_2 = $film_waste_weight_2; // Отходы, масса 2
        $this->glue_cost_2 = $glue_cost_2; // Стоимость клея 2
        $this->glue_expense_2 = $glue_expense_2; // Расход клея 2
        $this->work_cost_2 = $work_cost_2; // Общая стоимость трудозатрат 2
        $this->work_time_2 = $work_time_2; // Общее время трудозатрат 2
        
        $this->film_waste_cost_3 = $film_waste_cost_3; // Отходы, стоимость 3
        $this->film_waste_weight_3 = $film_waste_weight_3; // Отходы, масса 3
        $this->glue_cost_3 = $glue_cost_3; // Стоимость клея 3
        $this->glue_expense_3 = $glue_expense_3; // Расход клея 3
        $this->work_cost_3 = $work_cost_3; // Общая стоимость трудозатрат 3
        $this->work_time_3 = $work_time_3; // Общее время трудозатрат 3
        
        $this->gap = $gap; // Фактический зазор между этикетками
        $this->priladka_printing = $priladka_printing; // Метраж приладки одного тиража, м
        
        $this->techmap_id = $techmap_id; // ID тех. карты
        $this->techmap_date = $techmap_date; // Дата тех. карты
        $this->supplier_id = $supplier_id; // ID поставщика материала
        $this->side = $side; // Сторона печати
        $this->winding = $winding; // Намотка до
        $this->winding_unit = $winding_unit; // Намотка до, единица измерения
        $this->spool = $spool; // Шпуля
        $this->labels = $labels; // Бирки
        $this->package = $package; // Упаковка
        $this->photolabel = $photolabel; // Фотометка
        $this->roll_type = $roll_type; // Тип ролика
        $this->comment = $comment; // Комментарий
        
        $this->supplier = $supplier;
    }
    
    // Получение из базы
    public static function Create($id) {
        $sql = "select cr.usd, cr.euro, "
                . "cr.cost, cr.cost_per_unit, cr.shipping_cost, cr.shipping_cost_per_unit, cr.income, cr.income_per_unit, "
                . "cr.cliche_cost, cr.shipping_cliche_cost, cr.income_cliche, "
                . "cr.knife_cost, cr.shipping_knife_cost, cr.income_knife, "
                . "cr.total_weight_dirty, "
                . "cr.film_cost_1, cr.film_cost_per_unit_1, cr.width_1, cr.weight_pure_1, cr.length_pure_1, cr.weight_dirty_1, cr.length_dirty_1, "
                . "cr.film_cost_2, cr.film_cost_per_unit_2, cr.width_2, cr.weight_pure_2, cr.length_pure_2, cr.weight_dirty_2, cr.length_dirty_2, "
                . "cr.film_cost_3, cr.film_cost_per_unit_3, cr.width_3, cr.weight_pure_3, cr.length_pure_3, cr.weight_dirty_3, cr.length_dirty_3, "
                . "cr.film_waste_cost_1, cr.film_waste_weight_1, cr.ink_cost, cr.ink_weight, cr.work_cost_1, cr.work_time_1, "
                . "cr.film_waste_cost_2, cr.film_waste_weight_2, cr.glue_cost_2, cr.glue_expense_2, cr.work_cost_2, cr.work_time_2, "
                . "cr.film_waste_cost_3, cr.film_waste_weight_3, cr.glue_cost_3, cr.glue_expense_3, cr.work_cost_3, cr.work_time_3, "
                . "cr.gap, cr.priladka_printing, "
                . "tm.id techmap_id, tm.date techmap_date, tm.supplier_id, tm.side, tm.winding, tm.winding_unit, tm.spool, tm.labels, tm.package, tm.photolabel, tm.roll_type, tm.comment, "
                . "sup.name supplier "
                . "from calculation c "
                . "inner join calculation_result cr on cr.calculation_id = c.id "
                . "left join techmap tm on tm.calculation_id = c.id "
                . "left join supplier sup on tm.supplier_id = sup.id "
                . "where c.id = $id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            return new CalculationResult($row['usd'], $row['euro'],
                    $row['cost'], $row['cost_per_unit'], $row['shipping_cost'], $row['shipping_cost_per_unit'], $row['income'], $row['income_per_unit'],
                    $row['cliche_cost'], $row['shipping_cliche_cost'], $row['income_cliche'],
                    $row['knife_cost'], $row['shipping_knife_cost'], $row['income_knife'],
                    $row['total_weight_dirty'],
                    $row['film_cost_1'], $row['film_cost_per_unit_1'], $row['width_1'], $row['weight_pure_1'], $row['length_pure_1'], $row['weight_dirty_1'], $row['length_dirty_1'],
                    $row['film_cost_2'], $row['film_cost_per_unit_2'], $row['width_2'], $row['weight_pure_2'], $row['length_pure_2'], $row['weight_dirty_2'], $row['length_dirty_2'],
                    $row['film_cost_3'], $row['film_cost_per_unit_3'], $row['width_3'], $row['weight_pure_3'], $row['length_pure_3'], $row['weight_dirty_3'], $row['length_dirty_3'],
                    $row['film_waste_cost_1'], $row['film_waste_weight_1'], $row['ink_cost'], $row['ink_weight'], $row['work_cost_1'], $row['work_time_1'],
                    $row['film_waste_cost_2'], $row['film_waste_weight_2'], $row['glue_cost_2'], $row['glue_expense_2'], $row['work_cost_2'], $row['work_time_2'],
                    $row['film_waste_cost_3'], $row['film_waste_weight_3'], $row['glue_cost_3'], $row['glue_expense_3'], $row['work_cost_3'], $row['work_time_3'],
                    $row['gap'], $row['priladka_printing'], 
                    $row['techmap_id'], $row['techmap_date'], $row['supplier_id'], $row['side'], $row['winding'], $row['winding_unit'], $row['spool'], $row['labels'], $row['package'], $row['photolabel'], $row['roll_type'], $row['comment'], 
                    $row['supplier']);
        }
        else {
            return "Ошибка при получении результатов рассчёта";
        }
    }
}
?>