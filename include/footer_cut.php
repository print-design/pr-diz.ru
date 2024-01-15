
<script>
    function ShowTakeTable(id) {
        $('a.show_table[data-id=' + id + ']').addClass('d-none');
        $('a.hide_table[data-id=' + id + ']').removeClass('d-none');
        $('table.take_table[data-id=' + id + ']').removeClass('d-none');
    }
    
    function HideTakeTable(id) {
        $('a.hide_table[data-id=' + id + ']').addClass('d-none');
        $('a.show_table[data-id=' + id + ']').removeClass('d-none');
        $('table.take_table[data-id=' + id + ']').addClass('d-none');
    }
    
    <?php if(IsInRole(CUTTER_USERS)): ?>
    function ShowCutterName() {
        $('span#top_user_name').load("<?=APPLICATION ?>/cut/_get_user.php");
    }
    
    ShowCutterName();
    setInterval(ShowCutterName, 60000);
    <?php endif; ?>
</script>