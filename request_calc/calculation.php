<?php
class CalculationItem {
    public $name;
    public $value;
    public $display;
    public $formula;
    public $comment;
    
    public function __construct($name, $value, $formula, $comment) {
        $this->name = $name;
        $this->value = $value;
        $this->formula = $formula;
        $this->comment = $comment;
        
        if(is_float($value) || is_double($value)) {
            $this->display = number_format($value, 2, ",", " ");
        }
        elseif(is_string($value)) {
            $this->display = str_replace(".", ",", $value);
        }
        else {
            $this->display = $value;
        }
    }
}

class TuningData {
    public $time;
    public $length;
    public $waste_percent;
    
    public function __construct($time, $length, $waste_percent) {
        $this->time = $time;
        $this->length = $length;
        $this->waste_percent = $waste_percent;
    }
}

class MachineData {
    public $price;
    public $speed;
    public $max_width;
    
    public function __construct($price, $speed, $max_width) {
        $this->price = $price;
        $this->speed = $speed;
        $this->max_width = $max_width;
    }
}

class InkData {
    public $c;
    public $c_currency;
    public $c_expense;
    public $m;
    public $m_currency;
    public $m_expense;
    public $y;
    public $y_currency;
    public $y_expense;
    public $k;
    public $k_currency;
    public $k_expense;
    public $white;
    public $white_currency;
    public $white_expense;
    public $panton;
    public $panton_currency;
    public $panton_expense;
    public $lacquer;
    public $lacquer_currency;
    public $lacquer_expense;
    public $solvent_etoxipropanol;
    public $solvent_etoxipropanol_currency;
    public $solvent_flexol82;
    public $solvent_flexol82_currency;
    public $solvent_part;
    public $min_price;
    
    public function __construct($c, $c_currency, $c_expense, $m, $m_currency, $m_expense, $y, $y_currency, $y_expense, $k, $k_currency, $k_expense, $white, $white_currency, $white_expense, $panton, $panton_currency, $panton_expense, $lacquer, $lacquer_currency, $lacquer_expense, $solvent_etoxipropanol, $solvent_etoxipropanol_currency, $solvent_flexol82, $solvent_flexol82_currency, $solvent_part, $min_price) {
        $this->c = $c;
        $this->c_currency = $c_currency;
        $this->c_expense = $c_expense;
        $this->m = $m;
        $this->m_currency = $m_currency;
        $this->m_expense = $m_expense;
        $this->y = $y;
        $this->y_currency = $y_currency;
        $this->y_expense = $y_expense;
        $this->k = $k;
        $this->k_currency = $k_currency;
        $this->k_expense = $k_expense;
        $this->white = $white;
        $this->white_currency = $white_currency;
        $this->white_expense = $white_expense;
        $this->panton = $panton;
        $this->panton_currency = $panton_currency;
        $this->panton_expense = $panton_expense;
        $this->lacquer = $lacquer;
        $this->lacquer_currency = $lacquer_currency;
        $this->lacquer_expense = $lacquer_expense;
        $this->solvent_etoxipropanol = $solvent_etoxipropanol;
        $this->solvent_etoxipropanol_currency = $solvent_etoxipropanol_currency;
        $this->solvent_flexol82 = $solvent_flexol82;
        $this->solvent_flexol82_currency = $solvent_flexol82_currency;
        $this->solvent_part = $solvent_part;
        $this->min_price = $min_price;
    }
}

class GlueData {
    public $glue;
    public $glue_currency;
    public $glue_expense;
    public $glue_expense_pet;
    public $solvent;
    public $solvent_currency;
    public $solvent_part;
    
    public function __construct($glue, $glue_currency, $glue_expense, $glue_expense_pet, $solvent, $solvent_currency, $solvent_part) {
        $this->glue = $glue;
        $this->glue_currency = $glue_currency;
        $this->glue_expense = $glue_expense;
        $this->glue_expense_pet = $glue_expense_pet;
        $this->solvent = $solvent;
        $this->solvent_currency = $solvent_currency;
        $this->solvent_part = $solvent_part;
    }
}

class PriceData {
    public $value;
    public $currency;
    
    public function __construct($value, $currency) {
        $this->value = $value;
        $this->currency = $currency;
    }
}

class Calculation {
    // Типы работы
    const WORK_TYPE_NOPRINT = 1;
    const WORK_TYPE_PRINT = 2;
    
    // Единицы размера тиража
    const KG = 'kg';
    const PIECES = 'pieces';
    
    // Лыжи
    const NO_SKI = 0;
    const STANDARD_SKI = 1;
    const NONSTANDARD_SKI = 2;

    // Валюты
    const USD = "usd";
    const EURO = "euro";
    
    // Краски
    const CMYK = "cmyk";
    const PANTON = "panton";
    const WHITE = "white";
    const LACQUER = "lacquer";
    
    // CMYK
    const CYAN = "cyan";
    const MAGENDA = "magenta";
    const YELLOW = "yellow";
    const KONTUR = "kontur";
    
    // Формы
    const OLD = "old";
    const FLINT = "flint";
    const KODAK = "kodak";
    const TVER = "tver";
    
    // Машины
    const COMIFLEX = 'comiflex';

    function GetWidth($ski, $streams_number, $stream_width, $width_ski) {
        $result = 0;
    
        switch($ski) {
            case self::NO_SKI:
                $result = $streams_number * $stream_width;
                break;
        
            case self::STANDARD_SKI:
                $result = $streams_number * $stream_width + 20;
                break;
        
            case self::NONSTANDARD_SKI:
                $result = $width_ski;
                break;
        }
    
        return $result;
    }
    
    function GetWidthFormula($ski, $streams_number, $stream_width, $width_ski) {
        $result = "";
    
        switch($ski) {
            case self::NO_SKI:
                $result = "$streams_number * $stream_width";
                break;
        
            case self::STANDARD_SKI:
                $result = "$streams_number * $stream_width + 20";
                break;
        
            case self::NONSTANDARD_SKI:
                $result = "$width_ski";
                break;
        }
    
        return $result;
    }
    
    function GetWidthComment($ski) {
        $result = "";
    
        switch($ski) {
            case self::NO_SKI:
                $result = "количество ручьёв * ширина ручья";
                break;
        
            case self::STANDARD_SKI:
                $result = "количество ручьёв * ширина ручья + 20 мм";
                break;
        
            case self::NONSTANDARD_SKI:
                $result = "вводится вручную";
                break;
        }
    
        return $result;
    }
    
    function GetCurrencyRate($currency, $usd, $euro) {
        switch($currency) {
            case self::USD:
                return $usd;
            
            case self::EURO:
                return $euro;
            
            default :
                return 1;
        }
    }
    
    function GetInkPrice($ink, $cmyk, $c, $c_currency, $m, $m_currency, $y, $y_currency, $k, $k_currency, $panton, $panton_currency, $white, $white_currency, $lacquer, $lacquer_currency) {
        switch ($ink) {
            case self::CMYK:
                switch ($cmyk) {
                    case self::CYAN:
                        return new PriceData($c, $c_currency);
                        
                    case self::MAGENDA:
                        return new PriceData($m, $m_currency);
                        
                    case self::YELLOW:
                        return new PriceData($y, $y_currency);
                        
                    case self::KONTUR:
                        return new PriceData($k, $k_currency);
                        
                    default :
                        return null;
                }
                
            case self::PANTON:
                return new PriceData($panton, $panton_currency);
                
            case self::WHITE:
                return new PriceData($white, $white_currency);
                
            case self::LACQUER:
                return new PriceData($lacquer, $lacquer_currency);
                
            default :
                return null;
        }
    }
    
    function GetInkExpense($ink, $cmyk, $c_expense, $m_expense, $y_expense, $k_expense, $panton_expense, $white_expense, $lacquer_expense) {
        switch ($ink) {
            case self::CMYK:
                switch ($cmyk) {
                    case self::CYAN:
                        return $c_expense;
                        
                    case self::MAGENDA:
                        return $m_expense;
                        
                    case self::YELLOW:
                        return $y_expense;
                        
                    case self::KONTUR:
                        return $k_expense;
                }
            case self::PANTON:
                return $panton_expense;
                
            case self::WHITE:
                return $white_expense;
                
            case self::LACQUER:
                return $lacquer_expense;
                
            default :
                return null;
        }
    }
    
    function Display($value) {
        if(is_float($value) || is_double($value)) {
            return number_format($value, 2, ",", " ");
        }
        elseif(is_string($value)) {
            return str_replace(".", ",", $value);
        }
        else {
            return $value;
        }
    }

    public $laminations_number = 0;
    public /*CalculationItem*/ $weight;
    public /*CalculationItem*/ $width, $lamination1_width, $lamination2_width;
    public /*CalculationItem*/ $m2pure;
    public /*CalculationItem*/ $mpogpure;
    public /*CalculationItem*/ $waste_length, $lamination1_waste_length, $lamination2_waste_length;
    public /*CalculationItem*/ $mpogdirty, $lamination1_mpogdirty, $lamination2_mpogdirty;
    public /*CalculationItem*/ $m2dirty, $lamination1_m2dirty, $lamination2_m2dirty;
    public /*CalculationItem*/ $mpure, $lamination1_mpure, $lamination2_mpure;
    public /*CalculationItem*/ $lengthpure, $lamination1_lengthpure, $lamination2_lengthpure;
    public /*CalculationItem*/ $mdirty, $lamination1_mdirty, $lamination2_mdirty;
    public /*CalculationItem*/ $lengthdirty, $lamination1_lengthdirty, $lamination2_lengthdirty;
    public /*CalculationItem*/ $film_price, $lamination1_film_price, $lamination2_film_price;
    public /*CalculationItem*/ $tuning_time, $lamination1_tuning_time, $lamination2_tuning_time;
    public /*CalculationItem*/ $print_time, $lamination1_time, $lamination2_time;
    public /*CalculationItem*/ $work_time, $lamination1_work_time, $lamination2_work_time;
    public /*CalculationItem*/ $work_price, $lamination1_work_price, $lamination2_work_price;
    public /*CalculationItem*/ $print_area;
    public /*CalculationItem*/ $ink_1kg_mix_weight; // расход КраскаСмеси на 1 кг краски
    public /*CalculationItem*/ $ink_solvent_kg_price; // цена 1 кг чистого раствортеля для краски
    public $ink_kg_prices; // цена 1 кг чистой краски
    public $mix_ink_kg_prices; // цена 1 кг КраскаСмеси
    public $ink_expenses; // расход КраскаСмеси
    public $ink_prices; // стоимость КраскаСмеси
    public /*CalculationItem*/ $glue_kg_weight; // расход КлееСмеси на 1 кг клея
    public /*CalculationItem*/ $glue_kg_price; // цена 1 кг чистого клея
    public /*CalculationItem*/ $glue_solvent_kg_price; // цена 1 кг чистого растворителя для клея
    public /*CalculationItem*/ $mix_glue_kg_price; // цена 1 кг КлееСмеси
    public /*CalculationItem*/ $glue_area1;
    public /*CalculationItem*/ $glue_area2;
    public /*CalculationItem*/ $glue_expense1;
    public /*CalculationItem*/ $glue_expense2;
    public /*CalculationItem*/ $glue_price1;
    public /*CalculationItem*/ $glue_price2;

    public function __construct(TuningData $tuning_data, 
            TuningData $laminator_tuning_data,
            MachineData $machine_data,
            MachineData $laminator_machine_data,
            InkData $ink_data,
            GlueData $glue_data,
            $usd, // Курс доллара
            $euro, // Курс евро
            $unit, // Кг или шт
            $quantity, // Размер тиража в кг или шт
            $work_type_id, // Тип работы: с печатью или без печати
        
            $film, // Основная пленка, марка
            $thickness, // Основная пленка, толщина, мкм
            $density, // Основная пленка, плотность, г/м2
            $price, // Основная пленка, цена
            $currency, // Основная пленка, валюта
            $customers_material, // Основная плёнка, другая, материал заказчика
            $ski, // Основная пленка, лыжи
            $width_ski, // Основная пленка, ширина пленки, мм
        
            $lamination1_film, // Ламинация 1, марка
            $lamination1_thickness, // Ламинация 1, толщина, мкм
            $lamination1_density, // Ламинация 1, плотность, г/м2
            $lamination1_price, // Ламинация 1, цена
            $lamination1_currency, // Ламинация 1, валюта
            $lamination1_customers_material, // Ламинация 1, другая, материал заказчика
            $lamination1_ski, // Ламинация 1, лыжи
            $lamination1_width_ski, // Ламинация 1, ширина пленки, мм
        
            $lamination2_film, // Ламинация 2, марка
            $lamination2_thickness, // Ламинация 2, толщина, мкм
            $lamination2_density, // Ламинация 2, плотность, г/м2
            $lamination2_price, // Ламинация 2, цена
            $lamination2_currency, // Ламинация 2, валюта
            $lamination2_customers_material, // Ламинация 2, другая, уд. вес
            $lamination2_ski, // Ламинация 2, лыжи
            $lamination2_width_ski,  // Ламинация 2, ширина пленки, мм
        
            $machine_id, // Машина
            $machine_shortname, // Короткое наименование машины
            $stream_width, // Ширина ручья, мм
            $streams_number, // Количество ручьёв
            $raport, // Рапорт
            $lamination_roller_width, // Ширина ламинирующего вала
            $ink_number, // Красочность
            
            $ink_1, $ink_2, $ink_3, $ink_4, $ink_5, $ink_6, $ink_7, $ink_8, 
            $color_1, $color_2, $color_3, $color_4, $color_5, $color_6, $color_7, $color_8, 
            $cmyk_1, $cmyk_2, $cmyk_3, $cmyk_4, $cmyk_5, $cmyk_6, $cmyk_7, $cmyk_8, 
            $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, 
            $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8
            ) {
        if(!empty($lamination2_film) && !empty($lamination2_thickness) && !empty($lamination2_density)) {
            $this->laminations_number = 2;
        }
        elseif(!empty ($lamination1_film) && !empty ($lamination1_thickness) && !empty ($lamination1_density)) {
            $this->laminations_number = 1;
        }
        
        // Масса тиража, кг
        if($unit == self::KG) {
            $this->weight = new CalculationItem("Масса тиража, кг", $quantity, "|= $quantity", "размер тиража в кг");
        }
        else {
            exit("Расчёт в штуках ещё не готов");
        }

        // Ширина материала, мм
        $this->width = new CalculationItem("Ширина материала (осн), мм", $this->GetWidth($ski, $streams_number, $stream_width, $width_ski), "|= ".$this->GetWidthFormula($ski, $streams_number, $stream_width, $width_ski), $this->GetWidthComment($ski));
        
        if($this->laminations_number > 0) {
            $this->lamination1_width = new CalculationItem("Ширина материала (лам 1), мм", $this->GetWidth($lamination1_ski, $streams_number, $stream_width, $lamination1_width_ski), "|= ".$this->GetWidthFormula($lamination1_ski, $streams_number, $stream_width, $lamination1_width_ski), $this->GetWidthComment($lamination1_ski));
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_width = new CalculationItem("Ширина материала (лам 2), мм", $this->GetWidth($lamination2_ski, $streams_number, $stream_width, $lamination2_width_ski), "|= ".$this->GetWidthFormula($lamination2_ski, $streams_number, $stream_width, $lamination2_width_ski), $this->GetWidthComment($lamination2_ski));
        }

        // Площадь чистая
        $this->m2pure = new CalculationItem("М2 чистые, м2", $this->weight->value * 1000 / ($density + (empty($lamination1_density) ? 0 : $lamination1_density) + (empty($lamination2_density) ? 0 : $lamination2_density)), "|= ".$this->weight->display." * 1000 / (".$this->Display($density)." + ".(empty($lamination1_density) ? 0 : $this->Display($lamination1_density))." + ".(empty($lamination2_density) ? 0 : $this->Display($lamination2_density)).")", "масса тиража * 1000 / (уд. вес осн + уд. вес лам 1 + уд. вес лам 2)");
        
        // Метры погонные чистые
        $this->mpogpure = new CalculationItem("М пог. чистые, м", $this->m2pure->value / ($streams_number * $stream_width / 1000), "|= ".$this->m2pure->display." / ($streams_number * $stream_width / 1000)", "м2 чистые / (количество ручьёв * ширина ручья / 1000)");
        
        // СтартСтопОтход
        if(!empty($machine_id)) {
            $this->waste_length = new CalculationItem("СтартСтопОтход (осн), м", $tuning_data->waste_percent * $this->mpogpure->value / 100, "|= ".$this->Display($tuning_data->waste_percent)." * ".$this->mpogpure->display." / 100", "СтартСтопОтход печати * м. пог. чистые / 100");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_waste_length = new CalculationItem("СтартСтопОтход (лам 1), м", $laminator_tuning_data->waste_percent * $this->mpogpure->value / 100, "|= ".$this->Display($laminator_tuning_data->waste_percent)." * ".$this->mpogpure->display." / 100", "СтартСтопОтход ламинации * м. пог. чистые / 100");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_waste_length = new CalculationItem("СтартСтопОтход (лам 2), м", $laminator_tuning_data->waste_percent * $this->mpogpure->value / 100, "|= ".$this->Display($laminator_tuning_data->waste_percent)." * ".$this->mpogpure->display." / 100", "СтартСтопОтход ламинации * м. пог. чистые / 100");
        }
        
        // Метры погонные грязные
        if(!empty($machine_id) && $this->laminations_number > 0) {
            $this->mpogdirty = new CalculationItem("М. пог. грязные (осн), м", $this->mpogpure->value + ($ink_number * $tuning_data->length) + ($this->laminations_number * $laminator_tuning_data->length) + $this->waste_length->value, "|= ".$this->mpogpure->display." + (".$ink_number." * ".$this->Display($tuning_data->length).") + (".$this->laminations_number." * ".$this->Display($laminator_tuning_data->length).") + ".$this->waste_length->display, "м. пог. чистые + (красочность * метраж приладки 1 краски) + (количество ламинаций * метраж приладки ламинации) + СтартСтопОтход осн");
        }
        elseif (!empty ($machine_id)) {
            $this->mpogdirty = new CalculationItem("М. пог. грязные (осн), м", $this->mpogpure->value + ($ink_number * $tuning_data->length) + $this->waste_length->value, "|= ".$this->mpogpure->display." + (".$ink_number." * ".$this->Display($tuning_data->length).") + ".$this->waste_length->display, "м. пог. чистые + (красочность * метраж приладки 1 краски) + СтартСтопОтход осн");
        }
        elseif ($this->laminations_number > 0) {
            $this->mpogdirty = new CalculationItem("М. пог. грязные (осн), м", $this->mpogpure->value + ($this->laminations_number * $laminator_tuning_data->length), "|= ".$this->mpogpure->display." + (".$this->laminations_number." * ".$this->Display($laminator_tuning_data->length).")", "м. пог. чистые + (количество ламинаций * метраж приладки ламинации)");
        }
        else {
            $this->mpogdirty = new CalculationItem("М. пог. грязные (осн), м", $this->mpogpure->value, "|= ".$this->mpogpure->display, "м. пог. чистые");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_mpogdirty = new CalculationItem("М. пог. грязные (лам 1), м", $this->mpogpure->value + ($this->laminations_number * $laminator_tuning_data->length) + $this->lamination1_waste_length->value, "|= ".$this->mpogpure->display." + (".$this->laminations_number." * ".$this->Display($laminator_tuning_data->length).") + ".$this->lamination1_waste_length->display, "м. пог. чистые + (количество ламинаций * метраж приладки ламинации) + СтартСтопОтход лам 1");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_mpogdirty = new CalculationItem("М. пог. грязные (лам 2), м", $this->mpogpure->value + ($this->laminations_number * $laminator_tuning_data->length) + $this->lamination2_waste_length->value, "|= ".$this->mpogpure->display." + (".$this->laminations_number." * ".$this->Display($laminator_tuning_data->length).") + ".$this->lamination2_waste_length->display, "м. пог. чистые + (количество ламинаций * метраж приладки ламинации) + СтартСтопОтход лам 2");
        }
        
        // Площадь грязная
        $this->m2dirty = new CalculationItem("М2 грязные (осн), м2", $this->mpogdirty->value * $this->width->value / 1000, "|= ".$this->mpogdirty->display." * ".$this->width->display." / 1000", "м. пог. грязные * ширина материала осн / 1000");
        
        if($this->laminations_number > 0) {
            $this->lamination1_m2dirty = new CalculationItem("М2 грязные (лам 1), м2", $this->lamination1_mpogdirty->value * $this->lamination1_width->value / 1000, "|= ".$this->lamination1_mpogdirty->display." * ".$this->lamination1_width->display." / 1000", "м. пог. грязные * ширина материала лам 1 / 1000");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_m2dirty = new CalculationItem("М2 грязные (лам 2), м2", $this->lamination2_mpogdirty->value * $this->lamination2_width->value / 1000, "|= ".$this->lamination2_mpogdirty->display." * ".$this->lamination2_width->display." / 1000", "м. пог. грязные * ширина материала лам 2 / 1000");
        }
    
        //****************************************
        // Массы и длины плёнок
        //****************************************
    
        // Масса плёнки чистая (без приладки), кг
        $this->mpure = new CalculationItem("Масса плёнки чистая (осн), кг", $this->mpogpure->value * $this->width->value / 1000 * $density / 1000, "|= ".$this->mpogpure->display." * ".$this->width->display." / 1000 * ".$this->Display($density)." / 1000", "м. пог. чистые * ширина материала осн / 1000 * уд. вес осн / 1000");
    
        if($this->laminations_number > 0) {
            $this->lamination1_mpure = new CalculationItem("Масса плёнки чистая (лам 1), кг", $this->mpogpure->value * $this->lamination1_width->value / 1000 * $lamination1_density / 1000, "|= ".$this->mpogpure->display." * ".$this->lamination1_width->display." / 1000 * ".$this->Display($lamination1_density)." / 1000", "м. пог. чистые * ширина материала лам 1 / 1000 * уд. вес лам 1 / 1000");
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_mpure = new CalculationItem("Масса плёнки чистая (лам 2), кг", $this->mpogpure->value * $this->lamination2_width->value / 1000 * $lamination2_density / 1000, "|= ".$this->mpogpure->display." * ".$this->lamination2_width->display." / 1000 * ".$this->Display($lamination2_density)." / 1000", "м. пог. чистые * ширина материала лам 2 / 1000 * уд. вес лам 2 / 1000");
        }
    
        // Длина пленки чистая, м
        $this->lengthpure = new CalculationItem("Длина плёнки чистая (осн), м", $this->mpogpure->value, "|= ".$this->mpogpure->display, "м. пог. чистые");
    
        if($this->laminations_number > 0) {
            $this->lamination1_lengthpure = new CalculationItem("Длина плёнки чистая (лам 1), м", $this->mpogpure->value, "|= ".$this->mpogpure->display, "м. пог. чистые");
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_lengthpure = new CalculationItem("Длина плёнки чистая (лам 2), м", $this->mpogpure->value, "|= ".$this->mpogpure->display, "м. пог. чистые");
        }
        
        // Масса плёнки грязная (с приладкой), кг
        $this->mdirty = new CalculationItem("Масса плёнки грязная (осн), м", $this->m2dirty->value * $density / 1000, "|= ".$this->m2dirty->display." * ".$this->Display($density)." / 1000", "м2 грязные * уд. вес осн / 1000");
    
        if($this->laminations_number > 0) {
            $this->lamination1_mdirty = new CalculationItem("Масса плёнки грязная (лам 1), м", $this->lamination1_m2dirty->value * $lamination1_density / 1000, "|= ".$this->lamination1_m2dirty->display." * ".$this->Display($lamination1_density)." / 1000", "м2 грязные * уд. вес лам 1 / 1000");
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_mdirty = new CalculationItem("Масса плёнки грязная (лам 2), м", $this->lamination2_m2dirty->value * $lamination2_density / 1000, "|= ".$this->lamination2_m2dirty->display." * ".$this->Display($lamination2_density)." / 1000", "м2 грязные * уд. вес лам 2 / 1000");
        }
    
        // Длина плёнки грязная, м
        $this->lengthdirty =  new CalculationItem("Длина плёнки грязная (осн), м", $this->mpogdirty->value, "|= ".$this->mpogdirty->display, "м пог. грязные осн");
    
        if($this->laminations_number > 0) {
            $this->lamination1_lengthdirty = new CalculationItem("Длина плёнки грязная (лам 1), м2", $this->lamination1_mpogdirty->value, "|= ".$this->lamination1_mpogdirty->display, "м. пог. грязные лам 1");
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_lengthdirty = new CalculationItem("Длина плёнки грязная (лам 2), м2", $this->lamination2_mpogdirty->value, "|= ".$this->lamination2_mpogdirty->display, "м. пог. грязные лам 2");
        }
    
        //****************************************
        // Общая стоимость плёнок
        //****************************************
    
        // Общая стоимость грязная (с приладки), руб
        $this->film_price = new CalculationItem("Общая стоимость плёнки (осн)", $this->mdirty->value * $price * $this->GetCurrencyRate($currency, $usd, $euro), "|= ".$this->mdirty->display." * ".$this->Display($price)." * ".$this->Display($this->GetCurrencyRate($currency, $usd, $euro)), "масса пленки осн * цена плёнки * курс валюты");
    
        if($this->laminations_number > 0) {
            $this->lamination1_film_price = new CalculationItem("Общая стоимость плёнки (лам 1)", $this->lamination1_mdirty->value * $lamination1_price * $this->GetCurrencyRate($lamination1_currency, $usd, $euro), "|= ".$this->lamination1_mdirty->display." * ".$this->Display($lamination1_price)." * ".$this->Display($this->GetCurrencyRate($lamination1_currency, $usd, $euro)), "масса плёнки лам 1 * цена плёнки * курс валюты");
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_film_price = new CalculationItem("Общая стоимость плёнки (лам 2)", $this->lamination2_mdirty->value * $lamination2_price * $this->GetCurrencyRate($lamination2_currency, $usd, $euro), "|= ".$this->lamination2_mdirty->display." * ".$this->Display($lamination2_price)." * ".$this->Display($this->GetCurrencyRate($lamination2_currency, $usd, $euro)), "масса плёнки лам 2 * цена плёнки * курс валюты");
        }
        
        //*****************************************
        // Время - деньги
        //*****************************************
        
        // Время приладки
        if(!empty($machine_id)) {
            $this->tuning_time = new CalculationItem("Время приладки (осн), мин", $ink_number * $tuning_data->time, "|= $ink_number * ".$this->Display($tuning_data->time), "Красочность * время приладки");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_tuning_time = new CalculationItem("Время приладки (лам 1), мин", $laminator_tuning_data->time, "|= ".$this->Display($laminator_tuning_data->time), "Время приладки ламинатора");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_tuning_time = new CalculationItem("Время приладки (лам 2), мин", $laminator_tuning_data->time, "|= ".$this->Display($laminator_tuning_data->time), "Время приладки ламинатора");
        }
        
        // Время печати и ламинации (без приладки)
        if(!empty($machine_id)) {
            $this->print_time = new CalculationItem("Время печати без приладки (осн), ч", ($this->mpogpure->value + $this->waste_length->value) / 1000 / $machine_data->speed, "|= (".$this->mpogpure->display." + ".$this->waste_length->display.") / 1000 / ".$this->Display($machine_data->speed), "(м. пог. чистые + СтартСтопОтход) / 1000 / скорость работы машины");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_time = new CalculationItem("Время ламинации без приладки (лам 1), ч", ($this->mpogpure->value + $this->lamination1_waste_length->value) / 1000 / $laminator_machine_data->speed, "|= (".$this->mpogpure->display." + ".$this->lamination1_waste_length->display.") / 1000 / ".$this->Display($laminator_machine_data->speed), "(м. пог. чистые + СтартСтопОтход) / 1000 / скорость работы ламинатора");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_time = new CalculationItem("Время ламинации без приладки (лам 2), ч", ($this->mpogpure->value + $this->lamination2_waste_length->value) / 1000 / $laminator_machine_data->speed, "|= (".$this->mpogpure->display." + ".$this->lamination2_waste_length->display.") / 1000 / ".$this->Display($laminator_machine_data->speed), "(м. пог. чистые + СтартСтопОтход) / 1000 / скорость работы ламинатора");
        }
        
        // Общее время выполнения тиража
        if(!empty($machine_id)) {
            $this->work_time = new CalculationItem("Общее время выполнения (осн), ч", $this->tuning_time->value / 60 + $this->print_time->value, "|= ".$this->tuning_time->display." / 60 + ".$this->print_time->display, "время приладки / 60 + время печати");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_work_time = new CalculationItem("Общее время выполнения (лам 1), ч", $this->lamination1_tuning_time->value / 60 + $this->lamination1_time->value, "|= ".$this->lamination1_tuning_time->display." / 60 + ".$this->lamination1_time->display, "время приладки / 60 + время ламинации");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_work_time = new CalculationItem("Общее время выполнения (лам 2), ч", $this->lamination2_tuning_time->value / 60 + $this->lamination2_time->value, "|= ".$this->lamination2_tuning_time->display." / 60 + ".$this->lamination2_time->display, "время приладки / 60 + время ламинации");
        }
        
        // Стоимость выполнения тиража
        if(!empty($machine_id)) {
            $this->work_price = new CalculationItem("Стоимость выполнения (осн), руб", $this->work_time->value * $machine_data->price, "|= ".$this->work_time->display." * ".$this->Display($machine_data->price), "общее время выполнения осн * цена работы оборудования осн");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_work_price = new CalculationItem("Стоимость выполнения (лам 1), руб", $this->lamination1_work_time->value * $laminator_machine_data->price, "|= ".$this->lamination1_work_time->display." * ".$this->Display($laminator_machine_data->price), "общее время выполнения лам 1 * цена работы оборудования лам 1");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_work_price = new CalculationItem("Стоимость выполнения (лам 2), руб", $this->lamination2_work_time->value * $laminator_machine_data->price, "|= ".$this->lamination2_work_time->display." * ".$this->Display($laminator_machine_data->price), "общее время выполнения лам 2 * цена работы оборудования лам 2");
        }
        
        //****************************************
        // Расход краски
        //****************************************
        
        if(!empty($machine_id)) {
            // Площадь запечатки
            $this->print_area = new CalculationItem("Площадь запечатки, м2", $this->mpogdirty->value * ($stream_width * $streams_number + 10) / 1000, "|= ".$this->mpogdirty->display." * ($stream_width * $streams_number + 10) / 1000", "м. пог. грязные * (ширина ручья * кол-во ручьёв + 10 мм) / 1000");
            
            // Расход КраскаСмеси на 1 кг краски
            $this->ink_1kg_mix_weight = new CalculationItem("Расход КраскаСмеси на 1 кг краски, кг", 1 + $ink_data->solvent_part, "|= 1 + ".$this->Display($ink_data->solvent_part), "1 + расход растворителя на 1 кг краски");
            
            // Цена 1 кг чистого растворителя для краски
            if($machine_shortname == self::COMIFLEX) {
                $this->ink_solvent_kg_price = new CalculationItem("Цена 1 кг чистого флексоля 82, руб", $ink_data->solvent_flexol82 * $this->GetCurrencyRate($ink_data->solvent_flexol82_currency, $usd, $euro), "|= ".$this->Display($ink_data->solvent_flexol82)." * ".$this->Display($this->GetCurrencyRate($ink_data->solvent_flexol82_currency, $usd, $euro)), "цена 1 кг флексоля 82 * курс валюты");
            }
            else {
                $this->ink_solvent_kg_price = new CalculationItem("Цена 1 кг чистого этоксипропанола, руб", $ink_data->solvent_etoxipropanol * $this->GetCurrencyRate($ink_data->solvent_etoxipropanol_currency, $usd, $euro), "|= ".$this->Display($ink_data->solvent_etoxipropanol)." * ".$this->Display($this->GetCurrencyRate($ink_data->solvent_etoxipropanol_currency, $usd, $euro)), "цена 1 кг этоксипропанола * курс валюты");
            }
            
            $this->ink_kg_prices = array();
            $this->mix_ink_kg_prices = array();
            $this->mix_ink_solvent_kg_prices = array();
            $this->ink_expenses = array();
            $this->ink_prices = array();
            
            for($i=1; $i<=$ink_number; $i++) {
                $ink = "ink_$i";
                $cmyk = "cmyk_$i";
                $percent = "percent_$i";
                
                // Цена 1 кг чистой краски, руб
                $price = $this->GetInkPrice($$ink, $$cmyk, $ink_data->c, $ink_data->c_currency, $ink_data->m, $ink_data->m_currency, $ink_data->y, $ink_data->y_currency, $ink_data->k, $ink_data->k_currency, $ink_data->panton, $ink_data->panton_currency, $ink_data->white, $ink_data->white_currency, $ink_data->lacquer, $ink_data->lacquer_currency);
                $ink_kg_price = new CalculationItem("Цена 1 кг чистой краски (краска $i), руб", $price->value * $this->GetCurrencyRate($price->currency, $usd, $euro), "|= ".$this->Display($price->value)." * ". $this->Display($this->GetCurrencyRate($price->currency, $usd, $euro)), "цена 1 кг краски * курс валюты");
                $this->ink_kg_prices[$i] = $ink_kg_price;
                
                // Цена 1 кг КраскаСмеси, руб
                $mix_ink_kg_price = new CalculationItem("Цена 1 кг КраскаСмеси (краска $i), руб", (($ink_kg_price->value * 1) + ($this->ink_solvent_kg_price->value * $ink_data->solvent_part)) / $this->ink_1kg_mix_weight->value, "|= ((".$this->Display($ink_kg_price->display)." * 1) + (".$this->Display($this->ink_solvent_kg_price->display)." * ".$this->Display($ink_data->solvent_part).")) / ".$this->ink_1kg_mix_weight->display, "((цена 1 кг чистой краски * 1) + (цена 1 кг чистого растворителя * расход растворителя на 1 кг краски)) / расход КраскаСмеси на 1 кг краски");
                $this->mix_ink_kg_prices[$i] = $mix_ink_kg_price;
                
                // Расход КраскаСмеси
                $ink_expense = new CalculationItem("Расход КраскаСмеси (краска $i), кг", $this->print_area->value * $this->GetInkExpense($$ink, $$cmyk, $ink_data->c_expense, $ink_data->m_expense, $ink_data->y_expense, $ink_data->k_expense, $ink_data->panton_expense, $ink_data->white_expense, $ink_data->lacquer_expense) / 1000  * $$percent / 100, "|= ".$this->print_area->display." * ".$this->Display($this->GetInkExpense($$ink, $$cmyk, $ink_data->c_expense, $ink_data->m_expense, $ink_data->y_expense, $ink_data->k_expense, $ink_data->panton_expense, $ink_data->white_expense, $ink_data->lacquer_expense))." / 1000  * ".$$percent." / 100", "площадь запечатки * расход КраскаСмеси за 1 м2 / 1000 * процент краски / 100");
                $this->ink_expenses[$i] = $ink_expense;
                
                // Стоимость КраскаСмеси
                $ink_price = new CalculationItem("Стоимость КраскаСмеси (краска $i), руб", $ink_expense->value * $mix_ink_kg_price->value, "|= ".$ink_expense->display." * ".$mix_ink_kg_price->display, "Расход КраскаСмеси * цена 1 кг КраскаСмеси");
                $this->ink_prices[$i] = $ink_price;
            }
        }
        
        //********************************************
        // Расход клея
        //********************************************
        
        if($this->laminations_number > 0) {
            // Расход КлееСмеси на 1 кг клея
            $this->glue_kg_weight = new CalculationItem("Расход КлееСмеси на 1 кг клея, кг", 1 + $glue_data->solvent_part, "|= 1 + ".$this->Display($glue_data->solvent_part), "1 + расход растворителя на 1 кг клея");
            
            // Цена 1 кг чистого клея
            $this->glue_kg_price = new CalculationItem("Цена 1 кг чистого клея, руб", $glue_data->glue * $this->GetCurrencyRate($glue_data->glue_currency, $usd, $euro), "|= ".$this->Display($glue_data->glue)." * ".$this->Display($this->GetCurrencyRate($glue_data->glue_currency, $usd, $euro)), "цена 1 кг клея * курс валюты");
            
            // Цена 1 кг чистого растворителя для клея
            $this->glue_solvent_kg_price = new CalculationItem("Цена 1 кг чистого растворителя для клея", $glue_data->solvent * $this->GetCurrencyRate($glue_data->solvent_currency, $usd, $euro), "|= ".$this->Display($glue_data->solvent)." * ".$this->Display($this->GetCurrencyRate($glue_data->solvent_currency, $usd, $euro)), "цена 1 кг растворителя для клея * курс валюты");
            
            // Цена 1 кг КлееСмеси
            $this->mix_glue_kg_price = new CalculationItem("Цена 1 кг КлееСмеси, руб", (($this->glue_kg_price->value * 1) + ($this->glue_solvent_kg_price->value * $glue_data->solvent_part)) / $this->glue_kg_weight->value, "|= ((".$this->glue_kg_price->display." * 1) + (".$this->glue_solvent_kg_price->display." * ".$this->Display($glue_data->solvent_part).")) / ".$this->glue_kg_weight->display, "((цена 1 кг чистого клея * 1) + (цена 1 кг чистого растворителя * расход растворителя на 1 кг клея)) / расходл КлееСмеси на 1 кг клея");
            
            // Площадь заклейки (лам 1), м2
            $this->glue_area1 = new CalculationItem("Площадь заклейки (лам 1), м2", $this->lamination1_mpogdirty->value * $lamination_roller_width / 1000, "|= ".$this->lamination1_mpogdirty->display." * ".$this->Display($lamination_roller_width)." / 1000", "м. пог. грязные лам 1 * ширина ламинирующего вала / 1000");
            
            // Расход КлееСмеси (лам 1), кг
            if((strlen($film) > 3 && substr($film, 0, 3) == "Pet") || (strlen($lamination1_film) > 3 && substr($lamination1_film, 0, 3) == "Pet")) {
                $this->glue_expense1 = new CalculationItem("Расход КлееСмеси (лам 1), кг", $this->glue_area1->value * $glue_data->glue_expense_pet / 1000, "|= ".$this->glue_area1->display." * ".$this->Display($glue_data->glue_expense_pet)." / 1000", "площадь заклейки лам 1 * расход КлееСмеси для ПЭТ в 1 м2 / 1000");
            }
            else {
                $this->glue_expense1 = new CalculationItem("Расход КлееСмеси (лам 1), кг", $this->glue_area1->value * $glue_data->glue_expense / 1000, "|= ".$this->glue_area1->display." * ".$this->Display($glue_data->glue_expense)." / 1000", "площадь заклейки лам 1 * расход КлееСмеси в 1 м2 / 1000");
            }
            
            // Стоимость КлееСмеси (лам 1), руб
            $this->glue_price1 = new CalculationItem("Стоимость КлееСмеси (лам 1), руб", $this->glue_expense1->value * $this->mix_glue_kg_price->value, "|= ".$this->glue_expense1->display." * ".$this->mix_glue_kg_price->display, "расход КлееСмеси лам 1 * цена 1 кг КлееСмеси");
        }
        
        if($this->laminations_number > 1) {
            // Площадь заклейки (лам 2), м2
            $this->glue_area2 = new CalculationItem("Площадь заклейки (лам 2), м2", $this->lamination2_mpogdirty->value * $lamination_roller_width / 1000, "|= ".$this->lamination2_mpogdirty->display." * ".$this->Display($lamination_roller_width)." / 1000", "м. пог. грязные лам 2 * ширина ламинирующего вала / 1000");
            
            // Расход КлееСмеси (лам 2), кг
            if((strlen($lamination1_film) > 3 && substr($lamination1_film, 0, 3) == "Pet") || (strlen($lamination2_film) > 3 && substr($lamination2_film, 0, 3) == "Pet")) {
                $this->glue_expense2 = new CalculationItem("Расход КлееСмеси (лам 2), кг", $this->glue_area2->value * $glue_data->glue_expense_pet / 1000, "|= ".$this->glue_area2->display." * ".$this->Display($glue_data->glue_expense_pet)." / 1000", "площадь заклейки лам 2 * расход КлееСмеси для ПЭТ в 1 м2");
            }
            else {
                $this->glue_expense2 = new CalculationItem("Расход КлееСмеси (лам 2), кг", $this->glue_area2->value * $glue_data->glue_expense / 1000, "|= ".$this->glue_area2->display." * ".$this->Display($glue_data->glue_expense)." / 1000", "площадь заклейки лам 2 * расход КлееСмеси в 1 м2 / 1000");
            }
            
            // Стоимость КлееСмеси (лам 2)
            $this->glue_price2 = new CalculationItem("Стоимость КлееСмеси (лам 2), руб", $this->glue_expense2->value * $this->mix_glue_kg_price->value, "|= ".$this->glue_expense2->display." * ".$this->mix_glue_kg_price->display, "расход КлееСмеси лам 2 * цена 1 кг КлееСмеси");
        }
    }
}
?>