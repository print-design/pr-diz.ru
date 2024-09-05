<?php
include '../include/topscripts.php';
include './calculation.php';
include './calculation_result.php';

// Атрибут "поле неактивно"
$disabled_attr = " disabled='disabled'";

// Получение объекта
$id = filter_input(INPUT_GET, 'id');
$calculation = CalculationBase::Create($id);
$calculation_result = CalculationResult::Create($id);

$new_forms_number = 0;

for($i=1; $i<=$calculation->ink_number; $i++) {
    $ink_var = "ink_$i";
    $$ink_var = $calculation->$ink_var;
    
    $color_var = "color_$i";
    $$color_var = $calculation->$color_var;
    
    $cmyk_var = "cmyk_$i";
    $$cmyk_var = $calculation->$cmyk_var;
    
    $lacquer_var = "lacquer_$i";
    $$lacquer_var = $calculation->$lacquer_var;
    
    $percent_var = "percent_$i";
    $$percent_var = $calculation->$percent_var;
    
    $cliche_var = "cliche_$i";
    $$cliche_var = $calculation->$cliche_var;
    
    if($calculation->work_type_id == WORK_TYPE_PRINT) {
        if(!empty($$cliche_var) && $$cliche_var != CLICHE_OLD) {
            $new_forms_number++;
        }
    }
}

if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
    $new_forms_number += ($calculation->cliches_count_flint + $calculation->cliches_count_kodak);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            table.calculation-table tr th, table.calculation-table tr td {
                padding-top: 3px;
                padding-right: 3px;
                padding-bottom: 3px;
                padding-left: 5px;
                vertical-align: top;
            }
            
            table.calculation-table tr td {
                white-space: nowrap;
            }
            
            #calculation {
                position: absolute;
                bottom: auto;
                right: 10px;
                margin-top: 0;
            }
            
            h1 { font-size: 26px; font-weight: 600; margin: 0; padding: 0; }
            h2 { font-size: 20px; margin: 0; padding: 0; }
            h3 { font-size: 16px; }
            #right-panel { line-height: 1.3rem; }
            #right-panel .value { font-size: 18px; }
            #left_side { width: 45%; }
            #calculation { width: 50%; }
            #calculation .value { font-size: 16px; }
            .btn { display: none; }
            #costs { display: block!important; }
            
            #status {
                width: 100%;
                padding: 6px;
                margin-top: 10px;
                margin-bottom: 10px;
                border-radius: 8px;
                font-weight: bold;
                text-align: center;
                border: solid 2px gray;
                color: gray;
            }
        </style>
    </head>
    <body>
        <?php
        if(!empty($calculation->work_type_id) && $calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
            include './right_panel_self_adhesive.php';
        }
        else {
            include './right_panel.php';
        }
        ?>
        <!-- Левая половина -->
        <div class="co_l-_5" id="left_side">
            <h1><?= $calculation->name ?></h1>
            <h2>№<?=$calculation->customer_id."-".$calculation->num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?></h2>
            <?php
            include '../include/order_status_details_print.php';
            include './left_panel.php';
            ?>
        </div>
        <script>
            var css = '@page { size: landscape; margin: 8mm; }',
                    head = document.head || document.getElementsByTagName('head')[0],
                    style = document.createElement('style');
            
            style.type = 'text/css';
            style.media = 'print';
            
            if (style.styleSheet){
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }
            
            head.appendChild(style);
            
            window.print();
        </script>
    </body>
</html>