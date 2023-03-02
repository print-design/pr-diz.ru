<?php
// Данные приладки
class DataPriladka {
    public $time; // Время приладки
    public $length; // Длина приладки 1 краски
    public $stamp; // Длина приладки штампа
    public $waste_percent; // Процент отходов от приладки
    
    // Конструктор
    public function __construct($time, $length, $stamp, $waste_percent) {
        $this->time = $time;
        $this->length = $length;
        $this->stamp = $stamp;
        $this->waste_percent = $waste_percent;
    }
}

// Характеристика печатной машины
class DataMachine {
    public $price; // Цена
    public $speed; // Скорость
    public $width; // Ширина машины
    public $vaporization_expense; // Расход растворителя на испарение
    
    // Контруктор
    public function __construct($price, $speed, $width, $vaporization_expense) {
        $this->price = $price;
        $this->speed = $speed;
        $this->width = $width;
        $this->vaporization_expense = $vaporization_expense;
    }
}

// Характеристика ламинатора
class DataLaminator {
    public $price; // Цена
    public $speed; // Скорость
    public $max_width; // Максимальная ширина
    
    // Конструктор
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
    public $ski; // ширина одной лыжи
    
    // Конструктор
    public function __construct($gap_raport, $gap_stream, $ski) {
        $this->gap_raport = $gap_raport;
        $this->gap_stream = $gap_stream;
        $this->ski = $ski;
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
    public $lacquer_glossy_price;
    public $lacquer_glossy_currency;
    public $lacquer_glossy_expense;
    public $lacquer_matte_price;
    public $lacquer_matte_currency;
    public $lacquer_matte_expense;
    public $solvent_etoxipropanol_price;
    public $solvent_etoxipropanol_currency;
    public $solvent_flexol82_price;
    public $solvent_flexol82_currency;
    public $solvent_part; // Расход растворителя на 1 кг краски
    public $min_price_per_ink; // Мин. стоимость 1 цвета
    public $self_adhesive_laquer_price; // Самоклейка, цена лака за кг
    public $self_adhesive_laquer_currency; // Самоклейка, валюьа лака
    public $self_adhesive_laquer_expense; // Самоклейка, расход чистого лака
    public $min_percent; // Минимальный процент запечатки
    
    // Конструктор
    public function __construct($c_price, $c_currency, $c_expense, $m_price, $m_currency, $m_expense, $y_price, $y_currency, $y_expense, $k_price, $k_currency, $k_expense, $white_price, $white_currency, $white_expense, $panton_price, $panton_currency, $panton_expense, $lacquer_glossy_price, $lacquer_glossy_currency, $lacquer_glossy_expense, $lacquer_matte_price, $lacquer_matte_currency, $lacquer_matte_expense, $solvent_etoxipropanol_price, $solvent_etoxipropanol_currency, $solvent_flexol82_price, $solvent_flexol82_currency, $solvent_part, $min_price_per_ink, $self_adhesive_laquer_price, $self_adhesive_laquer_currency, $self_adhesive_laquer_expense, $min_percent) {
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
        $this->lacquer_glossy_price = $lacquer_glossy_price;
        $this->lacquer_glossy_currency = $lacquer_glossy_currency;
        $this->lacquer_glossy_expense = $lacquer_glossy_expense;
        $this->lacquer_matte_price = $lacquer_matte_price;
        $this->lacquer_matte_currency = $lacquer_matte_currency;
        $this->lacquer_matte_expense = $lacquer_matte_expense;
        $this->solvent_etoxipropanol_price = $solvent_etoxipropanol_price;
        $this->solvent_etoxipropanol_currency = $solvent_etoxipropanol_currency;
        $this->solvent_flexol82_price = $solvent_flexol82_price;
        $this->solvent_flexol82_currency = $solvent_flexol82_currency;
        $this->solvent_part = $solvent_part;
        $this->min_price_per_ink = $min_price_per_ink;
        $this->self_adhesive_laquer_price = $self_adhesive_laquer_price;
        $this->self_adhesive_laquer_currency = $self_adhesive_laquer_currency;
        $this->self_adhesive_laquer_expense = $self_adhesive_laquer_expense;
        $this->min_percent = $min_percent;
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
    const ET_SELF_ADHESIVE = 5; // Самоклеящиеся материалы
    
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
    
    // Лак
    const LACQUER_GLOSSY = "glossy";
    const LACQUER_MATTE = "matte";
    
    // Формы
    const OLD = "old";
    const FLINT = "flint";
    const KODAK = "kodak";
    const REPEAT = "repeat";
    
    // ID ламинатора
    const SOLVENT_YES = 1;
    const SOLVENT_NO = 2;
    
    // Машины
    // Используется в условии: если комифлекс, то растворитель флексоль 32, если нет - этоксипропанол
    const COMIFLEX = 'comiflex';
    
    // Исходные величины для вычислений
    public $data_priladka, $data_priladka_laminator, $data_machine, $data_gap, $data_laminator, $data_ink, $data_glue, $data_cliche, $data_extracharge, 
            $usd, $euro, $date, $name, $unit, $quantity, $quantities, $work_type_id,
            $film_1, $thickness_1, $density_1, $price_1, $currency_1, $customers_material_1, $ski_1, $width_ski_1,
            $film_2, $thickness_2, $density_2, $price_2, $currency_2, $customers_material_2, $ski_2, $width_ski_2,
            $film_3, $thickness_3, $density_3, $price_3, $currency_3, $customers_material_3, $ski_3, $width_ski_3,
            $machine, $machine_id, $machine_shortname, $length, $stream_width, $streams_number, $raport, $lamination_roller_width, $ink_number,
            
            $ink_1, $ink_2, $ink_3, $ink_4, $ink_5, $ink_6, $ink_7, $ink_8, 
            $color_1, $color_2, $color_3, $color_4, $color_5, $color_6, $color_7, $color_8, 
            $cmyk_1, $cmyk_2, $cmyk_3, $cmyk_4, $cmyk_5, $cmyk_6, $cmyk_7, $cmyk_8, 
            $lacquer_1, $lacquer_2, $lacquer_3, $lacquer_4, $lacquer_5, $lacquer_6, $lacquer_7, $lacquer_8, 
            $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, 
            $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, 
            
            $cliche_in_price, $cliches_count_flint, $cliches_count_kodak, $cliches_count_old, $extracharge, $extracharge_cliche, $customer_pays_for_cliche,
            $knife, $extracharge_knife, $knife_in_price, $customer_pays_for_knife, $extra_expense;
    
    // Конструктор
    public function __construct(DataPriladka $data_priladka, 
            DataPriladka $data_priladka_laminator,
            DataMachine $data_machine,
            DataGap $data_gap,
            DataLaminator $data_laminator,
            DataInk $data_ink,
            DataGlue $data_glue,
            DataCliche $data_cliche,
            array $data_extracharge,
            $usd, // Курс доллара
            $euro, // Курс евро
            $date, // Дата
            $name, // Наименование
            $unit, // Кг или шт
            $quantity, // Размер тиража в кг или шт
            array $quantities, // Размер тиража в шт
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
        
            $machine, // Полное наименование машины
            $machine_id, // ID машина
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
            $lacquer_1, $lacquer_2, $lacquer_3, $lacquer_4, $lacquer_5, $lacquer_6, $lacquer_7, $lacquer_8, // Тип лака (глянцевый, матовый)
            $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, // Процент данной краски
            $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, // Форма (старая, Флинт, Кодак)
            
            $cliche_in_price, // Включить ПФ в себестоимость
            $cliches_count_flint, // Количество форм Флинт
            $cliches_count_kodak, // Количество форм Кодак
            $cliches_count_old, // Количество старых форм
            $extracharge, // Наценка на тираж
            $extracharge_cliche, // Наценка на ПФ
            $customer_pays_for_cliche, // Заказчик платит за ПФ
            $knife, // Стоимость ножа
            $extracharge_knife, // Наценка на нож
            $knife_in_price, // Нож включается в стоимость
            $customer_pays_for_knife, // Заказчик платит за нож
            $extra_expense // Дополнительные расходы с кг/шт
            ) {
        $this->data_priladka = $data_priladka;
        $this->data_priladka_laminator = $data_priladka_laminator;
        $this->data_machine = $data_machine;
        $this->data_gap = $data_gap;
        $this->data_laminator = $data_laminator;
        $this->data_ink = $data_ink;
        $this->data_glue = $data_glue;
        $this->data_cliche = $data_cliche;
        $this->data_extracharge = $data_extracharge;
        $this->usd = $usd; // Курс доллара
        $this->euro = $euro; // Курс евро
        $this->date = $date; // Дата
        $this->name = $name; // Наименование
        $this->unit = $unit; // Кг или шт
        $this->quantity = $quantity; // Размер тиража в кг или шт
        $this->quantities = $quantities; // Размер тиража в шт
        $this->work_type_id = $work_type_id; // Тип работы: с печатью или без печати
        
        $this->film_1 = $film_1; // Основная пленка, марка
        $this->thickness_1 = $thickness_1; // Основная пленка, толщина, мкм
        $this->density_1 = $density_1; // Основная пленка, плотность, г/м2
        $this->price_1 = $price_1; // Основная пленка, цена
        $this->currency_1 = $currency_1; // Основная пленка, валюта
        $this->customers_material_1 = $customers_material_1; // Основная плёнка, другая, материал заказчика
        $this->ski_1 = $ski_1; // Основная пленка, лыжи
        $this->width_ski_1 = $width_ski_1; // Основная пленка, ширина пленки, мм
        
        $this->film_2 = $film_2; // Ламинация 1, марка
        $this->thickness_2 = $thickness_2; // Ламинация 1, толщина, мкм
        $this->density_2 = $density_2; // Ламинация 1, плотность, г/м2
        $this->price_2 = $price_2; // Ламинация 1, цена
        $this->currency_2 = $currency_2; // Ламинация 1, валюта
        $this->customers_material_2 = $customers_material_2; // Ламинация 1, другая, материал заказчика
        $this->ski_2 = $ski_2; // Ламинация 1, лыжи
        $this->width_ski_2 = $width_ski_2; // Ламинация 1, ширина пленки, мм
        
        $this->film_3 = $film_3; // Ламинация 2, марка
        $this->thickness_3 = $thickness_3; // Ламинация 2, толщина, мкм
        $this->density_3 = $density_3; // Ламинация 2, плотность, г/м2
        $this->price_3 = $price_3; // Ламинация 2, цена
        $this->currency_3 = $currency_3; // Ламинация 2, валюта
        $this->customers_material_3 = $customers_material_3; // Ламинация 2, другая, уд. вес
        $this->ski_3 = $ski_3; // Ламинация 2, лыжи
        $this->width_ski_3 = $width_ski_3;  // Ламинация 2, ширина пленки, мм
        
        $this->machine = $machine; // Полное наименование машины
        $this->machine_id = $machine_id; // ID машины
        $this->machine_shortname = $machine_shortname; // Короткое наименование машины
        $this->length = $length; // Длина этикетки, мм
        $this->stream_width = $stream_width; // Ширина ручья, мм
        $this->streams_number = $streams_number; // Количество ручьёв
        $this->raport = $raport; // Рапорт
        $this->lamination_roller_width = $lamination_roller_width; // Ширина ламинирующего вала
        $this->ink_number = $ink_number; // Красочность
        
        $this->ink_1 = $ink_1; $this->ink_2 = $ink_2; $this->ink_3 = $ink_3; $this->ink_4 = $ink_4; $this->ink_5 = $ink_5; $this->ink_6 = $ink_6; $this->ink_7 = $ink_7; $this->ink_8 = $ink_8; // Тип краски (CMYK, пантон, белая, лак)
        $this->color_1 = $color_1; $this->color_2 = $color_2; $this->color_3 = $color_3; $this->color_4 = $color_4; $this->color_5 = $color_5; $this->color_6 = $color_6; $this->color_7 = $color_7; $this->color_8 = $color_8; // Номер пантона
        $this->cmyk_1 = $cmyk_1; $this->cmyk_2 = $cmyk_2; $this->cmyk_3 = $cmyk_3; $this->cmyk_4 = $cmyk_4; $this->cmyk_5 = $cmyk_5; $this->cmyk_6 = $cmyk_6; $this->cmyk_7 = $cmyk_7; $this->cmyk_8 = $cmyk_8; // Тип CMYK (cyan, magenda, yellow, kontur)
        $this->lacquer_1 = $lacquer_1; $this->lacquer_2 = $lacquer_2; $this->lacquer_3 = $lacquer_3; $this->lacquer_4 = $lacquer_4; $this->lacquer_5 = $lacquer_5; $this->lacquer_6 = $lacquer_6; $this->lacquer_7 = $lacquer_7; $this->lacquer_8 = $lacquer_8; // Тип лака (глянцевый, матовый)
        $this->percent_1 = $percent_1; $this->percent_2 = $percent_2; $this->percent_3 = $percent_3; $this->percent_4 = $percent_4; $this->percent_5 = $percent_5; $percent_6 = $percent_6; $this->percent_7 = $percent_7; $this->percent_8 = $percent_8; // Процент данной краски
        $this->cliche_1 = $cliche_1; $this->cliche_2 = $cliche_2; $this->cliche_3 = $cliche_3; $this->cliche_4 = $cliche_4; $this->cliche_5 = $cliche_5; $this->cliche_6 = $cliche_6; $this->cliche_7 = $cliche_7; $this->cliche_8 = $cliche_8; // Форма (старая, Флинт, Кодак)
        
        $this->cliche_in_price = $cliche_in_price; // Включить ПФ в себестоимость
        $this->cliches_count_flint = $cliches_count_flint; // Количество форм Флинт
        $this->cliches_count_kodak = $cliches_count_kodak; // Количество форм Кодак
        $this->cliches_count_old = $cliches_count_old; // Количество старых форм
        $this->extracharge = $extracharge; // Наценка на тираж
        $this->extracharge_cliche = $extracharge_cliche; // Наценка на ПФ
        $this->customer_pays_for_cliche = $customer_pays_for_cliche; // Заказчик платит за ПФ
        $this->knife = $knife; // Стоимость ножа
        $this->extracharge_knife = $extracharge_knife; // Наценка на нож
        $this->knife_in_price = $knife_in_price; // Нож включается в стоимость
        $this->customer_pays_for_knife = $customer_pays_for_knife; // Заказчик платит за нож
        $this->extra_expense = $extra_expense;
    }
    
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
    function GetInkPrice($ink, $cmyk, $lacquer, $c_price, $c_currency, $m_price, $m_currency, $y_price, $y_currency, $k_price, $k_currency, $panton_price, $panton_currency, $white_price, $white_currency, $lacquer_glossy_price, $lacquer_glossy_currency, $lacquer_matte_price, $lacquer_matte_currency) {
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
                switch ($lacquer) {
                    case self::LACQUER_MATTE:
                        return new DataPrice($lacquer_matte_price, $lacquer_matte_currency);
                        
                    default :
                        return new DataPrice($lacquer_glossy_price, $lacquer_glossy_currency);
                }
                
            default :
                return null;
        }
    }
    
    // Получение расхода краски
    function GetInkExpense($ink, $cmyk, $lacquer, $c_expense, $m_expense, $y_expense, $k_expense, $panton_expense, $white_expense, $lacquer_glossy_expense, $lacquer_matte_expense) {
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
                switch($lacquer) {
                    case self::LACQUER_MATTE:
                        return $lacquer_matte_expense;
                        
                    default :
                        return $lacquer_glossy_expense;
                }
                
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
    
    // Подготовка параметров для конструктора класса расчёта
    // и создание объекта расчёта
    public static function Create($id) {
        // ПОЛУЧЕНИЕ ИСХОДНЫХ ДАННЫХ
        $date = null;
        $name = null;
        $unit = null; // Кг или шт
        $quantity = null; // Размер тиража
        $work_type_id = null; // Типа работы: с печатью или без печати
        
        $film_1 = null; // Основная пленка, марка
        $thickness_1 = null; // Основная пленка, толщина, мкм
        $density_1 = null; // Основная пленка, плотность, г/м2
        $price_1 = null; // Основная пленка, цена
        $currency_1 = null; // Основная пленка, валюта
        $customers_material_1 = null; // Основная плёнка, другая, материал заказчика
        $ski_1 = null; // Основная пленка, лыжи
        $width_ski_1 = null; // Основная пленка, ширина пленки, мм
        
        $film_2 = null; // Ламинация 1, марка
        $thickness_2 = null; // Ламинация 1, толщина, мкм
        $density_2 = null; // Ламинация 1, плотность, г/м2
        $price_2 = null; // Ламинация 1, цена
        $currency_2 = null; // Ламинация 1, валюта
        $customers_material_2 = null; // Ламинация 1, другая, материал заказчика
        $ski_2 = null; // Ламинация 1, лыжи
        $width_ski_2 = null; // Ламинация 1, ширина пленки, мм
        
        $film_3 = null; // Ламинация 2, марка
        $thickness_3 = null; // Ламинация 2, толщина, мкм
        $density_3 = null; // Ламинация 2, плотность, г/м2
        $price_3 = null; // Ламинация 2, цена
        $currency_3 = null; // Ламинация 2, валюта
        $customers_material_3 = null; // Ламинация 2, другая, уд. вес
        $ski_3 = null; // Ламинация 2, лыжи
        $width_ski_3 = null;  // Ламинация 2, ширина пленки, мм
        
        $machine = null;
        $machine_shortname = null;
        $machine_id = null;
        $laminator = null;
        $laminator_id = null;
        $length = null; // Длина этикетки, мм
        $width = null; // Обрезная ширина, мм (если плёнка без печати)
        $stream_width = null; // Ширина ручья, мм (если плёнка с печатью)
        $streams_number = null; // Количество ручьёв
        $raport = null; // Рапорт
        $lamination_roller_width = null; // Ширина ламинирующего вала
        $ink_number = 0; // Красочность
        
        $cliche_in_price = null; // Включить формы в стоимость
        $extracharge = null; // Наценка на тираж
        $extracharge_cliche = null; // Наценка на ПФ
        $customer_pays_for_cliche = null; // Заказчик платит за ПФ
        $extra_expense = null; // Дополнительные расходы с кг/шт
        
        $sql = "select rc.date, rc.name, rc.unit, rc.quantity, rc.work_type_id, "
                . "f.name film, fv.thickness thickness, fv.weight density, "
                . "rc.film_variation_id, rc.price, rc.currency, rc.individual_film_name, rc.individual_thickness, rc.individual_density, "
                . "rc.customers_material, rc.ski, rc.width_ski, "
                . "lamination1_f.name lamination1_film, lamination1_fv.thickness lamination1_thickness, lamination1_fv.weight lamination1_density, "
                . "rc.lamination1_film_variation_id, rc.lamination1_price, rc.lamination1_currency, rc.lamination1_individual_film_name, rc.lamination1_individual_thickness, rc.lamination1_individual_density, "
                . "rc.lamination1_customers_material, rc.lamination1_ski, rc.lamination1_width_ski, "
                . "lamination2_f.name lamination2_film, lamination2_fv.thickness lamination2_thickness, lamination2_fv.weight lamination2_density, "
                . "rc.lamination2_film_variation_id, rc.lamination2_price, rc.lamination2_currency, rc.lamination2_individual_film_name, rc.lamination2_individual_thickness, rc.lamination2_individual_density, "
                . "rc.lamination2_customers_material, rc.lamination2_ski, rc.lamination2_width_ski, "
                . "m.name machine, m.shortname machine_shortname, rc.machine_id, lam.name laminator, rc.laminator_id, rc.length, rc.stream_width, rc.streams_number, rc.raport, rc.lamination_roller_width, rc.ink_number, "
                . "rc.ink_1, rc.ink_2, rc.ink_3, rc.ink_4, rc.ink_5, rc.ink_6, rc.ink_7, rc.ink_8, "
                . "rc.color_1, rc.color_2, rc.color_3, rc.color_4, rc.color_5, rc.color_6, rc.color_7, rc.color_8, "
                . "rc.cmyk_1, rc.cmyk_2, rc.cmyk_3, rc.cmyk_4, rc.cmyk_5, rc.cmyk_6, rc.cmyk_7, rc.cmyk_8, "
                . "rc.lacquer_1, rc.lacquer_2, rc.lacquer_3, rc.lacquer_4, rc.lacquer_5, rc.lacquer_6, rc.lacquer_7, rc.lacquer_8, "
                . "rc.percent_1, rc.percent_2, rc.percent_3, rc.percent_4, rc.percent_5, rc.percent_6, rc.percent_7, rc.percent_8, "
                . "rc.cliche_1, rc.cliche_2, rc.cliche_3, rc.cliche_4, rc.cliche_5, rc.cliche_6, rc.cliche_7, rc.cliche_8, "
                . "rc.cliche_in_price, rc.cliches_count_flint, rc.cliches_count_kodak, rc.cliches_count_old, rc.extracharge, rc.extracharge_cliche, rc.customer_pays_for_cliche, "
                . "rc.knife, rc.extracharge_knife, rc.knife_in_price, rc.customer_pays_for_knife, rc.extra_expense "
                . "from calculation rc "
                . "left join machine m on rc.machine_id = m.id "
                . "left join laminator lam on rc.laminator_id = lam.id "
                . "left join film_variation fv on rc.film_variation_id = fv.id "
                . "left join film f on fv.film_id = f.id "
                . "left join film_variation lamination1_fv on rc.lamination1_film_variation_id = lamination1_fv.id "
                . "left join film lamination1_f on lamination1_fv.film_id = lamination1_f.id "
                . "left join film_variation lamination2_fv on rc.lamination2_film_variation_id = lamination2_fv.id "
                . "left join film lamination2_f on lamination2_fv.film_id = lamination2_f.id "
                . "where rc.id = $id";
        $fetcher = new Fetcher($sql);
        
        if ($row = $fetcher->Fetch()) {
            $date = $row['date'];
            $name = $row['name'];
            
            $unit = $row['unit']; // Кг или шт
            $quantity = $row['quantity']; // Размер тиража в кг или шт
            $work_type_id = $row['work_type_id']; // Тип работы: с печатью или без печати
            
            if(!empty($row['film_variation_id'])) {
                $film_1 = $row['film']; // Основная пленка, марка
                $thickness_1 = $row['thickness']; // Основная пленка, толщина, мкм
                $density_1 = $row['density']; // Основная пленка, плотность, г/м2
            }
            else {
                $film_1 = $row['individual_film_name']; // Основная пленка, марка
                $thickness_1 = $row['individual_thickness']; // Основная пленка, толщина, мкм
                $density_1 = $row['individual_density']; // Основная пленка, плотность, г/м2
            }
            $price_1 = $row['price']; // Основная пленка, цена
            $currency_1 = $row['currency']; // Основная пленка, валюта
            $customers_material_1 = $row['customers_material']; // Основная плёнка, другая, материал заказчика
            $ski_1 = $row['ski']; // Основная пленка, лыжи
            $width_ski_1 = $row['width_ski']; // Основная пленка, ширина пленки, мм
            
            if(!empty($row['lamination1_film_variation_id'])) {
                $film_2 = $row['lamination1_film']; // Ламинация 1, марка
                $thickness_2 = $row['lamination1_thickness']; // Ламинация 1, толщина, мкм
                $density_2 = $row['lamination1_density']; // Ламинация 1, плотность, г/м2
            }
            else {
                $film_2 = $row['lamination1_individual_film_name']; // Ламинация 1, марка
                $thickness_2 = $row['lamination1_individual_thickness']; // Ламинация 1, толщина, мкм
                $density_2 = $row['lamination1_individual_density']; // Ламинация 1, плотность, г/м2
            }
            $price_2 = $row['lamination1_price']; // Ламинация 1, цена
            $currency_2 = $row['lamination1_currency']; // Ламинация 1, валюта
            $customers_material_2 = $row['lamination1_customers_material']; // Ламинация 1, другая, материал заказчика
            $ski_2 = $row['lamination1_ski']; // Ламинация 1, лыжи
            $width_ski_2 = $row['lamination1_width_ski']; // Ламинация 1, ширина пленки, мм
            
            if(!empty($row['lamination2_film_variation_id'])) {
                $film_3 = $row['lamination2_film']; // Ламинация 2, марка
                $thickness_3 = $row['lamination2_thickness']; // Ламинация 2, толщина, мкм
                $density_3 = $row['lamination2_density']; // Ламинация 2, плотность, г/м2
            }
            else {
                $film_3 = $row['lamination2_individual_film_name']; // Ламинация 2, марка
                $thickness_3 = $row['lamination2_individual_thickness']; // Ламинация 2, толщина, мкм
                $density_3 = $row['lamination2_individual_density']; // Ламинация 2, плотность, г/м2
            }
            $price_3 = $row['lamination2_price']; // Ламинация 2, цена
            $currency_3 = $row['lamination2_currency']; // Ламинация 2, валюта
            $customers_material_3 = $row['lamination2_customers_material']; // Ламинация 2, другая, уд. вес
            $ski_3 = $row['lamination2_ski']; // Ламинация 2, лыжи
            $width_ski_3 = $row['lamination2_width_ski'];  // Ламинация 2, ширина пленки, мм
            
            $machine = $row['machine'];
            $machine_shortname = $row['machine_shortname'];
            $machine_id = $row['machine_id'];
            $laminator = $row['laminator'];
            $laminator_id = $row['laminator_id'];
            $length = $row['length']; // Длина этикетки, мм
            $stream_width = $row['stream_width']; // Ширина ручья, мм
            $streams_number = $row['streams_number']; // Количество ручьёв
            $raport = $row['raport']; // Рапорт
            $lamination_roller_width = $row['lamination_roller_width']; // Ширина ламинирующего вала
            $ink_number = $row['ink_number']; // Красочность
            
            $ink_1 = $row['ink_1']; $ink_2 = $row['ink_2']; $ink_3 = $row['ink_3']; $ink_4 = $row['ink_4']; $ink_5 = $row['ink_5']; $ink_6 = $row['ink_6']; $ink_7 = $row['ink_7']; $ink_8 = $row['ink_8'];
            $color_1 = $row['color_1']; $color_2 = $row['color_2']; $color_3 = $row['color_3']; $color_4 = $row['color_4']; $color_5 = $row['color_5']; $color_6 = $row['color_6']; $color_7 = $row['color_7']; $color_8 = $row['color_8'];
            $cmyk_1 = $row['cmyk_1']; $cmyk_2 = $row['cmyk_2']; $cmyk_3 = $row['cmyk_3']; $cmyk_4 = $row['cmyk_4']; $cmyk_5 = $row['cmyk_5']; $cmyk_6 = $row['cmyk_6']; $cmyk_7 = $row['cmyk_7']; $cmyk_8 = $row['cmyk_8'];
            $lacquer_1 = $row['lacquer_1']; $lacquer_2 = $row['lacquer_2']; $lacquer_3 = $row['lacquer_3']; $lacquer_4 = $row['lacquer_4']; $lacquer_5 = $row['lacquer_5']; $lacquer_6 = $row['lacquer_6']; $lacquer_7 = $row['lacquer_7']; $lacquer_8 = $row['lacquer_8'];
            $percent_1 = $row['percent_1']; $percent_2 = $row['percent_2']; $percent_3 = $row['percent_3']; $percent_4 = $row['percent_4']; $percent_5 = $row['percent_5']; $percent_6 = $row['percent_6']; $percent_7 = $row['percent_7']; $percent_8 = $row['percent_8'];
            $cliche_1 = $row['cliche_1']; $cliche_2 = $row['cliche_2']; $cliche_3 = $row['cliche_3']; $cliche_4 = $row['cliche_4']; $cliche_5 = $row['cliche_5']; $cliche_6 = $row['cliche_6']; $cliche_7 = $row['cliche_7']; $cliche_8 = $row['cliche_8'];
            
            $cliche_in_price = $row['cliche_in_price']; // Включать стоимиость ПФ в тираж
            $cliches_count_flint = $row['cliches_count_flint']; // Количество форм Флинт
            $cliches_count_kodak = $row['cliches_count_kodak']; // Количество форм Кодак
            $cliches_count_old = $row['cliches_count_old']; // Количество старых форм
            $extracharge = $row['extracharge']; // Наценка на тираж
            $extracharge_cliche = $row['extracharge_cliche']; // Наценка на ПФ
            $customer_pays_for_cliche = $row['customer_pays_for_cliche']; // Заказчик платит за ПФ
            
            $knife = $row['knife']; // Стоимость ножа
            $extracharge_knife = $row['extracharge_knife']; // Наценка на нож
            $knife_in_price = $row['knife_in_price']; // Нож включен в себестоимость
            $customer_pays_for_knife = $row['customer_pays_for_knife']; // Заказчик платит за нож
            $extra_expense = $row['extra_expense']; // Дополнительные расходы с кг/шт
            
            // Если тип работы - плёнка без печати, то 
            // машина = пустая, красочность = 0, рапорт = 0
            if($work_type_id == Calculation::WORK_TYPE_NOPRINT) {
                $machine_id = null;
                $ink_number = 0;
                $raport = 0;
            }
            
            // Если нет ламинации, то ламинатор = пустой, ширина ламинирующего вала = 0, лыжи для плёнки 2 = 0
            if(empty($film_2) && empty($film_3)) {
                $laminator_id = null;
                $lamination_roller_width = 0;
                $ski_2 = 0;
            }
            
            // Если нет ламинации 2, то лыжи для плёнки 3 = 0
            if(empty($film_3)) {
                $ski_3 = 0;
            }
        }
        
        // Курсы валют
        $usd = null;
        $euro = null;
        
        if(!empty($date)) {
            $sql = "select usd, euro from currency where date <= '$date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $usd = $row['usd'];
                $euro = $row['euro'];
            }
        }
        
        // Размеры тиражей
        $quantities = array();
        
        if($work_type_id == CalculationBase::WORK_TYPE_SELF_ADHESIVE && empty($error_message)) {
            $sql = "select id, quantity from calculation_quantity where calculation_id = $id";
            $fetcher = new Fetcher($sql);
            
            while($row = $fetcher->Fetch()) {
                $quantities[$row['id']] = $row['quantity'];
            }
        }
        
        // ПОЛУЧЕНИЕ НОРМ
        $data_priladka = new DataPriladka(null, null, null, null);
        $data_priladka_laminator = new DataPriladka(null, null, null, null);
        $data_machine = new DataMachine(null, null, null, null);
        $data_laminator = new DataLaminator(null, null, null);
        $data_gap = new DataGap(null, null, null);
        $data_ink = new DataInk(null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
        $data_glue = new DataGlue(null, null, null, null, null, null, null);
        $data_cliche = new DataCliche(null, null, null, null, null, null);
        $data_extracharge = array();
        
        if(!empty($date)) {
            if(empty($machine_id)) {
                $data_priladka = new DataPriladka(0, 0, 0, 0);
            }
            else {
                $sql = "select time, length, stamp, waste_percent from norm_priladka where date <= '$date' and machine_id = $machine_id order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                if ($row = $fetcher->Fetch()) {
                    $data_priladka = new DataPriladka($row['time'], $row['length'], $row['stamp'], $row['waste_percent']);
                }
            }
            
            if(empty($laminator_id)) {
                $data_priladka_laminator = new DataPriladka(0, 0, 0, 0);
            }
            else {
                $sql = "select time, length, waste_percent from norm_laminator_priladka where date <= '$date' and laminator_id = $laminator_id order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                if($row = $fetcher->Fetch()) {
                    $data_priladka_laminator = new DataPriladka($row['time'], $row['length'], 0, $row['waste_percent']);
                }
            }
            
            if(empty($machine_id)) {
                $data_machine = new DataMachine(0, 0, 0, 0);
            }
            else {
                $sql = "select price, speed, width, vaporization_expense from norm_machine where date <= '$date' and machine_id = $machine_id order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                if ($row = $fetcher->Fetch()) {
                    $data_machine = new DataMachine($row['price'], $row['speed'], $row['width'], $row['vaporization_expense']);
                }
            }
            
            if(empty($laminator_id)) {
                $data_laminator = new DataLaminator(0, 0, 0);
            }
            else {
                $sql = "select price, speed, max_width from norm_laminator where date <= '$date' and laminator_id = $laminator_id order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                if($row = $fetcher->Fetch()) {
                    $data_laminator = new DataLaminator($row['price'], $row['speed'], $row['max_width']);
                }
            }
            
            $sql = "select gap_raport, gap_stream, ski from norm_gap where date <= '$date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $data_gap = new DataGap($row['gap_raport'], $row['gap_stream'], $row['ski']);
            }
            
            $sql = "select c_price, c_currency, c_expense, m_price, m_currency, m_expense, y_price, y_currency, y_expense, k_price, k_currency, k_expense, white_price, white_currency, white_expense, panton_price, panton_currency, panton_expense, lacquer_glossy_price, lacquer_glossy_currency, lacquer_glossy_expense, lacquer_matte_price, lacquer_matte_currency, lacquer_matte_expense, solvent_etoxipropanol_price, solvent_etoxipropanol_currency, solvent_flexol82_price, solvent_flexol82_currency, solvent_part, min_price_per_ink, self_adhesive_laquer_price, self_adhesive_laquer_currency, self_adhesive_laquer_expense, min_percent "
                    . "from norm_ink where date <= '$date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $data_ink = new DataInk($row['c_price'], $row['c_currency'], $row['c_expense'], $row['m_price'], $row['m_currency'], $row['m_expense'], $row['y_price'], $row['y_currency'], $row['y_expense'], $row['k_price'], $row['k_currency'], $row['k_expense'], $row['white_price'], $row['white_currency'], $row['white_expense'], $row['panton_price'], $row['panton_currency'], $row['panton_expense'], $row['lacquer_glossy_price'], $row['lacquer_glossy_currency'], $row['lacquer_glossy_expense'], $row['lacquer_matte_price'], $row['lacquer_matte_currency'], $row['lacquer_matte_expense'], $row['solvent_etoxipropanol_price'], $row['solvent_etoxipropanol_currency'], $row['solvent_flexol82_price'], $row['solvent_flexol82_currency'], $row['solvent_part'], $row['min_price_per_ink'], $row['self_adhesive_laquer_price'], $row['self_adhesive_laquer_currency'], $row['self_adhesive_laquer_expense'], $row['min_percent']);
            }
            
            if(empty($laminator_id)) {
                $data_glue = new DataGlue(0, 0, 0, 0, 0, 0, 0);
            }
            else {
                $sql = "select glue_price, glue_currency, glue_expense, glue_expense_pet, solvent_price, solvent_currency, solvent_part "
                        . "from norm_glue where date <= '$date' and laminator_id = $laminator_id order by id desc limit 1";
                $fetcher = new Fetcher($sql);
                if($row = $fetcher->Fetch()) {
                    $data_glue = new DataGlue($row['glue_price'], $row['glue_currency'], $row['glue_expense'], $row['glue_expense_pet'], $row['solvent_price'], $row['solvent_currency'], $row['solvent_part']);
                }
            }
            
            $sql = "select flint_price, flint_currency, kodak_price, kodak_currency, scotch_price, scotch_currency "
                    . "from norm_cliche where date <= '$date' order by id desc limit 1";
            $fetcher = new Fetcher($sql);
            if($row = $fetcher->Fetch()) {
                $data_cliche = new DataCliche($row['flint_price'], $row['flint_currency'], $row['kodak_price'], $row['kodak_currency'], $row['scotch_price'], $row['scotch_currency']);
            }
            
            $sql = "select extracharge_type_id, from_weight, to_weight, value from extracharge";
            $fetcher = new Fetcher($sql);
            while($row = $fetcher->Fetch()) {
                array_push($data_extracharge, new DataExtracharge($row['value'], $row['extracharge_type_id'], $row['from_weight'], $row['to_weight']));
            }
        }
    
        if($work_type_id == self::WORK_TYPE_SELF_ADHESIVE && empty($error_message)) {
            return new CalculationSelfAdhesive($data_priladka, 
                    $data_priladka_laminator,
                    $data_machine,
                    $data_gap,
                    $data_laminator,
                    $data_ink,
                    $data_glue,
                    $data_cliche,
                    $data_extracharge,
                    $usd, // Курс доллара
                    $euro, // Курс евро
                    $date, // Дата
                    $name, // Наименование
                    $unit, // Кг или шт
                    $quantity, // Размер тиража в кг или шт
                    $quantities, // Размер тиража в шт
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
                    
                    $machine, // Полное наименование машины
                    $machine_id, // ID машины
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
                    $lacquer_1, $lacquer_2, $lacquer_3, $lacquer_4, $lacquer_5, $lacquer_6, $lacquer_7, $lacquer_8, // Тип лака (глянцевый, матовый)
                    $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, // Процент данной краски
                    $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, // Форма (старая, Флинт, Кодак)
                    
                    $cliche_in_price, // Включить ПФ в себестоимость
                    $cliches_count_flint, // Количество форм Флинт
                    $cliches_count_kodak, // Количество форм Кодак
                    $cliches_count_old, // Количество старых форм
                    $extracharge, // Наценка на тираж
                    $extracharge_cliche, // Наценка на ПФ
                    $customer_pays_for_cliche, // Заказчик платит за ПФ
                    $knife, // Стоимость ножа
                    $extracharge_knife, // Наценка на нож
                    $knife_in_price, // Нож включается в стоимость
                    $customer_pays_for_knife, // Заказчик платит за нож
                    $extra_expense); // Дополнительные расходы с кг/шт
        }
        elseif(empty ($error_message)) {
            return new Calculation($data_priladka, 
                    $data_priladka_laminator,
                    $data_machine,
                    $data_gap,
                    $data_laminator,
                    $data_ink,
                    $data_glue,
                    $data_cliche,
                    $data_extracharge,
                    $usd, // Курс доллара
                    $euro, // Курс евро
                    $date, // Дата
                    $name, // Наименование
                    $unit, // Кг или шт
                    $quantity, // Размер тиража в кг или шт
                    $quantities, // Размер тиража в шт
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
                    
                    $machine, // Полное наименование машины
                    $machine_id, // ID машина
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
                    $lacquer_1, $lacquer_2, $lacquer_3, $lacquer_4, $lacquer_5, $lacquer_6, $lacquer_7, $lacquer_8, // Тип лака (глянцевый, матовый)
                    $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, // Процент данной краски
                    $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, // Форма (старая, Флинт, Кодак)
                    
                    $cliche_in_price, // Включить ПФ в себестоимость
                    $cliches_count_flint, // Количество форм Флинт
                    $cliches_count_kodak, // Количество форм Кодак
                    $cliches_count_old, // Количество старых форм
                    $extracharge, // Наценка на тираж
                    $extracharge_cliche, // Наценка на ПФ
                    $customer_pays_for_cliche, // Заказчик платит за ПФ
                    $knife, // Стоимость ножа
                    $extracharge_knife, // Наценка на нож
                    $knife_in_price, // Нож включается в стоимость
                    $customer_pays_for_knife, // Заказчик платит за нож
                    $extra_expense); // Дополнительные расходы с кг/шт
        }
        else {
            return $error_message;
        }
    }
}

// Расчёт
class Calculation extends CalculationBase {
    public $laminations_number = 0; // количество ламинаций
    
    public $uk1, $uk2, $uk3, $ukpf, $ukcuspaypf; // уравнивающий коэффициент 1, 2, 3, ПФ, ЗаказчикПлатитЗаПФ
    public $area_pure_start = 0; // м2 чистые, м2 (рассчитывается: длина * ширина * кол-во в шт.; используется для вычисления массы тиража, если он в шт.)
    public $weight = 0; // масса тиража, кг
    public $width_start_1, $width_start_2, $width_start_3; // ширина материала начальная (до приведения к числу кратному 5), мм
    public $width_1, $width_2, $width_3; // ширина материала кратная 5, мм 
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
    
    public $vaporization_area_dirty; // м2 испарения грязная
    public $vaporization_areas_pure; // массив: м2 испарения чистая
    public $vaporization_expenses; // массив: расход испарения растворителя, кг
    public $vaporization_costs; // массив: стоимость испарения растворителя, руб
    
    public $ink_kg_prices; // массив: цена 1 кг каждой чистой краски
    public $mix_ink_kg_prices; // массив: цена 1 кг каждой КраскаСмеси
    public $ink_expenses; // массив: расход каждой КраскаСмеси
    public $ink_costs; // массив: стоимость каждой КраскаСмеси
    public $ink_costs_mix; // массив: расход краска + растворитель каждой краски
    public $ink_costs_final; // массив: стоимость каждой КраскаСмеси финальная
    
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
    public $cliche_area; // площадь формы, м2
    public $cliche_new_number; // количество новых форм
    public $cliche_costs; // массив: стоимость каждой формы, руб
    public $scotch_costs; // массив: стоимость скотча
    public $scotch_cost; // общая себестоимость скотча
    
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
    public $income_cliche; // Прибыль ПФ
    public $total_weight_dirty; // Общая масса с приладкой 
    public $film_cost_per_unit_1, $film_cost_per_unit_2, $film_cost_per_unit_3; // Масса с приладкой на 1 кг
    public $film_waste_cost_1, $film_waste_cost_2, $film_waste_cost_3; // Отходы, стоимость
    public $film_waste_weight_1, $film_waste_weight_2, $film_waste_weight_3; // Отходы, масса
    public $total_extra_expense; // Общие дополнительные расходы

    // Конструктор
    public function __construct(DataPriladka $data_priladka, 
            DataPriladka $data_priladka_laminator,
            DataMachine $data_machine,
            DataGap $data_gap,
            DataLaminator $data_laminator,
            DataInk $data_ink,
            DataGlue $data_glue,
            DataCliche $data_cliche,
            array $data_extracharge,
            $usd, // Курс доллара
            $euro, // Курс евро
            $date, // Дата
            $name, // Наименование
            $unit, // Кг или шт
            $quantity, // Размер тиража в кг или шт
            array $quantities, // Размер тиража в шт
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
        
            $machine, // Полное наименование машины
            $machine_id, // ID машины
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
            $lacquer_1, $lacquer_2, $lacquer_3, $lacquer_4, $lacquer_5, $lacquer_6, $lacquer_7, $lacquer_8, // Тип лака (глянцевый, матовый)
            $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, // Процент данной краски
            $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, // Форма (старая, Флинт, Кодак)
            
            $cliche_in_price, // Включить ПФ в себестоимость
            $cliches_count_flint, // Количество форм Флинт
            $cliches_count_kodak, // Количество форм Кодак
            $cliches_count_old, // Количество старых форм
            $extracharge, // Наценка на тираж
            $extracharge_cliche, // Наценка на ПФ
            $customer_pays_for_cliche, // Заказчик платит за ПФ
            $knife, // Стоимость ножа
            $extracharge_knife, // Наценка на нож
            $knife_in_price, // Нож включается в стоимость
            $customer_pays_for_knife, // Заказчик платит за нож
            $extra_expense // Дополнительные расходы с кг/шт
            ) {
        parent::__construct($data_priladka, $data_priladka_laminator, $data_machine, $data_gap, $data_laminator, $data_ink, $data_glue, $data_cliche, $data_extracharge, 
                $usd, $euro, $date, $name, $unit, $quantity, $quantities, $work_type_id, 
                $film_1, $thickness_1, $density_1, $price_1, $currency_1, $customers_material_1, $ski_1, $width_ski_1, 
                $film_2, $thickness_2, $density_2, $price_2, $currency_2, $customers_material_2, $ski_2, $width_ski_2, 
                $film_3, $thickness_3, $density_3, $price_3, $currency_3, $customers_material_3, $ski_3, $width_ski_3, 
                $machine, $machine_id, $machine_shortname, $length, $stream_width, $streams_number, $raport, $lamination_roller_width, $ink_number, 
                $ink_1, $ink_2, $ink_3, $ink_4, $ink_5, $ink_6, $ink_7, $ink_8, 
                $color_1, $color_2, $color_3, $color_4, $color_5, $color_6, $color_7, $color_8, 
                $cmyk_1, $cmyk_2, $cmyk_3, $cmyk_4, $cmyk_5, $cmyk_6, $cmyk_7, $cmyk_8, 
                $lacquer_1, $lacquer_2, $lacquer_3, $lacquer_4, $lacquer_5, $lacquer_6, $lacquer_7, $lacquer_8, 
                $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, 
                $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, 
                $cliche_in_price, $cliches_count_flint, $cliches_count_kodak, $cliches_count_old, $extracharge, $extracharge_cliche, $customer_pays_for_cliche, 
                $knife, $extracharge_knife, $knife_in_price, $customer_pays_for_knife, $extra_expense);
                
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
        
        // Уравнивающий коэфф. ЗаказчикПлатитЗаПФ, когда платит заказчик = 1, когда платим мы = 0
        $this->ukcuspaypf = $customer_pays_for_cliche == 1 ? 1 : 0;
        
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

        // Ширина материала (начальная) 1, мм
        // Если без лыж: количество ручьёв * ширина ручья
        // Если стандартные лыжи: количество ручьёв * ширина ручья + 20
        // Если нестандартные лыжи: ширина материала вводится вручную
        switch($ski_1) {
            case self::NO_SKI:
                $this->width_start_1 = $streams_number * $stream_width;
                break;
                
            case self::STANDARD_SKI:
                $this->width_start_1 = $streams_number * $stream_width + 20;
                break;
        
            case self::NONSTANDARD_SKI:
                $this->width_start_1 = $width_ski_1;
                break;
            
            default :
                $this->width_start_1 = 0;
                break;
        }
        
        // Ширина материала (начальная) 2, мм
        switch($ski_2) {
            case self::NO_SKI:
                $this->width_start_2 = $streams_number * $stream_width;
                break;
        
            case self::STANDARD_SKI:
                $this->width_start_2 = $streams_number * $stream_width + 20;
                break;
        
            case self::NONSTANDARD_SKI:
                $this->width_start_2 = $width_ski_2;
                break;
            
            default :
                $this->width_start_2 = 0;
                break;
        }
        
        // Ширина материала (начальная) 3, мм
        switch($ski_3) {
            case self::NO_SKI:
                $this->width_start_3 = $streams_number * $stream_width;
                break;
        
            case self::STANDARD_SKI:
                $this->width_start_3 = $streams_number * $stream_width + 20;
                break;
        
            case self::NONSTANDARD_SKI:
                $this->width_start_3 = $width_ski_3;
                break;
            
            default :
                $this->width_start_3 = 0;
                break;
        }
        
        // Ширина материала (кратная 5) 1, мм
        $this->width_1 = ceil($this->width_start_1 / 5) * 5;
        
        // Ширина материала (кратная 5) 2, мм
        $this->width_2 = ceil($this->width_start_2 / 5) * 5;
        
        // Ширина материала (кратная 5) 3, мм
        $this->width_3 = ceil($this->width_start_3 / 5) * 5;
        
        
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
        $this->priladka_time_1 = $ink_number * $data_priladka->time / 60;
        
        // Время приладки 2, мин
        $this->priladka_time_2 = $data_priladka_laminator->time * $this->uk2 / 60;
        
        // Время приладки 3, мин
        $this->priladka_time_3 = $data_priladka_laminator->time * $this->uk3 / 60;
        

        // Время печати (без приладки) 1, ч
        // Если печати нет, то сразу возвращаем 0, иначе получится деление на 0
        $this->print_time_1 = $data_machine->speed == 0 ? 0 : ($this->length_pure_start_1 + $this->waste_length_1) / $data_machine->speed / 1000 * $this->uk1;
        
        // Время ламинации (без приладки) 2, ч
        // Если печати нет, то сразу возвращаем 0, иначе получится деление на 0
        $this->lamination_time_2 = $data_laminator->speed == 0 ? 0 : ($this->length_pure_start_2 + $this->waste_length_2) / $data_laminator->speed / 1000 * $this->uk2;
        
        // Время ламинации (без приладки) 3, ч
        // Если печати нет, то сразу возвращаем 0, иначе получится деление на 0
        $this->lamination_time_3 = $data_laminator->speed == 0 ? 0 : ($this->length_pure_start_3 + $this->waste_length_3) / $data_laminator->speed / 1000 * $this->uk3;
        
        
        // Общее время выполнения тиража 1, ч
        $this->work_time_1 = $this->priladka_time_1 + $this->print_time_1;
         
        // Общее время выполнения тиража 2, ч
        $this->work_time_2 = $this->priladka_time_2 + $this->lamination_time_2;
        
        // Общее время выполнения тиража 3, ч
        $this->work_time_3 = $this->priladka_time_3 + $this->lamination_time_3;
        
        
        // Стоимость выполнения тиража 1, руб
        $this->work_cost_1 = $this->work_time_1 * $data_machine->price;
        
        // Стоимость выполнения тиража 2, руб
        $this->work_cost_2 = $this->work_time_2 * $data_laminator->price;
        
        // Стоимость выполнения тиража 3, руб
        $this->work_cost_3 = $this->work_time_3 * $data_laminator->price;
        
        //****************************************
        // Расход краски
        //****************************************
        
        // Площадь запечатки, м2
        $this->print_area = $this->length_dirty_1 * ($stream_width * $streams_number + 10) / 1000;
        
        // Расход КраскаСмеси на 1 кг краски, кг
        $this->ink_1kg_mix_weight = 1 + $data_ink->solvent_part;
        
        // Цена 1 кг чистого флексоля 82, руб
        $this->ink_flexol82_kg_price = $data_ink->solvent_flexol82_price;
        
        // Цена 1 кг чистого этоксипропанола, руб
        $this->ink_etoxypropanol_kg_price = $data_ink->solvent_etoxipropanol_price;
        
        // Если печатаем на Комифлекс, то пользуемся флексолем82, иначе - этоксипропанолом
        $ink_solvent_kg_price = 0;
        $ink_solvent_currency = 1;
        
        if($machine_shortname == self::COMIFLEX) {
            $ink_solvent_kg_price = $this->ink_flexol82_kg_price;
            $ink_solvent_currency = self::GetCurrencyRate($data_ink->solvent_flexol82_currency, $usd, $euro);
        }
        else {
            $ink_solvent_kg_price = $this->ink_etoxypropanol_kg_price;
            $ink_solvent_currency = self::GetCurrencyRate($data_ink->solvent_etoxipropanol_currency, $usd, $euro);
        }
        
        // М2 испарения растворителя грязная, м2
        $this->vaporization_area_dirty = $data_machine->width * $this->length_dirty_start_1 / 1000;
        
        // Создаём массив: м2 испарения чистая
        $this->vaporization_areas_pure = array();
        
        // Создаём массив: расход испарения растворителя, кг
        $this->vaporization_expenses = array();
        
        // Создаём массив: стоимость испарения растворителя, руб
        $this->vaporization_costs = array();
        
        // Создаём массивв цен за 1 кг каждой чистой краски
        $this->ink_kg_prices = array();
        
        // Создаём массив цен за 1 кг каждой КраскаСмеси
        $this->mix_ink_kg_prices = array();
        
        // Создаём массив расходов каждой КраскаСмеси
        $this->ink_expenses = array();
        
        // Создаём массив стоимостей каждой КраскаСмеси
        $this->ink_costs = array();
        
        // Создаём массив расходов краска + растворитель
        $this->ink_costs_mix = array();
        
        // Создаём массив финальных стоимостей каждой КраскаСмеси
        $this->ink_costs_final = array();
        
        // Перебираем все краски и помещаем в каждый из четырёх массивов данные по каждой краске
        for($i=1; $i<=$ink_number; $i++) {
            $ink = "ink_$i";
            $cmyk = "cmyk_$i";
            $lacquer = "lacquer_$i";
            $percent = "percent_$i";
            
            // Цена 1 кг чистой краски, руб
            $price = $this->GetInkPrice($$ink, $$cmyk, $$lacquer, $data_ink->c_price, $data_ink->c_currency, $data_ink->m_price, $data_ink->m_currency, $data_ink->y_price, $data_ink->y_currency, $data_ink->k_price, $data_ink->k_currency, $data_ink->panton_price, $data_ink->panton_currency, $data_ink->white_price, $data_ink->white_currency, $data_ink->lacquer_glossy_price, $data_ink->lacquer_glossy_currency, $data_ink->lacquer_matte_price, $data_ink->lacquer_matte_currency);
            $ink_kg_price = $price->value * self::GetCurrencyRate($price->currency, $usd, $euro);
            $this->ink_kg_prices[$i] = $ink_kg_price;
            
            // Цена 1 кг КраскаСмеси, руб
            $mix_ink_kg_price = (($ink_kg_price * 1) + ($ink_solvent_kg_price * $data_ink->solvent_part)) / $this->ink_1kg_mix_weight;
            $this->mix_ink_kg_prices[$i] = $mix_ink_kg_price;
            
            // Расход КраскаСмеси, кг
            $ink_expense = $this->print_area * $this->GetInkExpense($$ink, $$cmyk, $$lacquer, $data_ink->c_expense, $data_ink->m_expense, $data_ink->y_expense, $data_ink->k_expense, $data_ink->panton_expense, $data_ink->white_expense, $data_ink->lacquer_glossy_expense, $data_ink->lacquer_matte_expense) * $$percent / 1000 / 100;
            $this->ink_expenses[$i] = $ink_expense;
            
            // Стоимость КраскаСмеси, руб
            $ink_cost = $ink_expense * $mix_ink_kg_price;
            $this->ink_costs[$i] = $ink_cost;
            
            // М2 испарения растворителя чистая, м2
            $this->vaporization_areas_pure[$i] = $this->vaporization_area_dirty - ($this->print_area * $$percent / 100);
        
            // Расход испарения растворителя, кг
            $this->vaporization_expenses[$i] = $this->vaporization_areas_pure[$i] * $data_machine->vaporization_expense / 1000;
        
            // Стоимость испарения растворителя, руб
            $this->vaporization_costs[$i] = $this->vaporization_expenses[$i] * $ink_solvent_kg_price * $ink_solvent_currency;
            
            // Расход (краска + растворитель на одну краску), руб
            $this->ink_costs_mix[$i] = $this->ink_costs[$i] + $this->vaporization_costs[$i];
            
            // Стоимость КраскаСмеси финальная, руб
            if($this->ink_costs_mix[$i] < $data_ink->min_price_per_ink) {
                $this->ink_costs_final[$i] = floatval($data_ink->min_price_per_ink);
            }
            else {
                $this->ink_costs_final[$i] = floatval($this->ink_costs_mix[$i]);
            }
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
        
        // Высота форм, м
        $this->cliche_height = ($raport + 20) / 1000;
        
        // Ширина форм, м
        $this->cliche_width = ($streams_number * $stream_width + 20 + ((!empty($ski_1) && $ski_1 == self::NO_SKI) ? 0 : 20)) / 1000;
        
        // Площадь форм, м2
        $this->cliche_area = $this->cliche_height * $this->cliche_width;
        
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
        // Стоимость скотча
        //********************************************
        
        // Стоимость скотча для каждой краски
        $this->scotch_costs = array();
        
        for($i=1; $i<=8; $i++) {
            $cliche_area = 0;
            
            if($i <= $ink_number) {
                $cliche_area = $this->cliche_area;
            }
            
            $this->scotch_costs[$i] = $cliche_area * $data_cliche->scotch_price * self::GetCurrencyRate($data_cliche->scotch_currency, $usd, $euro);
        }
        
        // Общая себестоимость скотча
        $this->scotch_cost = array_sum($this->scotch_costs);
        
        //********************************************
        // НАЦЕНКА
        //********************************************
        
        // Если имеющаяся наценка не пустая, оставляем её
        // Если пустая, вычисляем
        if(!empty($extracharge) && $extracharge > 0) { // !!!! Значение 0.000 не считается EMPTY
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
                if($item->ech_type == $ech_type && round($this->weight) >= $item->min_weight && (round($this->weight) <= $item->max_weight || empty($item->max_weight))) {
                    $this->extracharge = $item->value;
                }
            }
        }
        
        // Наценка на ПФ
        $this->extracharge_cliche = $extracharge_cliche;
        
        // Если УКПФ = 1 (то есть, ПФ включены в себестоимость), то наценка на ПФ всегда 0
        if($this->ukpf == 1) {
            $this->extracharge_cliche = 0;
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
            $this->ink_cost += $this->ink_costs_final[$i];
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
        
        // Отгрузочная стоимость ПФ
        $this->shipping_cliche_cost = $this->cliche_cost * (1 + ($this->extracharge_cliche / 100)) * $this->ukcuspaypf * (($this->ukpf - 1) / -1);
        
        // Прибыль ПФ
        $this->income_cliche = ($this->shipping_cliche_cost - $this->cliche_cost) * (($this->ukpf - 1) / -1);
        
        // Себестоимость
        $this->cost = $this->film_cost + $this->work_cost + $this->ink_cost + $this->glue_cost + ($this->cliche_cost * $this->ukpf) + $this->scotch_cost;
        
        // Себестоимость за единицу
        $this->cost_per_unit = $this->cost / $quantity;
        
        // Отгрузочная стоимость
        $this->shipping_cost = $this->cost * (1 + ($this->extracharge / 100));
        
        // Отгрузочная стоимость за единицу
        $this->shipping_cost_per_unit = $this->shipping_cost / $quantity;
        
        // !!!! Корректируем отгрузочную стоимость, чтобы она точно равнялась стоимости за единицу (округлённой до 3), умноженной на размер тиража
        $this->shipping_cost = round($this->shipping_cost_per_unit, 3) * $quantity;
        
        // Прибыль
        $this->income = ($this->shipping_cost - $this->cost) - ($extra_expense * $quantity);
        
        // Прибыль за единицу
        $this->income_per_unit = $this->shipping_cost_per_unit - $this->cost_per_unit - $extra_expense;
        
        // Масса плёнки с приладкой
        $this->total_weight_dirty = $this->weight_dirty_1 + $this->weight_dirty_2 + $this->weight_dirty_3;
        
        // Стоимость плёнки на единицу
        $this->film_cost_per_unit_1 = $price_1 * self::GetCurrencyRate($currency_1, $usd, $euro);
        $this->film_cost_per_unit_2 = $price_2 * self::GetCurrencyRate($currency_2, $usd, $euro);
        $this->film_cost_per_unit_3 = $price_3 * self::GetCurrencyRate($currency_3, $usd, $euro);
        
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
    public $quantity = 0; // Суммарное количество этикеток
    public $quantities_count = 0; // Количество тиражей
    public $ukpf, $ukcuspaypf; // Уравнивающий коэффициент ПФ, ЗаказчикПлатитЗаПФ
    public $ukknife, $ukcuspayknife; // Уравнивающий коэффициент нож, ЗаказчикПлатитЗаНож
    
    public $width_start = 0; // Ширина материала (начальная), мм
    public $width_mat = 0; // Ширина материала (кратная 5), мм
    public $length_label_dirty = 0; // Высота этикетки грязная
    public $width_dirty = 0; // Ширина этикетки грязная
    public $number_in_raport_dirty = 0; // Количество этикеток в рапорте грязный
    public $number_in_raport_pure = 0; // Количество этикеток в рапорте чистый
    public $gap = 0; // Фактический зазор между этикетками
    
    public $priladka_printing = 0; // Метраж приладки одного тиража, м
    public $area_pure = 0; // М2 чистые, м2
    public $length_pog_pure = 0; // М пог. чистые, м
    public $waste_length = 0; // СтартСтопОтход, м
    public $length_pog_dirty = 0; // М пог. грязные, м
    public $area_dirty = 0; // М2 грязные, м2
    
    public $weight_pure = 0; // Масса плёнки чистая (без приладки), кг
    public $length_pure = 0; // Длина плёнки чистая, м
    public $weight_dirty = 0; // Масса плёнки грязная (с приладкой), кг
    public $length_dirty = 0; // Длина плёнки грязная, м
    
    public $film_cost = 0; // Себестоимость грязная
    public $priladka_time = 0; // Приладка Время, ч
    public $print_time = 0; // Время печати тиража, без приладки, ч
    public $work_time = 0; // Общее время выполнения тиража, ч
    public $work_cost = 0; // Стоимость выполнения, руб
    
    public $print_area = 0; // М2 запечатки, м2
    public $ink_1kg_mix_weight = 0; // Масса краски в смеси, кг
    public $ink_etoxypropanol_kg_price = 0; // Цена 1 кг чистого этоксипропанола
    
    public $vaporization_area_dirty; // м2 испарения грязная
    public $vaporization_area_pure; // м2 испарения чистая
    public $vaporization_expense; // расход испарения растворителя, кг
    
    public $ink_kg_prices; // массив: цена 1 кг каждой чистой краски
    public $mix_ink_kg_prices; // массив: цена 1 кг каждой краскаСмеси
    public $ink_expenses; // массив: расход каждой КраскаСмеси
    public $ink_costs; // массив: стоимость каждой КраскаСмеси
    public $ink_costs_mix; // массив: расход краска + растворитель каждой краски
    public $ink_costs_final; // массив: стоимость каждой КраскаСммеси финальная
    
    public $cliche_height; // Высота формы, мм
    public $cliche_width; // ширина формы, мм
    public $cliche_area; // площадь формы, мм
    public $cliche_flint_price; // себестоимость формы Флинт, руб
    public $cliche_kodak_price; // себестоимость формы Кодак, руб
    public $cliche_all_flint_price; // себестоимость всех форм Флинт, руб
    public $cliche_all_kodak_price; // себестоимость всех форм Кодак, руб
    public $cliche_new_number; // количество новых форм
    public $scotch_costs; // массив: стоимость скотча
    public $scotch_cost; // общая себестоимость скотча
    
    public $extracharge = 0; // Наценка на тираж
    public $extracharge_cliche = 0; // Наценка на ПФ
    public $extracharge_knife = 0; // Наценка на нож
    public $ink_cost; // стоимость красок
    public $ink_expense; // расход красок
    public $cost; // себестоимость
    public $cost_per_unit; // себестоимость за единицу
    public $cliche_cost; // себестоимость форм
    public $knife_cost; // себестоимость ножа
    public $shipping_cost; // отгрузочная стоимость
    public $shipping_cost_per_unit; // отгрузочная стоимость за диницу
    public $shipping_cliche_cost; // отгрузочная стоимость ПФ
    public $shipping_knife_cost; // отгрузочная стоимость ножа
    public $income; // прибыль
    public $income_per_unit; // прибыль за единицу
    public $income_cliche; // прибыль ПФ
    public $income_knife; // прибыль на нож
    public $total_weight_dirty; // общая масса с приладкой
    public $film_cost_per_unit; // Масса с приладкой на 1 кг
    public $film_waste_cost; // отходы, стоимость
    public $film_waste_weight; // отходы, масса
    public $total_extra_expense; // Общие дополнительные расходы
    
    public $lengths; // Длины тиражей

    public function __construct(DataPriladka $data_priladka, 
            DataPriladka $data_priladka_laminator,
            DataMachine $data_machine,
            DataGap $data_gap,
            DataLaminator $data_laminator,
            DataInk $data_ink,
            DataGlue $data_glue,
            DataCliche $data_cliche,
            array $data_extracharge,
            $usd, // Курс доллара
            $euro, // Курс евро
            $date, // Дата
            $name, // Наименование
            $unit, // Кг или шт
            $quantity, // Размер тиража в кг или шт
            array $quantities, // Размер тиража в шт
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
        
            $machine, // Полное наименование машины
            $machine_id, // ID машины
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
            $lacquer_1, $lacquer_2, $lacquer_3, $lacquer_4, $lacquer_5, $lacquer_6, $lacquer_7, $lacquer_8, // Тип лака (глянцевый, матовый)
            $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, // Процент данной краски
            $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, // Форма (старая, Флинт, Кодак)
            
            $cliche_in_price, // Включить ПФ в себестоимость
            $cliches_count_flint, // Количество форм Флинт
            $cliches_count_kodak, // Количество форм Кодак
            $cliches_count_old, // Количество старых форм
            $extracharge, // Наценка на тираж
            $extracharge_cliche, // Наценка на ПФ
            $customer_pays_for_cliche, // Заказчик платит за ПФ
            $knife, // Стоимость ножа
            $extracharge_knife, // Наценка на нож
            $knife_in_price, // Нож включается в стоимость
            $customer_pays_for_knife, // Заказчик платит за нож
            $extra_expense // Дополнительные расходы с кг/шт
            ) {
        parent::__construct($data_priladka, $data_priladka_laminator, $data_machine, $data_gap, $data_laminator, $data_ink, $data_glue, $data_cliche, $data_extracharge, 
                $usd, $euro, $date, $name, $unit, $quantity, $quantities, $work_type_id, 
                $film_1, $thickness_1, $density_1, $price_1, $currency_1, $customers_material_1, $ski_1, $width_ski_1, 
                $film_2, $thickness_2, $density_2, $price_2, $currency_2, $customers_material_2, $ski_2, $width_ski_2, 
                $film_3, $thickness_3, $density_3, $price_3, $currency_3, $customers_material_3, $ski_3, $width_ski_3, 
                $machine, $machine_id, $machine_shortname, $length, $stream_width, $streams_number, $raport, $lamination_roller_width, $ink_number, 
                $ink_1, $ink_2, $ink_3, $ink_4, $ink_5, $ink_6, $ink_7, $ink_8, 
                $color_1, $color_2, $color_3, $color_4, $color_5, $color_6, $color_7, $color_8, 
                $cmyk_1, $cmyk_2, $cmyk_3, $cmyk_4, $cmyk_5, $cmyk_6, $cmyk_7, $cmyk_8, 
                $lacquer_1, $lacquer_2, $lacquer_3, $lacquer_4, $lacquer_5, $lacquer_6, $lacquer_7, $lacquer_8, 
                $percent_1, $percent_2, $percent_3, $percent_4, $percent_5, $percent_6, $percent_7, $percent_8, 
                $cliche_1, $cliche_2, $cliche_3, $cliche_4, $cliche_5, $cliche_6, $cliche_7, $cliche_8, 
                $cliche_in_price, $cliches_count_flint, $cliches_count_kodak, $cliches_count_old, $extracharge, $extracharge_cliche, $customer_pays_for_cliche, 
                $knife, $extracharge_knife, $knife_in_price, $customer_pays_for_knife, $extra_expense);
        
        // Суммарный размер тиража
        $this->quantity = array_sum($quantities);
        
        // Количество тиражей
        $this->quantities_count = count($quantities);
        
        // Если материал заказчика, то цена его = 0
        if($customers_material_1 == true) $price = 0;
        
        // Уравнивающий коэфф. ПФ (УКПФ)=0, когда ПФ не включен в стоимость, =1, когда ПФ включен в стоимость
        $this->ukpf = $cliche_in_price == 1 ? 1 : 0;
        
        // Уравнивающий коэффициент ЗаказчикПлатитЗаПФ: когда платит заказчик = 1, когда платим мы = 0
        $this->ukcuspaypf = $customer_pays_for_cliche == 1 ? 1 : 0;
        
        // Уравнивающий коэфф. нож (УКНОЖ)=0, когда нож не включен в стоимость, =1, когда нож включен в стоимость
        $this->ukknife = $knife_in_price == 1 ? 1 : 0;
        
        // Уравнивающий коэфф. ЗаказчикПлатитЗаНож: когда платит заказчик = 1, когда платим мы = 0
        $this->ukcuspayknife = $customer_pays_for_knife == 1 ? 1 : 0;
        
        // НИЖЕ НАЧИНАЕТСЯ ВЫЧИСЛЕНИЕ
        
        // Ширина материала (начальная), мм
        // Если стадартные лыжи: (количество ручьёв * (ширина ручья + расстояние между ручьями)) + (ширина одной лыжи * 2)
        // Если нестандартные лыжи: ширина материала вводится вручную
        switch ($ski_1) {
            case self::STANDARD_SKI:
                $this->width_start = ($streams_number * ($stream_width + $data_gap->gap_stream)) + ($data_gap->ski * 2);
                break;
            
            case self::NONSTANDARD_SKI:
                $this->width_start = $width_ski_1;
                break;
            
            default :
                $this->width_start = 0;
                break;
        }
        
        // Ширина материала (кратная 5), мм
        $this->width_mat = ceil($this->width_start / 5) * 5;
        
        // Высота этикетки грязная, мм
        $this->length_label_dirty = $length + $data_gap->gap_raport;
        
        // Ширина этикетки грязная, мм
        $this->width_dirty = $stream_width + $data_gap->gap_stream;
        
        // Количество этикеток в рапорте грязное
        $this->number_in_raport_dirty = $raport / $this->length_label_dirty;
        
        // Количество этикеток в рапорте чистое
        $this->number_in_raport_pure = floor($this->number_in_raport_dirty);
        
        // Защита от деления на ноль
        if($this->number_in_raport_pure == 0) $this->number_in_raport_pure = 1;
        
        // Фактический зазор, мм
        $this->gap = ($raport - ($length * $this->number_in_raport_pure)) / $this->number_in_raport_pure;
        
        //***************************
        // Рассчёт по КГ
        //***************************
        
        // Метраж приладки одного тиража, м
        $this->priladka_printing = ($ink_number * $data_priladka->length) + $data_priladka->stamp;
        
        // М2 чистые, м2
        $this->area_pure = ($length + $this->gap) * ($stream_width + $data_gap->gap_stream) * $this->quantity / 1000000;
        
        // М. пог. чистые, м
        $this->length_pog_pure = $this->area_pure / ($this->width_dirty * $streams_number / 1000);
        
        // СтартСтопОтход, м
        $this->waste_length = $data_priladka->waste_percent * $this->length_pog_pure / 100;
        
        // М пог. грязные, м
        $this->length_pog_dirty = $this->length_pog_pure + ($this->quantities_count * $this->priladka_printing) + $this->waste_length;
        
        // М2 грязные, м2
        $this->area_dirty = $this->length_pog_dirty * $this->width_mat / 1000;
        
        //***************************
        // Массы и длины плёнок
        //***************************
        
        // Масса плёнки чистая (без приладки), кг
        $this->weight_pure = $this->length_pog_pure * $this->width_mat * $density_1 / 1000000;
        
        // Длина плёнки чистая, м
        $this->length_pure = $this->length_pog_pure;
        
        // Масса плёнки грязная (с приладкой), кг
        $this->weight_dirty = $this->area_dirty * $density_1 / 1000;
        
        // Длина плёнки грязная, м
        $this->length_dirty = $this->length_pog_dirty;
        
        //*****************************
        // Себестоимость плёнок
        //*****************************
        
        // Себестоимость плёнки грязная (с приладкой), руб
        $this->film_cost = $this->area_dirty * $price_1 * self::GetCurrencyRate($currency_1, $usd, $euro);
        
        //*****************************
        // Время - деньги
        //*****************************
        
        // Время приладки, ч
        $this->priladka_time = $ink_number * $data_priladka->time / 60 * $this->quantities_count;
        
        // Время печати тиража, без приладки, ч
        $this->print_time = ($this->length_pog_pure + $this->waste_length) / $data_machine->speed / 1000;
        
        // Общее время выполнения тиража, ч
        $this->work_time = $this->priladka_time + $this->print_time;
        
        // Стоимость выполнения, руб
        $this->work_cost = $this->work_time * $data_machine->price;
        
        //************************
        // Расход краски
        //************************
        
        // М2 запечатки, м2
        $this->print_area = (($stream_width + $data_gap->gap_stream) * ($length + $data_gap->gap_raport) * $this->quantity / 1000000) + ($this->length_pog_dirty * 0.01);
        
        // Масса краски в смеси, кг
        $this->ink_1kg_mix_weight = 1 + $data_ink->solvent_part;
        
        // Цена 1 кг чистого этоксипропанола, руб
        $this->ink_etoxypropanol_kg_price = $data_ink->solvent_etoxipropanol_price * self::GetCurrencyRate($data_ink->solvent_etoxipropanol_currency, $usd, $euro);
        
        
        // М2 испарения грязная, м2
        $this->vaporization_area_dirty = $data_machine->width * $this->length_pog_dirty / 100;
        
        // М2 испарения чистая, м2
        $this->vaporization_area_pure = $this->vaporization_area_dirty - $this->print_area;
        
        // Расход испарения растворителя, кг
        $this->vaporization_expense = $this->vaporization_area_pure * $data_machine->vaporization_expense;
        
        
        // Создаём массив цен за 1 кг каждой краски
        $this->ink_kg_prices = array();
        
        // Создаём массив цен за 1 кг каждой КраскаСмеси
        $this->mix_ink_kg_prices = array();
        
        // Создаём массив расходов каждой КраскаСмеси
        $this->ink_expenses = array();
        
        // Создаём массив стоимостей каждой КраскаСмеси
        $this->ink_costs = array();
        
        // Создаём массив расходов краска + растворитель
        $this->ink_costs_mix = array();
        
        // Создаём массив финальных стоимостей каждой КраскаСмеси
        $this->ink_costs_final = array();
        
        // Перебираем все краски и помещаем в каждый из четырёх массивов данные по каждой краске
        for($i=1; $i<=$ink_number; $i++) {
            $ink = "ink_$i";
            $cmyk = "cmyk_$i";
            $lacquer = "lacquer_$i";
            $percent = "percent_$i";
            
            // Поскольку в самоклейке лак используется без растворителя, для лака используем другой расчёт
            if($$ink == CalculationBase::LACQUER) {
                // Цена 1 кг чистой краски, руб
                $ink_kg_price = $data_ink->self_adhesive_laquer_price * self::GetCurrencyRate($data_ink->self_adhesive_laquer_currency, $usd, $euro);
                $this->ink_kg_prices[$i] = $ink_kg_price;
                
                // Расход чистой краски, кг
                $ink_expense = $this->print_area * $data_ink->self_adhesive_laquer_expense * $$percent / 1000 / 100;
                $this->ink_expenses[$i] = $ink_expense;
                
                // Стоимость чистой краски, руб
                $ink_cost = $ink_expense * $ink_kg_price;
                $this->ink_costs[$i] = $ink_cost;
                
                // Поскольку здесь лак используется без растворителя, то стоимость финальная не меняется
                $this->ink_costs_final[$i] = $this->ink_costs[$i];
            }
            else {
                // Цена 1 кг чистой краски, руб
                $ink_price = $this->GetInkPrice($$ink, $$cmyk, $$lacquer, $data_ink->c_price, $data_ink->c_currency, $data_ink->m_price, $data_ink->m_currency, $data_ink->y_price, $data_ink->y_currency, $data_ink->k_price, $data_ink->k_currency, $data_ink->panton_price, $data_ink->panton_currency, $data_ink->white_price, $data_ink->white_currency, $data_ink->lacquer_glossy_price, $data_ink->lacquer_glossy_currency, $data_ink->lacquer_matte_price, $data_ink->lacquer_matte_currency);
                $ink_kg_price = $ink_price->value * self::GetCurrencyRate($ink_price->currency, $usd, $euro);
                $this->ink_kg_prices[$i] = $ink_kg_price;
            
                // Цена 1 кг КраскаСмеси, руб
                $mix_ink_kg_price = (($ink_kg_price * 1) + ($this->ink_etoxypropanol_kg_price * $data_ink->solvent_part)) / $this->ink_1kg_mix_weight;
                $this->mix_ink_kg_prices[$i] = $mix_ink_kg_price;
            
                // Расход КраскаСмеси, кг
                $ink_expense = $this->print_area * $this->GetInkExpense($$ink, $$cmyk, $$lacquer, $data_ink->c_expense, $data_ink->m_expense, $data_ink->y_expense, $data_ink->k_expense, $data_ink->panton_expense, $data_ink->white_expense, $data_ink->lacquer_glossy_expense, $data_ink->lacquer_matte_expense) * $$percent / 1000 / 100;
                $this->ink_expenses[$i] = $ink_expense;
            
                // Стоимость КраскаСмеси, руб
                $ink_cost = $ink_expense * $mix_ink_kg_price;
                $this->ink_costs[$i] = $ink_cost;
                
                // Расходы (КраскаСмеси на одну краску), руб
                $this->ink_costs_mix[$i] = $this->ink_costs[$i];
                
                // Стоимость КраскаСмеси финальная, руб
                if($this->ink_costs_mix[$i] < $data_ink->min_price_per_ink) {
                    $this->ink_costs_final[$i] = floatval($data_ink->min_price_per_ink);
                }
                else {
                    $this->ink_costs_final[$i] = floatval($this->ink_costs_mix[$i]);
                }
            }
        }
        
        //********************************
        // Стоимость форм
        //********************************
        
        // Высота форм, м
        $this->cliche_height = ($raport + 20) / 1000;
        
        // Ширина форм, м (для самоклейки без лыж не бывает)
        $this->cliche_width = ($streams_number * $this->width_dirty + 20 + 20) / 1000;
        
        // Площадь форм, м2
        $this->cliche_area = $this->cliche_height * $this->cliche_width;
        
        // Себестоимость 1 формы Флинт, руб
        $this->cliche_flint_price = $this->cliche_area * $data_cliche->flint_price * self::GetCurrencyRate($data_cliche->flint_currency, $usd, $euro);
        
        // Себестоимость 1 формы Кодак, руб
        $this->cliche_kodak_price = $this->cliche_area * $data_cliche->kodak_price * self::GetCurrencyRate($data_cliche->kodak_currency, $usd, $euro);
        
        // Себестоимость всех форм Флинт, руб
        $this->cliche_all_flint_price = $cliches_count_flint * $this->cliche_flint_price;
        
        // Себестоимость всех форм Кодак, руб
        $this->cliche_all_kodak_price = $cliches_count_kodak * $this->cliche_kodak_price;
        
        // Количество новых форм
        $this->cliche_new_number = $cliches_count_flint + $cliches_count_kodak;
        
        //********************************************
        // Стоимость скотча
        //********************************************
        
        // Стоимость скотча для каждой краски
        $this->scotch_costs = array();
        
        for($i=1; $i<=8; $i++) {
            $cliche_area = 0;
            
            if($i <= $ink_number) {
                $cliche_area = $this->cliche_area;
            }
            
            $this->scotch_costs[$i] = $cliche_area * $data_cliche->scotch_price * self::GetCurrencyRate($data_cliche->scotch_currency, $usd, $euro);
        }
        
        // Общая себестоимость скотча
        $this->scotch_cost = array_sum($this->scotch_costs);
        
        //********************************
        // НАЦЕНКА
        //********************************
        
        // Если имеющаяся наценка не пустая, оставляем её
        // Если пустая, высисляем
        if(!empty($extracharge) && $extracharge > 0) { // !!!! Значение 0.000 не считается EMPTY
            $this->extracharge = $extracharge;
        }
        else {
            $ech_type = self::ET_SELF_ADHESIVE;
            
            foreach($data_extracharge as $item) {
                if($item->ech_type == $ech_type && round($this->weight_dirty) >= $item->min_weight && (round($this->weight_dirty) <= $item->max_weight || empty($item->max_weight))) {
                    $this->extracharge = $item->value;
                }
            }
        }
        
        // Наценка на ПФ
        $this->extracharge_cliche = $extracharge_cliche;
        
        // Если УКПФ = 1, то наценка на ПФ всегда 0
        if($this->ukpf == 1) {
            $this->extracharge_cliche = 0;
        }
        
        // Наценка на нож
        $this->extracharge_knife = $extracharge_knife;
        
        // Если УКНОЖ = 1, то наценка на нож всегда 0
        if($this->ukknife == 1) {
            $this->extracharge_knife = 0;
        }
        
        //*********************************
        // ПРАВАЯ ПАНЕЛЬ
        //*********************************
        
        // Общая стоимость всех КраскаСмеси
        $this->ink_cost = 0;
        
        for($i=1; $i<=$ink_number; $i++) {
            $this->ink_cost += $this->ink_costs_final[$i];
        }
        
        // Общий расход всех КраскаСмеси
        $this->ink_expense = 0;
        
        for($i=1; $i<=$ink_number; $i++) {
            $this->ink_expense += $this->ink_expenses[$i];
        }
        
        // Себестоимость ПФ
        $this->cliche_cost = $this->cliche_all_flint_price + $this->cliche_all_kodak_price;
        
        // Отгрузочная стоимость ПФ
        $this->shipping_cliche_cost = $this->cliche_cost * (1 + ($this->extracharge_cliche / 100)) * $this->ukcuspaypf * (($this->ukpf - 1) / -1);
        
        // Прибыль ПФ
        $this->income_cliche = ($this->shipping_cliche_cost - $this->cliche_cost) * (($this->ukpf - 1) / -1);
        
        // Себестоимость ножа
        $this->knife_cost = $knife;
        
        // Отгрузочная стоимость ножа
        $this->shipping_knife_cost = $this->knife_cost * (1 + ($this->extracharge_knife / 100)) * $this->ukcuspayknife * (($this->ukknife - 1) / -1);
        
        // Прибыль на нож
        $this->income_knife = ($this->shipping_knife_cost - $this->knife_cost) * (($this->ukknife - 1) / -1);
        
        // Себестоимость
        $this->cost = $this->film_cost + $this->work_cost + $this->ink_cost + ($this->cliche_cost * $this->ukpf) + ($this->knife_cost * $this->ukknife) + $this->scotch_cost;
        
        // Себестоимость за единицу
        $this->cost_per_unit = $this->cost / $this->quantity;
        
        // Отгрузочная стоимость
        $this->shipping_cost = $this->cost * (1 + ($this->extracharge / 100));
        
        // Отгрузочная стоимость за единицу
        $this->shipping_cost_per_unit = $this->shipping_cost / $this->quantity;
        
        // !!!! Корректируем отгрузочную стоимость, чтобы она точно равнялась стоимости за единицу (округлённой до 3), умноженной на размер тиража
        $this->shipping_cost = round($this->shipping_cost_per_unit, 3) * $this->quantity;
        
        // Прибыль
        $this->income = $this->shipping_cost - $this->cost - ($extra_expense * $this->quantity);
        
        // Прибыль за единицу
        $this->income_per_unit = $this->shipping_cost_per_unit - $this->cost_per_unit - $extra_expense;
        
        // Масса плёнки с приладкой
        $this->total_weight_dirty = $this->weight_dirty;
        
        // Стоимость плёнки за единицу
        $this->film_cost_per_unit = $price_1 * self::GetCurrencyRate($currency_1, $usd, $euro);
        
        // Отходы плёнки, стоимость
        $this->film_waste_cost = ($this->weight_dirty - $this->weight_pure) * $price_1 * self::GetCurrencyRate($currency_1, $usd, $euro);
        
        // Отходы плёнки, масса
        $this->film_waste_weight = $this->weight_dirty - $this->weight_pure;
        
        // Длины тиражей
        $this->lengths = array();
        
        foreach($quantities as $key => $quantity) {
            $this->lengths[$key] = ($length + $this->gap) * $quantities[$key] / $streams_number / 1000;
        }
    }
}
?>