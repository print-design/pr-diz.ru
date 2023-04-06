<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'administrator', 'manager-senior'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            .wrapper {
                display: flex;
                width: 100%;
                align-items: stretch;
            }
            
            #sidebar {
                position: relative;
                min-width: 382px;
                max-width: 382px;
                transition: all 0.3s;
            }
            
            #sidebar.active {
                margin-left: -362px;
            }
            
            #sidebar_toggle_button {
                position: absolute;
                top: 0px;
                right: 0px;
            }
            
            @media (max-width: 768px) {
                #sidebar {
                    margin-left: -362px;
                }
                #sidebar.active {
                    margin-left: 0;
                }
                #sidebarCollapse span {
                    display: none;
                }
            }
        </style>
    </head>
    <body>
        <?php
        include '../include/header_plan.php';
        ?>
        <div class="container-fluid">
            <?php
            include '../include/subheader_print.php';
            
            if(!empty($error_message)) {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <h1><?=$print_header ?></h1>
            <div class="wrapper">
                <nav id="sidebar">
                    <div id="sidebar_toggle_button">
                        <button type="button" id="sidebarCollapse" class="btn btn-link"><img src="../images/icons/collapse.png" style="margin-right: 8px;" />Скрыть<img src="../images/icons/expand.png" style="margin-left: 8px; display: none;" id="expand_arrow" /></button>
                    </div>
                    <h2>Очередь</h2>
                    <?php
                    $sql = "select";
                    ?>
                </nav>
                <div id="content">
                    <h2>План</h2>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $(document).ready(function () {
                $('#sidebarCollapse').on('click', function () {
                    $('#sidebar').toggleClass('active');
                    $('#expand_arrow').toggle();
                });
            });
        </script>
    </body>
</html>