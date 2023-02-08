$("#edit_column_type").on('change', function(){
    var edit_column_type = $('#edit_column_type').find(":selected").val();

    edit_column_type == 'blank' ? $('#edit_column_type').after("<span style='color:red' class='columnTypeError'>Please select any one data type.</span>") : $('.columnTypeError').remove();

    if (edit_column_type == 'enum') {
        $(".cloned_input").remove();

        $(".edit_possible").css("display", "block"); // display clone div for enum
        $(".edit_decimal_div").css("display", "none"); // hide clone div for decimal/double/float
        $(".edit_character_div").css("display", "none"); // hide clone div for varchar

        $(".edit_clone_one").find("input:last").clone().attr('name', 'possible_values[]').addClass("cloned_input").insertAfter(".edit_clone_one");
    } else if (edit_column_type == 'decimal' || column_type == 'double' || column_type == 'float') {
        $(".cloned_input").remove();

        $(".edit_decimal_div").css("display", "block"); // display clone div for decimal/double/float
        $(".edit_possible").css("display", "none"); // hide clone div for enum
        $(".edit_character_div").css("display", "none"); // hide clone div for varchar

        $(".edit_clone_two").find("input:last").clone().attr('name', 'edit_decimal_precision[]').addClass("cloned_input").attr('placeholder', 'Decimal precision').insertAfter(".edit_clone_two");
        $(".edit_clone_two").find("input:last").clone().attr('name', 'edit_decimal_total_number[]').addClass("cloned_input").attr('placeholder', 'Total number').insertAfter(".edit_clone_two");
    } else if (column_type == 'string') {
        $(".cloned_input").remove();

        $(".edit_character_div").css("display", "block"); // display clone div for varchar
        $(".edit_possible").css("display", "none"); // hide clone div for enum
        $(".edit_decimal_div").css("display", "none"); // hide clone div for decimal/double/float

        $(".edit_clone_three").find("input:last").clone().attr('name', 'edit_character_limit').addClass("cloned_input").attr('placeholder', 'Character limit').insertAfter(".edit_clone_three");
    } else {
        $(".cloned_input").remove();

        $(".edit_possible").css("display", "none"); // hide clone div for enum
        $(".edit_decimal_div").css("display", "none"); // hide clone div for decimal/double/float
        $(".edit_character_div").css("display", "none"); // hide clone div for varchar
    }
});