<?php
include '../include/topscripts.php';
include './_queue.php';

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
                min-width: 397px;
                max-width: 397px;
                padding-right: 15px;
                transition: all 0.3s;
            }
            
            #sidebar.active {
                margin-left: -397px;
            }
            
            #sidebar_toggle_button {
                position: absolute;
                top: 0px;
                right: 3px;
            }
            
            @media (max-width: 768px) {
                #sidebar {
                    margin-left: -397px;
                }
                #sidebar.active {
                    margin-left: 0;
                }
                #sidebarCollapse span {
                    display: none;
                }
            }
            
            .queue_item {
                border-radius: 15px;
                box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                padding: 15px;
                margin: 5px 5px 8px 5px;
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
            <div class="wrapper" style="position: fixed; top: 170px; bottom: 0px;">
                <nav id="sidebar">
                    <div id="sidebar_toggle_button">
                        <button type="button" id="sidebarCollapse" class="btn btn-link"><img src="../images/icons/collapse.png" style="margin-right: 8px;" />Скрыть</button>
                    </div>
                    <h2>Очередь</h2>
                    <div id="queue" style="height: 90%; overflow: auto;">
                        <?php
                        $queue = new Queue($machine);
                        $queue->Show();
                        ?>
                    </div>
                </nav>
                <div id="content">
                    <div class="d-flex justify-content-start">
                        <button type="button" id="sidebarExpand" class="btn btn-link" style="display: none; padding-left: 0;">
                            <img src="../images/icons/expand.png" style="margin-right: 8px;" />
                        </button>
                        <h2>План</h2>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $(document).ready(function () {
                $('#sidebarCollapse').on('click', function () {
                    $('#sidebar').addClass('active');
                    $('#sidebarExpand').show();
                });
                
                $('#sidebarExpand').on('click', function() {
                    $('#sidebar').removeClass('active');
                    $('#sidebarExpand').hide();
                });
            });
        </script>
    </body>
</html>