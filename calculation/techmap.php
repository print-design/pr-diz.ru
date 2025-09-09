<?php
include '../include/topscripts.php';
include './calculation.php';
include './calculation_result.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD]))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не указан id, направляем к списку
if(null === filter_input(INPUT_GET, 'id')) {
    header('Location: '.APPLICATION.'/calculation/');
}

// Данные получены из другой тех. карты
const FROM_OTHER_TECHMAP = "from_other_techmap";

// Объекты, к которым присоединяются картинки
const PRINTING = "printing";
const STREAM = "stream";

// Валидация формы
$form_valid = true;
$error_message = '';

$side_valid = '';
$winding_valid = '';
$winding_unit_valid = '';
$spool_valid = '';
$labels_valid = '';
$package_valid = '';
$photolabel_valid = '';
$roll_type_valid = '';
$cliche_valid = '';
$requirement_valid = '';
$streams_valid = array();

// Создание технологической карты
if(null !== filter_input(INPUT_POST, 'techmap_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    
    if(empty($id)) {
        $error_message == "Не указан ID расчёта";
        $form_valid = false;
    }
    
    $calculation = CalculationBase::Create($id);
    
    $techmap_id = filter_input(INPUT_POST, 'techmap_id');
    
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    
    $side = filter_input(INPUT_POST, 'side');
    if(empty($side)) {
        $side_valid = ISINVALID;
        $form_valid = false;
    }
    
    $winding = filter_input(INPUT_POST, 'winding');
    if(empty($winding)) {
        $winding_valid = ISINVALID;
        $form_valid = false;
    }
    
    $winding_unit = filter_input(INPUT_POST, 'winding_unit');
    if(empty($winding_unit)) {
        $winding_unit_valid = ISINVALID;
        $form_valid = false;
    }
    
    $spool = filter_input(INPUT_POST, 'spool');
    if(empty($spool)) {
        $spool_valid = ISINVALID;
        $form_valid = false;
    }
    
    $labels = filter_input(INPUT_POST, 'labels');
    if(empty($labels)) {
        $labels_valid = ISINVALID;
        $form_valid = false;
    }
    
    $package = filter_input(INPUT_POST, 'package');
    if(empty($package)) {
        $package_valid = ISINVALID;
        $form_valid = false;
    }
    
    $photolabel = filter_input(INPUT_POST, 'photolabel');
    if($photolabel != CalculationResult::PHOTOLABEL_LEFT && $photolabel != CalculationResult::PHOTOLABEL_RIGHT && $photolabel != CalculationResult::PHOTOLABEL_BOTH && $photolabel != CalculationResult::PHOTOLABEL_NONE && $photolabel != CalculationResult::PHOTOLABEL_NOT_FOUND) {
        $photolabel_valid = ISINVALID;
        $form_valid = false;
    }
    
    $roll_type = filter_input(INPUT_POST, 'roll_type');
    if(empty($roll_type)) $roll_type = 0;
    if(empty($roll_type) && $photolabel != CalculationResult::PHOTOLABEL_NONE && $photolabel != CalculationResult::PHOTOLABEL_NOT_FOUND) {
        $roll_type_valid = ISINVALID;
        $form_valid = false;
    }
    
    $comment = filter_input(INPUT_POST, 'comment');
    
    if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
        // Проверяем, чтобы были заполнены формы для всех красок
        $sql = "select count(distinct cq.id) * c.ink_number - count(cc.id) "
                . "from calculation_cliche cc "
                . "right join calculation_quantity cq on cc.calculation_quantity_id = cq.id "
                . "inner join calculation c on cq.calculation_id = c.id where c.id = $id";
        
        $fetcher = new Fetcher($sql);
        $row = $fetcher->Fetch();
        
        if($row[0] === null || $row[0] > 0) {
            $cliche_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    // Проверяем, чтобы были заполнены все требования для материалов
    $sql = "select requirement1, requirement2, requirement3 from calculation where id = $id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $laminations_number = 0;
        if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE) {
            $laminations_number = $calculation->laminations_number;
        }
        
        if(($calculation->work_type_id != WORK_TYPE_NOPRINT && empty($row['requirement1'])) || 
            ($laminations_number > 0 && empty($row['requirement2'])) || 
            ($laminations_number > 1 && empty($row['requirement3']))) {
            $requirement_valid = ISINVALID;
            $form_valid = false;
        }
    }
    
    // Проверяем, чтобы были заполнены наименования ручьёв 
    foreach($_POST as $key => $value) {
        $head_length = mb_strlen("stream_");
        if(mb_strlen($key) > $head_length && mb_substr($key, 0, $head_length) == "stream_") {
            if(empty($value)) {
                $streams_valid[$key] = ISINVALID;
                $form_valid = false;
            }
            else {
                $streams_valid[$key] = '';
            }
        }
    }
    
    if($form_valid) {
        if(empty($supplier_id)) {
            $supplier_id = "NULL";
        }
        $comment = addslashes($comment ?? '');
        
        $sql = "";
        
        if(empty($techmap_id)) {
            $sql = "insert into techmap (calculation_id, supplier_id, side, winding, winding_unit, spool, labels, package, photolabel, roll_type, comment) "
                    . "values($id, $supplier_id, $side, $winding, '$winding_unit', $spool, $labels, $package, '$photolabel', $roll_type, '$comment')";
        }
        else {
            $sql = "update techmap set supplier_id = $supplier_id, side = $side, winding = $winding, winding_unit = '$winding_unit', spool = $spool, "
                    . "labels = $labels, package = $package, photolabel = '$photolabel', roll_type = $roll_type, comment = '$comment' where id = $techmap_id";
        }
        
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            $sql = "select id, position, name from calculation_stream where calculation_id = $id order by position";
            $grabber = new Grabber($sql);
            $result = $grabber->result;
            $error_message = $grabber->error;
            
            $stream_position_ids_names = array();
            
            foreach($result as $position_id_name) {
                $stream_position_ids_names[$position_id_name['position']] = array('id' => $position_id_name['id'], 'name' => $position_id_name['name']);
            }
            
            if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE) {
                foreach($_POST as $key => $value) {
                    $head_length = mb_strlen("stream_");
                    
                    if(mb_strlen($key) > $head_length && mb_substr($key, 0, $head_length) == "stream_") {
                        $substrings = explode('_', $key);
                        
                        if(count($substrings) > 1) {
                            $stream_id = $substrings[1];
                            $name = addslashes($value);
                            $sql = "update calculation_stream set name = '$name' where id = $stream_id";
                            $executer = new Executer($sql);
                            $error_message = $executer->error;
                        }
                    }
                }
            }
        }
        
        if(empty($error_message)) {
            // При установлении этого статуса расчёт в другой раздел не переходит, поэтому менять status_date не надо
            // (в разделах сортировка по status_date)
            $sql = "update calculation set status_id = ".ORDER_STATUS_TECHMAP." where id = $id and status_id = ".ORDER_STATUS_CALCULATION;
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
    }
}

// Удаление картинки
if(null !== filter_input(INPUT_POST, 'delete_image_submit')) {
    $object = filter_input(INPUT_POST, 'object');
    $id = filter_input(INPUT_POST, 'id');
    $image = filter_input(INPUT_POST, 'image');
    
    if(!empty($object) && !empty($id) || !empty($image)) {
        $sql = "";
        
        if($object == PRINTING) {
            $sql = "select image$image, pdf$image from calculation_quantity where id = $id";
        }
        elseif($object == STREAM) {
            $sql = "select image$image, pdf$image from calculation_stream where id = $id";
        }
        
        if(!empty($sql)) {
            $fetcher = new Fetcher($sql);
            
            if($row = $fetcher->Fetch()) {
                $filename = $row["image$image"];
                $filepath = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/$object/mini/$filename";
                if(file_exists($filepath)) {
                    unlink($filepath);
                }
                
                $filepath = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/$object/$filename";
                if(file_exists($filepath)) {
                    unlink($filepath);
                }
                
                $filename = $row["pdf$image"];
                if(!empty($filename)) {
                    $filepath = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/$object/pdf/$filename";
                    if(file_exists($filepath)) {
                        unlink($filepath);
                    }
                    
                    $filename = $row["image$image"];
                    $filepath = $_SERVER['DOCUMENT_ROOT'].APPLICATION."/content/$object/pdf/$filename";
                    if(file_exists($filepath)) {
                        unlink($filepath);
                    }
                }
                
                $sql = "";
                
                if($object == PRINTING) {
                    $sql = "update calculation_quantity set image$image = '', pdf$image = '' where id = $id";
                }
                elseif($object == STREAM) {
                    $sql = "update calculation_stream set image$image = '', pdf$image = '' where id = $id";
                }
                
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
        }
    }
}

// Выгрузка картинки
if(null !== filter_input(INPUT_POST, 'download_image_submit')) {
    $object = filter_input(INPUT_POST, 'object');
    $id = filter_input(INPUT_POST, 'id');
    $image = filter_input(INPUT_POST, 'image');
    
    if(!empty($object) && !empty($id) && !empty($image)) {
        $sql = "";
        
        if($object == PRINTING) {
            $sql = "select concat(c.name, cq.id) name, cq.image$image, cq.pdf$image from calculation_quantity cq inner join calculation c on cq.calculation_id = c.id where cq.id = $id";
        }
        elseif ($object == STREAM) {
            $sql = "select name, image$image, pdf$image from calculation_stream where id = $id";
        }
        
        if(!empty($sql)) {
            $targetname = "image";
            $fetcher = new Fetcher($sql);
            
            if($row = $fetcher->Fetch()) {
                if(!empty($row['name'])) {
                    $targetname = $row['name'].'_'.$image;
                    $targetname = str_replace('.', '', $targetname);
                    $targetname = str_replace(',', '', $targetname);
                    $targetname = htmlspecialchars($targetname);
                }
                
                $filename = $row["image$image"];
                $filepath = "../content/$object/$filename";
                $extension = "";
                
                if(!empty($row["pdf$image"])) {
                    $filename = $row["pdf$image"];
                    $filepath = "../content/$object/pdf/$filename";
                    $extension = "pdf";
                }
                else {
                    $substrings = explode('.', $filename);
                    if(count($substrings) > 1) {
                        $extension = $substrings[count($substrings) - 1];
                    }
                }
                
                $targetname = $targetname.'.'.$extension;
                
                DownloadSendHeaders($targetname);
                readfile($filepath);
                exit();
            }
        }
    }
}

// Постановка в план технологической карты
if(null !== filter_input(INPUT_POST, 'plan_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    
    if(!empty($id)) {
        // При постановке в план расчёт переходит в другой раздел, поэтому меняется status_date
        $sql = "update calculation set status_id = ".ORDER_STATUS_WAITING.", status_date = now(), to_work_date = now() where id = $id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
    }
    
    if(empty($error_message)) {
        header('Location: details.php'. BuildQuery('id', $id));
    }
}

// Удаление технологической карты
if(null !== filter_input(INPUT_POST, 'delete_techmap_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $techmap_id = filter_input(INPUT_POST, 'techmap_id');
    
    if(!empty($id) && !empty($techmap_id)) {
        $sql = "delete from techmap where id = $techmap_id";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        
        if(empty($error_message)) {
            // При возвращении в статус "сделан расчёт" нет перехода в другой раздел, поэтому status_date остаётся прежним
            $sql = "update calculation set status_id = ".ORDER_STATUS_CALCULATION." where id = $id";
            $executer = new Executer($sql);
            $error_message = $executer->error;
        }
        
        if(empty($error_message)) {
            header("Location: details.php". BuildQuery('id', $id));
        }
    }
}

// ПОЛУЧЕНИЕ ОБЪЕКТА
$id = filter_input(INPUT_GET, 'id');
$calculation = CalculationBase::Create($id);
$calculation_result = CalculationResult::Create($id);

if(!empty($calculation->ink_number)) {
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
    }
}

$lamination = (empty($calculation->laminations_number) || $calculation->laminations_number == 0) ? "нет" : $calculation->laminations_number;

$supplier_id = filter_input(INPUT_POST, 'supplier_id');
if($supplier_id === null) $supplier_id = $calculation_result->supplier_id;

$side = filter_input(INPUT_POST, 'side');
if($side === null) $side = $calculation_result->side;

$winding = filter_input(INPUT_POST, 'winding');
if($winding === null) $winding =  $calculation_result->winding;

$winding_unit = filter_input(INPUT_POST, 'winding_unit');
if($winding_unit === null) $winding_unit = $calculation_result->winding_unit;

$spool = filter_input(INPUT_POST, 'spool');
if($spool === null) $spool = $calculation_result->spool;

$labels = filter_input(INPUT_POST, 'labels');
if($labels === null) $labels = $calculation_result->labels;

$package = filter_input(INPUT_POST, 'package');
if($package === null) $package = $calculation_result->package;

$photolabel = filter_input(INPUT_POST, 'photolabel');
if($photolabel === null) $photolabel = $calculation_result->photolabel;

$roll_type = filter_input(INPUT_POST, 'roll_type');
if($roll_type === null) $roll_type = $calculation_result->roll_type;

$comment = filter_input(INPUT_POST, 'comment');
if($comment === null) $comment = $calculation_result->comment;

// Отходы
$waste1 = "";
$waste2 = "";
$waste3 = "";
$waste = "";

if(in_array($calculation->film_1, WASTE_PRESS_FILMS)) {
    $waste1 = WASTE_PRESS;
}
elseif($calculation->film_1 == WASTE_PAPER_FILM) {
    $waste1 = WASTE_PAPER;
}
elseif(empty ($calculation->film_1)) {
    $waste1 = "";
}
else {
    $waste1 = WASTE_KAGAT;
}

if(in_array($calculation->film_2, WASTE_PRESS_FILMS)) {
    $waste2 = WASTE_PRESS;
}
elseif ($calculation->film_2 == WASTE_PAPER_FILM) {
    $waste2 = WASTE_PAPER;
}
elseif(empty ($calculation->film_2)) {
    $waste2 = "";
}
else {
    $waste2 = WASTE_KAGAT;
}

if(in_array($calculation->film_3, WASTE_PRESS_FILMS)) {
    $waste3 = WASTE_PRESS;
}
elseif($calculation->film_3 == WASTE_PAPER_FILM) {
    $waste3 = WASTE_PAPER;
}
elseif(empty ($calculation->film_3)) {
    $waste3 = "";
}
else {
    $waste3 = WASTE_KAGAT;
}

$waste = $waste1;
if(!empty($waste2) && $waste2 != $waste1) $waste = WASTE_KAGAT;
if(!empty($waste3) && $waste3 != $waste2) $waste = WASTE_KAGAT;

$machine_coeff = null;

if(!empty($calculation->machine_id)) {
    $machine_coeff = PRINTER_MACHINE_COEFFICIENTS[$calculation->machine_id];
}

// Тиражи и формы
$printings = array();
$cliches = array();
$repeats = array();
$cliches_used_flint = 0;
$cliches_used_kodak = 0;
$cliches_used_old = 0;

if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
    $sql = "select id, quantity, length, image1, image2 from calculation_quantity where calculation_id = $id";
    $grabber = new Grabber($sql);
    $error_message = $grabber->error;
    $printings = $grabber->result;
    
    $sql = "select calculation_quantity_id, sequence, name, repeat_from from calculation_cliche where calculation_quantity_id in (select id from calculation_quantity where calculation_id = $id)";
    $fetcher = new Fetcher($sql);
    $error_message = $fetcher->error;
    while($row = $fetcher->Fetch()) {
        $cliches[$row['calculation_quantity_id']][$row['sequence']] = $row['name'];
        $repeats[$row['calculation_quantity_id']][$row['sequence']] = $row['repeat_from'];
        
        switch ($row['name']) {
            case CLICHE_FLINT:
                $cliches_used_flint++;
                break;
            case CLICHE_KODAK:
                $cliches_used_kodak++;
                break;
            case CLICHE_OLD:
                $cliches_used_old++;
                break;
        }
    }
}

// Если ручьи не созданы, создаём их
if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE) {
    $streams_count = 0;
    
    $sql = "select count(id) from calculation_stream where calculation_id = $id";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        $streams_count = $row[0];
    }
    
    if($streams_count == 0) {
        $widths_count = 0;
        $sql = "select stream_number, width from calculation_stream_width where calculation_id = $id";
        $grabber = new Grabber($sql);
        $stream_widths = $grabber->result;
        $error_message = $grabber->error;
        
        if(count($stream_widths) > 0) {
            foreach ($stream_widths as $item) {
                $stream_position = $item['stream_number'];
                $stream_width =  $item['width'];
                $sql = "insert into calculation_stream(calculation_id, position, width) values ($id, $stream_position, $stream_width)";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
        }
        else {
            for($i = 0; $i < $calculation->streams_number; ++$i) {
                $stream_width = $calculation->stream_width;
                $sql = "insert into calculation_stream(calculation_id, position, width) values ($id, $i, $stream_width)";
                $executer = new Executer($sql);
                $error_message = $executer->error;
            }
        }
    }
}

// Список ручьёв
$streams = array();

if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE) {
    $sql = "select id, position, name, width, image1, image2 from calculation_stream where calculation_id = $id order by position";
    $grabber = new Grabber($sql);
    $streams = $grabber->result;
    $error_message = $grabber->error;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            .row {
                width: 900px;
            }
            
            h1 {
                font-size: 33px;
            }
            
            h2, .name {
                font-size: 26px;
                font-weight: bold;
                line-height: 45px;
            }
            
            h3 {
                font-size: 20px;
            }
            
            .subtitle {
                font-weight: bold;
                font-size: 20px;
                line-height: 40px
            }
            
            table {
                width: 100%;
            }
            
            tr {
                border-bottom: solid 1px #e3e3e3;
            }
            
            th {
                white-space: nowrap;
                padding-right: 30px;
                vertical-align: top;
            }
            
            td {
                line-height: 25px;
            }
            
            tr td:nth-child(2) {
                text-align: right;
                padding-left: 10px;
            }
            
            .printing_title {
                font-size: large;
            }
            
            .roll-selector input {
                margin:0;
                padding:0;
                -webkit-appearance:none;
                -moz-appearance:none;
                appearance:none;
            }
            
            .roll-selector label {
                cursor:pointer;
                border: solid 5px white;
            }
            
            .roll-selector label:hover {
                border: solid 5px lightblue;
            }
            
            .roll-selector input[type="radio"]:checked + label {
                background-image: url(../images/icons/check.svg);
                background-position-x: 100%;
                background-position-y: 100%;
                background-repeat: no-repeat;
            }
            
            #status {
                width: 100%;
                padding: 12px;
                margin-top: 40p;
                margin-bottom: 20px;
                border-radius: 10px;
                font-weight: bold;
                text-align: center; 
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_zakaz.php';
        ?>
        <div id="confirm_delete" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header font-weight-bold" style="font-size: x-large;">
                        Удалить выбранный файл?
                        <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body">
                        Вы удаляете файл &laquo;<span id="deleted_file_name"></span>&raquo;.<br />
                        После удаления файл будет невозможно восстановить.
                    </div>
                    <div class="modal-footer" style="justify-content: flex-start;">
                        <button type="button" class="btn btn-light" style="width: 120px;" onclick="javascript: document.forms.delete_image_form.submit();"><img src="../images/icons/trash3.svg" class="mr-2" />Удалить</button>
                        <button type="button" class="btn btn-light" style="width: 120px;" data-dismiss="modal">Отмена</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="big_image" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header font-weight-bold" style="font-size: x-large;">
                        <div id="big_image_header"></div>
                        <button type="button" class="close" data-dismiss="modal"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="modal-body d-flex justify-content-center"><img id="big_image_img" class="img-fluid" alt="Изображение" /></div>
                    <div class="modal-footer" style="justify-content: flex-start;">
                        <button type="button" class="btn btn-dark" onclick="javascript: document.forms.download_image_form.submit();"><img src="../images/icons/download.svg" class="mr-2 align-middle" />Скачать</button>
                        <button type="button" class="btn btn-light" data-toggle="modal" data-target="#confirm_delete" data-dismiss="modal"><img src="../images/icons/trash3.svg" class="mr-2 align-middle" />Удалить</button>
                    </div>
                </div>
            </div>
        </div>
        <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
        <div id="set_printings" class="modal fade show">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header font-weight-bold" style="font-size: x-large;">
                        <div class="d-inline-block" style="position: relative; top: -3px;"><img src="../images/icons/printing.svg" style="top: 20px;" /></div>
                        &nbsp;&nbsp;&nbsp;
                        Настроить тиражи
                        <button type="button" class="close set_printings_dismiss" data-dismiss="modal"><i class="fas fa-times" style="color: #EC3A7A;"></i></button>
                    </div>
                    <?php
                    $printing_sequence = 0;
                    foreach ($printings as $printing):
                    $printing_sequence++;
                    $display = "d-none";
                    if($printing_sequence == 1) $display = "d-block";
                    ?>
                    <div style="max-height: 70vh; overflow-y: scroll;" class="modal-body set_printings set_printings_<?=$printing_sequence ?> <?=$display ?>">
                        <div class="printing_title font-weight-bold"><span style="font-size: x-large;">Тираж <?=$printing_sequence ?></span>&nbsp;&nbsp;&nbsp;<span style="font-size: large;"><?= DisplayNumber(floatval($printing['length']), 0) ?> м</span></div>
                        <div class="d-flex justify-content-start mb-3">
                            <div class="mr-2">
                                <div>Новая Flint <?=$machine_coeff ?></div>
                                <div>Новая Kodak <?=$machine_coeff ?></div>
                                <div>Старая</div>
                            </div>
                            <div class="text-right ml-2">
                                <div class="cliches_used_flint">выбрано <span class="flint_used"><?=$cliches_used_flint ?></span> из <?=$calculation->cliches_count_flint ?></div>
                                <div class="cliches_used_kodak">выбрано <span class="kodak_used"><?=$cliches_used_kodak ?></span> из <?=$calculation->cliches_count_kodak ?></div>
                                <div class="cliches_used_old">выбрано <span class="old_used"><?=$cliches_used_old ?></span> из <?=$calculation->cliches_count_old ?></div>
                            </div>
                        </div>
                        <?php
                        if(!empty($calculation->ink_number)):
                        for($i = 1; $i <= $calculation->ink_number; $i++):
                        $ink_var = "ink_$i";
                        $color_var = "color_$i";
                        $cmyk_var = "cmyk_$i";
                        $lacquer_var = "lacquer_$i";
                        
                        $cliche_width_style = " w-100";
                        $repeat_display_style = " d-none";
                        if(!empty($cliches[$printing['id']][$i]) && $cliches[$printing['id']][$i] == CLICHE_REPEAT) {
                            $cliche_width_style = " w-50";
                            $repeat_display_style = "";
                        }
                        ?>
                        <div class="d-flex justify-content-between">
                            <div class="form-group<?=$cliche_width_style ?>">
                                <label for="select_cliche_<?=$printing['id'] ?>_<?=$i ?>">
                                    <?php
                                    switch($$ink_var) {
                                        case INK_CMYK:
                                            switch ($$cmyk_var) {
                                                case CMYK_CYAN:
                                                    echo 'Cyan';
                                                    break;
                                                case CMYK_MAGENDA:
                                                    echo 'Magenda';
                                                    break;
                                                case CMYK_YELLOW:
                                                    echo 'Yellow';
                                                    break;
                                                case CMYK_KONTUR:
                                                    echo 'Kontur';
                                                    break;
                                            }
                                            break;
                                        case INK_PANTON:
                                            echo 'Pantone '.$$color_var;
                                            break;
                                        case INK_WHITE:
                                            echo 'Белая';
                                            break;
                                        case INK_LACQUER:
                                            switch ($$lacquer_var) {
                                                case LACQUER_GLOSSY:
                                                    echo 'Лак глянцевый';
                                                    break;
                                                case LACQUER_MATTE:
                                                    echo 'Лак матовый';
                                                    break;
                                                default :
                                                    echo 'Лак';
                                                    break;
                                            }
                                            break;
                                    }
                                    ?>
                                </label>
                                <select class="form-control select_cliche" id="select_cliche_<?=$printing['id'] ?>_<?=$i ?>" data-printing-id="<?=$printing['id'] ?>" data-sequence="<?=$i ?>">
                                    <?php
                                    // Если для этой краски назначена конкретная форма, то она выбрана в списке
                                    $flint_selected = '';
                                    if(!empty($cliches[$printing['id']][$i]) && $cliches[$printing['id']][$i] == CLICHE_FLINT) {
                                        $flint_selected = " selected='selected'";
                                    }
                                    $kodak_selected = '';
                                    if(!empty($cliches[$printing['id']][$i]) && $cliches[$printing['id']][$i] == CLICHE_KODAK) {
                                        $kodak_selected = " selected='selected'";
                                    }
                                    $old_selected = '';
                                    if(!empty($cliches[$printing['id']][$i]) && $cliches[$printing['id']][$i] == CLICHE_OLD) {
                                        $old_selected = " selected='selected'";
                                    }
                                    $repeat_selected = '';
                                    if(!empty($cliches[$printing['id']][$i]) && $cliches[$printing['id']][$i] == CLICHE_REPEAT) {
                                        $repeat_selected = " selected='selected'";
                                    }
                                
                                    $flint_hidden = '';
                                    if(empty($flint_selected) && $cliches_used_flint >= $calculation->cliches_count_flint) {
                                        $flint_hidden = " hidden='hidden'";
                                    }
                                    $kodak_hidden = '';
                                    if(empty($kodak_selected) && $cliches_used_kodak >= $calculation->cliches_count_kodak) {
                                        $kodak_hidden = " hidden='hidden'";
                                    }
                                    $old_hidden = '';
                                    if(empty($old_selected) && $cliches_used_old >= $calculation->cliches_count_old) {
                                        $old_hidden = " hidden='hidden'";
                                    }
                                    $repeat_hidden = '';
                                    if($printing_sequence == 1) {
                                        $repeat_hidden = " hidden='hidden'";
                                    }
                                    ?>
                                    <option value="">Ждем данные</option>
                                    <option class="option_flint" id="option_flint_<?=$printing['id'] ?>_<?=$i ?>" value="<?= CLICHE_FLINT ?>"<?=$flint_selected ?><?=$flint_hidden ?>>Новая Flint <?=$machine_coeff ?></option>
                                    <option class="option_kodak" id="option_kodak_<?=$printing['id'] ?>_<?=$i ?>" value="<?= CLICHE_KODAK ?>"<?=$kodak_selected ?><?=$kodak_hidden ?>>Новая Kodak <?=$machine_coeff ?></option>
                                    <option class="option_old" id="option_old_<?=$printing['id'] ?>_<?=$i ?>" value="<?= CLICHE_OLD ?>"<?=$old_selected ?><?=$old_hidden ?>>Старая</option>
                                    <option class="option_repeat" id="option_repeat_<?=$printing['id'] ?>_<?=$i ?>" value="<?= CLICHE_REPEAT ?>"<?=$repeat_selected ?><?=$repeat_hidden ?>>Повторное использование</option>
                                </select>
                            </div>
                            <div class="form-group pl-2 w-50<?=$repeat_display_style ?>">
                                <label for="repeat_from_<?=$printing['id'] ?>_<?=$i ?>">С какого тиража</label>
                                <select class="form-control repeat_from" id="repeat_from_<?=$printing['id'] ?>_<?=$i ?>" data-printing-id="<?=$printing['id'] ?>" data-sequence="<?=$i ?>">
                                    <?php
                                    for($rep_pr = 1; $rep_pr < $printing_sequence; $rep_pr++):
                                        $rep_pr_selected = (!empty($repeats[$printing['id']][$i]) && $repeats[$printing['id']][$i] == $rep_pr) ? " selected='selected'" : "";
                                    ?>
                                    <option<?=$rep_pr_selected ?>><?= $rep_pr ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        <?php
                        endfor;
                        endif;
                        ?>
                    </div>
                    <div class="modal-footer set_printings set_printings_<?=$printing_sequence ?> <?=$display ?>" style="justify-content: flex-start;">
                        <?php if($printing_sequence == 1): ?>
                        <button type="button" class="btn btn-light" data-dismiss="modal">Закрыть</button>
                        <?php else: ?>
                        <button type="button" class="btn btn-light change_printing" data-printing="<?=($printing_sequence - 1) ?>"><i class="fas fa-chevron-left mr-2"></i>Тираж <?=($printing_sequence - 1) ?></button>
                        <?php endif; ?>
                        <?php if($printing_sequence == count($printings)): ?>
                        <button type="button" class="btn btn-dark" data-dismiss="modal">Завершить</button>
                        <?php else: ?>
                        <button type="button" class="btn btn-dark change_printing" data-printing="<?=($printing_sequence + 1) ?>">Тираж <?=($printing_sequence + 1) ?><i class="fas fa-chevron-right ml-2"></i></button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <form id="delete_image_form" method="post">
            <input type="hidden" id="object" name="object" />
            <input type="hidden" id="id" name="id" />
            <input type="hidden" id="image" name="image" />
            <input type="hidden" name="delete_image_submit" value="1" />
            <input type="hidden" name="scroll" />
        </form>
        <form id="download_image_form" method="post">
            <input type="hidden" id="object" name="object" />
            <input type="hidden" id="id" name="id" />
            <input type="hidden" id="image" name="image" />
            <input type="hidden" name="download_image_submit" value="1" />
        </form>
        <div class="container-fluid">
            <div class="text-nowrap nav2">
                <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER]))): ?>
                <a href="details.php?<?= http_build_query($_GET) ?>" class="mr-4">Расчёт</a>
                <?php endif; ?>
                <a href="techmap.php?<?= http_build_query($_GET) ?>" class="mr-4 active">Тех. карта</a>
                <?php if(IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_SCHEDULER], ROLE_NAMES[ROLE_LAM_HEAD], ROLE_NAMES[ROLE_FLEXOPRINT_HEAD], ROLE_NAMES[ROLE_STOREKEEPER])) && in_array($calculation->status_id, ORDER_STATUSES_IN_CUT)): ?>
                <a href="cut.php?<?= http_build_query($_GET) ?>" class="mr-4">Результаты</a>
                <?php endif; ?>
            </div>
            <hr />
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between">
                <div><h1><?= empty($calculation_result->techmap_id) ? "Составление тех. карты" : "Технологическая карта" ?></h1></div>
                <div>
                    <?php
                    if(!empty($calculation_result->techmap_id)):     
                    $print_class = "d-block";
                    $no_print_class = "d-none";
                    
                    if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                        $total_cliches_count = count($printings) * $calculation->ink_number;
                        $valid_cliches_count = 0;
                    
                        foreach ($cliches as $cliches_row) {
                            $valid_cliches_count += count($cliches_row);
                        }
                        
                        if($total_cliches_count - $valid_cliches_count > 0) {
                            $print_class = "d-none";
                            $no_print_class = "d-block";
                        }
                    }
                    ?>
                    <a class="btn btn-outline-dark mt-2 no_print_btn <?=$no_print_class ?>" id="top_no_print_btn" target="_self" style="width: 3rem;" title="Печать" href="javascript: void(0);" onclick="javascript: $('#cliche_validation').removeClass('d-none'); $('#cliche_validation').addClass('d-block'); window.location.replace('#cliche_validation');"><i class="fa fa-print"></i></a>
                    <a class="btn btn-outline-dark mt-2 print_btn <?=$print_class ?>" id="top_print_btn" target="_blank" style="width: 3rem;" title="Печать" href="print_tm.php?id=<?=$id ?>"><i class="fa fa-print"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <div>
                    <div class="name">Заказчик: <?=$calculation->customer ?></div>
                    <div class="name">Наименование: <?=$calculation->name ?></div>
                    <div class="subtitle">№<?=$calculation->customer_id ?>-<?=$calculation->num_for_customer ?> от <?= DateTime::createFromFormat('Y-m-d H:i:s', $calculation->date)->format('d.m.Y') ?></div>
                </div>
                <div>
                    <button type="btn" class="btn btn-outline-dark" data-toggle="modal" data-target="#techmapModal">Подгрузить из другого заказа</button>
                </div>
            </div>
            <div class="row">
                <div class="col-5">
                    <?php include '../include/order_status_details.php'; ?>
                    <h2 class="mt-2">Остальная информация</h2>
                    <table>
                        <tr>
                            <th>Карта составлена</th>
                            <td class="text-left"><?= empty($calculation_result->techmap_date) ? date('d.m.Y H:i') : DateTime::createFromFormat('Y-m-d H:i:s', $calculation_result->techmap_date)->format('d.m.Y H:i') ?></td>
                        </tr>
                        <tr>
                            <th>Заказчик</th>
                            <td class="text-left" style="line-height: 18px;"><?=$calculation->customer ?></td>
                        </tr>
                        <tr>
                            <th>Название заказа</th>
                            <td class="text-left" style="line-height: 18px;"><?=$calculation->name ?></td>
                        </tr>
                        <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <th>Объем заказа</th>
                            <td class="text-left"><strong><?= DisplayNumber(intval($calculation->quantity), 0) ?> <?=$calculation->unit == 'kg' ? 'кг' : 'шт' ?></strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= DisplayNumber(floatval($calculation_result->length_pure_1), 0) ?> м</td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Менеджер</th>
                            <td class="text-left"><?=$calculation->first_name ?> <?=$calculation->last_name ?></td>
                        </tr>
                        <tr>
                            <th>Тип работы</th>
                            <td class="text-left"><?=WORK_TYPE_NAMES[$calculation->work_type_id] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-4">
                    <h2 style="line-height: 30px;">Информация для печатника</h2>
                    <div class="subtitle" style="line-height: 20px;">Печать</div>
                </div>
                <div class="col-4">
                    <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE): ?>
                    <h2 style="line-height: 30px;">Информация для ламинации</h2>
                    <div class="subtitle" style="line-height: 20px;">Кол-во ламинаций: <?=$lamination ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-4">
                    <h2 style="line-height: 30px;">Информация для резчика</h2>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-4">
                    <table>
                        <tr>
                            <td style="padding-top: 5px;">Машина</td>
                            <td style="padding-top: 5px;">
                                <?php
                                if(!empty($calculation->machine_id)) {
                                    echo (mb_stristr(PRINTER_SHORTNAMES[$calculation->machine_id], "zbs") ? "ZBS" : PRINTER_NAMES[$calculation->machine_id]);
                                }
                                ?>
                            </td>
                        </tr>
                        <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td style="line-height: 18px;">Поставщик мат-ла</td>
                            <td>
                                <?php
                                if(empty($calculation_result->techmap_id)) {
                                    echo "Ждем данные";
                                }
                                elseif(empty ($calculation_result->supplier)) {
                                    echo "Любой";
                                }
                                else {
                                    echo $calculation_result->supplier;
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Марка мат-ла</td>
                            <td style="line-height: 18px;"><?= $calculation->film_1 ?></td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td class="text-nowrap"><?= DisplayNumber(floatval($calculation->thickness_1), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(rtrim(DisplayNumber(floatval($calculation->density_1), 2), "0"), ",").' г/м<sup>2</sup>' ?></td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td><?= DisplayNumber(floatval($calculation_result->width_1), 0) ?> мм</td>
                        </tr>
                        <tr>
                            <td style="line-height: 18px;"><?= $calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? "На приладку 1 тиража" : "Метраж на приладку" ?></td>
                            <td><?= DisplayNumber(floatval($calculation->data_priladka->length) * floatval($calculation->ink_number), 0) ?> м</td>
                        </tr>
                        <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td>Всего тиражей</td>
                            <td><?= count($printings) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td>Метраж на тираж</td>
                            <td><?= DisplayNumber(floatval($calculation_result->length_pure_1), 0) ?> м</td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td>Всего мат-ла</td>
                            <td><?= DisplayNumber(floatval($calculation_result->length_dirty_1), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Печать</td>
                            <td>
                                <?php
                                switch ($side) {
                                    case CalculationResult::SIDE_FRONT:
                                        echo 'Лицевая';
                                        break;
                                    case CalculationResult::SIDE_BACK:
                                        echo 'Оборотная';
                                        break;
                                    default :
                                        echo "Ждем данные";
                                        break;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Рапорт</td>
                            <td><?= DisplayNumber(floatval($calculation->raport), 3) ?></td>
                        </tr>
                        <tr>
                            <td>Растяг</td>
                            <td>
                                <?php
                                if(empty($calculation->machine_id)) {
                                    echo "Нет";
                                }
                                else {
                                    $count = 0;
                                    $sql = "select count(id) from raport where active = 1 and machine_id = ".$calculation->machine_id." and value = ".$calculation->raport;
                                    $fetcher = new Fetcher($sql);
                                    if($row = $fetcher->Fetch()) {
                                        if($row[0] == 0) {
                                            echo "Да";
                                        }
                                        else {
                                            echo "Нет";
                                        }
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?= $calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? "Ширина этикетки" : "Ширина ручья" ?></td>
                            <?php if(!empty($calculation->stream_width)): ?>
                            <td><?=$calculation->stream_width.(empty($calculation->stream_width) ? "" : " мм") ?></td>
                            <?php else: ?>
                            <td style="white-space: nowrap;">Разная ≈ <?=rtrim(rtrim(number_format(array_sum($calculation->stream_widths) / $calculation->streams_number, 2, ",", ""), "0"), ",") ?> мм</td>
                            <?php endif; ?>
                        </tr>
                        <tr>
                            <td>Длина этикетки</td>
                            <td><?= DisplayNumber(floatval($calculation->length), 0).(empty($calculation->length) ? "" : " мм") ?></td>
                        </tr>
                        <tr>
                            <td>Кол-во ручьёв</td>
                            <td><?=$calculation->streams_number ?></td>
                        </tr>
                        <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
                        <tr>
                            <td style="line-height: 18px;">Этикеток в рапорте</td>
                            <td><?=$calculation->number_in_raport ?></td>
                        </tr>
                        <tr>
                            <td>Красочность</td>
                            <td><?=$calculation->ink_number ?> цв.</td>
                        </tr>
                        <tr>
                            <td>Штамп</td>
                            <td><?= (empty($calculation->knife) || $calculation->knife == 0) ? "Старый" : "Новый" ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td style="line-height: 18px;">Требование по материалу</td>
                            <td style="line-height: 18px;">
                                <div class="edit_requirement_link d-inline" id="edit_requirement_link_1"><a class="edit_requirement" href="javascript: void(0);" onclick="javascript: EditRequirement(1);"><img class="ml-2" src="../images/icons/edit1.svg" /></a></div>
                                <div class="requirement_label d-inline" id="requirement_label_1"><?= (empty($calculation->requirement1) ? "Ждем данные" : $calculation->requirement1) ?></div>
                                <div class="edit_requirement_form d-none" id="edit_requirement_form_1">
                                    <form class="requirement_form form-inline">
                                        <textarea rows="3" class="requirement_input" name="requirement" id="requirement_input_1" data-id="<?=$id ?>" data-i="1"><?=$calculation->requirement1 ?></textarea>
                                        <a class="btn btn-outline-dark" href="javascsript: void(0);" onclick="javascript: event.preventDefault(); SaveRequirement(<?=$id ?>, 1);">OK</a>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-4">
                    <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE): ?>
                    <h3>Ламинация 1</h3>
                    <table>
                        <tr>
                            <td>Марка мат-ла</td>
                            <td><?= $calculation->film_2 ?></td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td class="text-nowrap"><?= DisplayNumber(floatval($calculation->thickness_2), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(rtrim(DisplayNumber(floatval($calculation->density_2), 2), "0"), ",").' г/м<sup>2</sup>' ?></td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td><?= DisplayNumber(floatval($calculation_result->width_2), 0) ?> мм</td>
                        </tr>
                        <tr>
                            <td style="line-height: 18px;">Метраж на приладку</td>
                            <td><?= DisplayNumber(floatval($calculation->data_priladka_laminator->length) * $calculation->uk2, 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Метраж на тираж</td>
                            <td><?= DisplayNumber(floatval($calculation_result->length_pure_2), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Всего мат-ла</td>
                            <td><?= DisplayNumber(floatval($calculation_result->length_dirty_2), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Ламинационный вал</td>
                            <td><?= DisplayNumber(floatval($calculation->lamination_roller_width), 0) ?> мм</td>
                        </tr>
                        <tr>
                            <td>Анилокс</td>
                            <td>Нет</td>
                        </tr>
                        <tr>
                            <td style="line-height: 18px;">Требование по материалу</td>
                            <td style="line-height: 18px;">
                                <div class="edit_requirement_link d-inline" id="edit_requirement_link_2"><a class="edit_requirement" href="javascript: void(0);" onclick="javascript: EditRequirement(2);"><img class="ml-2" src="../images/icons/edit1.svg" /></a></div>
                                <div class="requirement_label d-inline" id="requirement_label_2"><?= (empty($calculation->requirement2) ? "Ждем данные" : $calculation->requirement2) ?></div>
                                <div class="edit_requirement_form d-none" id="edit_requirement_form_2">
                                    <form class="requirement_form form-inline">
                                        <textarea rows="3" class="requirement_input" name="requirement" id="requirement_input_2" data-id="<?=$id ?>" data-i="2"><?=$calculation->requirement2 ?></textarea>
                                        <a class="btn btn-outline-dark" href="javascsript: void(0);" onclick="javascript: event.preventDefault(); SaveRequirement(<?=$id ?>, 2);">OK</a>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <?php endif; ?>
                </div>
                <div class="col-4">
                    <h3>Информация для резчика</h3>
                    <table>
                        <tr>
                            <td>Отгрузка в</td>
                            <td><?=$calculation->unit == KG ? 'Кг' : 'Шт' ?></td>
                        </tr>
                        <tr>
                            <td>Готовая продукция</td>
                            <td style="line-height: 18px;"><?=$calculation->unit == 'kg' ? 'Взвешивать' : 'Записывать метраж' ?></td>
                        </tr>
                        <tr>
                            <td><?=$calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? "Обр. шир. / Гор. зазор" : "Обрезная ширина" ?></td>
                            <td>
                                <?php
                                if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                                    if(empty($calculation->data_gap->gap_stream)) {
                                        echo DisplayNumber(intval($calculation->stream_width), 0)." мм";
                                    }
                                    else {
                                        echo DisplayNumber(floatval($calculation->stream_width) + floatval($calculation->data_gap->gap_stream), 2)." / ".DisplayNumber(floatval($calculation->data_gap->gap_stream), 2)." мм";
                                    }
                                }
                                else if(empty ($calculation->stream_width)) {
                                    echo "Разная";
                                }
                                else {
                                    echo DisplayNumber(intval($calculation->stream_width), 0)." мм";
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Намотка до</td>
                            <td>
                                <?php
                                if(empty($winding)) {
                                    echo 'Ждем данные';
                                }
                                elseif(empty ($winding_unit)) {
                                    echo 'Нет данных по кг/мм/м/шт';
                                }
                                elseif($winding_unit == 'pc') {
                                    if(empty($calculation->length)) {
                                        echo 'Нет данных по длине этикетки';
                                    }
                                    elseif($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                                        echo DisplayNumber((floatval($winding) * floatval($calculation->raport) / 1000) / floatval($calculation->number_in_raport), 0);
                                    }
                                    else {
                                        echo DisplayNumber(floatval($winding) * floatval($calculation->length) / 1000, 0);
                                    }
                                }
                                else {
                                    echo DisplayNumber(floatval($winding), 0);
                                }
                                
                                switch ($winding_unit) {
                                    case 'kg':
                                        echo " кг";
                                        break;
                                    case 'mm':
                                        echo " мм";
                                        break;
                                    case 'm':
                                        echo " м";
                                        break;
                                    case 'pc':
                                        echo " м";
                                        break;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="line-height: 18px;">Прим. метраж намотки</td>
                            <td style="line-height: 18px;">
                                <?php
                                /* 1) Если намотка до =«кг», то Примерный метраж = (намотка до *1000*1000)/((уд вес пленка 1 + уд вес пленка 2 + уд вес пленка 3)*обрезная ширина))
                                 * 1) Если намотка до =«кг», то Примерный метраж = (намотка до *1000*1000)/((уд вес пленка 1 + уд вес пленка 2 + уд вес пленка 3)*обрезная ширина))-200
                                 * 2) Если намотка до = «мм» , то значение = "Нет"
                                 * 3) Если намотка до = «м», то значение = "Нет"
                                 * 4) Если намотка до = «шт» , то значение = "Нет" */
                                if(empty($winding) || empty($winding_unit)) {
                                    echo 'Ждем данные';
                                }
                                elseif(empty ($calculation->density_1)) {
                                    echo 'Нет данных по уд. весу пленки';
                                }
                                elseif(empty ($calculation_result->width_1)) {
                                    echo 'Нет данных по ширине мат-ла';
                                }
                                elseif($winding_unit == 'kg') {
                                    echo DisplayNumber((floatval($winding) * 1000 * 1000) / ((floatval($calculation->density_1) + ($calculation->density_2 === null ? 0 : floatval($calculation->density_2)) + ($calculation->density_3 === null ? 0 : floatval($calculation->density_3))) * floatval($calculation->stream_width ?? (max($calculation->stream_widths) / $calculation->streams_number))) - 200, 0)." м";
                                }
                                else {
                                    echo 'Нет';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Шпуля</td>
                            <td><?= empty($spool) ? "Ждем данные" : $spool." мм" ?></td>
                        </tr>
                        <tr>
                            <td>Этикеток в 1 м. пог.</td>
                            <td><?= DisplayNumber($calculation->number_in_meter, 4) ?></td>
                        </tr>
                        <tr>
                            <td>Бирки</td>
                            <td>
                                <?php
                                switch ($labels) {
                                    case CalculationResult::LABEL_PRINT_DESIGN:
                                        echo "Принт-Дизайн";
                                        break;
                                    case CalculationResult::LABEL_FACELESS:
                                        echo "Безликие";
                                        break;
                                    default :
                                        echo "Ждем данные";
                                        break;
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Склейки</td>
                            <td>Помечать</td>
                        </tr>
                        <tr>
                            <td>Отходы</td>
                            <td><?=$waste ?></td>
                        </tr>
                        <tr>
                            <td>Упаковка</td>
                            <td>
                                <?php
                                switch ($package) {
                                    case CalculationResult::PACKAGE_PALLETED:
                                        echo "Паллетирование";
                                        break;
                                    case CalculationResult::PACKAGE_BULK:
                                        echo "Россыпью";
                                        break;
                                    case CalculationResult::PACKAGE_EUROPALLET:
                                        echo "Европаллет";
                                        break;
                                    case CalculationResult::PACKAGE_BOXES:
                                        echo "Коробки";
                                        break;
                                    default :
                                        echo "Ждем данные";
                                        break;
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE): ?>
            <div class="row mt-3">
                <div class="col-4">
                    <h3>Красочность: <?=$calculation->ink_number ?> цв.</h3>
                    <table>
                        <?php
                        for($i = 1; $i <= $calculation->ink_number; $i++):
                        $ink_var = "ink_$i";
                        $color_var = "color_$i";
                        $cmyk_var = "cmyk_$i";
                        $lacquer_var = "lacquer_$i";
                        $percent_var = "percent_$i";
                        $cliche_var = "cliche_$i";
                        ?>
                        <tr>
                            <td>
                                <div class="color_label d-inline" id="color_label_<?=$i ?>">
                                <?php
                                switch ($$ink_var) {
                                    case INK_CMYK:
                                        switch ($$cmyk_var) {
                                            case CMYK_CYAN:
                                                echo "Cyan";
                                                break;
                                            case CMYK_MAGENDA:
                                                echo "Magenda";
                                                break;
                                            case CMYK_YELLOW:
                                                echo "Yellow";
                                                break;
                                            case CMYK_KONTUR:
                                                echo "Kontur";
                                                break;
                                        }
                                        break;
                                    case INK_PANTON:
                                        echo "P".$$color_var;
                                        break;
                                    case INK_WHITE;
                                        echo "Белая";
                                        break;
                                    case INK_LACQUER;
                                        switch ($$lacquer_var) {
                                            case LACQUER_GLOSSY:
                                                echo 'Лак глянцевый';
                                                break;
                                            case LACQUER_MATTE:
                                                echo 'Лак матовый';
                                                break;
                                            default :
                                                echo "Лак";
                                                break;
                                        }
                                        break;
                                }
                                ?>
                                </div>
                                <?php
                                if($$ink_var == INK_PANTON):
                                ?>
                                <div class="edit_panton_link d-inline" id="edit_panton_link_<?=$i ?>"><a class="edit_panton" href="javascript: void(0);" onclick="javascript: EditPanton(<?=$i ?>);"><img class="ml-2" src="../images/icons/edit1.svg" /></a></div>
                                <div class="edit_panton_form d-none" id="edit_panton_form_<?=$i ?>">
                                    <form class="panton_form form-inline">
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">P</span></div>
                                            <input type="text" class="form-control color_input" name="color" id="color_input_<?=$i ?>" value="<?=$$color_var ?>" data-id="<?=$id ?>" data-i="<?=$i ?>" />
                                            <div class="input-group-append">
                                                <a class="btn btn-outline-dark" href="javascript: void(0);" onclick="javascript: SavePanton(<?=$id ?>, <?=$i ?>);">OK</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                switch ($$cliche_var) {
                                    case CLICHE_OLD:
                                        echo "Старая";
                                        break;
                                    case CLICHE_FLINT:
                                        echo "Новая Flint $machine_coeff";
                                        break;
                                    case CLICHE_KODAK:
                                        echo "Новая Kodak $machine_coeff";
                                        break;
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </table>
                </div>
                <div class="col-4">
                    <h3>Ламинация 2</h3>
                    <table>
                        <tr>
                            <td>Марка мат-ла</td>
                            <td><?= $calculation->film_3 ?></td>
                        </tr>
                        <tr>
                            <td>Толщина</td>
                            <td class="text-nowrap"><?= DisplayNumber(floatval($calculation->thickness_3), 0).' мкм&nbsp;&ndash;&nbsp;'.rtrim(rtrim(DisplayNumber(floatval($calculation->density_3), 2), "0"), ",").' г/м<sup>2</sup>' ?></td>
                        </tr>
                        <tr>
                            <td>Ширина мат-ла</td>
                            <td><?= DisplayNumber(floatval($calculation_result->width_3), 0) ?> мм</td>
                        </tr>
                        <tr>
                            <td style="line-height: 18px;">Метраж на приладку</td>
                            <td><?= DisplayNumber(floatval($calculation->data_priladka_laminator->length) * $calculation->uk3, 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Метраж на тираж</td>
                            <td><?= DisplayNumber(floatval($calculation_result->length_pure_3), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td>Всего мат-ла</td>
                            <td><?= DisplayNumber(floatval($calculation_result->length_dirty_3), 0) ?> м</td>
                        </tr>
                        <tr>
                            <td style="line-height: 18px;">Требование по материалу</td>
                            <td style="line-height: 18px;">
                                <div class="edit_requirement_link d-inline" id="edit_requirement_link_3"><a class="edit_requirement" href="javascript: void(0);" onclick="javascript: EditRequirement(3);"><img class="ml-2" src="../images/icons/edit1.svg" /></a></div>
                                <div class="requirement_label d-inline" id="requirement_label_3"><?= (empty($calculation->requirement3) ? "Ждем данные" : $calculation->requirement3) ?></div>
                                <div class="edit_requirement_form d-none" id="edit_requirement_form_3">
                                    <form class="requirement_form form-inline">
                                        <textarea rows="3" class="requirement_input" name="requirement" id="requirement_input_3" data-id="<?=$id ?>" data-i="3"><?=$calculation->requirement3 ?></textarea>
                                        <a class="btn btn-outline-dark form-control" href="javascsript: void(0);" onclick="javascript: event.preventDefault(); SaveRequirement(<?=$id ?>, 3);">OK</a>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
            <div class="mt-5 mb-3">
                <button type="button" id="show_set_printings" class="btn btn-outline-dark" data-toggle="modal" data-target="#set_printings">Настроить тиражи</button>
            </div>
            <div class="row">
                <?php
                $printing_sequence = 0;
                foreach($printings as $printing):
                    $printing_sequence++;
                ?>
                <div class="col-3">
                    <div class="printing_title font-weight-bold">Тираж <?=$printing_sequence ?></div>
                    <div class="d-flex justify-content-between font-italic border-bottom">
                        <div><?= DisplayNumber(intval($printing['quantity']), 0) ?> шт</div>
                        <div><?= DisplayNumber(floatval($printing['length']), 0) ?> м</div>
                    </div>
                    <table class="mb-1 w-100">
                    <?php
                    for($i = 1; $i <= $calculation->ink_number; $i++):
                    $ink_var = "ink_$i";
                    $color_var = "color_$i";
                    $cmyk_var = "cmyk_$i";
                    $lacquer_var = "lacquer_$i";
                    ?>
                        <tr>
                            <td>
                                <?php
                                switch($$ink_var) {
                                    case INK_CMYK:
                                        switch ($$cmyk_var) {
                                            case CMYK_CYAN:
                                                echo 'Cyan';
                                                break;
                                            case CMYK_MAGENDA:
                                                echo 'Magenda';
                                                break;
                                            case CMYK_YELLOW:
                                                echo 'Yellow';
                                                break;
                                            case CMYK_KONTUR:
                                                echo 'Kontur';
                                                break;
                                        }
                                        break;
                                    case INK_PANTON:
                                        echo "P".$$color_var;
                                        break;
                                    case INK_WHITE:
                                        echo 'Белая';
                                        break;
                                    case INK_LACQUER:
                                        switch ($$lacquer_var) {
                                            case LACQUER_GLOSSY:
                                                echo 'Лак глянцевый';
                                                break;
                                            case LACQUER_MATTE:
                                                echo 'Лак матовый';
                                                break;
                                            default :
                                                echo 'Лак';
                                                break;
                                        }
                                        break;
                                }
                                ?>
                            </td>
                            <td id="cliche_<?=$printing['id'] ?>_<?=$i ?>">
                                <?php
                                if(empty($cliches[$printing['id']][$i])) {
                                    echo 'Ждем данные';
                                }
                                else {
                                    switch ($cliches[$printing['id']][$i]) {
                                        case CLICHE_FLINT:
                                            echo "Новая Flint $machine_coeff";
                                            break;
                                        case CLICHE_KODAK:
                                            echo "Новая Kodak $machine_coeff";
                                            break;
                                        case CLICHE_OLD:
                                            echo "Старая";
                                            break;
                                        case CLICHE_REPEAT:
                                            echo "Повт. исп. с тир. ".$repeats[$printing['id']][$i];
                                            break;
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endfor; ?>
                    </table>
                    <?php
                    $button1_wrapper_class = 'd-block';
                    if(!empty($printing['image1'])) {
                        $button1_wrapper_class = 'd-none';
                    }
                    $button2_wrapper_class = 'd-block';
                    if(!empty($printing['image2'])) {
                        $button2_wrapper_class = 'd-none';
                    }
                    ?>
                    <div class="font-italic">Загрузите файл</div>
                    <div class="d-flex justify-content-between">
                        <div id="mini_button1_wrapper_printing_<?=$printing['id'] ?>" class="form-group <?=$button1_wrapper_class ?>" style="margin-bottom: 0;">
                            <label for="image1_printing_<?=$printing['id'] ?>" class="btn btn-sm btn-light"><img src="../images/icons/upload_file.svg" class="mr-2 align-baseline" />С под.</label>
                            <input type="file" accept="image/*,application/pdf" name="image1_printing_<?=$printing['id'] ?>" id="image1_printing_<?=$printing['id'] ?>" class="d-none color_input" onchange="UploadImage('printing', <?=$printing['id'] ?>, 1);" />
                        </div>
                        <div id="mini_button2_wrapper_printing_<?=$printing['id'] ?>" class="form-group <?=$button2_wrapper_class ?>" style="margin-bottom: 0;">
                            <label for="image2_printing_<?=$printing['id'] ?>" class="btn btn-sm btn-light"><img src="../images/icons/upload_file.svg" class="mr-2 align-baseline" />Без п.</label>
                            <input type="file" accept="image/*,application/pdf" name="image2_printing_<?=$printing['id'] ?>" id="image2_printing_<?=$printing['id'] ?>" class="d-none color_input" onchange="UploadImage('printing', <?=$printing['id'] ?>, 2);" />
                        </div>
                    </div>
                    <?php
                    $image1_wrapper_class = 'd-block';
                    if(empty($printing['image1'])) {
                        $image1_wrapper_class = 'd-none';
                    }
                    $image2_wrapper_class = 'd-block';
                    if(empty($printing['image2'])) {
                        $image2_wrapper_class = "d-none";
                    }
                    ?>
                    <div class="d-flex justify-content-between pb-2">
                        <div id="mini_image1_wrapper_printing_<?=$printing['id'] ?>" class="w-50 <?=$image1_wrapper_class ?>">
                            <a id="mini_image1_link_printing_<?=$printing['id'] ?>" 
                               href="javascript: void(0);" 
                               data-toggle="modal" 
                               data-target="#big_image" 
                               data-filename="<?=$printing['image1'] ?>" 
                               onclick="javascript: document.forms.delete_image_form.object.value = 'printing'; document.forms.delete_image_form.id.value = <?=$printing['id'] ?>; document.forms.delete_image_form.image.value = 1; document.forms.download_image_form.object.value = 'printing'; document.forms.download_image_form.id.value = <?=$printing['id'] ?>; document.forms.download_image_form.image.value = 1; $('#big_image_header').text('Тираж <?=$printing_sequence ?>'); $('#deleted_file_name').text('Тираж <?= $printing_sequence ?>, с подписью заказчика'); $('#big_image_img').attr('src', '../content/printing/' + $(this).attr('data-filename') + '?' + Date.now());">
                                <img id="mini_image1_printing_<?=$printing['id'] ?>" src="../content/printing/mini/<?=$printing['image1'].'?'. time() ?>" class="img-fluid" />
                            </a>
                            С подписью <a href="javascript: void(0);" data-toggle="modal" data-target="#confirm_delete" style="font-weight: bold; font-size: x-large; vertical-align: central;" onclick="javascript: $('#deleted_file_name').text('Тираж <?=$printing_sequence ?>, с подписью заказчика'); document.forms.delete_image_form.object.value = 'printing'; document.forms.delete_image_form.id.value = <?=$printing['id'] ?>; document.forms.delete_image_form.image.value = 1;">&times;</a>
                        </div>
                        <div id="mini_image2_wrapper_printing_<?=$printing['id'] ?>" class="w-50 <?=$image2_wrapper_class ?>">
                            <a id="mini_image2_link_printing_<?=$printing['id'] ?>" 
                               href="javascript: void(0);" 
                               data-toggle="modal" 
                               data-target="#big_image" 
                               data-filename="<?=$printing['image2'] ?>" 
                               onclick="javascript: document.forms.delete_image_form.object.value = 'printing'; document.forms.delete_image_form.id.value = <?=$printing['id'] ?>; document.forms.delete_image_form.image.value = 2; document.forms.download_image_form.object.value = 'printing'; document.forms.download_image_form.id.value = <?=$printing['id'] ?>; document.forms.download_image_form.image.value = 2; $('#big_image_header').text('Тираж <?=$printing_sequence ?>'); $('#deleted_file_name').text('Тираж <?= $printing_sequence ?>, без подписи заказчика'); $('#big_image_img').attr('src', '../content/printing/' + $(this).attr('data-filename') + '?' + Date.now());">
                                <img id="mini_image2_printing_<?=$printing['id'] ?>" src="../content/printing/mini/<?=$printing['image2'].'?'. time() ?>" class="img-fluid" />
                            </a>
                            Без подписи <a href="javascript: void(0);" data-toggle="modal" data-target="#confirm_delete" style="font-weight: bold; font-size: x-large; vertical-align: central;" onclick="javascript: $('#deleted_file_name').text('Тираж <?=$printing_sequence ?>, без подписи заказчика'); document.forms.delete_image_form.object.value = 'printing'; document.forms.delete_image_form.id.value = <?=$printing['id'] ?>; document.forms.delete_image_form.image.value = 2;">&times;</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <a name="form" />
            <div id="cliche_validation" class="text-danger<?= empty($cliche_valid) ? " d-none" : " d-block" ?>">Укажите формы для каждой краски</div>
            <div id="requirement_validation" class="text-danger<?= empty($requirement_valid) ? " d-none" : " d-block" ?>">Укажите требование по материалу</div>
            <div style="position: relative;">
                <form class="mt-3" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="scroll" />
                    <input type="hidden" name="id" value="<?= $id ?>" />
                    <input type="hidden" name="techmap_id" value="<?=$calculation_result->techmap_id ?>" />
                    <div class="row">
                        <div class="col-6">
                            <h2>Информация для резчика</h2>
                            <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
                            <div class="form-group">
                                <label for="supplier_id">Поставщик мат-ла</label>
                                <select id="supplier_id" name="supplier_id" class="form-control">
                                    <option value="">Любой</option>
                                    <?php
                                    $sql = "select id, name from supplier where id in (select supplier_id from supplier_film_variation where film_variation_id = (select film_variation_id from calculation where id = $id))";
                                    $fetcher = new Fetcher($sql);
                                    while($row = $fetcher->Fetch()):
                                        $checked = $supplier_id == $row['id'] ? " selected='selected'" : "";
                                    ?>
                                    <option value="<?=$row['id'] ?>"<?=$checked ?>><?=$row['name'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="side">Печать</label>
                                <select id="side" name="side" class="form-control<?=$side_valid ?>" required="required">
                                    <?php if($lamination == "нет"): ?>
                                    <option value="" hidden="hidden">...</option>
                                    <option value="<?= CalculationResult::SIDE_FRONT ?>"<?= $side == 1 ? " selected='selected'" : "" ?>>Лицевая</option>
                                    <?php endif; ?>
                                    <option value="<?= CalculationResult::SIDE_BACK ?>"<?= $side == 2 ? " selected='selected'" : "" ?>>Оборотная</option>
                                </select>
                                <div class="invalid-feedback">Сторона обязательно</div>
                            </div>
                            <div class="form-group">
                                <label for="winding">Намотка до</label>
                                <div class="input-group">
                                    <input type="text" 
                                        id="winding" 
                                        name="winding" 
                                        class="form-control int-only<?=$winding_valid ?>" 
                                        placeholder="Намотка до" 
                                        value="<?= $winding ?>" 
                                        required="required" 
                                        onmousedown="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                        onfocus="javascript: $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder');" 
                                        onmouseup="javascript: $(this).attr('id', 'winding'); $(this).attr('name', 'winding'); $(this).attr('placeholder', 'Намотка до');" 
                                        onkeydown="javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); $(this).removeAttr('placeholder'); }" 
                                        onkeyup="javascript: $(this).attr('id', 'winding'); $(this).attr('name', 'winding'); $(this).attr('placeholder', 'Намотка до');" 
                                        onfocusout="javascript: $(this).attr('id', 'winding'); $(this).attr('name', 'winding'); $(this).attr('placeholder', 'Намотка до');" />
                                    <div class="input-group-append">
                                        <select id="winding_unit" name="winding_unit" required="required">
                                            <option value="" hidden="hidden">...</option>
                                            <option value="kg"<?= $winding_unit == 'kg' ? " selected='selected'" : "" ?>>Кг</option>
                                            <option value="mm"<?= $winding_unit == 'mm' ? " selected='selected'" : "" ?>>мм</option>
                                            <option value="m"<?= $winding_unit == 'm' ? " selected='selected'" : "" ?>>Метры</option>
                                            <option value="pc"<?= $winding_unit == 'pc' ? " selected='selected'" : "" ?>>шт</option>
                                        </select>
                                    </div>
                                    <div class="invalid-feedback">Намотка обязательно</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="spool">Шпуля</label>
                                <select id="spool" name="spool" class="form-control<?=$spool_valid ?>" required="required">
                                    <option value="" hidden="hidden">...</option>
                                    <option<?= $spool == 40 ? " selected='selected'" : "" ?>>40</option>
                                    <option<?= $spool == 76 ? " selected='selected'" : "" ?>>76</option>
                                    <option<?= $spool == 152 ? " selected='selected'" : "" ?>>152</option>
                                </select>
                                <div class="invalid-feedback">Шпуля обязательно</div>
                            </div>
                            <div class="form-group">
                                <label for="labels">Бирки</label>
                                <select id="labels" name="labels" class="form-control<?=$labels_valid ?>" required="required">
                                    <option value="" hidden="hidden">...</option>
                                    <option value="<?= CalculationResult::LABEL_PRINT_DESIGN ?>"<?= $labels == 1 ? " selected='selected'" : "" ?>>Принт-Дизайн</option>
                                    <option value="<?= CalculationResult::LABEL_FACELESS ?>"<?= $labels == 2 ? " selected='selected'" : "" ?>>Безликие</option>
                                </select>
                                <div class="invalid-feedback">Бирки обязательно</div>
                            </div>
                            <div class="form-group">
                                <label for="package">Упаковка</label>
                                <select id="package" name="package" class="form-control<?=$package_valid ?>" required="required">
                                    <option value="" hidden="hidden">...</option>
                                    <option value="<?= CalculationResult::PACKAGE_PALLETED ?>"<?= $package == CalculationResult::PACKAGE_PALLETED ? " selected='selected'" : "" ?>>Паллетирование</option>
                                    <option value="<?= CalculationResult::PACKAGE_BULK ?>"<?= $package == CalculationResult::PACKAGE_BULK ? " selected='selected'" : "" ?>>Россыпью</option>
                                    <option value="<?= CalculationResult::PACKAGE_EUROPALLET ?>"<?= $package == CalculationResult::PACKAGE_EUROPALLET ? " selected='selected'" : "" ?>>Европаллет</option>
                                    <option value="<?= CalculationResult::PACKAGE_BOXES ?>"<?= $package == CalculationResult::PACKAGE_BOXES ? " selected='selected'" : "" ?>>Коробки</option>
                                </select>
                                <div class="invalid-feedback">Упаковка обязательно</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <h3 style="margin-top: 20px;">Выберите фотометку</h3>
                            <div class="form-group">
                                <label for="photolabel">Фотометка</label>
                                <select id="photolabel" name="photolabel" class="form-control<?=$photolabel_valid ?>" required="required">
                                    <option value="<?= CalculationResult::PHOTOLABEL_LEFT ?>"<?=$photolabel == CalculationResult::PHOTOLABEL_LEFT ? " selected='selected'" : "" ?>>Левая</option>
                                    <option value="<?= CalculationResult::PHOTOLABEL_RIGHT ?>"<?=$photolabel == CalculationResult::PHOTOLABEL_RIGHT ? " selected='selected'" : "" ?>>Правая</option>
                                    <option value="<?= CalculationResult::PHOTOLABEL_BOTH ?>"<?=$photolabel == CalculationResult::PHOTOLABEL_BOTH ? " selected='selected'" : "" ?>>Две фотометки</option>
                                    <option value="<?= CalculationResult::PHOTOLABEL_NONE ?>"<?=$photolabel == CalculationResult::PHOTOLABEL_NONE || (empty($photolabel) && $calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) ? " selected='selected'" : "" ?>>Без фотометки</option>
                                    <option value="<?= CalculationResult::PHOTOLABEL_NOT_FOUND ?>"<?=$photolabel == CalculationResult::PHOTOLABEL_NOT_FOUND ? " selected='selected'" : "" ?>>Нет нужной намотки</option>
                                </select>
                                <div class="invalid-feedback">Расположение фотометки обязательно</div>
                            </div>
                            <div class="form-group roll-selector">
                                <?php
                                $roll_folder = $calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE ? "roll" : "roll_left";
                                switch ($photolabel) {
                                    case CalculationResult::PHOTOLABEL_LEFT:
                                        $roll_folder = "roll_left";
                                        break;
                                    case CalculationResult::PHOTOLABEL_RIGHT:
                                        $roll_folder = "roll_right";
                                        break;
                                    case CalculationResult::PHOTOLABEL_BOTH:
                                        $roll_folder = "roll_both";
                                        break;
                                    case CalculationResult::PHOTOLABEL_NONE:
                                        $roll_folder = "roll";
                                        break;
                                }
                                
                                if($photolabel != CalculationResult::PHOTOLABEL_NOT_FOUND):
                                ?>
                                <input type="radio" class="form-check-inline" id="roll_type_1" name="roll_type" value="1"<?= $roll_type == 1 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_1" id="roll_type_1_label" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_1_image" src="../images/<?=$roll_folder ?>/roll_type_1_black.svg<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline" id="roll_type_2" name="roll_type" value="2"<?= $roll_type == 2 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_2" id="roll_type_2_label" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_2_image" src="../images/<?=$roll_folder ?>/roll_type_2_black.svg<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline" id="roll_type_3" name="roll_type" value="3"<?= $roll_type == 3 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_3" id="roll_type_3_label" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_3_image" src="../images/<?=$roll_folder ?>/roll_type_3_black.svg<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline" id="roll_type_4" name="roll_type" value="4"<?= $roll_type == 4 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_4" id="roll_type_4_label" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_4_image" src="../images/<?=$roll_folder ?>/roll_type_4_black.svg<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline" id="roll_type_5" name="roll_type" value="5"<?= $roll_type == 5 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_5" id="roll_type_5_label" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_5_image" src="../images/<?=$roll_folder ?>/roll_type_5_black.svg<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline" id="roll_type_6" name="roll_type" value="6"<?= $roll_type == 6 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_6" id="roll_type_6_label" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_6_image" src="../images/<?=$roll_folder ?>/roll_type_6_black.svg<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline" id="roll_type_7" name="roll_type" value="7"<?= $roll_type == 7 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_7" id="roll_type_7_label" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_7_image" src="../images/<?=$roll_folder ?>/roll_type_7_black.svg<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                                <input type="radio" class="form-check-inline" id="roll_type_8" name="roll_type" value="8"<?= $roll_type == 8 ? " checked='checked'" : "" ?> />
                                <label for="roll_type_8" id="roll_type_8_label" style="position: relative; padding-bottom: 15px; padding-right: 4px;"><img id="roll_type_8_image" src="../images/<?=$roll_folder ?>/roll_type_8_black.svg<?='?'. time() ?>" style="height: 30px; width: auto;" /></label>
                                <?php endif; ?>
                            </div>
                            <div id="roll_type_validation" class="text-danger<?= empty($roll_type_valid) ? " d-none" : " d-block" ?>">Выберите сторону печати</div>
                            <h3>Комментарий</h3>
                            <textarea rows="6" name="comment" class="form-control"><?= html_entity_decode($comment ?? '') ?></textarea>
                        </div>
                    </div>
                    <?php if($calculation->work_type_id != WORK_TYPE_SELF_ADHESIVE): ?>
                    <div class="row">
                        <div class="col-12">
                            <h2>Наименования</h2>
                            <?php
                            $stream_ordinal = 0;
                            foreach($streams as $stream):
                            ?>
                            <h3>Ручей <?=(++$stream_ordinal).(count($calculation->stream_widths) > 0 ? ": Ширина ручья ".$stream['width']." мм" : "") ?></h3>
                            <div class="form-group">
                                <input type="text" name="stream_<?=$stream['id'] ?>" class="form-control<?=empty($streams_valid["stream_".$stream['id']]) ? "" : $streams_valid["stream_".$stream['id']] ?>" value="<?= htmlentities($stream['name'] ?? '') ?>" placeholder="Наименование" autocomplete="off" required="required" />
                                <div class="invalid-feedback">Наименование обязательно</div>
                            </div>
                            <?php if($calculation->work_type_id != WORK_TYPE_NOPRINT): ?>
                            <p class="font-weight-bold">Загрузите файл оригинал-макета</p>
                            <div class="d-flex justify-content-start">
                                <?php
                                $button1_wrapper_class = 'd-block';
                                if(!empty($stream['image1'])) {
                                    $button1_wrapper_class = 'd-none';
                                }
                                $button2_wrapper_class = 'd-block';
                                if(!empty($stream['image2'])) {
                                    $button2_wrapper_class = 'd-none';
                                }
                                ?>
                                <div id="mini_button1_wrapper_stream_<?=$stream['id'] ?>" class="form-group mr-4 <?=$button1_wrapper_class ?>">
                                    <label for="image1_stream_<?=$stream['id'] ?>" class="btn btn-light"><img src="../images/icons/upload_file.svg" class="mr-2 align-baseline" /> С подписью заказчика</label>
                                    <input type="file" accept="image/*,application/pdf" name="image1_stream_<?=$stream['id'] ?>" id="image1_stream_<?=$stream['id'] ?>" class="d-none color_input" onchange="UploadImage('stream', <?=$stream['id'] ?>, 1);" />
                                </div>
                                <div id="mini_button2_wrapper_stream_<?=$stream['id'] ?>" class="form-group <?=$button2_wrapper_class ?>">
                                    <label for="image2_stream_<?=$stream['id'] ?>" class="btn btn-light"><img src="../images/icons/upload_file.svg" class="mr-2 align-baseline" /> Без подписи заказчика</label>
                                    <input type="file" accept="image/*,application/pdf" name="image2_stream_<?=$stream['id'] ?>" id="image2_stream_<?=$stream['id'] ?>" class="d-none color_input" onchange="UploadImage('stream', <?=$stream['id'] ?>, 2);" />
                                </div>
                            </div>
                            <div class="d-flex justify-content-start">
                                <?php
                                $image1_wrapper_class = 'd-block';
                                if(empty($stream['image1'])) {
                                    $image1_wrapper_class = 'd-none';
                                }
                                $image2_wrapper_class = 'd-block';
                                if(empty($stream['image2'])) {
                                    $image2_wrapper_class = 'd-none';
                                }
                                ?>
                                <div id="mini_image1_wrapper_stream_<?=$stream['id'] ?>" class="mr-4 <?=$image1_wrapper_class ?>">
                                    <a id="mini_image1_link_stream_<?=$stream['id'] ?>" 
                                       href="javascript: void(0);" 
                                       data-toggle="modal" 
                                       data-target="#big_image" 
                                       data-filename="<?=$stream['image1'] ?>" 
                                       onclick="javascript: document.forms.delete_image_form.object.value = 'stream'; document.forms.delete_image_form.id.value = <?=$stream['id'] ?>; document.forms.delete_image_form.image.value = 1; document.forms.download_image_form.object.value = 'stream'; document.forms.download_image_form.id.value = <?=$stream['id'] ?>; document.forms.download_image_form.image.value = 1; $('#big_image_header').text('<?= empty($stream['name']) ? "Ручей ".$stream_ordinal : htmlentities($stream['name'] ?? '') ?>'); $('#deleted_file_name').text('<?= empty($stream['name']) ? "Ручей ".$stream_ordinal : htmlentities($stream['name'] ?? '') ?>, с подписью заказчика'); $('#big_image_img').attr('src', '../content/stream/' + $(this).attr('data-filename') + '?' + Date.now());">
                                        <img id="mini_image1_stream_<?=$stream['id'] ?>" src="../content/stream/mini/<?=$stream['image1'].'?'. time() ?>" class="img-fluid" />
                                    </a>
                                    <div class="mb-2">С подписью <a href="javascript: void(0);" data-toggle="modal" data-target="#confirm_delete" style="font-weight: bold; font-size: x-large; vertical-align: central;" onclick="javascript: $('#deleted_file_name').text('<?= empty($stream['name']) ? "Ручей ".$stream_ordinal : htmlentities($stream['name'] ?? '') ?>, с подписью заказчика'); document.forms.delete_image_form.object.value = 'stream'; document.forms.delete_image_form.id.value = <?=$stream['id'] ?>; document.forms.delete_image_form.image.value = 1;">&times;</a></div>
                                </div>
                                <div id="mini_image2_wrapper_stream_<?=$stream['id'] ?>" class="<?=$image2_wrapper_class ?>">
                                    <a id="mini_image2_link_stream_<?=$stream['id'] ?>" 
                                       href="javascript: void(0);" 
                                       data-toggle="modal" 
                                       data-target="#big_image" 
                                       data-filename="<?=$stream['image2'] ?>" 
                                       onclick="javascript: document.forms.delete_image_form.object.value = 'stream'; document.forms.delete_image_form.id.value = <?=$stream['id'] ?>; document.forms.delete_image_form.image.value = 2; document.forms.download_image_form.object.value = 'stream'; document.forms.download_image_form.id.value = <?=$stream['id'] ?>; document.forms.download_image_form.image.value = 2; $('#big_image_header').text('<?= empty($stream['name']) ? "Ручей ".$stream_ordinal : htmlentities($stream['name'] ?? '') ?>'); $('#deleted_file_name').text('<?= empty($stream['name']) ? "Ручей ".$stream_ordinal : htmlentities($stream['name'] ?? '') ?>, без подписи заказчика'); $('#big_image_img').attr('src', '../content/stream/' + $(this).attr('data-filename') + '?' + Date.now());">
                                        <img id="mini_image2_stream_<?=$stream['id'] ?>" src="../content/stream/mini/<?=$stream['image2'].'?'. time() ?>" class="img-fluid" />
                                    </a>
                                    <div class="mb-2">Без подписи <a href="javascript: void(0);" data-toggle="modal" data-target="#confirm_delete" style="font-weight: bold; font-size: x-large; vertical-align: central;" onclick="javascript: $('#deleted_file_name').text('<?= empty($stream['name']) ? "Ручей ".$stream_ordinal : htmlentities($stream['name'] ?? '') ?>, без подписи заказчика'); document.forms.delete_image_form.object.value = 'stream'; document.forms.delete_image_form.id.value = <?=$stream['id'] ?>; document.forms.delete_image_form.image.value = 2;">&times;</a></div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-6 d-flex justify-content-start mt-3">
                            <div>
                                <?php
                                $submit_class = " d-none";
                                $plan_class = " d-none";
                                if(empty($calculation_result->techmap_id) || filter_input(INPUT_POST, FROM_OTHER_TECHMAP) !== null || !$form_valid) {
                                    $submit_class = "";
                                }
                                elseif($calculation->status_id == ORDER_STATUS_TECHMAP && $calculation->work_type_id == WORK_TYPE_NOPRINT) {
                                    $plan_class = "";
                                }
                                elseif($calculation->status_id == ORDER_STATUS_TECHMAP && $calculation->work_type_id == WORK_TYPE_PRINT && count(array_filter($streams, function($x) { return empty($x["image1"]) || empty($x["image2"]); })) == 0) {
                                    $plan_class = "";
                                }
                                elseif($calculation->status_id == ORDER_STATUS_TECHMAP && $calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE && count(array_filter($printings, function($x) { return empty($x["image1"]) || empty($x["image2"]); })) == 0) {
                                    $plan_class = "";
                                }
                                ?>
                                <button type="submit" name="techmap_submit" id="techmap_submit" class="btn btn-dark mr-4 draft<?=$submit_class ?>" style="width: 175px;">Сохранить</button>
                                <button type="submit" name="plan_submit" id="plan_submit" class="btn btn-dark mr-4 draft<?=$plan_class ?>" style="width: 175px;">Поставить в план</button>
                            </div>
                            <div>
                                <?php
                                if(!empty($calculation_result->techmap_id)):
                            
                                $print_class = "d-block";
                                $no_print_class = "d-none";
                    
                                if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE) {
                                    $total_cliches_count = count($printings) * $calculation->ink_number;
                                    $valid_cliches_count = 0;
                    
                                    foreach ($cliches as $cliches_row) {
                                        $valid_cliches_count += count($cliches_row);
                                    }
                        
                                    if($total_cliches_count - $valid_cliches_count > 0) {
                                        $print_class = "d-none";
                                        $no_print_class = "d-block";
                                    }
                                }
                                ?>
                                <a id="no_print_btn" href="javascript: void(0);" target="_self" class="btn btn-outline-dark no_print_btn <?=$no_print_class ?>" style="width: 175px;" onclick="javascript: $('#cliche_validation').removeClass('d-none'); $('#cliche_validation').addClass('d-block'); window.location.replace('#cliche_validation');">Печать</a>
                                <a id="print_btn" href="print_tm.php?id=<?=$id ?>" target="_blank" class="btn btn-outline-dark print_btn <?=$print_class ?>" style="width: 175px;">Печать</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </form>
                <?php if(!empty($calculation_result->techmap_id) && $calculation->status_id == ORDER_STATUS_TECHMAP): ?>
                <div style="position: absolute; right: 0px; bottom: 0px;">
                    <form method="post">
                        <input type="hidden" name="id" value="<?=$id ?>" />
                        <input type="hidden" name="techmap_id" value="<?= $calculation_result->techmap_id ?>" />
                        <input type="hidden"  name="delete_techmap_submit" value="1" />
                        <button type="button" class="btn btn-dark" style="width: 175px;" onclick="javascript: if(confirm('Действительно удалить тех. карту?')) { this.form.submit(); };">Удалить тех. карту</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <!-- Подгрузка тех. карты из другого заказа -->
        <div class="modal fixed-left fade" id="techmapModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-aside" role="document">
                <div class="modal-content" style="padding-left: 32px; padding-right: 32px; padding-bottom: 32px; padding-top: 84px; width: 521px; overflow-y: auto;">
                    <h2><?=$calculation->customer ?></h2>
                    <?php
                    $sql = "select c.id c_id, c.date c_date, c.name c_name, "
                            . "tm.id tm_id, tm.supplier_id tm_supplier_id, tm.side tm_side, tm.winding tm_winding, tm.winding_unit tm_winding_unit, tm.spool tm_spool, tm.labels tm_labels, tm.package tm_package, tm.photolabel tm_photolabel, tm.roll_type tm_roll_type, tm.comment tm_comment "
                            . "from calculation c "
                            . "inner join techmap tm on tm.calculation_id = c.id "
                            . "where customer_id = ".$calculation->customer_id
                            . " and work_type_id = ".$calculation->work_type_id." ";
                    if(!empty($calculation_result->techmap_id)) {
                        $sql .= "and tm.id <> ".$calculation_result->techmap_id." ";
                    }
                    switch($lamination) {
                        case "нет":
                            $sql .= "and c.lamination1_film_variation_id is null and c.lamination1_individual_film_name = '' ";
                            break;
                        case "1";
                            $sql .= "and (c.lamination1_film_variation_id is not null or c.lamination1_individual_film_name <> '') "
                                    . "and c.lamination2_film_variation_id is null and c.lamination2_individual_film_name = '' ";
                            break;
                        case "2";
                            $sql .= "and (c.lamination2_film_variation_id is not null or c.lamination2_individual_film_name <> '') ";
                            break;
                        default :
                            $sql .= "and false ";
                            break;
                    }
                    $sql .= "order by c.date desc";
                    $fetcher = new Fetcher($sql);
                    
                    while($row = $fetcher->Fetch()):
                    $c_id = $row['c_id'];
                    $c_date = DateTime::createFromFormat('Y-m-d H:i:s', $row['c_date'])->format('d.m.Y');
                    $c_name = $row['c_name'];
                    $tm_id = $row['tm_id'];
                    $tm_supplier_id = $row['tm_supplier_id'];
                    $tm_side = $row['tm_side'];
                    $tm_winding = $row['tm_winding'];
                    $tm_winding_unit = $row['tm_winding_unit'];
                    $tm_spool = $row['tm_spool'];
                    $tm_labels = $row['tm_labels'];
                    $tm_package = $row['tm_package'];
                    $tm_photolabel = $row['tm_photolabel'];
                    $tm_roll_type = $row['tm_roll_type'];
                    $tm_comment = $row['tm_comment'];
                    ?>
                    <div class="border-bottom mb-2">
                        <div class="d-flex justify-content-between">
                            <div class="pt-2 font-weight-bold" style="font-size: large;"><?='ТК от '.$c_date ?></div>
                            <div>
                                <form method="post" action="#form">
                                    <input type="hidden" name="<?=FROM_OTHER_TECHMAP ?>" value="1" />
                                    <input type="hidden" name="id" value="<?= $c_id ?>" />
                                    <input type="hidden" name="techmap_id" value="<?=$tm_id ?>" />
                                    <input type="hidden" name="supplier_id" value="<?=$tm_supplier_id ?>" />
                                    <input type="hidden" name="side" value="<?=$tm_side ?>" />
                                    <input type="hidden" name="winding" value="<?= $tm_winding ?>" />
                                    <input type="hidden" name="winding_unit" value="<?=$tm_winding_unit ?>" />
                                    <input type="hidden" name="spool" value="<?=$tm_spool ?>" />
                                    <input type="hidden" name="labels" value="<?=$tm_labels ?>" />
                                    <input type="hidden" name="package" value="<?=$tm_package ?>" />
                                    <input type="hidden" name="photolabel" value="<?=$tm_photolabel ?>" />
                                    <input type="hidden" name="roll_type" value="<?=$tm_roll_type ?>" />
                                    <input type="hidden" name="comment" value="<?=$tm_comment ?>" />
                                    <button type="submit" class="btn btn-light">+ Подцепить</button>
                                </form>
                            </div>
                        </div>
                        <div style="font-size: large;"><?=$c_name ?></div>
                    </div>
                    <?php endwhile; ?>
                    <button type="button" class="close" data-dismiss='modal' style="position: absolute; right: 34px; top: 34px; z-index: 2000;"><img src="../images/icons/close_modal_red.svg" /></button>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            // Скрываем сообщение о невалидном значении стороны печати
            $('.roll-selector input').change(function(){
                $('#roll_type_validation').removeClass('d-block');
                $('#roll_type_validation').addClass('d-none');
            });
            
            // Скрываем сообщение о невалидном заполнении форм
            $('button#show_set_printings').click(function() {
                $('#cliche_validation').removeClass('d-block');
                $('#cliche_validation').addClass('d-none');
            });
            
            // Показываем кнопку "Сохранить" при внесении изменений
            <?php if(!empty($calculation_result->techmap_id)): ?>
                $('select').change(function() {
                    $('#techmap_submit').removeClass('d-none');
                });
                
                $('input').not('.color_input').change(function() {
                    $('#techmap_submit').removeClass('d-none');
                });
                
                $('input').not('.color_input').keydown(function() {
                    $('#techmap_submit').removeClass('d-none');
                });
                
                $('textarea').keydown(function() {
                    $('#techmap_submit').removeClass('d-none');
                });
                
                $('textarea').change(function() {
                    $('#techmap_submit').removeClass('d-none');
                });
            <?php endif; ?>
            
            // Редактируем пантоны
            $('.color_input').keydown(function(e) {
                if(e.which === 13) {
                    e.preventDefault();
                    SavePanton($(this).attr('data-id'), $(this).attr('data-i'));
                }
            });
            
            // Изменение рисунка роликов при выборе фотометки
            $('select#photolabel').change(function() {
                $('#roll_type_validation').removeClass('d-block');
                $('#roll_type_validation').addClass('d-none');
                
                switch($(this).val()) {
                    case '<?= CalculationResult::PHOTOLABEL_LEFT ?>':
                        for(var i = 1; i <= 8; i++) {
                            $('img#roll_type_' + i + '_image').attr('src', '../images/roll_left/roll_type_' + i + '_black.svg<?='?'. time() ?>');
                            $('input#roll_type_' + i).removeClass('d-none');
                            $('label#roll_type_' + i + '_label').removeClass('d-none');
                        }
                        break;
                    case '<?= CalculationResult::PHOTOLABEL_RIGHT ?>':
                        for(var i = 1; i <= 8; i++) {
                            $('img#roll_type_' + i + '_image').attr('src', '../images/roll_right/roll_type_' + i + '_black.svg<?='?'. time() ?>');
                            $('input#roll_type_' + i).removeClass('d-none');
                            $('label#roll_type_' + i + '_label').removeClass('d-none');
                        }
                        break;
                    case '<?= CalculationResult::PHOTOLABEL_BOTH ?>':
                        for(var i = 1; i <= 8; i++) {
                            $('img#roll_type_' + i + '_image').attr('src', '../images/roll_both/roll_type_' + i + '_black.svg<?='?'. time() ?>');
                            $('input#roll_type_' + i).removeClass('d-none');
                            $('label#roll_type_' + i + '_label').removeClass('d-none');
                        }
                        break;
                    case '<?= CalculationResult::PHOTOLABEL_NOT_FOUND ?>':
                        for(var i = 1; i <= 8; i++) {
                            $('img#roll_type_' + i + '_image').attr('src', '../images/roll/roll_type_' + i + '_black.svg<?='?'. time() ?>');
                            $('input#roll_type_' + i).addClass('d-none');
                            $('label#roll_type_' + i + '_label').addClass('d-none');
                        }
                        break;
                    default :
                        for(var i = 1; i <= 8; i++) {
                            $('img#roll_type_' + i + '_image').attr('src', '../images/roll/roll_type_' + i + '_black.svg<?='?'. time() ?>');
                            $('input#roll_type_' + i).removeClass('d-none');
                            $('label#roll_type_' + i + '_label').removeClass('d-none');
                        }
                        break;
                }
            });
            
            function EditPanton(i) {
                $('.color_label').removeClass('d-none');
                $('.color_label').addClass('d-inline');
                $('#color_label_' + i).removeClass('d-inline');
                $('#color_label_' + i).addClass('d-none');
                
                $('.edit_panton_link').removeClass('d-none');
                $('.edit_panton_link').addClass('d-inline');
                $('#edit_panton_link_' + i).removeClass('d-inline');
                $('#edit_panton_link_' + i).addClass('d-none');
                
                $('.edit_panton_form').removeClass('d-inline');
                $('.edit_panton_form').addClass('d-none');
                $('#edit_panton_form_' + i).removeClass('d-none');
                $('#edit_panton_form_' + i).addClass('d-inline');
                
                $('#color_input_' + i).focus();
            }
            
            function SavePanton(id, i) {
                $.ajax({ url: "_edit_panton.php?id=" + id + "&i=" + i + "&value=" + $('#color_input_' + i).val() })
                        .done(function(data) {
                            $('#color_label_' + i).text('P' + data);
                            
                            $('#edit_panton_form_' + i).removeClass('d-inline');
                            $('#edit_panton_form_' + i).addClass('d-none');
                            
                            $('#color_label_' + i).removeClass('d-none');
                            $('#color_label_' + i).addClass('d-inline');
                            
                            $('#edit_panton_link_' + i).removeClass('d-none');
                            $('#edit_panton_link_' + i).addClass('d-inline');
                        })
                        .fail(function() {
                            alert('Ошибка при редактировании пантона');
                        });
            }
            
            function EditRequirement(i) {
                $('.requirement_label').removeClass('d-none');
                $('.requirement_label').addClass('d-inline');
                $('#requirement_label_' + i).removeClass('d-inline');
                $('#requirement_label_' + i).addClass('d-none');
                
                $('.edit_requirement_link').removeClass('d-none');
                $('.edit_requirement_link').addClass('d-inline');
                $('#edit_requirement_link_' + i).removeClass('d-inline');
                $('#edit_requirement_link_' + i).addClass('d-none');
                
                $('.edit_requirement_form').removeClass('d-inline');
                $('.edit_requirement_form').addClass('d-none');
                $('#edit_requirement_form_' + i).removeClass('d-none');
                $('#edit_requirement_form_' + i).addClass('d-inline');
                    
                    
                $('#requirement_input_' + i).attr('onmousedown', "javascript: $(this).removeAttr('id'); $(this).removeAttr('name');");
                $('#requirement_input_' + i).attr('onmouseup', "javascript: $(this).attr('id', 'requirement_input_" + i + "'); $(this).attr('name', 'requirement');");
                $('#requirement_input_' + i).attr('onkeydown', "javascript: if(event.which != 10 && event.which != 13) { $(this).removeAttr('id'); $(this).removeAttr('name'); }");
                $('#requirement_input_' + i).attr('onkeyup', "javascript: $(this).attr('id', 'requirement_input_" + i + "'); $(this).attr('name', 'requirement');");
                $('#requirement_input_' + i).attr('onfocusout', "javascript: $(this).attr('id', 'requirement_input_" + i + "'); $(this).attr('name', 'requirement');");
                
                $('#requirement_input_' + i).focus();
            }
            
            function SaveRequirement(id, i) {
                var val = $('#requirement_input_' + i).val();
                
                if(val === '') {
                    $('#requirement_input_' + i).focus();
                    return false;
                }
            
                $.ajax({ url: "_edit_requirement.php?id=" + id + "&i=" + i + "&value=" + val })
                        .done(function(data) {
                            if(data === '') {
                                $('#requirement_label_' + i).text('Ждем данные');
                            }
                            else {
                                $('#requirement_label_' + i).text(data);
                            }
                    
                            $('#edit_requirement_form_' + i).removeClass('d-inline');
                            $('#edit_requirement_form_' + i).addClass('d-none');
                            
                            $('#requirement_label_' + i).removeClass('d-none');
                            $('#requirement_label_' + i).addClass('d-inline');
                            
                            $('#edit_requirement_link_' + i).removeClass('d-none');
                            $('#edit_requirement_link_' + i).addClass('d-inline');
                            
                            $('#requirement_validation').removeClass('d-block');
                            $('#requirement_validation').addClass('d-none');
                        })
                        .fail(function() {
                            alert('Ошибка при редактировании требования по материалу');
                        });
            }
            
            function UploadImage(object, id, image) {
                $('#mini_image' + image + '_' + object + '_' + id).attr('src', '../images/loading-cargando.gif');
                $('#mini_image' + image + '_wrapper_' + object + '_' + id).removeClass('d-none');
                $('#mini_image' + image + '_wrapper_' + object + '_' + id).addClass('d-block');
                
                var formData = new FormData();
                formData.set('object', object);
                formData.set('id', id);
                formData.set('image', image);
                formData.set('file', $("#image" + image + "_" + object + "_" + id)[0].files[0]);
                
                $.ajax({
                    url: "_upload_image.php",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    processData: false, // Prevent jQuery from processing data
                    contentType: false, // Prevent jQuery from setting 
                    success: function(response) {
                        if(response.error.length > 0) {
                            alert(response.error);
                            $('#mini_image' + image + '_' + object + '_' + id).removeAttr('src');
                            $('#mini_image' + image + '_wrapper_' + object + '_' + id).removeClass('d-block');
                            $('#mini_image' + image + '_wrapper_' + object + '_' + id).addClass('d-none');
                        }
                        else {
                            if(response.filename.length > 0) {
                                $('#mini_button' + image + '_wrapper_' + object + '_' + id).removeClass('d-block');
                                $('#mini_button' + image + '_wrapper_' + object + '_' + id).addClass('d-none');
                                $('#mini_image' + image + '_link_' + object + '_' + id).attr('data-filename', response.filename + '?' + Date.now());
                                $('#mini_image' + image + '_' + object + '_' + id).attr('src', '../content/' + object + '/mini/' + response.filename + '?' + Date.now());
                            }
                            
                            if(response.info.length > 0) {
                                alert(response.info);
                            }
                            
                            if(response.to_plan_visible === true) {
                                if($('#plan_submit').hasClass('d-none')) {
                                    $('#plan_submit').removeClass('d-none');
                                }
                            }
                            else {
                                if(!$('#plan_submit').hasClass('d-none')) {
                                    $('#plan_submit').addClass('d-none');
                                }
                            }
                        }
                    },
                    error: function() {
                        alert('Ошибка при загрузке файла.');
                        $('#mini_image' + image + '_' + object + '_' + id).removeAttr('src');
                        $('#mini_image' + image + '_wrapper_' + object + '_' + id).removeClass('d-block');
                        $('#mini_image' + image + '_wrapper_' + object + '_' + id).addClass('d-none');
                    }
                });
            }
            
            <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
                
            // Переход между страницами редактирования форм тиражей
            $('.change_printing').click(function() {
                $('.set_printings').removeClass('d-block');
                $('.set_printings').addClass('d-none');
                $('.set_printings_' + $(this).attr('data-printing')).removeClass('d-none');
                $('.set_printings_' + $(this).attr('data-printing')).addClass('d-block');
            });
            
            // Обработка выбора формы (начальные значения)
            <?php
            if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE):
                $total_cliches_count = count($printings) * $calculation->ink_number;
                $valid_cliches_count = 0;
                    
                foreach ($cliches as $cliches_row) {
                    $valid_cliches_count += count($cliches_row);
                }    
            ?>
                total_cliches_count = <?=$total_cliches_count ?>;
                valid_cliches_count = <?=$valid_cliches_count ?>;
            <?php endif; ?>
                
            old_cliche = '';
                
            $('.select_cliche').mousedown(function() {
                old_cliche = $(this).val();
            });
            
            $('.select_cliche').focusin(function() {
                old_cliche = $(this).val();
            });
            
            // Обработка выбора формы
            $('.select_cliche').change(function() {
                if($(this).val() === '<?= CLICHE_REPEAT ?>') {
                    $(this).parent().removeClass('w-100');
                    $(this).parent().addClass('w-50');
                    $(this).parent().next().removeClass('d-none');
                }
                else {
                    $(this).parent().removeClass('w-50');
                    $(this).parent().addClass('w-100');
                    $(this).parent().next().addClass('d-none');
                }
                
                $.ajax({ dataType: 'JSON', url: '_edit_cliche.php?printing_id=' + $(this).attr('data-printing-id') + '&sequence=' + $(this).attr('data-sequence') + '&cliche=' + $(this).val() + '&machine_coeff=<?=$machine_coeff ?>&repeat_from=' + $('select#repeat_from_' + $(this).attr('data-printing-id') + '_' + $(this).attr('data-sequence')).val() })
                        .done(function(data) {
                            if(data.error !== '') {
                                alert(data.error);
                            }
                            else {
                                var cliche = '';
                                switch(data.cliche) {
                                    case '<?= CLICHE_FLINT ?>':
                                        cliche = 'Новая Flint ' + data.machine_coeff;
                                        break;
                                    case '<?= CLICHE_KODAK ?>':
                                        cliche = 'Новая Kodak ' + data.machine_coeff;
                                        break;
                                    case '<?= CLICHE_OLD ?>':
                                        cliche = 'Старая';
                                        break;
                                    case '<?= CLICHE_REPEAT ?>':
                                        cliche = 'Повт. исп. с тир. ' + $('select#repeat_from_' + data.printing_id + '_' + data.sequence).val();
                                        break;
                                    default :
                                        cliche = 'Ждем данные';
                                        break;
                                }
                                $('#cliche_' + data.printing_id + '_' + data.sequence).text(cliche);
                                
                                $('span.flint_used').text(data.flint_used);
                                $('span.kodak_used').text(data.kodak_used);
                                $('span.old_used').text(data.old_used);
                                
                                $('option.option_flint').removeAttr('hidden');
                                $('option.option_kodak').removeAttr('hidden');
                                $('option.option_old').removeAttr('hidden');
                                if(data.flint_used >= <?=$calculation->cliches_count_flint ?>) {
                                    $('option.option_flint').attr('hidden', 'hidden');
                                }
                                if(data.kodak_used >= <?=$calculation->cliches_count_kodak ?>) {
                                    $('option.option_kodak').attr('hidden', 'hidden');
                                }
                                if(data.old_used >= <?=$calculation->cliches_count_old ?>) {
                                    $('option.option_old').attr('hidden', 'hidden');
                                }
                                $('option#option_' + data.cliche + '_' + data.printing_id + '_' + data.sequence).removeAttr('hidden');
                                
                                // Если заполнены не все формы, то запрещаем печатать техкарту
                                <?php if($calculation->work_type_id == WORK_TYPE_SELF_ADHESIVE): ?>
                                    empty_old_cliche = old_cliche === '';
                                    empty_new_cliche = data.cliche === '';
                                    
                                    if(empty_old_cliche && !empty_new_cliche) {
                                        valid_cliches_count++;
                                    }
                                    else if(!empty_old_cliche && empty_new_cliche) {
                                        valid_cliches_count--;
                                    }
                                    
                                    if(valid_cliches_count < total_cliches_count) {
                                        $('.print_btn').removeClass('d-block');
                                        $('.print_btn').addClass('d-none');
                                        
                                        $('.no_print_btn').removeClass('d-none');
                                        $('.no_print_btn').addClass('d-block');
                                    }
                                    else {
                                        $('.no_print_btn').removeClass('d-block');
                                        $('.no_print_btn').addClass('d-none');
                                        
                                        $('.print_btn').removeClass('d-none');
                                        $('.print_btn').addClass('d-block');
                                    }
                                <?php endif; ?>
                            }
                        })
                        .fail(function() {
                            alert("Ошибка при выборе формы");
                        });
            });
            
            // Обработка выбора, с какого тиража повторное использование
            $('.repeat_from').change(function() {
                $.ajax({ dataType: 'JSON', url: '_edit_repeat.php?printing_id=' + $(this).attr('data-printing-id') + '&sequence=' + $(this).attr('data-sequence') + '&repeat_from=' + $(this).val() })
                        .done(function(data) {
                            if(data.error !== '') {
                                alert(data.error);
                            }
                            else {
                                cliche = 'Повт. исп. с тир. ' + data.repeat_from;
                                $('#cliche_' + data.printing_id + '_' + data.sequence).text(cliche);
                            }
                        })
                        .fail(function() {
                            alert('Ошибка при изменении с какого тиража повторное использование');
                        });
            });
            
            <?php endif; ?>
        </script>
    </body>
</html>