<?php
function CheckCuts($user_id) {
    if(!($_SERVER['PHP_SELF'] == APPLICATION.'/cutter/material.php' && !empty(filter_input(INPUT_GET, 'cutting_id')))) {
        $id = null;
        $source_is_from_pallet = null;
        $source_id = null;
        
        $sql = "select id, source_is_from_pallet, source_id from cutting where date is null and cutter_id = $user_id";
        $fetcher = new Fetcher($sql);
        if($row = $fetcher->Fetch()) {
            $id = $row['id'];
            $source_is_from_pallet = $row['source_is_from_pallet'];
            $source_id = $row['source_id'];
        }
        
        // Если есть незакрытая заявка и нет ни одного исходного ролика, переводим на страницу создания исходного ролика
        if(!empty($id)) {
            if((empty($source_is_from_pallet) || empty($source_id)) && 
                    $_SERVER['PHP_SELF'] != APPLICATION.'/cutter/source.php' && 
                    $_SERVER['PHP_SELF'] != APPLICATION.'/cutter/logout.php') {
                header("Location: ".APPLICATION.'/cutter/source.php?cutting_id='.$id);
            }
        }
    }
}
?>