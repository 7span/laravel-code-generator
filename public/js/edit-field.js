$('#editFieldForm').on('submit', function(e){
    e.preventDefault();

    var column_type = $('#edit_column_type').find(":selected").val();
    var column_name = $("input[name='edit_column_name']").val();
    var column_validation = $('#edit_column_validation').find(":selected").val();

    if (column_type == "blank") {
        $('#column_type').after("<span style='color:red' class='columnTypeError'>Please select any one data type.</span>")
    } else {
        var which_row = $("#tr_row_for_edit").attr('data-row');

        $('.table tbody tr[data-row="' + which_row + '"]').attr('data-column-type', column_type).attr('data-column-name', column_name).attr('data-column-validation', column_validation);;

        $('.table tbody tr[data-row="' + which_row + '"]').find("th:eq(0)").text(column_type);  
        $('.table tbody tr[data-row="' + which_row + '"]').find("td:eq(0)").text(column_name);  
        $('.table tbody tr[data-row="' + which_row + '"]').find("td:eq(1)").text(column_validation);

        if (column_type == 'enum') {
            $("input[name='table_fields[" + column_name + "]']").remove();

            var values = [];
            $("input[name='possible_values[]']").each(function() {
                values.push($(this).val());
            });

            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':'" + values + "'}";
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        } else if (column_type == 'decimal' || column_type == 'double' || column_type == 'float') {
            $("input[name='table_fields[" + column_name + "]']").remove();

            var total_number = $("input[name='decimal_total_number']").val();
            var decimal_precision = $("input[name='decimal_precision']").val();

            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':'', 'total_number':'" + total_number + "', 'decimal_precision':'" + decimal_precision + "'}";
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        } else if (column_type == 'string') {
            $("input[name='table_fields[" + column_name + "]']").remove();

            var character_limit = $("input[name='character_limit']").val();

            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':'', 'character_limit':'" + character_limit + "'}";
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        } else {
            $("input[name='table_fields[" + column_name + "]']").remove();
            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':''}";
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        }

        if ($(".added_input").length == 0) {
            $('#scope_fields').hide();
            $('#label_scope_fields').hide();
            $('#addScopeButton').hide();
        } else {
            $('#scope_fields').show();
            $('#label_scope_fields').show();
        }

        $('#editFieldModal').modal('toggle');
        $('#editFieldForm').trigger('reset');
        
        $(".edit_possible").css("display", "none");
        $(".cloned_input").remove();
    }
});