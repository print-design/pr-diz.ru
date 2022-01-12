<?php
function CheckCuts($user_id) {
    $php_self = filter_input(INPUT_SERVER, 'PHP_SELF');
    
    $sql = "select count(id) from cut where cutter_id = $user_id and id not in (select cut_id from cut_source)";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    
    if($row[0] == 0) {
        if(mb_substr_count($php_self, 'next.php') > 0 || mb_substr_count($php_self, 'print.php') > 0 || mb_substr_count($php_self, 'close.php') > 0) {
            header("Location: ".APPLICATION."/cutter/");
        }
    }
    else {
        if(mb_substr_count($php_self, 'next.php') == 0 && mb_substr_count($php_self, 'print.php') == 0 && mb_substr_count($php_self, 'close.php') == 0) {
            header("Location: print.php");
        }
    }
}
?>