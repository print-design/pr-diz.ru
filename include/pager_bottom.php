<?php
$pager_pages_count = ceil($pager_total_count / $pager_take);
$pager_get_params = filter_input_array(INPUT_GET);

if($pager_pages_count > 1) {
?>
<nav aria-label="Page navigation example">
    <ul class="pagination">
        <?php
        if($pager_page > 1) {
            $pager_get_params[PAGE] = $pager_page - 1;
        ?>
        <li class="page-item">
            <a class="page-link" href="?<?= http_build_query($pager_get_params) ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>
        <?php
        }
        for($i = 1; $i <= $pager_pages_count; $i++) {
            $pager_get_params[PAGE] = $i;
            $disabled = "";
            if($i == $pager_page) $disabled = " disabled";
            echo "<li class='page-item".$disabled."'><a class='page-link' href='?".http_build_query($pager_get_params)."'>".$i."</a></li>";
        }
        if($pager_page < $pager_pages_count) {
            $pager_get_params[PAGE] = $pager_page + 1;
        ?>
        <li class="page-item">
            <a class="page-link" href="?<?= http_build_query($pager_get_params) ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
        <?php
        }
        ?>
    </ul>
</nav>
<?php
}
?>