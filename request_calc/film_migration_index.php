<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager', 'administrator'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header_sklad.php';
        ?>
        <div class="container-fluid">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Марка</th>
                        <th>Марка старая</th>
                        <th>Толщина</th>
                        <th>Толщина старая</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select c.id, f.name film, c.brand_name film_old, fv.thickness, c.thickness thickness_old "
                            . "from calculation c "
                            . "left join film_variation fv on c.film_variation_id = fv.id "
                            . "left join film f on fv.film_id = f.id";
                    $fetcher = new Fetcher($sql);
                    while ($row = $fetcher->Fetch()):
                    ?>
                    <tr>
                        <td><?=$row['film'] ?></td>
                        <td><?=$row['film_old'] ?></td>
                        <td><?=$row['thickness'] ?></td>
                        <td><?=$row['thickness_old'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>