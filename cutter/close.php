<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'cutter'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение cut_id, возвращаемся на первую страницу
$cut_id = $_REQUEST['cut_id'];
if(empty($cut_id)) {
    header('Location: '.APPLICATION.'/cutter/');
}

// Статус "СВОБОДНЫЙ"
$free_status_id = 1;

// Статус "РАСКРОИЛИ"
$cut_status_id = 3;

// Список исходных роликов
$cut_sources = array();

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$sources_count_valid = '';
for($i=1; $i<=19; $i++) {
    $source_valid = 'source_'.$i.'_valid';
    $$source_valid = '';
    
    $source_message = 'source_'.$i.'_message';
    $$source_message = 'ID исходного ролика обязательно';
}

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'close-submit')) {
    $sources_count = filter_input(INPUT_POST, 'sources_count');
    if(empty($sources_count)) {
        $sources_count_valid = ISINVALID;
        $form_valid = false;
    }
    
    // Получаем параметры раскроя
    $sql = " select fb.name film_brand, c.thickness, c.width "
            . "from cut c inner join film_brand fb on c.film_brand_id = fb.id "
            . "where c.id=$cut_id";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    $film_brand = $row['film_brand'];
    $thickness = $row['thickness'];
    $width = $row['width'];
    
    for($i=1; $i<=$sources_count; $i++) {
        $source = filter_input(INPUT_POST, 'source_'.$i);
        $source_valid = 'source_'.$i.'_valid';
        $source_message = 'source_'.$i.'_message';
        
        if(empty($source)) {
            $$source_valid = ISINVALID;
            $$source_message = 'ID исходного ролика обязательно';
            $form_valid = false;
        }
        
        // Проверяем, чтобы номер рулона соответствовал реальному рулону и имел такие же параметры
        
        if(mb_substr($source, 0, 1) == "р" || mb_substr($source, 0, 1) == "Р") {
            // Ищем такой среди свободных роликов
            $roll_id = mb_substr($source, 1);
            
            $sql = "select fb.name film_brand, r.thickness, r.width "
                    . "from roll r "
                    . "inner join film_brand fb on r.film_brand_id = fb.id "
                    . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                    . "where r.id = $roll_id and (rsh.status_id is null or rsh.status_id = $free_status_id)";
            $fetcher = new Fetcher($sql);
            
            if($row = $fetcher->Fetch()) {
                if($row['film_brand'] == $film_brand && $row['thickness'] == $thickness && $row['width'] == $width) {
                    $cut_source = array();
                    $cut_source['cut_id'] = $cut_id;
                    $cut_source['is_from_pallet'] = 0;
                    $cut_source['roll_id'] = $roll_id;
                    array_push($cut_sources, $cut_source);
                }
                else {
                    $$source_valid = ISINVALID;
                    $$source_message = "Марка/толщина/ширина не совпадают";
                }
            }
            else {
                $$source_valid = ISINVALID;
                $$source_message = "Нет ролика с таким номером";
            }
        }
        elseif(mb_substr($source, 0, 1) == "п" || mb_substr ($source, 0, 1) == "П") {
            // Ищем среди роликов в паллетах
            $pallet_trim = mb_substr($source, 1);
            $substrings = mb_split("\D", $pallet_trim);
            
            if(count($substrings) == 2) {
                $pallet_id = $substrings[0];
                $ordinal = $substrings[1];
                
                $sql = "select pr.id roll_id, fb.name film_brand, p.thickness, p.width "
                        . "from pallet p "
                        . "inner join pallet_roll pr on pr.pallet_id = p.id "
                        . "inner join film_brand fb on p.film_brand_id = fb.id "
                        . "left join (select * from pallet_roll_status_history where id in (select max(id) from pallet_roll_status_history group by pallet_roll_id)) prsh on prsh.pallet_roll_id = pr.id "
                        . "where p.id = $pallet_id and pr.ordinal = $ordinal and (prsh.status_id is null or prsh.status_id = $free_status_id)";
                $fetcher = new Fetcher($sql);
                
                if($row = $fetcher->Fetch()) {
                    if($row['film_brand'] == $film_brand && $row['thickness'] == $thickness && $row['width'] == $width) {
                        $cut_source = array();
                        $cut_source['cut_id'] = $cut_id;
                        $cut_source['is_from_pallet'] = 1;
                        $cut_source['roll_id'] = $row['roll_id'];
                        array_push($cut_sources, $cut_source);
                    }
                    else {
                        $$source_valid = ISINVALID;
                        $$source_message = "Марка/толщина/ширина не совпадают";
                    }
                }
                else {
                    $$source_valid = ISINVALID;
                    $$source_message = "Нет ролика с таким номером";
                }
            }
            else {
                $$source_valid = ISINVALID;
                $$source_message = "Нет ролика с таким номером";
            }
        }
        else {
            $$source_valid = ISINVALID;
            $$source_message = "Нет ролика с таким номером";
        }
    }

    if($form_valid) {
        foreach ($cut_sources as $cut_source) {
            $user_id = GetUserId();
            $cut_id = $cut_source['cut_id'];
            $is_from_pallet = $cut_source['is_from_pallet'];
            $roll_id = $cut_source['roll_id'];
            $sql = "insert into cut_source (cut_id, is_from_pallet, roll_id) values ($cut_id, $is_from_pallet, $roll_id)";
            $executer = new Executer($sql);
            $error_message = $executer->error;
            
            if(empty($error_message)) {
                if($is_from_pallet == 0) {
                    $sql = "insert into roll_status_history (roll_id, status_id, user_id) values($roll_id, $cut_status_id, $user_id)";
                    $executer = new Executer($sql);
                    $error_message = $executer->error;
                }
                else {
                    $sql = "insert into pallet_roll_status_history (pallet_roll_id, status_id, user_id) values($roll_id, $cut_status_id, $user_id)";
                    $executer = new Executer($sql);
                    $error_message = $executer->error;
                }
                
                if(empty($error_message)) {
                    header('Location: '.APPLICATION.'/cutter/remain.php?cut_id='.$cut_id);
                }
            }
        }
    }
}

// Получение объекта
$date = '';
$sql = "select DATE_FORMAT(c.date, '%d.%m.%Y') date from cut c where c.id=$cut_id";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $date = $row['date'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include '../include/style_mobile.php';
        ?>
    </head>
    <body>
        <div class="container-fluid header">
            <nav class="navbar navbar-expand-sm justify-content-start">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?=APPLICATION ?>/cutter/next.php?cut_id=<?=$cut_id ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    </li>
                </ul>
            </nav>
        </div>
        <div id="topmost"></div>
        <div class="container-fluid">
            <h1>Нарезка <?=$cut_id ?> / <?=$date ?></h1>
            <p class="mb-3 mt-3" style="font-size: large;">Введите ID исходных ролей</p>
            <form method="post" id="sources_form">
                <div class="form-group" id="count-group">
                    <label for="sources_count">Кол-во исходных ролей</label>
                    <input type="text" id="sources_count" name="sources_count" class="form-control w-50 int-only" value="<?= filter_input(INPUT_POST, 'sources_count') ?>" required="required" autocomplete="off" />
                    <div class="invalid-feedback">Число, макс. 19</div>
                </div>
                <?php
                for($i=1; $i<=19; $i++):
                $source_valid_name = 'source_'.$i.'_valid';
                $source_group_display_class = ' d-none';
                $source_message = 'source_'.$i.'_message';
                
                $sources_count = filter_input(INPUT_POST, 'sources_count');
                
                if(null !== $sources_count && intval($sources_count) >= intval($i)) {
                    $source_group_display_class = '';
                }
                ?>
                <div class="form-group source_group<?=$source_group_display_class ?>" id="source_<?=$i ?>_group">
                    <label for="source_<?=$i ?>">ID <?=$i ?>-го исходного роля</label>
                    <input type="text" id="source_<?=$i ?>" name="source_<?=$i ?>" class="form-control no-latin<?=$$source_valid_name ?>" value="<?= filter_input(INPUT_POST, 'source_'.$i) ?>" autocomplete="off" />
                    <div class="invalid-feedback"><?=$$source_message ?></div>
                </div>
                <?php endfor; ?>
                <div class="form-group">
                    <button type="submit" class="btn btn-dark form-control mt-4" id="close-submit" name="close-submit">Закрыть заявку</button>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        include '../include/footer_mobile.php';
        ?>
        <script>
            // В поле "Кол-во исходных ролей" ограничиваем значения: целые числа от 1 до 19
            $('#sources_count').keydown(function(e) {
                if(!KeyDownLimitIntValue($(e.target), e, 19)) {
                    $(this).addClass('is-invalid');
                    
                    return false;
                }
                else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            $('#sources_count').keyup(function() {
                SetSources($(this).val());
            });
            
            $('#sources_count').change(function() {
                if($(this).val() > 19) {
                    $(this).addClass('is-invalid');
                }
                else {
                    $(this).removeClass('is-invalid');
                }
                
                ChangeLimitIntValue($(this), 19);
                SetSources($(this).val());
            });
            
            // Показ каждого источника
            function SetSources(sources_count) {
                $('.source_group').addClass('d-none');
                $('.source_group input').removeAttr('required');
                
                if(sources_count != '') {
                    iSourcesCount = parseInt(sources_count);
                    if(!isNaN(iSourcesCount)) {
                        for(i=1; i<=iSourcesCount; i++) {
                            $('#source_' + i + '_group').removeClass('d-none');
                            $('#source_' + i + '_group input').attr('required', 'required');
                        }
                    }
                }
            }
        </script>
    </body>
</html>