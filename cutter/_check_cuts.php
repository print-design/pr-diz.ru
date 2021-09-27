<?php
function CheckCuts($user_id) {
    $php_self = filter_input(INPUT_SERVER, 'PHP_SELF');
    
    // Проверяем, имеются ли незакрытые нарезки
    $sql = "select count(id) from cut where cutter_id = $user_id and id not in (select cut_id from cut_source)";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    
    if($row[0] == 0) {
        $cut_id = null;
        $sql = "select id, remain from cut where cutter_id = $user_id and id in (select cut_id from cut_source) order by id desc limit 1";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $cut_id = $row[0];
            $remain = $row['remain'];
        
            // Если незакрытых нарезок нет, то 
            // если страница не "Остаточный ролик"
            // берём последнюю закрытую нарезку и проверяем есть ли остаточный ролик и равна ли сумма длин исходных роликов сумме длин намоток (+-300 м)
            // и если их разница больше 300 м и нет остаточного ролика, перекидываем на страницу "остаточный ролик"
            if(mb_substr_count($php_self, '/remain.php') == 0) { // remain.php, но не print_remain.php
                if(empty($remain)) {
                    $sum_source = 0;
                    $sql = "select "
                            . "ifnull((select sum(r.length) "
                            . "from cut_source cs "
                            . "inner join roll r on cs.roll_id = r.id "
                            . "where cs.cut_id = $cut_id "
                            . "and cs.is_from_pallet = 0), 0) "
                            . "+ "
                            . "ifnull((select sum(pr.length) "
                            . "from cut_source cs "
                            . "inner join pallet_roll pr on cs.roll_id = pr.id "
                            . "where cs.cut_id = $cut_id "
                            . "and cs.is_from_pallet = 1), 0) "
                            . "sum_source";
                    $fetcher = new Fetcher($sql);
                    if($row = $fetcher->Fetch()) {
                        $sum_source = $row['sum_source'];
                    }
        
                    $sum_wind = 0;
                    $sql = "select ifnull((select sum(length) from cut_wind where cut_id = $cut_id), 0) sum_length";
                    $fetcher = new Fetcher($sql);
                    if($row = $fetcher->Fetch()) {
                        $sum_wind = $row['sum_length'];
                    }
        
                    $sum_diff = intval($sum_source) - intval($sum_wind);
                    if($sum_diff > 300) {
                        header("Location: ".APPLICATION."/cutter/remain.php");
                    }
                }
            }
        }
        else {
            // Если страница "Остаточный ролик", а остаточный ролик есть, то уходим с неё
            if(!empty($remain)) {
                header("Location: ".APPLICATION."/cutter/");
            }
        }

        // Если незакрытых нарезок нет, то нечего делать на страницах "Намотка №", "Печать замотки" и "Закрытие нарезки"
        if(mb_substr_count($php_self, 'next.php') > 0 || mb_substr_count($php_self, 'print.php') > 0 || mb_substr_count($php_self, 'close.php') > 0) {
            header("Location: ".APPLICATION."/cutter/");
        }
    }
    else {
        // Если незакрытая нарезка есть, то перекидываем на страницу "Печать" последней намотки этой нарезки
        if(mb_substr_count($php_self, 'next.php') == 0 && mb_substr_count($php_self, 'print.php') == 0 && mb_substr_count($php_self, 'close.php') == 0) {
            header("Location: print.php");
        }
    }
}
?>