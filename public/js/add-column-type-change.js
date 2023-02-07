$("#column_type").on('change', function(){
    var column_type = $('#column_type').find(":selected").val();
    if (column_type == 'enum') {
        $(".cloned_input").remove();
        $(".possible").css("display", "block");
        $(".clone_one").find("input:last").clone().attr('name', 'possible_values[]').addClass("cloned_input").insertAfter(".clone_one");
    } else if (column_type == 'decimal' || column_type == 'double' || column_type == 'float') {
        $(".cloned_input").remove();
        $(".decimal_div").css("display", "block");
        $(".clone_two").find("input:last").clone().attr('name', 'decimal_precision').addClass("cloned_input").attr('placeholder', 'Decimal precision').insertAfter(".clone_two");
        $(".clone_two").find("input:last").clone().attr('name', 'decimal_total_number').addClass("cloned_input").attr('placeholder', 'Total number').insertAfter(".clone_two");
    } else if (column_type == 'string') {
        $(".cloned_input").remove();
        $(".string_div").css("display", "block");
        $(".clone_three").find("input:last").clone().attr('name', 'character_limit').addClass("cloned_input").attr('placeholder', 'Character limit').insertAfter(".clone_three");
    } else {
        $(".possible").css("display", "none");
        $(".cloned_input").remove();
    }
});