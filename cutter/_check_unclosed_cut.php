<script>
    <?php
    // Проверка, имеются ли нарезки, у которых нет исходного ролика
    $user_id = GetUserId();
    $sql = "select id from cut where cutter_id = $user_id and id not in (select cut_id from cut_source)";
    $fetcher = new Fetcher($sql);
    if($row = $fetcher->Fetch()) {
        ?>
            OpenAjaxPage('_close.php');
        <?php
    }
    ?>
</script>