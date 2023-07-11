<?php
if(!LoggedIn()) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>