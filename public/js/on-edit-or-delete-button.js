$("body").on("click", ".btn-edit", function(){
    row = $(this).closest('tr');
    var column_type = $(this).parents("tr").attr('data-column-type');
    var column_name = $(this).parents("tr").attr('data-column-name');
    var column_validation = $(this).parents("tr").attr('data-column-validation');

    $('#edit_column_type').val(column_type).attr('disabled','disabled');
    $('#edit_column_name').val(column_name).attr('disabled','disabled');
    $('#edit_column_validation').val(column_validation);
    // alert($('#edit_column_name').html());

    if (column_type == 'enum') {
        var json_for_possible_values = $("input[name='table_fields[" + column_name +"]']").attr('value').replace(/'/g, '"');
        var json_to_object = JSON.parse(json_for_possible_values);
        var pos_values_string = json_to_object['possible_values'];
        var pos_values_array = pos_values_string.split(",");
        
        pos_values_array.forEach(function(p_val) {
            $('#myEditForm .edit_clone_one').after('<input name="possible_values[]" class="form-control input-lg cloned_input" type="text" value="' + p_val + '" />')
        });
        
        $(".edit_possible").css("display", "block");
    } else if (column_type == 'decimal' || column_type == 'double' || column_type == 'float') {
        var row = $("input[name='table_fields[" + column_name +"]']").attr('value').replace(/'/g, '"');
        var json_to_object = JSON.parse(row);
        var total_number = json_to_object['total_number'];
        var decimal_precision = json_to_object['decimal_precision'];

        $('#myEditForm .edit_clone_two').after('<input name="decimal_precision" class="form-control input-lg cloned_input" type="text" value="' + decimal_precision + '" />')
        $('#myEditForm .edit_clone_two').after('<input name="decimal_total_number" class="form-control input-lg cloned_input" type="text" value="' + total_number + '" />')

        $(".edit_decimal_div").css("display", "block");
    }

    $("#tr_row_for_edit").remove();
    $('#myEditForm #edit_column_type').before('<input name="tr_row_for_edit" id="tr_row_for_edit" type="hidden" data-row="'+$(this).parents('tr').attr('data-row')+'" style="display:none" />')

    $('#editFieldModal').modal('toggle');
});

$("body").on("click", ".btn-delete", function(){
    $(this).parents("tr").remove();
    var column_name = $(this).parents("tr").attr('data-column-name');
    $("input[name='table_fields["+column_name+"]']").remove();
});