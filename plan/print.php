<?php
include '../include/topscripts.php';
include '../calculation/status_ids.php';

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
                    $sql = "select c.id, c.name calculation, cus.name customer, cr.length_dirty_1, c.ink_number, c.raport, "
                            . "c.lamination1_film_variation_id, c.lamination1_individual_film_name, "
                            . "c.lamination2_film_variation_id, c.lamination2_individual_film_name, "
                            . "u.first_name, u.last_name "
                            . "from calculation c "
                            . "inner join customer cus on c.customer_id = cus.id "
                            . "inner join calculation_result cr on cr.calculation_id = c.id "
                            . "inner join user u on c.manager_id = u.id "
                            . "where c.status_id = ".CONFIRMED." order by id desc";
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
                        <h3><a href='../calculation/details.php?id=<?=$row['id'] ?>'><?=$row['calculation'] ?></a></h3>
                        <?=$row['customer'] ?>
                        <hr />
                        <div class="row">
                            <div class="col-6"><strong>Метраж:</strong> <?=number_format($row['length_dirty_1'], 0, ",", " "); ?></div>
                            <div class="col-6"><strong>Красочность:</strong> <?=$row['ink_number'] ?></div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>Ламинации:</strong> <?=$laminations_number ?></div>
                            <div class="col-6"><strong>Вал:</strong> <?=$row['raport'] ?></div>
                        </div>
                        <p><strong>Менеджер:</strong> <?=$row['last_name'] ?> <?= mb_substr($row['first_name'], 0, 1)  ?>.</p>
                    </div>
                    <?php endwhile; ?>
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