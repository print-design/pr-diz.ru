<?php
// Лыжи
const NO_SKI = 0;
const STANDARD_SKI = 1;
const NONSTANDARD_SKI = 2;

// Валюты
const USD = "usd";
const EURO = "euro";

class Calculation {
    function GetWidth($ski, $streams_number, $stream_width, $width_ski) {
        $result = 0;
    
        switch($ski) {
            case NO_SKI:
                $result = $streams_number * $stream_width;
                break;
        
            case STANDARD_SKI:
                $result = $streams_number * $stream_width + 20;
                break;
        
            case NONSTANDARD_SKI:
                $result = $width_ski;
                break;
        }
    
        return $result;
    }

    function GetCurrencyRate($currency, $usd, $euro) {
        switch($currency) {
            case USD:
                return $usd;
            
            case EURO:
                return $euro;
            
            default :
                return 1;
        }
    }

    public $laminations_number = 0;
    public $width;
    public $lamination1_width;
    public $lamination2_width;
    public $m2pure;
    public $mpogpure;
    public $waste_length;
    public $lamination1_waste_length;
    public $lamination2_waste_length;
    public $mpogdirty;
    public $lamination1_mpogdirty;
    public $lamination2_mpogdirty;
    public $m2dirty;
    public $lamination1_m2dirty;
    public $lamination2_m2dirty;
    public $mpure;
    public $lamination1_mpure;
    public $lamination2_mpure;
    public $lengthpure;
    public $lamination1_lengthpure;
    public $lamination2_lengthpure;
    public $mdirty;
    public $lamination1_mdirty;
    public $lamination2_mdirty;
    public $lengthdirty;
    public $lamination1_lengthdirty;
    public $lamination2_lengthdirty;
    public $film_price;
    public $lamination1_film_price;
    public $lamination2_film_price;
    
    public $result = array();

    public function __construct($tuning_data, 
            $laminator_tuning_data,
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

        $this->width = $this->GetWidth($ski, $streams_number, $stream_width, $width_ski);
        
        if($this->laminations_number > 0) {
            $this->lamination1_width = $this->GetWidth($lamination1_ski, $streams_number, $stream_width, $lamination1_width_ski);
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_width = $this->GetWidth($lamination2_ski, $streams_number, $stream_width, $lamination2_width_ski);
        }

        // Площадь чистая
        $this->m2pure = $quantity * 1000 / ($density + $lamination1_density ?? 0 + $lamination2_density ?? 0);
        
        // Метры погонные чистые
        $this->mpogpure = $this->m2pure / ($streams_number * $stream_width);
        
        // Метраж отходов, исходя из склее и инерции
        if(!empty($machine_id)) {
            $this->waste_length = $tuning_data[$machine_id]['waste_percent'] * $this->mpogpure / 100;
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_waste_length = $laminator_tuning_data['waste_percent'] * $this->mpogpure / 100;
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_waste_length = $laminator_tuning_data['waste_percent'] * $this->mpogpure / 100;
        }
        
        // Метры погонные грязные
        if(!empty($machine_id)) {
            $this->mpogdirty = $this->mpogpure * $tuning_data[$machine_id]['waste_percent'] + $ink_number * $tuning_data[$machine_id]['length'] + $this->laminations_number * $laminator_tuning_data['length'];
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_mpogdirty = $this->mpogpure * $tuning_data[$machine_id]['waste_percent'] + $laminator_tuning_data['length'] * 2;
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_mpogdirty = $this->mpogpure * $tuning_data[$machine_id]['waste_percent'] + $laminator_tuning_data['length'];
        }
        
        // Площадь грязная
        if(!empty($machine_id)) {
            $this->m2dirty = $this->mpogdirty * $this->width / 1000;
        }
        
        if($this->laminations_number > 0) {
            $this->lamination1_m2dirty = $this->lamination1_mpogdirty * $this->lamination1_width / 1000;
        }
        
        if($this->laminations_number > 1) {
            $this->lamination2_m2dirty = $this->lamination2_mpogdirty * $this->lamination2_width / 1000;
        }
    
        //****************************************
        // Массы и длины плёнок
        //****************************************
    
        // Масса плёнки чистая (без приладки), кг
        $this->mpure = $this->mpogpure * $this->width * $density / 1000;
    
        if($this->laminations_number > 0) {
            $this->lamination1_mpure = $this->mpogpure * $this->lamination1_width * $lamination1_density / 1000;
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_mpure = $this->mpogpure * $this->lamination2_width * $lamination2_density / 1000;
        }
    
        // Длина пленки чистая, м
        $this->lengthpure = $this->mpogpure;
    
        if($this->laminations_number > 0) {
            $this->lamination1_lengthpure = $this->mpogpure;
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_lengthpure = $this->mpogpure;
        }
    
        // Масса плёнки грязная (с приладкой), кг
        $this->mdirty = $this->m2dirty * $density / 1000;
    
        if($this->laminations_number > 0) {
            $this->lamination1_mdirty = $this->lamination1_m2dirty * $lamination1_density / 1000;
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_mdirty = $this->lamination2_m2dirty * $lamination2_density / 1000;
        }
    
        // Длина плёнки грязная, м
        $this->lengthdirty = $this->mpogdirty;
    
        if($this->laminations_number > 0) {
            $this->lamination1_lengthdirty = $this->lamination1_mpogdirty;
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_lengthdirty = $this->lamination2_mpogdirty;
        }
    
        //****************************************
        // Себестоимость плёнок
        //****************************************
    
        // Себестоимость грязная (с приладки), руб
        $this->film_price = $this->mdirty * $price * $this->GetCurrencyRate($currency, $usd, $euro);
    
        if($this->laminations_number > 0) {
            $this->lamination1_film_price = $this->lamination1_mdirty / $lamination1_price * $this->GetCurrencyRate($lamination1_currency, $usd, $euro);
        }
    
        if($this->laminations_number > 1) {
            $this->lamination2_film_price = $this->lamination2_mdirty / $lamination2_price * $this->GetCurrencyRate($lamination2_currency, $usd, $euro);
        }
            
        return $this->result;
    }
}
?>