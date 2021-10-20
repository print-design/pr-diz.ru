<?php
include '../include/topscripts.php';

if(null !== filter_input(INPUT_POST, 'export_calculation_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $file_name = "calculation_$id.txm";
    DownloadSendHeaders($file_name);
    
    echo mb_convert_encoding("НАИМЕНОВАНИЕ ЗАКАЗА :пипетка;\n", "cp1251");
    echo mb_convert_encoding("ЗАКАЗЧИК :амт Трейд;\n", "cp1251");
    echo mb_convert_encoding("МЕНЕДЖЕР :Вера Шеховцова;\n", "cp1251");
    echo mb_convert_encoding("РАЗМЕР ЭТИКЕТКИ :;\n", "cp1251");
    echo mb_convert_encoding("ДАТА :18.10.21;\n", "cp1251");
    echo mb_convert_encoding("ПЕЧАТЬ ЕСТЬ/НЕТ:1;\n", "cp1251");
    echo mb_convert_encoding("ТИП ФЛЕКСМАШИНЫ :Comiflex;\n", "cp1251");
    echo mb_convert_encoding("ТИП ФЛЕКСМАШИНЫ НОМЕР:4;\n", "cp1251");
    echo mb_convert_encoding("Вес заказа,кг :    500.00;\n", "cp1251");
    echo mb_convert_encoding("Количество этикеток в заказе,шт :         0;\n", "cp1251");
    echo mb_convert_encoding("ТИП ЗАКАЗА вес/количество:1;\n", "cp1251");
    echo mb_convert_encoding("Количество ручьев,шт :         3;\n", "cp1251");
    echo mb_convert_encoding("Количество зтикеток в одном ручье на рапорте,шт :         0;\n", "cp1251");
    echo mb_convert_encoding("Ширина ручья,мм :    240.00;\n", "cp1251");
    echo mb_convert_encoding("Длина этикетки вдоль рапорта вала,мм :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Рапорт вала,мм :   420.000;\n", "cp1251");
    echo mb_convert_encoding("Название типа материала :БОПП прозрачный;\n", "cp1251");
    echo mb_convert_encoding("Тип материала (номер):1;\n", "cp1251");
    echo mb_convert_encoding("Толщина материала,мкм :     25.00;\n", "cp1251");
    echo mb_convert_encoding("Удельный вес бумаги,грамм/м2 :     22.75;\n", "cp1251");
    echo mb_convert_encoding("Цена материала за 1 кг,руб :    252.00;\n", "cp1251");
    echo mb_convert_encoding("Средний курс рубля за 1 евро :     87.00;\n", "cp1251");
    echo mb_convert_encoding("Число красок :         2;\n", "cp1251");
    echo mb_convert_encoding("Число новых форм :         0;\n", "cp1251");
    echo mb_convert_encoding("Название изготовителя новых форм :Москва Флинт;\n", "cp1251");
    echo mb_convert_encoding("Изготовителя новых форм (номер):2;\n", "cp1251");
    echo mb_convert_encoding("Печать с лыжами :1;\n", "cp1251");
    echo mb_convert_encoding("Ширина лыж,м :      0.02;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentC :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentM :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentY :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentK :     30.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentBel :     30.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP1 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP2 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP3 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP4 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP5 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP6 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP7 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentP8 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Площадь тиража чистая,м2 : 21978.022;\n", "cp1251");
    echo mb_convert_encoding("Ширина тиража обрезная,мм :   720.000;\n", "cp1251");
    echo mb_convert_encoding("Ширина тиража с отходами,мм :   740.000;\n", "cp1251");
    echo mb_convert_encoding("Длина тиража чистая,м : 30525.031;\n", "cp1251");
    echo mb_convert_encoding("Длина тиража с отходами,м : 32040.781;\n", "cp1251");
    echo mb_convert_encoding("Площадь тиража с отходами,м2 : 23710.178;\n", "cp1251");
    echo mb_convert_encoding("Вес материала печати чистый,кг :   500.000;\n", "cp1251");
    echo mb_convert_encoding("Вес материала печати с отходами,кг :   539.407;\n", "cp1251");
    echo mb_convert_encoding("Вес материала готовой продукции чистый,кг :   500.000;\n", "cp1251");
    echo mb_convert_encoding("Вес материала готовой продукции с отходами,кг :   539.407;\n", "cp1251");
    echo mb_convert_encoding("Стоимость материала печати,руб:135930.450;\n", "cp1251");
    echo mb_convert_encoding("Время печати тиража без приладки,ч:       3.1;\n", "cp1251");
    echo mb_convert_encoding("Время приладки,ч :       1.3;\n", "cp1251");
    echo mb_convert_encoding("Время печати с приладкой,ч :       4.4;\n", "cp1251");
    echo mb_convert_encoding("Стоимость печати,руб :   6140.17;\n", "cp1251");
    echo mb_convert_encoding("Площадь печатной формы,см2 :   3344.00;\n", "cp1251");
    echo mb_convert_encoding("Стоимость 1 печатной формы,руб :   5294.89;\n", "cp1251");
    echo mb_convert_encoding("Стоимость комплекта печатной формы,руб :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Стоимость всех красок + лак + растворитель,руб:   9461.75;\n", "cp1251");
    echo mb_convert_encoding("Количество ламинаций:0;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО, себестоимость без печатных форм,руб : 152754.27;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО, себестоимость с печатными формами,руб : 152754.27;\n", "cp1251");
    echo mb_convert_encoding("Название типа материала первой ламинации: ;\n", "cp1251");
    echo mb_convert_encoding("Тип материала первой ламинации:0;\n", "cp1251");
    echo mb_convert_encoding("Толщина материала первой ламинации,мкм:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Удельный вес материала первой ламинации,грамм/м2:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Ширина материала первой ламинации,мм:    740.00;\n", "cp1251");
    echo mb_convert_encoding("Ширина вала первой ламинации,мм:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Цена материала за 1 кг первой ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Название типа материала второй ламинации: ;\n", "cp1251");
    echo mb_convert_encoding("Тип материала второй ламинации:0;\n", "cp1251");
    echo mb_convert_encoding("Толщина материала второй ламинации,мкм:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Удельный вес материала второй ламинации,грамм/м2:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Ширина материала второй ламинации,мм:    740.00;\n", "cp1251");
    echo mb_convert_encoding("Ширина вала второй ламинации,мм:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Цена материала за 1 кг второй ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Вес материала первой ламинации чистый,кг:     0.000;\n", "cp1251");
    echo mb_convert_encoding("Вес материала первой ламинации с отходами,кг:     0.000;\n", "cp1251");
    echo mb_convert_encoding("Стоимость материала первой ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Стоимость клеевого раствора первой ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Стоимость процесса первой ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Вес материала второй ламинации чистый,кг:     0.000;\n", "cp1251");
    echo mb_convert_encoding("Вес материала второй ламинации с отходами,кг:     0.000;\n", "cp1251");
    echo mb_convert_encoding("Стоимость материала второй ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Стоимость клеевого раствора второй ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Стоимость процесса второй ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО себестоимость ламинации,руб:      0.00;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО, себестоимость без печатных форм,руб : 152754.27;\n", "cp1251");
    echo mb_convert_encoding("ИТОГО, себестоимость с печатными формами,руб : 152754.27;\n", "cp1251");
    echo mb_convert_encoding("Номер вала первой ламинации:1;\n", "cp1251");
    echo mb_convert_encoding("Номер вала второй ламинации:3;\n", "cp1251");
    echo mb_convert_encoding("Итого, себестоимость за 1кг без форм, руб :    305.51;\n", "cp1251");
    echo mb_convert_encoding("Итого, себестоимость за 1кг с формами, руб :    305.51;\n", "cp1251");
    echo mb_convert_encoding("Итого, себестоимость за 1шт без форм, руб :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Итого, себестоимость за 1шт с формами, руб :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход лака, ProcentLak :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход клея при 1 ламинации, гр на м2:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход клея при 2 ламинации, гр на м2:      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentBel2 :      0.00;\n", "cp1251");
    echo mb_convert_encoding("Расход краски, ProcentK2 :      0.00;\n", "cp1251");
    
    die();
}
?>
<html>
    <body>
        <h1>Чтобы экспортировать рассчёт надо нажать на кнопку "Экспорт" еа странице расчёта.</h1>
    </body>
</html>