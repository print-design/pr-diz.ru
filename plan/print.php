<?php
include '../include/topscripts.php';
include './_queue.php';
include './_plan_timetable.php';

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
            
            /* Таблица */
            table.typography {
                border-radius: 15px;
                box-shadow: 0px 0px 40px rgb(0 0 0 / 15%);
                padding: 15px;
                color: #191919;
            }

            table.typography tr th {
                color: #68676C;
                border-top: 0;
            }

            table.typography tbody tr td {
                background-color: white;
            }

            table.print tr td.top {
                border-top: solid 2px darkgray;
            }

            table.typography tbody tr td.night {
                background-color: #F0F1FA;
            }

            thead#grafik-thead {
                background-color: lightcyan;
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
            <div class="wrapper" style="position: absolute; top: 170px; bottom: 0; left: 0; right: 0; padding-left: 75px;">
                <nav id="sidebar">
                    <div id="sidebar_toggle_button">
                        <button type="button" id="sidebarCollapse" class="btn btn-link"><img src="../images/icons/collapse.png" style="margin-right: 8px;" />Скрыть</button>
                    </div>
                    <h2>Очередь</h2>
                    <div id="queue" style="overflow: auto; position: absolute; top: 40px; bottom: 0; left: 0; right: 15px;">
                        <?php
                        $queue = new Queue($machine);
                        $queue->Show();
                        ?>
                    </div>
                </nav>
                <div id="content" style="width: 100%; position: relative;">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex justify-content-start">
                            <button type="button" id="sidebarExpand" class="btn btn-link" style="display: none; padding-left: 0;">
                                <img src="../images/icons/expand.png" style="margin-right: 8px;" />
                            </button>
                            <h2>План</h2>
                        </div>
                        <div class="d-flex justify-content-end">
                            <form class="form-inline" method="get">
                                <label for="from" style="font-size: larger;">от&nbsp;</label>
                                <input type="hidden" name="id" value="<?= filter_input(INPUT_GET, 'id') ?>" />
                                <input type="date" 
                                       id="from" name="from" 
                                       class="form-control mr-2" 
                                       value="<?= filter_input(INPUT_GET, 'from') ?>" 
                                       style="border: 0; width: 8.5rem;" 
                                       onchange="javascript: this.form.submit();" />
                                
                            </form>
                            <?php if(!empty(filter_input(INPUT_GET, 'from'))): ?>
                            <a href="<?= BuildQueryRemove('from') ?>" class="btn btn-light">Сбросить</a>
                            <?php endif; ?>
                            <button type="button" class="btn btn-light"><i class="fas fa-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Добавить событие</button>
                        </div>
                    </div>
                    <div id="timetable" style="overflow: auto; position: absolute; top: 40px; bottom: 0; left: 0; right: 0; padding: 5px;">
                        <?php
                        $date_from = null;
                        $date_to = null;
                        GetDateFromDateTo(filter_input(INPUT_GET, 'from'), null, $date_from, $date_to);
                        
                        $timetable = new PlanTimetable(filter_input(INPUT_GET, 'id'), $date_from, $date_to);
                        $timetable->Show();
                        ?>
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
                
                $('.select_employee1').change(function(e) {
                    $(e.target).val('');
                });
            });
        </script>
    </body>
</html>