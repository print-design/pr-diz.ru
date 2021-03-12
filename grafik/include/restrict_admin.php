<?php
if(!IsInRole('admin')) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>