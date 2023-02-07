$("#edit_column_type").on('change', function(){
    var edit_column_type = $('#edit_column_type').find(":selected").val();
    if (edit_column_type == 'enum') {
        $(".cloned_input").remove();
        $(".edit_possible").css("display", "block");
        $(".edit_clone_one").find("input:last").clone().attr('name', 'possible_values[]').addClass("cloned_input").insertAfter(".edit_clone_one");
    } else if (edit_column_type == 'decimal' || column_type == 'double' || column_type == 'float') {
        $(".cloned_input").remove();
        $(".edit_decimal_div").css("display", "block");
        $(".edit_clone_two").find("input:last").clone().attr('name', 'edit_decimal_precision[]').addClass("cloned_input").attr('placeholder', 'Decimal precision').insertAfter(".edit_clone_two");
        $(".edit_clone_two").find("input:last").clone().attr('name', 'edit_decimal_total_number[]').addClass("cloned_input").attr('placeholder', 'Total number').insertAfter(".edit_clone_two");
    } else {
        $(".edit_possible").css("display", "none");
        $(".cloned_input").remove();
    }
});