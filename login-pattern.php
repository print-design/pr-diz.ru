<?php
include 'include/topscripts.php';

// Если залогинен, перенаправляем на основную страницу
if(LoggedIn()) {
    header('Location: '.APPLICATION.'/');
}

// Карщика и ревизора перенаправляем в раздел car
if(IsInRole(array(ROLE_NAMES[ROLE_ELECTROCARIST], ROLE_NAMES[ROLE_AUDITOR]))) {
    header('Location: '.APPLICATION.'/car/');
}

// Резчика по раскрою перенаправляем в раздел cut
if(IsInRole(ROLE_NAMES[ROLE_CUTTER])) {
    header('Location: '.APPLICATION.'/cutter/');
}

// Маркиратора перенаправляем в раздел marker
if(IsInRole(ROLE_NAMES[ROLE_MARKER])) {
    header('Location: '.APPLICATION.'/marker/');
}
?>
<!DOCTYPE html>
<html lang="ru">
    <body>
        <h1>Login pattern</h1>
    </body>
</html>