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
        
        if(is_float($value)) {
            $this->display = number_format($value, 2, ",", " ");
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

class Calculation {
    // Лыжи
    const NO_SKI = 0;
    const STANDARD_SKI = 1;
    const NONSTANDARD_SKI = 2;

    // Валюты
    const USD = "usd";
    const EURO = "euro";

    function GetWidth($ski, $streams_number, $stream_width, $width_ski) {
        $result = 0;
    
        switch($ski) {
            case Calculation::NO_SKI:
                $result = $streams_number * $stream_width;
                break;
        
            case Calculation::STANDARD_SKI:
                $result = $streams_number * $stream_width + 20;
                break;
        
            case Calculation::NONSTANDARD_SKI:
                $result = $width_ski;
                break;
        }
    
        return $result;
    }
    
    function GetWidthFormula($ski, $streams_number, $stream_width, $width_ski) {
        $result = "";
    
        switch($ski) {
            case Calculation::NO_SKI:
                $result = "$streams_number * $stream_width";
                break;
        
            case Calculation::STANDARD_SKI:
                $result = "$streams_number * $stream_width + 20";
                break;
        
            case Calculation::NONSTANDARD_SKI:
                $result = "";
                break;
        }
    
        return $result;
    }

    function GetWidthComment($ski) {
        $result = "";
    
        switch($ski) {
            case Calculation::NO_SKI:
                $result = "количество ручьёв * ширина ручья";
                break;
        
            case Calculation::STANDARD_SKI:
                $result = "количество ручьёв * ширина ручья + 20 мм";
                break;
        
            case Calculation::NONSTANDARD_SKI:
                $result = "вводится вручную";
                break;
        }
    
        return $result;
    }

    function GetCurrencyRate($currency, $usd, $euro) {
        switch($currency) {
            case Calculation::USD:
                return $usd;
            
            case Calculation::EURO:
                return $euro;
            
            default :
                return 1;
        }
    }

    public $laminations_number = 0;
    public CalculationItem $width;
    public CalculationItem $lamination1_width;
    public CalculationItem $lamination2_width;
    public CalculationItem $m2pure;
    public CalculationItem $mpogpure;
    public CalculationItem $waste_length;
    public CalculationItem $lamination1_waste_length;
    public CalculationItem $lamination2_waste_length;
    public CalculationItem $mpogdirty;
    public CalculationItem $lamination1_mpogdirty;
    public CalculationItem $lamination2_mpogdirty;
    public CalculationItem $m2dirty;
    public CalculationItem $lamination1_m2dirty;
    public CalculationItem $lamination2_m2dirty;
    public CalculationItem $mpure;
    public CalculationItem $lamination1_mpure;
    public CalculationItem $lamination2_mpure;
    public CalculationItem $lengthpure;
    public CalculationItem $lamination1_lengthpure;
    public CalculationItem $lamination2_lengthpure;
    public CalculationItem $mdirty;
    public CalculationItem $lamination1_mdirty;
    public CalculationItem $lamination2_mdirty;
    public CalculationItem $lengthdirty;
    public CalculationItem $lamination1_lengthdirty;
    public CalculationItem $lamination2_lengthdirty;
    public CalculationItem $film_price;
    public CalculationItem $lamination1_film_price;
    public CalculationItem $lamination2_film_price;
    public CalculationItem $tuning_time;
    public CalculationItem $lamination1_tuning_time;
    public CalculationItem $lamination2_tuning_time;
    public CalculationItem $print_time;
    public CalculationItem $lamination1_time;
    public CalculationItem $lamination2_time;
    public CalculationItem $work_time;
    public CalculationItem $lamination1_work_time;
    public CalculationItem $lamination2_work_time;
    public CalculationItem $work_price;
    public CalculationItem $lamination1_work_price;
    public CalculationItem $lamination2_work_price;

    public function __construct(TuningData $tuning_data, 
            TuningData $laminator_tuning_data,
            MachineData $machine_data,
            MachineData $laminator_machine_data,
            $usd, // Курс доллара
            $euro, // Курс евро
            $quantity, // Масса тиража
        
            $film, // Основная пленка, марка
            $thickness, // Основная пленка, толщина, мкм
            $density, // Основная пленка, плотность, г/м2
            $price, // Основная пленка, цена
            $currency, // Основная пленка, валюта
            $individual_film_name, // Основная плёнка, другая, название
            $individual_thickness, // Основная плёнка, другая, толщина
            $individual_density, // Основная плёнка, другая, уд.вес
            $customers_material, // Основная плёнка, другая, материал заказчика
            $ski, // Основная пленка, лыжи
            $width_ski, // Основная пленка, ширина пленки, мм
        
            $lamination1_film, // Ламинация 1, марка
            $lamination1_thickness, // Ламинация 1, толщина, мкм
            $lamination1_density, // Ламинация 1, плотность, г/м2
            $lamination1_price, // Ламинация 1, цена
            $lamination1_currency, // Ламинация 1, валюта
            $lamination1_individual_film_name, // Ламинация 1, другая, название
            $lamination1_individual_thickness, // Ламинация 1, другая, толщина
            $lamination1_individual_density, // Ламинация 1, другая, уд. вес
            $lamination1_customers_material, // Ламинация 1, другая, материал заказчика
            $lamination1_ski, // Ламинация 1, лыжи
            $lamination1_width_ski, // Ламинация 1, ширина пленки, мм
        
            $lamination2_film, // Ламинация 2, марка
            $lamination2_thickness, // Ламинация 2, толщина, мкм
            $lamination2_density, // Ламинация 2, плотность, г/м2
            $lamination2_price, // Ламинация 2, цена
            $lamination2_currency, // Ламинация 2, валюта
            $lamination2_individual_film_name, // Ламинация 2, другая, название
            $lamination2_individual_thickness, // Ламинация 2, другая, толщина
            $lamination2_individual_density, // Ламинация 2, другая, уд.вес
            $lamination2_customers_material, // Ламинация 2, другая, уд. вес
            $lamination2_ski, // Ламинация 2, лыжи
            $lamination2_width_ski,  // Ламинация 2, ширина пленки, мм
        
            $machine_id, // Машина
            $stream_width, // Ширина ручья, мм
            $streams_number, // Количество ручьёв
            $raport, // Рапорт
            $ink_number // Красочность
            ) {
        
    
        if(!empty($lamination2_film) && !empty($lamination2_thickness) && !empty($lamination2_density)) {
            $this->laminations_number = 2;
        }
        elseif(!empty ($lamination1_film) && !empty ($lamination1_thickness) && !empty ($lamination1_density)) {
            $this->laminations_number = 1;
        }

        $this->width = new CalculationItem("Ширина материала (осн), мм", $this->GetWidth($ski, $streams_number, $stream_width, $width_ski), $this->GetWidthFormula($ski, $streams_number, $stream_width, $width_ski), $this->GetWidthComment($ski));
        
        if($this->laminations_number > 0) {
            $this->lamination1_width = new CalculationItem("Ширина материала (лам 1), мм", $this->GetWidth($lamination1_ski, $streams_number, $stream_width, $lamination1_width_ski), $this->GetWidthFormula($lamination1_ski, $streams_number, $stream_width, $lamination1_width_ski), $this->GetWidthComment($lamination1_ski));
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_width = new CalculationItem("Ширина материала (лам 2), мм", $this->GetWidth($lamination2_ski, $streams_number, $stream_width, $lamination2_width_ski), $this->GetWidthFormula($lamination2_ski, $streams_number, $stream_width, $lamination2_width_ski), $this->GetWidthComment($lamination2_ski));
        }

        // Площадь чистая
        $density_display = empty($density) ? "0" : number_format($density, 2, ",", " ");
        $lamination1_density_display = empty($lamination1_density) ? "0" : number_format($lamination1_density, 2, ",", " ");
        $lamination2_density_display = empty($lamination2_density) ? "0" : number_format($lamination2_density, 2, ",", " ");
        $this->m2pure = new CalculationItem("М2 чистые, м2", $quantity * 1000 / ($density + $lamination1_density ?? 0 + $lamination2_density ?? 0), "$quantity * 1000 / ($density_display + $lamination1_density_display + $lamination2_density_display)", "масса тиража * 1000 / (уд. вес осн + уд. вес лам 1 + уд. вес лам 2)");
        
        // Метры погонные чистые
        $this->mpogpure = new CalculationItem("М пог. чистые, м", $this->m2pure->value / ($streams_number * $stream_width), $this->m2pure->display." / ($streams_number * $stream_width)", "м2 чистые / (количество ручьёв * ширина ручья)");
        
        // Метраж отходов, исходя из склее и инерции
        if(!empty($machine_id)) {
            $this->waste_length = new CalculationItem("Метраж отходов (осн), м", $tuning_data->waste_percent * $this->mpogpure->value / 100, $tuning_data->waste_percent." * ".$this->mpogpure->display." / 100", "процент отходов печати * м. пог. чистые / 100");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_waste_length = new CalculationItem("Метраж отходов (лам 1), м", $laminator_tuning_data->waste_percent * $this->mpogpure->value / 100, $laminator_tuning_data->waste_percent." * ".$this->mpogpure->display." / 100", "процент отходов ламинации * м. пог. чистые / 100");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_waste_length = new CalculationItem("Метраж отходов (лам 2), м", $laminator_tuning_data->waste_percent * $this->mpogpure->value / 100, $laminator_tuning_data->waste_percent." * ".$this->mpogpure->display." / 100", "процент отходов ламинации * м. пог. чистые / 100");
        }
        
        // Метры погонные грязные
        if(!empty($machine_id)) {
            $this->mpogdirty = new CalculationItem("М. пог. грязные (осн), м", $this->mpogpure->value * $tuning_data->waste_percent + $ink_number * $tuning_data->waste_percent + $this->laminations_number * $laminator_tuning_data->length, $this->mpogpure->display." * ".$tuning_data->waste_percent." + ".$ink_number." * ".$tuning_data->length." + ".$this->laminations_number." * ".$laminator_tuning_data->length, "м. пог. чистые * общий процент отходов на печати + красочность * метраж приладки 1 краски + кол-во ламинаций * метраж приладки ламинации");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_mpogdirty = new CalculationItem("М. пог. грязные (лам 1), м", $this->mpogpure->value * $tuning_data->waste_percent + $laminator_tuning_data->length * 2, $this->mpogpure->display." * ".$tuning_data->waste_percent." + ".$laminator_tuning_data->length." * 2", "м. пог. чистые * общий процент отходов на печати + метраж приладки ламинации * 2");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_mpogdirty = new CalculationItem("М. пог. грязные (лам 2), м", $this->mpogpure->value * $tuning_data->waste_percent + $laminator_tuning_data->length, $this->mpogpure->display." * ".$tuning_data->waste_percent." + ".$laminator_tuning_data->length, "м. пог. чистые * общий процент отходов на печати + метраж приладки ламинации");
        }
        
        // Площадь грязная
        if(!empty($machine_id)) {
            $this->m2dirty = new CalculationItem("М2 грязные (осн), м2", $this->mpogdirty->value * $this->width->value / 1000, $this->mpogdirty->display." * ".$this->width->display." / 1000", "м. пог. грязные * ширина материала осн / 1000");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_m2dirty = new CalculationItem("М2 грязные (лам 1), м2", $this->lamination1_mpogdirty->value * $this->lamination1_width->value / 1000, $this->lamination1_mpogdirty->display." * ".$this->lamination1_width->display." / 1000", "м. пог. грязные * ширина материала лам 1 / 1000");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_m2dirty = new CalculationItem("М2 грязные (лам 2), м2", $this->lamination2_mpogdirty->value * $this->lamination2_width->value / 1000, $this->lamination2_mpogdirty->display." * ".$this->lamination2_width->display." / 1000", "м. пог. грязные * ширина материала лам 2 / 1000");
        }
    
        //****************************************
        // Массы и длины плёнок
        //****************************************
    
        // Масса плёнки чистая (без приладки), кг
        $this->mpure = new CalculationItem("Масса плёнки чистая (осн), кг", $this->mpogpure->value * $this->width->value * $density / 1000, $this->mpogpure->display." * ".$this->width->display." * ".$density_display." / 1000", "м. пог. чистые * ширина материала плёнки осн / 1000");
    
        if($this->laminations_number > 0) {
            $this->lamination1_mpure = new CalculationItem("Масса плёнки чистая (лам 1), кг", $this->mpogpure->value * $this->lamination1_width->value * $lamination1_density / 1000, $this->mpogpure->display." * ".$this->lamination1_width->display." * ".$lamination1_density_display." / 1000", "м. пог. чистые * ширина материала плёнки лам 1 / 1000");
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_mpure = new CalculationItem("Масса плёнки чистая (лам 2), кг", $this->mpogpure->value * $this->lamination2_width->value * $lamination2_density / 1000, $this->mpogpure->display." * ".$this->lamination2_width->display." * ".$lamination2_density_display." / 1000", "м. пог. чистые * ширина материала плёнки лам 2 / 1000");
        }
    
        // Длина пленки чистая, м
        $this->lengthpure = new CalculationItem("Длина плёнки чистая (осн), м", $this->mpogpure->value, $this->mpogpure->display, "м. пог. чистые");
    
        if($this->laminations_number > 0) {
            $this->lamination1_lengthpure = new CalculationItem("Длина плёнки чистая (лам 1), м", $this->mpogpure->value, $this->mpogpure->display, "м. пог. чистые");
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_lengthpure = new CalculationItem("Длина плёнки чистая (лам 2), м", $this->mpogpure->value, $this->mpogpure->display, "м. пог. чистые");
        }
    
        // Масса плёнки грязная (с приладкой), кг
        $this->mdirty = new CalculationItem("Масса плёнки грязная (осн), м", $this->m2dirty->value * $density / 1000, $this->m2dirty->display." * ".$density_display." / 1000", "м2 грязные * уд. вес осн / 1000");
    
        if($this->laminations_number > 0) {
            $this->lamination1_mdirty = new CalculationItem("Масса плёнки грязная (лам 1), м", $this->lamination1_m2dirty->value * $lamination1_density / 1000, $this->lamination1_m2dirty->display." * ".$lamination1_density_display." / 1000", "м2 грязные * уд. вес лам 1 / 1000");
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_mdirty = new CalculationItem("Масса плёнки грязная (лам 2), м", $this->lamination2_m2dirty->value * $lamination2_density / 1000, $this->lamination2_m2dirty->display." * ".$lamination2_density_display." / 1000", "м2 грязные * уд. вес лам 2 / 1000");
        }
    
        // Длина плёнки грязная, м
        $this->lengthdirty =  new CalculationItem("Длина плёнки грязная (осн), м", $this->mpogdirty->value, $this->mpogdirty->display, "м пог. грязные осн");
    
        if($this->laminations_number > 0) {
            $this->lamination1_lengthdirty = new CalculationItem("Длина плёнки грязная (лам 1), м2", $this->lamination1_mpogdirty->value, $this->lamination1_mpogdirty->display, "м. пог. грязные лам 1");
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_lengthdirty = new CalculationItem("Длина плёнки грязная (лам 2), м2", $this->lamination2_mpogdirty->value, $this->lamination2_mpogdirty->display, "м. пог. грязные лам 2");
        }
    
        //****************************************
        // Себестоимость плёнок
        //****************************************
    
        // Себестоимость грязная (с приладки), руб
        $this->film_price = new CalculationItem("Общая стоимость плёнки (осн)", $this->mdirty->value * $price * $this->GetCurrencyRate($currency, $usd, $euro), $this->mdirty->display." / $price * ".$this->GetCurrencyRate($currency, $usd, $euro), "Масса пленки осн / цена * курс валюты");
    
        if($this->laminations_number > 0) {
            $this->lamination1_film_price = new CalculationItem("Общая стоимость плёнки (лам 1)", $this->lamination1_mdirty->value * $lamination1_price * $this->GetCurrencyRate($lamination1_currency, $usd, $euro), $this->lamination1_mdirty->display." * $lamination1_price * ".$this->GetCurrencyRate($lamination1_currency, $usd, $euro), "Масса плёнки лам 1 / цена * курс валюты");
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_film_price = new CalculationItem("Общая стоимость плёнки (лам 2)", $this->lamination2_mdirty->value * $lamination2_price * $this->GetCurrencyRate($lamination2_currency, $usd, $euro), $this->lamination2_mdirty->display." * $lamination2_price * ".$this->GetCurrencyRate($lamination2_currency, $usd, $euro), "Масса плёнки лам 2 / цена * курс валюты");
        }
        
        //*****************************************
        // Время - деньги
        //*****************************************
        
        // Время приладки
        if(!empty($machine_id)) {
            $this->tuning_time = new CalculationItem("Время приладки (осн), мин", $ink_number * $tuning_data->time, "$ink_number * $tuning_data->time", "Красочность * время приладки");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_tuning_time = new CalculationItem("Время приладки (лам 1), мин", $laminator_tuning_data->time, $laminator_tuning_data->time, "Время приладки ламинатора");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_tuning_time = new CalculationItem("Время приладки (лам 2), мин", $laminator_tuning_data->time, $laminator_tuning_data->time, "Время приладки ламинатора");
        }
        
        // Время печати и ламинации (без приладки)
        if(!empty($machine_id)) {
            $this->print_time = new CalculationItem("Время печати без приладки (осн), ч", ($this->mpogpure->value + $this->waste_length->value) / 1000 / $machine_data->speed, "(".$this->mpogpure->display." + ".$this->waste_length->display.") / 1000 / ".$machine_data->speed, "(м. пог. чистые + метраж отходов) / 1000 / скорость работы машины");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_time = new CalculationItem("Время ламинации без приладки (лам 1), ч", ($this->mpogpure->value + $this->waste_length->value) / 1000 / $laminator_machine_data->speed, "(".$this->mpogpure->display." + ".$this->waste_length->display.") / 1000 / ".$laminator_machine_data->speed, "(м. пог. чистые + метраж отходов) / 1000 / скорость работы ламинатора");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_time = new CalculationItem("Время ламинации без приладки (лам 2), ч", ($this->mpogpure->value + $this->waste_length->value) / 1000 / $laminator_machine_data->speed, "(".$this->mpogpure->display." + ".$this->waste_length->display.") / 1000 / ".$laminator_machine_data->speed, "(м. пог. чистые + метраж отходов) / 1000 / скорость работы ламинатора");
        }
        
        // Общее время выполнения тиража
        if(!empty($machine_id)) {
            $this->work_time = new CalculationItem("Общее время выполнения (осн), ч", $this->tuning_time->value / 60 + $this->print_time->value, $this->tuning_time->display." / 60 + ".$this->print_time->display, "Время приладки / 60 + время печати");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_work_time = new CalculationItem("Общее время выполнения (лам 1), ч", $this->lamination1_tuning_time->value / 60 + $this->lamination1_time->value, $this->lamination1_tuning_time->display." / 60 + ".$this->lamination1_time->display, "Время приладки / 60 + время ламинации");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_work_time = new CalculationItem("Общее время выполнения (лам 2), ч", $this->lamination2_tuning_time->value / 60 + $this->lamination2_time->value, $this->lamination2_tuning_time->display." / 60 + ".$this->lamination2_time->display, "Время приладки / 60 + время ламинации");
        }
        
        // Стоимость выполнения тиража
        if(!empty($machine_id)) {
            $this->work_price = new CalculationItem("Стоимость выполнения (осн), руб", $this->work_time->value * $machine_data->price, $this->work_time->display." * ".$machine_data->price, "Общее время выполнения осн * стоимость работы оборудования осн");
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_work_price = new CalculationItem("Стоимость выполнения (лам 1), руб", $this->lamination1_work_time->value * $laminator_machine_data->price, $this->lamination1_work_time->display." * ".$laminator_machine_data->price, "Общее время выполнения лам 1 * стоимость работы оборудования лам 1");
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_work_price = new CalculationItem("Стоимость выполнения (лам 2), руб", $this->lamination2_work_time->value * $laminator_machine_data->price, $this->lamination2_work_time->display." * ".$laminator_machine_data->price, "Общее время выполнения лам 2 * стоимость работы оборудования лам 2");
        }
    }
}
?>