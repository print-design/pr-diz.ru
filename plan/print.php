<?php
include '../include/topscripts.php';
include '../calculation/status_ids.php';
include '../calculation/calculation.php';

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
                    <div style="height: 90%; overflow: auto;">
                        <?php
                        $sql = "select c.id, c.name calculation, cus.name customer, cr.length_dirty_1, c.ink_number, c.raport, "
                                . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                                . "c.lamination2_film_variation_id, c.lamination2_individual_film_name, "
                                . "u.first_name, u.last_name "
                                . "from calculation c "
                                . "inner join customer cus on c.customer_id = cus.id "
                                . "inner join calculation_result cr on cr.calculation_id = c.id "
                                . "inner join user u on c.manager_id = u.id "
                                . "where c.status_id = ".CONFIRMED." ";
                        if($machine == CalculationBase::ATLAS) {
                            $sql .= "and false ";
                        }
                        else {
                            $sql .= "and c.work_type_id = ".CalculationBase::WORK_TYPE_PRINT." ";
                        }
                        $sql .= "order by id desc";
                        $fetcher = new Fetcher($sql);
                    
                        while($row = $fetcher->Fetch()):
                        $laminations_number = 0;
                        if(!empty($row['lamination2_film_variation_id']) || !empty($row['lamination2_individual_film_name'])) {
                            $laminations_number = 2;
                        }
                        elseif(!empty($row['lamination1_film_variation_id']) || !empty($row['lamination1_individual_film_name'])) {
                            $laminations_number = 1;
                        }
                        ?>
                        <div class='queue_item'>
                            <div class="d-flex justify-content-between" style="border-bottom: solid 1px #E7E6ED; margin-bottom: 5px; padding-bottom: 5px;">
                                <div class="d-flex justify-content-start">
                                    <div style="padding-top: 10px; padding-right: 10px;"><img src="../images/icons/double-vertical-dots.svg" /></div>
                                    <div>
                                        <div style="font-weight: bold; font-size: large;"><a href='../calculation/details.php?id=<?=$row['id'] ?>'><?=$row['calculation'] ?></a></div>
                                        <?=$row['customer'] ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="d-flex justify-content-end" style="padding-top: 10px;">
                                        <div><img src="../images/icons/vertical-dots1.svg" /></div>
                                        <div style="padding-left: 10px;"><img src="../images/icons/right-arrow.svg" /></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6"><strong>Метраж:</strong> <?=number_format($row['length_dirty_1'], 0, ",", " "); ?></div>
                                <div class="col-6"><strong>Красочность:</strong> <?=$row['ink_number'] ?></div>
                            </div>
                            <div class="row">
                                <div class="col-6"><strong>Ламинации:</strong> <?=$laminations_number ?></div>
                                <div class="col-6"><strong>Вал:</strong> <?=$row['raport'] ?></div>
                            </div>
                            <div style="margin-top: 10px;"><strong>Менеджер:</strong> <?=$row['last_name'] ?> <?= mb_substr($row['first_name'], 0, 1)  ?>.</div>
                        </div>
                        <?php endwhile; ?>
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