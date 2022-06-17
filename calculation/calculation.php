<?php
// Данные приладки
class DataPriladka {
    public $time; // Время приладки
    public $length; // Длина приладки
    public $waste_percent; // Процент отходов от приладки
    
    // Конструктор
    public function __construct($time, $length, $waste_percent) {
        $this->time = $time;
        $this->length = $length;
        $this->waste_percent = $waste_percent;
    }
}

// Характеристика оборудования
class DataMachine {
    public $price; // Цена
    public $speed; // Скорость
    public $max_width; // Максимальная ширина
    
    // Контруктор
    public function __construct($price, $speed, $max_width) {
        $this->price = $price;
        $this->speed = $speed;
        $this->max_width = $max_width;
    }
}

// Зарактеристика зазоров
class DataGap {
    public $gap_raport; // ЗазорРапорт
    public $gap_stream; // ЗазорРучей
    
    // Конструктор
    public function __construct($gap_raport, $gap_stream) {
        $this->gap_raport = $gap_raport;
        $this->gap_stream = $gap_stream;
    }
}

// Данные по краскам
class DataInk {
    public $c_price; // Cyan, цена
    public $c_currency; // Cyan, валюта
    public $c_expense; // Cyan, расход
    public $m_price;
    public $m_currency;
    public $m_expense;
    public $y_price;
    public $y_currency;
    public $y_expense;
    public $k_price;
    public $k_currency;
    public $k_expense;
    public $white_price;
    public $white_currency;
    public $white_expense;
    public $panton_price;
    public $panton_currency;
    public $panton_expense;
    public $lacquer_price;
    public $lacquer_currency;
    public $lacquer_expense;
    public $solvent_etoxipropanol_price;
    public $solvent_etoxipropanol_currency;
    public $solvent_flexol82_price;
    public $solvent_flexol82_currency;
    public $solvent_part; // Расход растворителя на 1 кг краски
    public $min_price; // Ограничение на минимальную стоимость
    
    // Конструктор
    public function __construct($c_price, $c_currency, $c_expense, $m_price, $m_currency, $m_expense, $y_price, $y_currency, $y_expense, $k_price, $k_currency, $k_expense, $white_price, $white_currency, $white_expense, $panton_price, $panton_currency, $panton_expense, $lacquer_price, $lacquer_currency, $lacquer_expense, $solvent_etoxipropanol_price, $solvent_etoxipropanol_currency, $solvent_flexol82_price, $solvent_flexol82_currency, $solvent_part, $min_price) {
        $this->c_price = $c_price;
        $this->c_currency = $c_currency;
        $this->c_expense = $c_expense;
        $this->m_price = $m_price;
        $this->m_currency = $m_currency;
        $this->m_expense = $m_expense;
        $this->y_price = $y_price;
        $this->y_currency = $y_currency;
        $this->y_expense = $y_expense;
        $this->k_price = $k_price;
        $this->k_currency = $k_currency;
        $this->k_expense = $k_expense;
        $this->white_price = $white_price;
        $this->white_currency = $white_currency;
        $this->white_expense = $white_expense;
        $this->panton_price = $panton_price;
        $this->panton_currency = $panton_currency;
        $this->panton_expense = $panton_expense;
        $this->lacquer_price = $lacquer_price;
        $this->lacquer_currency = $lacquer_currency;
        $this->lacquer_expense = $lacquer_expense;
        $this->solvent_etoxipropanol_price = $solvent_etoxipropanol_price;
        $this->solvent_etoxipropanol_currency = $solvent_etoxipropanol_currency;
        $this->solvent_flexol82_price = $solvent_flexol82_price;
        $this->solvent_flexol82_currency = $solvent_flexol82_currency;
        $this->solvent_part = $solvent_part;
        $this->min_price = $min_price;
    }
}

// Данные по клею
class DataGlue {
    public $glue_price; // Клей, цена
    public $glue_currency; // Клей, валюта
    public $glue_expense; // Клей, расход
    public $glue_expense_pet; // Клей, расход (ламинация ПЭТ)
    public $solvent_price; // Растворитель, цена
    public $solvent_currency; // Растворитель, валюта
    public $solvent_part; // Расход растворителя на 1 кг клея
    
    // Конструктор
    public function __construct($glue_price, $glue_currency, $glue_expense, $glue_expense_pet, $solvent_price, $solvent_currency, $solvent_part) {
        $this->glue_price = $glue_price;
        $this->glue_currency = $glue_currency;
        $this->glue_expense = $glue_expense;
        $this->glue_expense_pet = $glue_expense_pet;
        $this->solvent_price = $solvent_price;
        $this->solvent_currency = $solvent_currency;
        $this->solvent_part = $solvent_part;
    }
}

// Данные по формам
class DataCliche {
    public $flint_price; // Флинт, цена
    public $flint_currency; // Флинт, валюта
    public $kodak_price; // Кодак, цена
    public $kodak_currency; // Кодак, валюта
    public $scotch_price; // Скотч, цена
    public $scotch_currency; // Скотч, валюта
    
    // Конструктор
    public function __construct($flint_price, $flint_currency, $kodak_price, $kodak_currency, $scotch_price, $scotch_currency) {
        $this->flint_price = $flint_price;
        $this->flint_currency = $flint_currency;
        $this->kodak_price = $kodak_price;
        $this->kodak_currency = $kodak_currency;
        $this->scotch_price = $scotch_price;
        $this->scotch_currency = $scotch_currency;
    }
}

// Цена вместе с валютой
class DataPrice {
    public $value; // цена
    public $currency; // валюта
    
    // Конструктор
    public function __construct($value, $currency) {
        $this->value = $value;
        $this->currency = $currency;
    }
}

// Наценка вместе с типом наценки, минимальным и максимальным весом
class DataExtracharge {
    public $value; // наценка
    public $ech_type; // тип наценкт
    public $min_weight; // минимальный вес
    public $max_weight; // максимальный вес
    
    // Конструктор
    public function __construct($value, $ech_type, $min_weight, $max_weight) {
        $this->value = $value;
        $this->ech_type = $ech_type;
        $this->min_weight = $min_weight;
        $this->max_weight = $max_weight;
    }
}

// Базовый класс для классов расчёта
class CalculationBase {
    // Типы работы
    const WORK_TYPE_NOPRINT = 1;
    const WORK_TYPE_PRINT = 2;
    const WORK_TYPE_SELF_ADHESIVE = 3;
    
    // Единицы размера тиража
    const KG = 'kg';
    const PIECES = 'pieces';
    
    // Типы наценки
    const ET_NOPRINT = 1; // Пленка без печати
    const ET_PRINT = 2; // Пленка с печатью без ламинации
    const ET_PRINT_1 = 3; // Пленка с печатью и ламинацией
    const ET_PRINT_2 = 4; // Пленка с печатью и двумя ламинациями
    
    // Лыжи
    const NO_SKI = 1;
    const STANDARD_SKI = 2;
    const NONSTANDARD_SKI = 3;

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
    
    // Машины
    // Используется в условии: если комифлекс, то растворитель флексоль 32, если нет - этоксипропанол
    const COMIFLEX = 'comiflex';
    
    // Получения курса валюты (get - функция получения)
    public static function GetCurrencyRate($currency, $usd, $euro) {
        switch($currency) {
            case self::USD:
                return $usd;
            
            case self::EURO:
                return $euro;
            
            default :
                return 1;
        }
    }
    
    // Получение цены на краску
    function GetInkPrice($ink, $cmyk, $c_price, $c_currency, $m_price, $m_currency, $y_price, $y_currency, $k_price, $k_currency, $panton_price, $panton_currency, $white_price, $white_currency, $lacquer_price, $lacquer_currency) {
        switch ($ink) {
            case self::CMYK:
                switch ($cmyk) {
                    case self::CYAN:
                        return new DataPrice($c_price, $c_currency);
                        
                    case self::MAGENDA:
                        return new DataPrice($m_price, $m_currency);
                        
                    case self::YELLOW:
                        return new DataPrice($y_price, $y_currency);
                        
                    case self::KONTUR:
                        return new DataPrice($k_price, $k_currency);
                        
                    default :
                        return null;
                }
                
            case self::PANTON:
                return new DataPrice($panton_price, $panton_currency);
                
            case self::WHITE:
                return new DataPrice($white_price, $white_currency);
                
            case self::LACQUER:
                return new DataPrice($lacquer_price, $lacquer_currency);
                
            default :
                return null;
        }
    }
    
    // Получение расхода краски
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
    
    // Получение типа лыж
    function GetSkiName($ski) {
        switch ($ski) {
            case Calculation::NO_SKI:
                return "Без лыж";
            case Calculation::STANDARD_SKI:
                return "Стандартные лыжи";
            case Calculation::NONSTANDARD_SKI:
                return "Нестандартные лыжи";
            default :
                return "Неизвестно";
        }
    }
    
    // Получение имени краски
    function GetInkName($ink) {
        switch ($ink) {
            case Calculation::CMYK:
                return "CMYK";
            case Calculation::PANTON:
                return "Пантон";
            case Calculation::WHITE:
                return "Белая";
            case Calculation::LACQUER:
                return "Лак";
            default :
                return "Неизвестная";
        }
    }
    
    // Получения типа формы
    function GetClicheName($cliche) {
        switch ($cliche) {
            case Calculation::OLD:
                return "старая";
            case Calculation::FLINT:
                return "новая Флинт";
            case Calculation::KODAK:
                return "новая Кодак";
            case Calculation::TVER:
                return "новая Тверь";
            default :
                return "Неизвестная";
        }
    }
    
    // Получение называния единицы размера тиража
    function GetUnitName($unit) {
        switch ($unit) {
            case Calculation::KG:
                return "кг";
            case Calculation::PIECES:
                return "шт";
            default :
                return "";
        }
    }
    
    // Получение названия валюты
    function GetCurrencyName($currency) {
        switch ($currency) {
            case Calculation::USD:
                return "USD";
            case Calculation::EURO:
                return "евро";
            default :
                return "руб";
        }
    }
    
    // Отображение чисел в удобном формате
    public static function Display($value, $decimals) {
        if(is_float($value) || is_double($value) || is_int($value)) {
            return number_format($value, $decimals, ",", " ");
        }
        elseif(is_string($value)) {
            return str_replace(".", ",", $value);
        }
        else {
            return $value;
        }
    }
}

// Расчёт
class Calculation extends CalculationBase {
    public $laminations_number = 0; // количество ламинаций
    
    public $uk1, $uk2, $uk3, $ukpf; // уравнивающий коэффициент 1, 2, 3, ПФ
    public $area_pure_start = 0; // м2 чистые, м2 (рассчитывается: длина * ширина * кол-во в шт.; используется для вычисления массы тиража, если он в шт.)
    public $weight = 0; // масса тиража, кг
    public $width_1, $width_2, $width_3; // ширина материала, мм 
    public $area_pure_1, $area_pure_2, $area_pure_3; // м2 чистые, м2 (рассчитывается: вес / плотность)
    public $length_pure_start_1, $length_pure_start_2, $length_pure_start_3; // м пог чистые, м
    public $waste_length_1, $waste_length_2, $waste_length_3; // СтартСтопОтход, м
    public $length_dirty_start_1, $length_dirty_start_2, $length_dirty_start_3; // м пог грязные, м
    public $area_dirty_1, $area_dirty_2, $area_dirty_3; // м2 грязные, м2
    public $weight_pure_1, $weight_pure_2, $weight_pure_3; // масса плёнки чистая, кг
    public $length_pure_1, $length_pure_2, $length_pure_3; // длина плёнки чистая, м
    public $weight_dirty_1, $weight_dirty_2, $weight_dirty_3; // масса плёнки грязная, кг
    public $length_dirty_1, $length_dirty_2, $length_dirty_3; // длина плёнки грязная, кг
    public $film_cost_1, $film_cost_2, $film_cost_3; // стоимость плёнки грязная, руб
    public $priladka_time_1, $priladka_time_2, $priladka_time_3; // время приладки, мин
    public $print_time_1, $lamination_time_2, $lamination_time_3; // время печати или ламинации без приладки, ч
    public $work_time_1, $work_time_2, $work_time_3; // время печати или ламинации с приладкой, ч
    public $work_cost_1, $work_cost_2, $work_cost_3; // стоимость печати или ламирации с приладкой, руб
    public $print_area; // площадь запечатки
    public $ink_1kg_mix_weight; // расход КраскаСмеси на 1 кг краски, кг
    public $ink_flexol82_kg_price; // цена 1 кг чистого флексоля 82 для краски, руб
    public $ink_etoxypropanol_kg_price; // цена 1 кг чистого этоксипропанола для краски, руб
    
    public $ink_kg_prices; // массив: цена 1 кг каждой чистой краски
    public $mix_ink_kg_prices; // массив: цена 1 кг каждой КраскаСмеси
    public $ink_expenses; // массив: расход каждой КраскаСмеси
    public $ink_costs; // массив: стоимость каждой КраскаСмеси
    
    public $glue_kg_weight; // расход КлеяСмеси на 1 кг клея, кг
    public $glue_kg_price; // цена 1 кг чистого клея, руб
    public $glue_solvent_kg_price; // цена 1 кг чистого растворителя для клея, руб
    public $mix_glue_kg_price; // цена 1 кг КлеяСмеси, руб
    public $glue_area2; // площадь заклейки, плёнка 2, м2
    public $glue_area3; // площадь заклейки, плёнка 3, м2
    public $glue_expense2; // расход КлеяСмеси, плёнка 2, кг
    public $glue_expense3; // расход КлеяСмеси, плёнка 3, кг
    public $glue_cost2; // стоимость КлеяСмеси, плёнка 2, руб
    public $glue_cost3; // стоимость КлеяСмеси, плёнка 3, руб
    
    public $cliche_height; // высота формы, мм
    public $cliche_width; // ширина формы, мм
    public $cliche_area; // площадь формы, см2
    public $cliche_new_number; // количество новых форм
    public $cliche_costs; // массив: стоимость каждой формы, руб
    
    public $extracharge = 0; // Наценка на тираж
    public $extracharge_cliche = 0; // Наценка на ПФ
    public $film_cost; // Общая стоимость вссех материалов
    public $work_cost; // Общая стоимость трудозатрат
    public $ink_cost; // Стоимость красок
    public $ink_expense; // Расход красок
    public $glue_cost; // Стоимость клея
    public $cliche_cost; // Себестоимость форм
    public $cost; // Себестоимость
    public $cost_per_unit; // Себестоимость за единицу 
    public $shipping_cost; // Отгрузочная стоимость 
    public $shipping_cost_per_unit; // Отгрузочная стоимость за единицу 
    public $shipping_cliche_cost; // Отгрузочная стоимость форм 
    public $income; // Прибыль
    public $income_per_unit; // Прибыль за единицу
    public $total_weight_dirty; // Общая масса с приладкой 
    public $film_cost_per_unit_1, $film_cost_per_unit_2, $film_cost_per_unit_3; // Масса с приладкой на 1 кг
    public $film_waste_cost_1, $film_waste_cost_2, $film_waste_cost_3; // Отходы, стоимость
    public $film_waste_weight_1, $film_waste_weight_2, $film_waste_weight_3; // Отходы, масса

    // Конструктор
    public function __construct(DataPriladka $data_priladka, 
            DataPriladka $data_priladka_laminator,
            DataMachine $data_machine,
            DataMachine $data_machine_laminator,
            DataInk $data_ink,
            DataGlue $data_glue,
            DataCliche $data_cliche,
            array $data_extracharge,
            $usd, // Курс доллара
            $euro, // Курс евро
            $unit, // Кг или шт
            $quantity, // Размер тиража в кг или шт
            $work_type_id, // Тип работы: с печатью или без печати
        
            $film_1, // Основная пленка, марка
            $thickness_1, // Основная пленка, толщина, мкм
            $density_1, // Основная пленка, плотность, г/м2
            $price_1, // Основная пленка, цена
            $currency_1, // Основная пленка, валюта
            $customers_material_1, // Основная плёнка, другая, материал заказчика
            $ski_1, // Основная пленка, лыжи
            $width_ski_1, // Основная пленка, ширина пленки, мм
        
            $film_2, // Ламинация 1, марка
            $thickness_2, // Ламинация 1, толщина, мкм
            $density_2, // Ламинация 1, плотность, г/м2
            $price_2, // Ламинация 1, цена
            $currency_2, // Ламинация 1, валюта
            $customers_material_2, // Ламинация 1, другая, материал заказчика
            $ski_2, // Ламинация 1, лыжи
            $width_ski_2, // Ламинация 1, ширина пленки, мм
        
            $film_3, // Ламинация 2, марка
            $thickness_3, // Ламинация 2, толщина, мкм
            $density_3, // Ламинация 2, плотность, г/м2
            $price_3, // Ламинация 2, цена
            $currency_3, // Ламинация 2, валюта
            $customers_material_3, // Ламинация 2, другая, уд. вес
            $ski_3, // Ламинация 2, лыжи
            $width_ski_3,  // Ламинация 2, ширина пленки, мм
        
            $machine_id, // Машина
            $machine_shortname, // Короткое наименование машины
            $length, // Длина этикетки, мм
            $stream_width, // Ширина ручья, мм
            $streams_number, // Количество ручьёв
            $raport, // Рапорт
            $lamination_roller_width, // Ширина ламинирующего вала
            $ink_number, // Красочность
            
            $ink_1, $ink_2, $ink_3, $ink_4, $ink_5, $ink_6, $ink_7, $ink_8, // Тип краски (CMYK, пантон, белая, лак)
            $color_1, $color_2, $color_3, $color_4, $color_5, $color_6, $color_7, $color_8, // Номер пантона
            $cmyk_1, $cmyk_2, $cmyk_3, $cmyk_4, $cmyk_5, $cmyk_6, $cmyk_7, $cmyk_8, // Тип CMYK (cyan, magenda, yellow, kontur)
            $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, // Процент данной краски
            $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, // Форма (старая, Флинт, Кодак)
            
            $cliche_in_price, // Включить ПФ в себестоимость
            $extracharge, // Наценка на тираж
            $extracharge_cliche // Наценка на ПФ
            ) {
        // Если нет одной ламинации или обеих, то толщина, плотность и цена плёнок для ламинации имеют пустые значения.
        // Присваиваем им значение 0, чтобы программа не сломалась при попытке вычилений с пустым значением.
        if(empty($thickness_2)) $thickness_2 = 0;
        if(empty($density_2)) $density_2 = 0;
        if(empty($price_2)) $price_2 = 0;
        if(empty($thickness_3)) $thickness_3 = 0;
        if(empty($density_3)) $density_3 = 0;
        if(empty($price_3)) $price_3 = 0;
        
        // Если нет рапорта, ширины вала ламинатора или красочности, присваиваем им 0
        if(empty($raport)) $raport = 0;
        if(empty($lamination_roller_width)) $lamination_roller_width = 0;
        if(empty($ink_number)) $ink_number = 0;
        
        // Определение количества ламинаций
        // Если плёнка 3, толщина 3 и плотность 3 - не пустые, то количество ламинаций - 2
        // Иначе, если плёнка 2, толщина 2 и плотность 2 - не пустые, то количество ламинаций - 1
        // Иначе количество ламинаций - 0
        if(!empty($film_3) && !empty($thickness_3) && !empty($density_3)) {
            $this->laminations_number = 2;
        }
        elseif(!empty ($film_2) && !empty ($thickness_2) && !empty ($density_2)) {
            $this->laminations_number = 1;
        }
        else {
            $this->laminations_number = 0;
        }
        
        // Если тип работы - плёнка без печати, то 
        // машина = пустая, красочность = 0, рапорт = 0
        if($work_type_id == self::WORK_TYPE_NOPRINT) {
            $machine_id = null;
            $ink_number = 0;
            $raport = 0;
        }
        
        // Если нет ламинации, то ширина ламинирующего вала = 0, лыжи для плёнки 2 = 0
        if($this->laminations_number == 0) {
            $lamination_roller_width = 0;
            $ski_2 = 0;
        }
        
        // Если нет ламинации 2, то лыжи для плёнки 3 = 0
        if($this->laminations_number < 2) {
            $ski_3 = 0;
        }
        
        // Если материал заказчика, то его цена = 0
        if($customers_material_1 == true) $price_1 = 0;
        if($customers_material_2 == true) $price_2 = 0;
        if($customers_material_3 == true) $price_3 = 0;
        
        // Уравнивующий коэф 1(УК1)=0 когда нет печати,=1 когда есть печать
        $this->uk1 = $work_type_id == self::WORK_TYPE_PRINT ? 1 : 0;
        
        // Уравнивующий коэф 2 (УК2)=0 когда нет ламинации 1 , = 1 когда есть ламинация 1
        $this->uk2 = $this->laminations_number > 0 ? 1 : 0;
        
        // Уравнивующий коэф 3 (УК3)=0 когда нет ламинации 2, = 1 когда есть ламинация 2
        $this->uk3 = $this->laminations_number > 1 ? 1 : 0;
        
        // Уравнивающий коэфф. ПФ (УКПФ)=0, когда ПФ не велючен в себестоимость, =1 когда ПФ включен в себестоимость
        $this->ukpf = $cliche_in_price == 1 ? 1 : 0;
        
        // НИЖЕ НАЧИНАЕТСЯ ВЫЧИСЛЕНИЕ
        
        // М2 чистые, м2
        // Считаем только если размер тиража выражен в штуках: длина * ширина ручья * размер тиража
        // Если размер тиража выражен в килограммах, то нам м2 чистые пока не нужны, вычислим их позже
        if($unit == self::KG) {
            $this->area_pure_start = 0;
        }
        else {
            $this->area_pure_start = $length * $stream_width * $quantity / 1000000;
        }
        
        // Масса тиража, кг
        // Если размер тиража выражен в килограммах, то это и будет масса тиража
        // Если размер тиража выражен в штуках, то считаем: м2 чистые * сумма плотностей всех плёнок
        if($unit == self::KG) {
            $this->weight = $quantity;
        }
        else {
            $this->weight = $this->area_pure_start * ($density_1 + $density_2 + $density_3) / 1000;
        }

        // Ширина материала 1, мм
        // Если без лыж: количество ручьёв * ширина ручья
        // Если стандартные лыжи: количество ручьёв * ширина ручья + 20
        // Если нестандартные лыжи: ширина материала вводится вручную
        switch($ski_1) {
            case self::NO_SKI:
                $this->width_1 = $streams_number * $stream_width;
                break;
                
            case self::STANDARD_SKI:
                $this->width_1 = $streams_number * $stream_width + 20;
                break;
        
            case self::NONSTANDARD_SKI:
                $this->width_1 = $width_ski_1;
                break;
            
            default :
                $this->width_1 = 0;
                break;
        }
        
        // Ширина материала 2, мм
        switch($ski_2) {
            case self::NO_SKI:
                $this->width_2 = $streams_number * $stream_width;
                break;
        
            case self::STANDARD_SKI:
                $this->width_2 = $streams_number * $stream_width + 20;
                break;
        
            case self::NONSTANDARD_SKI:
                $this->width_2 = $width_ski_2;
                break;
            
            default :
                $this->width_2 = 0;
                break;
        }
        
        // Ширина материала 1, мм
        switch($ski_3) {
            case self::NO_SKI:
                $this->width_3 = $streams_number * $stream_width;
                break;
        
            case self::STANDARD_SKI:
                $this->width_3 = $streams_number * $stream_width + 20;
                break;
        
            case self::NONSTANDARD_SKI:
                $this->width_3 = $width_ski_3;
                break;
            
            default :
                $this->width_3 = 0;
                break;
        }
        
        // М2 чистые 1, м2
        $this->area_pure_1 = $this->weight * 1000 / ($density_1 + $density_2 + $density_3);
        
        // М2 чистые 2, м2
        $this->area_pure_2 = $this->weight * 1000 / ($density_1 + $density_2 + $density_3) * $this->uk2;
        
        // М2 чистые 3, м2
        $this->area_pure_3 = $this->weight * 1000 / ($density_1 + $density_2 + $density_3) * $this->uk3;
        
        
        // М пог чистые 1, м
        $this->length_pure_start_1 = $this->area_pure_1 / ($streams_number * $stream_width / 1000);
        
        // М пог чистые 2, м
        $this->length_pure_start_2 = $this->area_pure_2 / ($streams_number * $stream_width / 1000);
        
        // М пог чистые 3, м
        $this->length_pure_start_3 = $this->area_pure_3 / ($streams_number * $stream_width / 1000);
        
        
        // СтартСтопОтход 1, м
        $this->waste_length_1 = $data_priladka->waste_percent * $this->length_pure_start_1 / 100;
        
        // СтартСтопОтход 2, м
        $this->waste_length_2 = $data_priladka_laminator->waste_percent * $this->length_pure_start_2 / 100;
                
        // СтартСтопОтход 3, м
        $this->waste_length_3 = $data_priladka_laminator->waste_percent * $this->length_pure_start_3 / 100;
        
        
        // М пог грязные 1, м
        $this->length_dirty_start_1 = $this->length_pure_start_1 + ($ink_number * $data_priladka->length) + ($this->laminations_number * $data_priladka_laminator->length) + $this->waste_length_1;
        
        // М пог грязные 2, м
        $this->length_dirty_start_2 = $this->length_pure_start_2 + ($this->laminations_number * $data_priladka_laminator->length) + $this->waste_length_2; 
        
        // М пог грязные 3, м
        $this->length_dirty_start_3 = $this->length_pure_start_3 + ($data_priladka_laminator->length * $this->uk3) + $this->waste_length_3;
        
        
        // М2 грязные 1, м2
        $this->area_dirty_1 = $this->length_dirty_start_1 * $this->width_1 / 1000;
        
        // М2 грязные 2, м2
        $this->area_dirty_2 = $this->length_dirty_start_2 * $this->width_2 / 1000;
        
        // М2 грязные 3, м2
        $this->area_dirty_3 = $this->length_dirty_start_3 * $this->width_3 / 1000;
        
        //****************************************
        // Массы и длины плёнок
        //****************************************
        
        // Масса плёнки чистая 1
        $this->weight_pure_1 = $this->length_pure_start_1 * $this->width_1 * $density_1 / 1000000;
        
        // Масса плёнки чистая 2
        $this->weight_pure_2 = $this->length_pure_start_2 * $this->width_2 * $density_2 / 1000000;
        
        // Масса плёнки чистая 3
        $this->weight_pure_3 = $this->length_pure_start_3 * $this->width_3 * $density_3 / 1000000;
                
    
        // Длина пленки чистая 1, м
        $this->length_pure_1 = $this->length_pure_start_1;
        
        // Длина пленки чистая 2, м
        $this->length_pure_2 = $this->length_pure_start_2;
        
        // Длина пленки чистая 3, м
        $this->length_pure_3 = $this->length_pure_start_3;
        
        
        // Масса плёнки грязная 1, кг
        $this->weight_dirty_1 = $this->area_dirty_1 * $density_1 / 1000;
        
        // Масса плёнки грязная 2, кг
        $this->weight_dirty_2 = $this->area_dirty_2 * $density_2 / 1000;
        
        // Масса плёнки грязная 3, кг
        $this->weight_dirty_3 = $this->area_dirty_3 * $density_3 / 1000;
        
        
        // Длина плёнки грязная 1, м
        $this->length_dirty_1 = $this->length_dirty_start_1;
         
        // Длина плёнки грязная 2, м
        $this->length_dirty_2 = $this->length_dirty_start_2;
        
        // Длина плёнки грязная 3, м
        $this->length_dirty_3 = $this->length_dirty_start_3;
    
        //****************************************
        // Общая стоимость плёнок
        //****************************************
        
        // Общая стоимость грязная 1, руб
        $this->film_cost_1 = $this->weight_dirty_1 * $price_1 * self::GetCurrencyRate($currency_1, $usd, $euro);
        
        // Общая стоимость грязная 2, руб
        $this->film_cost_2 = $this->weight_dirty_2 * $price_2 * self::GetCurrencyRate($currency_2, $usd, $euro);
        
        // Общая стоимость грязная 3, руб
        $this->film_cost_3 = $this->weight_dirty_3 * $price_3 * self::GetCurrencyRate($currency_3, $usd, $euro);
    
        //*****************************************
        // Время - деньги
        //*****************************************
        
        // Время приладки 1, мин
        $this->priladka_time_1 = $ink_number * $data_priladka->time;
        
        // Время приладки 2, мин
        $this->priladka_time_2 = $data_priladka_laminator->time * $this->uk2;
        
        // Время приладки 3, мин
        $this->priladka_time_3 = $data_priladka_laminator->time * $this->uk3;
        

        // Время печати (без приладки) 1, ч
        // Если печати нет, то сразу возвращаем 0, иначе получится деление на 0
        $this->print_time_1 = $data_machine->speed == 0 ? 0 : ($this->length_pure_start_1 + $this->waste_length_1) / $data_machine->speed / 1000 * $this->uk1;
        
        // Время ламинации (без приладки) 2, ч
        // Если печати нет, то сразу возвращаем 0, иначе получится деление на 0
        $this->lamination_time_2 = $data_machine_laminator->speed == 0 ? 0 : ($this->length_pure_start_2 + $this->waste_length_2) / $data_machine_laminator->speed / 1000 * $this->uk2;
        
        // Время ламинации (без приладки) 3, ч
        // Если печати нет, то сразу возвращаем 0, иначе получится деление на 0
        $this->lamination_time_3 = $data_machine_laminator->speed == 0 ? 0 : ($this->length_pure_start_3 + $this->waste_length_3) / $data_machine_laminator->speed / 1000 * $this->uk3;
        
        
        // Общее время выполнения тиража 1, ч
        $this->work_time_1 = $this->priladka_time_1 / 60 + $this->print_time_1;
         
        // Общее время выполнения тиража 2, ч
        $this->work_time_2 = $this->priladka_time_2 / 60 + $this->lamination_time_2;
        
        // Общее время выполнения тиража 3, ч
        $this->work_time_3 = $this->priladka_time_3 / 60 + $this->lamination_time_3;
        
        
        // Стоимость выполнения тиража 1, руб
        $this->work_cost_1 = $this->work_time_1 * $data_machine->price;
        
        // Стоимость выполнения тиража 2, руб
        $this->work_cost_2 = $this->work_time_2 * $data_machine_laminator->price;
        
        // Стоимость выполнения тиража 3, руб
        $this->work_cost_3 = $this->work_time_3 * $data_machine_laminator->price;
        
        //****************************************
        // Расход краски
        //****************************************
        
        // Площадь запечатки, м2
        $this->print_area = $this->length_dirty_1 * ($stream_width * $streams_number + 10) / 1000;
        
        // Расход КраскаСмеси на 1 кг краски, кг
        $this->ink_1kg_mix_weight = 1 + $data_ink->solvent_part;
        
        // Цена 1 кг чистого флексоля 82, руб
        $this->ink_flexol82_kg_price = $data_ink->solvent_flexol82_price * self::GetCurrencyRate($data_ink->solvent_flexol82_currency, $usd, $euro);
        
        // Цена 1 кг чистого этоксипропанола, руб
        $this->ink_etoxypropanol_kg_price = $data_ink->solvent_etoxipropanol_price * self::GetCurrencyRate($data_ink->solvent_etoxipropanol_currency, $usd, $euro);
        
        // Если печатаем на Комифлекс, то пользуемся флексолем82, иначе - этоксипропанолом
        $ink_solvent_kg_price = 0;
        
        if($machine_shortname == self::COMIFLEX) {
            $ink_solvent_kg_price = $this->ink_flexol82_kg_price;
        }
        else {
            $ink_solvent_kg_price = $this->ink_etoxypropanol_kg_price;
        }
        
        // Создаём массивв цен за 1 кг каждой чистой краски
        $this->ink_kg_prices = array();
        
        // Создаём массив цен за 1 кг каждой КраскаСмеси
        $this->mix_ink_kg_prices = array();
        
        // Создаём массив расходов каждой КраскаСмеси
        $this->ink_expenses = array();
        
        // Создаём массив стоимостей каждой КраскаСмеси
        $this->ink_costs = array();
        
        // Перебираем все краски и помещаем в каждый из четырёх массивов данные по каждой краске
        for($i=1; $i<=$ink_number; $i++) {
            $ink = "ink_$i";
            $cmyk = "cmyk_$i";
            $percent = "percent_$i";
            
            // Цена 1 кг чистой краски, руб
            $price = $this->GetInkPrice($$ink, $$cmyk, $data_ink->c_price, $data_ink->c_currency, $data_ink->m_price, $data_ink->m_currency, $data_ink->y_price, $data_ink->y_currency, $data_ink->k_price, $data_ink->k_currency, $data_ink->panton_price, $data_ink->panton_currency, $data_ink->white_price, $data_ink->white_currency, $data_ink->lacquer_price, $data_ink->lacquer_currency);
            $ink_kg_price = $price->value * self::GetCurrencyRate($price->currency, $usd, $euro);
            $this->ink_kg_prices[$i] = $ink_kg_price;
            
            // Цена 1 кг КраскаСмеси, руб
            $mix_ink_kg_price = (($ink_kg_price * 1) + ($ink_solvent_kg_price * $data_ink->solvent_part)) / $this->ink_1kg_mix_weight;
            $this->mix_ink_kg_prices[$i] = $mix_ink_kg_price;
            
            // Расход КраскаСмеси, кг
            $ink_expense = $this->print_area * $this->GetInkExpense($$ink, $$cmyk, $data_ink->c_expense, $data_ink->m_expense, $data_ink->y_expense, $data_ink->k_expense, $data_ink->panton_expense, $data_ink->white_expense, $data_ink->lacquer_expense) * $$percent / 1000 / 100;
            $this->ink_expenses[$i] = $ink_expense;
            
            // Стоимость КраскаСмеси, руб
            $ink_cost = $ink_expense * $mix_ink_kg_price;
            $this->ink_costs[$i] = $ink_cost;
        }
        
        //********************************************
        // Расход клея
        //********************************************
        
        // Расход КлеяСмеси на 1 кг клея, кг
        $this->glue_kg_weight = 1 + $data_glue->solvent_part;
        
        // Цена 1 кг чистого клея, руб
        $this->glue_kg_price = $data_glue->glue_price * self::GetCurrencyRate($data_glue->glue_currency, $usd, $euro);
        
        // Цена 1 кг чистого растворителя для клея, руб
        $this->glue_solvent_kg_price = $data_glue->solvent_price * self::GetCurrencyRate($data_glue->solvent_currency, $usd, $euro);
        
        // Цена 1 кг КлеяСмеси, руб
        $this->mix_glue_kg_price = ((1 * $this->glue_kg_price) + ($data_glue->solvent_part * $this->glue_solvent_kg_price)) / $this->glue_kg_weight;
        
        // Площадь заклейки 2, м2
        $this->glue_area2 = $this->length_dirty_2 * $lamination_roller_width / 1000;
        
        // Площадь заклейки 3, м2
        $this->glue_area3 = $this->length_dirty_3 * $lamination_roller_width / 1000;
        
        // Расход КлеяСмеси 2, кг
        // Если название плёнки начинается на "Pet", то используем расход краски для ламинации ПЭТ
        if((strlen($film_1) > 3 && substr($film_1, 0, 3) == "Pet") || (strlen($film_2) > 3 && substr($film_2, 0, 3) == "Pet")) {
            $this->glue_expense2 = $this->glue_area2 * $data_glue->glue_expense_pet / 1000;
        }
        else {
            $this->glue_expense2 = $this->glue_area2 * $data_glue->glue_expense / 1000;
        }
        
        // Расход КлеяСмеси 3, кг
        // Если название плёнки начинается на "Pet", то используем расход краски для ламинации ПЭТ
        if((strlen($film_2) > 3 && substr($film_2, 0, 3) == "Pet") || (strlen($film_3) > 3 && substr($film_3, 0, 3) == "Pet")) {
            $this->glue_expense3 = $this->glue_area3 * $data_glue->glue_expense_pet / 1000;
        }
        else {
            $this->glue_expense3 = $this->glue_area3 * $data_glue->glue_expense / 1000;
        }
        
        // Стоимость КлеяСмеси 2, руб
        $this->glue_cost2 = $this->glue_expense2 * $this->mix_glue_kg_price;
        
        // Стоимость КлеяСмеси 3, руб
        $this->glue_cost3 = $this->glue_expense3 * $this->mix_glue_kg_price;
        
        //***********************************
        // Стоимость форм
        //***********************************
        
        // Высота форм, мм
        $this->cliche_height = $raport + 20;
        
        // Ширина форм, мм
        $this->cliche_width = ($streams_number * $stream_width + 20) + ((!empty($ski_1) && $ski_1 == self::NO_SKI) ? 0 : 20);
        
        // Площадь форм, см
        $this->cliche_area = $this->cliche_height * $this->cliche_width / 100;
        
        // Создаём массив стоимостей каждой формы
        $this->cliche_costs = array();
        
        // Количество новых форм
        $this->cliche_new_number = 0;
        
        // Перебираем все формы, определяем стоимость каждой, помещаем эту величину в массив
        for($i=1; $i<=$ink_number; $i++) {
            $cliche = "cliche_$i";
            
            // Если форма не старая, то количество новых форм увеличиваем на 1
            if(!empty($$cliche) && $$cliche != self::OLD) {
                $this->cliche_new_number += 1;
            }
            
            $cliche_sm_price = 0;
            $cliche_currency = "";
            
            switch ($$cliche) {
                case self::FLINT:
                    $cliche_sm_price = $data_cliche->flint_price;
                    $cliche_currency = $data_cliche->flint_currency;
                    break;
                
                case self::KODAK:
                    $cliche_sm_price = $data_cliche->kodak_price;
                    $cliche_currency = $data_cliche->kodak_currency;
                    break;
            }
            
            // Стоимость формы, руб
            $cliche_cost = $this->cliche_area * $cliche_sm_price * self::GetCurrencyRate($cliche_currency, $usd, $euro);
            $this->cliche_costs[$i] = $cliche_cost;
        }
        
        //********************************************
        // НАЦЕНКА
        //********************************************
        
        // Если имеющаяся наценка не пустая, оставляем её
        // Если пустая, вычисляем
        if(!empty($extracharge)) {
            $this->extracharge = $extracharge;
        }
        else {
            $ech_type = 0;
            
            if($work_type_id == self::WORK_TYPE_NOPRINT) {
                $ech_type = self::ET_NOPRINT;
            }
            elseif($this->laminations_number == 0) {
                $ech_type = self::ET_PRINT;
            }
            elseif($this->laminations_number == 1) {
                $ech_type = self::ET_PRINT_1;
            }
            elseif($this->laminations_number == 2) {
                $ech_type = self::ET_PRINT_2;
            }
            
            foreach($data_extracharge as $item) {
                if($item->ech_type == $ech_type && round($this->weight) >= $item->min_weight && (round($this->weight) <= $item->max_weight || empty($item->msx_weight))) {
                    $this->extracharge = $item->value;
                }
            }    
        }
        
        // Если УКПФ = 1, то наценка на ПФ всегда 0
        if($this->ukpf == 1) {
            $this->extracharge_cliche = 0;
        }
        else {
            $this->extracharge_cliche = $extracharge_cliche;
        }
        
        //***********************************************
        //ПРАВАЯ ПАНЕЛЬ
        //***********************************************
        
        // Общая стоимость всех материалов
        $this->film_cost = $this->film_cost_1 + $this->film_cost_2 + $this->film_cost_3;
        
        // Общая стоимость трудозатрат
        $this->work_cost = $this->work_cost_1 + $this->work_cost_2 + $this->work_cost_3;
        
        // Общая стоимость всех КраскаСмеси
        $this->ink_cost = 0;
        
        for($i=1; $i<=$ink_number; $i++) {
            $this->ink_cost += $this->ink_costs[$i];
        }
        
        // Общий расход всех КраскаСмеси
        $this->ink_expense = 0;
        
        for($i=1; $i<=$ink_number; $i++) {
            $this->ink_expense += $this->ink_expenses[$i];
        }
        
        // Общая стоимость всех КлеяСмеси
        $this->glue_cost = $this->glue_cost2 + $this->glue_cost3;
        
        // Себестоимость ПФ
        $this->cliche_cost = 0;
        
        for($i=1; $i<=$ink_number; $i++) {
            $this->cliche_cost += $this->cliche_costs[$i];
        }
        
        // Себестоимость
        $this->cost = $this->film_cost + $this->work_cost + $this->ink_cost + $this->glue_cost + ($this->cliche_cost * $this->ukpf);
        
        // Себестоимость за единицу
        $this->cost_per_unit = $this->cost / $quantity;
        
        // Отгрузочная стоимость
        $this->shipping_cost = $this->cost + ($this->cost * $this->extracharge / 100);
        
        // Отгрузочная стоимость за единицу
        $this->shipping_cost_per_unit = $this->shipping_cost / $quantity;
        
        // Прибыль
        $this->income = $this->shipping_cost - $this->cost;
        
        // Прибыль за единицу
        $this->income_per_unit = $this->shipping_cost_per_unit - $this->cost_per_unit;
        
        // Отгрузочная стоимость ПФ
        $this->shipping_cliche_cost = $this->cliche_cost + ($this->cliche_cost * $this->extracharge_cliche / 100);
        
        // Масса плёнки с приладкой
        $this->total_weight_dirty = $this->weight_dirty_1 + $this->weight_dirty_2 + $this->weight_dirty_3;
        
        // Стоимость плёнки на единицу
        $this->film_cost_per_unit_1 = empty($this->weight_dirty_1) ? 0 : $this->film_cost_1 / $this->weight_dirty_1;
        $this->film_cost_per_unit_2 = empty($this->weight_dirty_2) ? 0 : $this->film_cost_2 / $this->weight_dirty_2;
        $this->film_cost_per_unit_3 = empty($this->weight_dirty_3) ? 0 : $this->film_cost_3 / $this->weight_dirty_3;
        
        // Отходы плёнки, стоимость
        $this->film_waste_cost_1 = ($this->weight_dirty_1 - $this->weight_pure_1) * $price_1 * self::GetCurrencyRate($currency_1, $usd, $euro);
        $this->film_waste_cost_2 = ($this->weight_dirty_2 - $this->weight_pure_2) * $price_2 * self::GetCurrencyRate($currency_2, $usd, $euro);
        $this->film_waste_cost_3 = ($this->weight_dirty_3 - $this->weight_pure_3) * $price_3 * self::GetCurrencyRate($currency_3, $usd, $euro);
        
        // Отходы плёнки, масса
        $this->film_waste_weight_1 = $this->weight_dirty_1 - $this->weight_pure_1;
        $this->film_waste_weight_2 = $this->weight_dirty_2 - $this->weight_pure_2;
        $this->film_waste_weight_3 = $this->weight_dirty_3 - $this->weight_pure_3; 
    }
}

// Расчёт для самоклеящейся бумаги
class CalculationSelfAdhesive extends CalculationBase {
    public $width_mat = 0; // Ширина материала
    public $length_label_dirty = 0; // Высота этикетки грязная
    public $width_dirty = 0; // Ширина этикетки грязная
    public $number_in_raport_dirty = 0; // Количество этикеток в рапорте грязный
    public $number_in_raport_pure = 0; // Количество этикеток в рапорте чистый
    public $gap = 0; // Фактический зазор между этикетками
    
    public $area_pure = 0; // М2 чистые, м2
    public $length_pog_pure = 0; // М пог. чистые, м
    public $waste_length = 0; // СтартСтопОтход, м
    public $length_pog_dirty = 0; // М пог. грязные, м
    public $area_dirty = 0; // М2 грязные, м2
    
    public $weight_pure = 0; // Масса плёнки чистая (без приладки), кг
    public $length_pure = 0; // Длина плёнки чистая, м
    public $weight_dirty = 0; // Масса плёнки грязная (с приладкой), кг
    public $length_dirty = 0; // Длина плёнки гразная, м
    
    public $film_cost_dirty = 0; // Себестоимость грязная
    public $priladka_time = 0; // Приладка Время, ч
    public $print_time = 0; // Время печати тиража, без приладки, ч
    public $work_time = 0; // Общее время выполнения тиража, ч
    public $work_cost = 0; // Стоимость выполнения, руб
    
    public $print_area = 0; // М2 запечатки, м2
    public $ink_1kg_mix_weight = 0; // Масса краски в смеси, кг
    public $ink_etoxypropanol_kg_price = 0; // Цена 1 кг чистого этоксипропанола
    
    public $ink_kg_prices; // массив: цена 1 кг каждой чистой краски
    public $mix_ink_kg_prices; // массив: цена 1 кг каждой краскаСмеси
    public $ink_expenses; // массив: расход каждой КраскаСмеси
    public $ink_costs; // массив: стоимость каждой КраскаСмеси

    public function __construct(DataPriladka $data_priladka, 
            DataMachine $data_machine, 
            DataGap $data_gap, 
            DataInk $data_ink, 
            DataCliche $data_cliche, 
            array $data_extracharge, 
            $usd, // Курс доллара
            $euro, // Курс евро
            $quantity, // Размер тиража в шт
            
            $film, // Марка бумаги
            $thickness, // Толщина, мкм
            $density, // Плотность, г/м2
            $price, // Цена
            $currency, // Валюта
            $customers_material, // Материал заказчика
            $ski, // Лыжи
            $width_ski, // Ширина материала
            
            $length, // Длина этикетки
            $stream_width, // Ширина ручья, мм
            $streams_number, // Количество ручьёв
            $raport, // Рапорт
            $ink_number, // Красочность
            
            $ink_1, $ink_2, $ink_3, $ink_4, $ink_5, $ink_6, $ink_7, $ink_8, // Тип краски (CMYK, пантон, белая, лак)
            $color_1, $color_2, $color_3, $color_4, $color_5, $color_6, $color_7, $color_8, // Номер пантона
            $cmyk_1, $cmyk_2, $cmyk_3, $cmyk_4, $cmyk_5, $cmyk_6, $cmyk_7, $cmyk_8, // Тип CMYK (cyan, magenda, yellow, kontur)
            $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, // Процент данной краски
            $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, // Форма (старая, Флинт, Кодак)
            
            $cliche_in_price, // Включить ПФ в себестоимость
            $extracharge, // Наценка на тираж
            $extracharge_cliche // Наценка на ПФ
            ) {
        // Если материал заказчика, то цена его = 0
        if($customers_material == true) $price = 0;
        
        // НИЖЕ НАЧИНАЕТСЯ ВЫЧИСЛЕНИЕ
        
        // Ширина материала, мм
        // Если стадартные лыжи: количество ручьёв * (ширина ручья + расстояние между ручьями) + 20
        // Если нестандартные лыжи: ширина материала вводится вручную
        switch ($ski) {
            case self::STANDARD_SKI:
                $this->width_mat = $streams_number * ($stream_width + $data_gap->gap_stream) + 20;
                break;
            
            case self::NONSTANDARD_SKI:
                $this->width_mat = $width_ski;
                break;
            
            default :
                $this->width_mat = 0;
                break;
        }
        
        // Высота этикетки грязная, мм
        $this->length_label_dirty = $length + $data_gap->gap_raport;
        
        // Ширина этикетки грязная, мм
        $this->width_dirty = $stream_width + $data_gap->gap_stream;
        
        // Количество этикеток в рапорте грязное
        $this->number_in_raport_dirty = $raport / $this->length_label_dirty;
        
        // Количество этикеток в рапорте чистое
        $this->number_in_raport_pure = floor($this->number_in_raport_dirty);
        
        // Фактический зазор, мм
        $this->gap = ($raport - ($length * $this->number_in_raport_pure)) / $this->number_in_raport_pure;
        
        //***************************
        // Рассчёт по КГ
        //***************************
        
        // М2 чистые, м2
        $this->area_pure = ($length + $this->gap) * (($stream_width + $data_gap->gap_stream) + $data_gap->gap_stream) * $quantity / 1000000;
        
        // М. пог. чистые, м
        $this->length_pog_pure = $this->area_pure / (($stream_width + $data_gap->gap_stream) + $data_gap->gap_stream) * $streams_number * 1000;
        
        // СтартСтопОтход, м
        $this->waste_length = $data_priladka->waste_percent * $this->length_pog_pure / 100;
        
        // М пог. грязные, м
        $this->length_pog_dirty = $this->length_pog_pure + ($ink_number * $data_priladka->length) + $this->waste_length;
        
        // М2 грязные, м2
        $this->area_dirty = $this->length_pog_dirty * $this->width_mat / 1000;
        
        //***************************
        // Массы и длины плёнок
        //***************************
        
        // Масса плёнки чистая (без приладки), кг
        $this->weight_pure = $this->length_pog_pure * $this->width_mat * $density / 1000000;
        
        // Длина плёнки чистая, м
        $this->length_pure = $this->length_pog_pure;
        
        // Масса плёнки грязная (с приладкой), кг
        $this->weight_dirty = $this->area_dirty * $density / 1000;
        
        // Длина плёнки грязная, м
        $this->length_dirty = $this->length_pog_dirty;
        
        //*****************************
        // Себестоимость плёнок
        //*****************************
        
        // Себестоимость плёнки грязная (с приладкой), руб
        $this->film_cost_dirty = $this->area_dirty * $price * self::GetCurrencyRate($currency, $usd, $euro);
        
        //*****************************
        // Время - деньги
        //*****************************
        
        // Приладка Время, ч
        $this->priladka_time = $ink_number * $data_priladka->time;
        
        // Время печати тиража, без приладки, ч
        $this->print_time = ($this->length_pog_pure + $this->waste_length) / $data_machine->speed / 1000;
        
        // Общее время выполнения тиража
        $this->work_time = $this->priladka_time + $this->print_time;
        
        // Стоимость выполнения
        $this->work_cost = $this->work_time * $data_machine->price;
        
        //************************
        // Расход краски
        //************************
        
        // М2 запечатки, м2
        $this->print_area = (($stream_width + $data_gap->gap_stream) * ($length * $data_gap->gap_raport) * $quantity / 1000000) + ($this->length_pog_dirty * 0.01);
        
        // Масса краски в смеси, кг
        $this->ink_1kg_mix_weight = 1 + $data_ink->solvent_part;
        
        // Цена 1 кг чистого этоксипропанола, руб
        $this->ink_etoxypropanol_kg_price = $data_ink->solvent_etoxipropanol_price * self::GetCurrencyRate($data_ink->solvent_etoxipropanol_currency, $usd, $euro);
        
        // Создаём массив цен за 1 кг каждой краски
        $this->ink_kg_prices = array();
        
        // Создаём массив цен за 1 кг каждой КраскаСмеси
        $this->mix_ink_kg_prices = array();
        
        // Создаём массив расходов каждой КраскаСмеси
        $this->ink_expenses = array();
        
        // Создаём массив стоимостей каждой КраскаСмеси
        $this->ink_costs = array();
        
        // Перебираем все краски и помещаем в каждый из четырёх массивов данные по каждой краске
        for($i=1; $i<=$ink_number; $i++) {
            $ink = "ink_$i";
            $cmyk = "cmyk_$i";
            $percent = "percent_$i";
            
            // Цена 1 кг чистой краски, руб
            $ink_price = $this->GetInkPrice($$ink, $$cmyk, $data_ink->c_price, $data_ink->c_currency, $data_ink->m_price, $data_ink->m_currency, $data_ink->y_price, $data_ink->y_currency, $data_ink->k_price, $data_ink->k_currency, $data_ink->panton_price, $data_ink->panton_currency, $data_ink->white_price, $data_ink->white_currency, $data_ink->lacquer_price, $data_ink->lacquer_currency);
            $ink_kg_price = $ink_price->value * self::GetCurrencyRate($ink_price->currency, $usd, $euro);
            $this->ink_kg_prices[$i] = $ink_kg_price;
            
            // Цена 1 КраскаСмеси, руб
            $mix_ink_kg_price = (($ink_kg_price * 1) + ($this->ink_etoxypropanol_kg_price * $data_ink->solvent_part)) / $this->ink_1kg_mix_weight;
            $this->mix_ink_kg_prices[$i] = $mix_ink_kg_price;
            
            // Расход КраскаСмеси, кг
            $ink_expense = $this->print_area * $this->GetInkExpense($$ink, $$cmyk, $data_ink->c_expense, $data_ink->m_expense, $data_ink->y_expense, $data_ink->k_expense, $data_ink->panton_expense, $data_ink->white_expense, $data_ink->lacquer_expense);
            $this->ink_expenses[$i] = $ink_expense;
            
            // Стоимость КраскаСмеси, руб
            $ink_cost = $ink_expense * $mix_ink_kg_price;
            $this->ink_costs[$i] = $ink_cost;
        }
    }
}
?>