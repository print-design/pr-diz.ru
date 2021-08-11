<script>
    <?php
    // Проверка, все ли нарезки закрыты (то есть, имеют исходные ролики
    // Если все закрыты, то перекидываем на первую страницу
    $user_id = GetUserId();
    $sql = "select count(id) from cut where cutter_id = $user_id and id not in (select cut_id from cut_source)";
    $fetcher = new Fetcher($sql);
    $row = $fetcher->Fetch();
    $count = intval($row[0]);
    
    if($count == 0) {
        ?>
            OpenAjaxPage('_index.php');
        <?php
    }
    ?>
</script>