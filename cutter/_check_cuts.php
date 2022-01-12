<?php
function CheckCuts($user_id) {
    if(!($_SERVER['PHP_SELF'] == APPLICATION.'/cutter/material.php' && !empty(filter_input(INPUT_GET, 'cutting_id')))) {
        $id = null;
        $is_from_pallet = null;
        $roll_id = null;
        
        $sql = "select c.id, cs.is_from_pallet, cs.roll_id "
                . "from cutting c "
                . "left join cutting_source cs on cs.cutting_id = c.id "
                . "where c.date is null and c.cutter_id = $user_id "
                . "order by cs.id desc";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $id = $row['id'];
            $is_from_pallet = $row['is_from_pallet'];
            $roll_id = $row['roll_id'];
        }
        
        // Если есть незакрытая заявка и нет ни одного исходного ролика, переводим на страницу создания исходного ролика
        if(!empty($id)) {
            if((empty($is_from_pallet) || empty($roll_id)) && 
                    $_SERVER['PHP_SELF'] != APPLICATION.'/cutter/source.php' && 
                    $_SERVER['PHP_SELF'] != APPLICATION.'/cutter/logout.php') {
                header("Location: ".APPLICATION.'/cutter/source.php?cutting_id='.$id);
            }
        }
    }
}
?>