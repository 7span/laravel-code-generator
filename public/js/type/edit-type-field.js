$('#editTypeFieldForm').on('submit', function(e){
    e.preventDefault();

    var which_row = $("#tr_row_for_edit_type_field").attr('data-row');

    var old_field = $('.type_table_table tbody tr[data-row="' + which_row + '"]').attr('data-field');

    var field = $("input[name='edit_field']").val();
    var alias = $("input[name='edit_alias']").val();
    var datatype = $("input[name='edit_datatype']").val();
    var description = $("input[name='edit_description']").val();

    $('.type_table_table tbody tr[data-row="' + which_row + '"]').attr('data-field', field).attr('data-alias', alias).attr('data-type', datatype).attr('data-description', description);

    $('.type_table_table tbody tr[data-row="' + which_row + '"]').find("td:eq(0)").text(field);  
    $('.type_table_table tbody tr[data-row="' + which_row + '"]').find("td:eq(1)").text(alias);  
    $('.type_table_table tbody tr[data-row="' + which_row + '"]').find("td:eq(2)").text(datatype);
    $('.type_table_table tbody tr[data-row="' + which_row + '"]').find("td:eq(3)").text(description);

    $("input[name='type_fields[" + old_field + "]']").remove();

    var value = "{'field':" + field + "', 'alias':" + alias + "', 'type':'" + datatype + "', 'description':'" + description + " of xyz'}";
    $('#makeTypeFileForm #type_name').after('<input type="' + datatype + '" name="type_fields[' + field + ']" value="' + value + '" class="added_type_input" style="display:none" />')

    $('.modal').modal('hide');
    $('#editTypeFieldForm').trigger('reset');
});

$('.edit_type_field_close').on('click', function(e){
    $('.modal').modal('hide');
});