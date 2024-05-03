<script>
    function CutCalculate(input) {
        weight = input.val();
        input.parent().parent().next().val(weight);
        input.parent().parent().next().next().find('input[type=text]').val(weight);
    }
</script>