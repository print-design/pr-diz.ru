<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array(ROLE_NAMES[ROLE_TECHNOLOGIST], ROLE_NAMES[ROLE_STOREKEEPER], ROLE_NAMES[ROLE_MANAGER], ROLE_NAMES[ROLE_MANAGER_SENIOR]))) {
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
            <div class="row">
                <div class="col-6">
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
                        $sql = "select c.id, f.name film, fb.name film_old, fv.thickness, c.thickness thickness_old "
                                . "from cut c "
                                . "left join film_variation fv on c.film_variation_id = fv.id "
                                . "left join film f on fv.film_id = f.id "
                                . "left join film_brand fb on c.film_brand_id = fb.id";
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
                <div class="col-6">
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
                            $sql = "select c.id, f.name film, fb.name film_old, fv.thickness, c.thickness thickness_old "
                                    . "from cutting c "
                                    . "left join film_variation fv on c.film_variation_id = fv.id "
                                    . "left join film f on fv.film_id = f.id "
                                    . "left join film_brand fb on c.film_brand_id = fb.id";
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
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>