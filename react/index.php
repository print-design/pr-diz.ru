<?php
include '../include/topscripts.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <div class="container-fluid">
            <h1>Здесь будет React</h1>
            <div id="like_button_container"></div>
        </div>
        <!-- Загрузим React. -->
        <!-- Примечание: при деплое на продакшен замените «development.js» на «production.min.js». -->
        <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
        <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
        
        <!-- Загрузим наш React-компонент. -->
        <script src="like_button.js"></script>
    </body>
    <?php
    include '../include/footer.php';
    ?>
</html>