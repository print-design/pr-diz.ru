<?php
if(!IsInRole('accountant')) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>