$('#editFieldForm').on('submit', function(e){
    e.preventDefault();

    var column_type = $('#edit_column_type').find(":selected").val();

    let names = [];

    jQuery('#myTable > tbody > tr').each(function(index, value) {
        names.push($('td:eq(0)', this).text());
    });


    if (column_type == "blank") {
        $('#column_type').after("<span style='color:red' class='columnTypeError'>Please select any one data type.</span>")
    } else {
        var which_row = $("#tr_row_for_edit").attr('data-row');
        var old_column_name = $('.table tbody tr[data-row="' + which_row + '"]').attr('data-column-name');
        var column_name = $("input[name='edit_column_name']").val();

        if($.inArray(column_name,names) != -1){
            $('#column_type').after("<span style='color:red' class='columnTypeError'>Column already exists.</span>");
            return true;
        }

        var column_validation = $('#edit_column_validation').val().join('|');

        $('.table tbody tr[data-row="' + which_row + '"]').attr('data-column-type', column_type).attr('data-column-name', column_name).attr('data-column-validation', column_validation);;

        $('.table tbody tr[data-row="' + which_row + '"]').find("th:eq(0)").text(column_type);
        $('.table tbody tr[data-row="' + which_row + '"]').find("td:eq(0)").text(column_name);
        $('.table tbody tr[data-row="' + which_row + '"]').find("td:eq(1)").text(column_validation);

        if (column_type == 'enum') {
            $("input[name='table_fields[" + old_column_name + "]']").remove();

            var values = [];
            $("input[name='possible_values[]']").each(function() {
                values.push($(this).val());
            });

            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':'" + values + "'}";
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        } else if (column_type == 'decimal' || column_type == 'double' || column_type == 'float') {
            $("input[name='table_fields[" + old_column_name + "]']").remove();

            var total_number = $("input[name='decimal_total_number']").val();
            var decimal_precision = $("input[name='decimal_precision']").val();

            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':'', 'total_number':'" + total_number + "', 'decimal_precision':'" + decimal_precision + "'}";
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        } else if (column_type == 'string') {
            $("input[name='table_fields[" + old_column_name + "]']").remove();

            var character_limit = $("input[name='character_limit']").val();

            console.log('edit_character' + character_limit);
            
            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':'', 'character_limit':'" + character_limit + "'}";
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')



           // $("input[name='table_fields[" + old_column_name + "]']").remove();

          


        } else if (column_type == 'foreignKey') {
            $("input[name='table_fields[" + old_column_name + "]']").remove();

            var table_name = $("input[name='table_name']").val();

            var value = "{'type':'" + column_type + "', 'validation':'optional', 'possible_values':'', 'table_name':'" + table_name + "'}";

            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        }else if(column_type == 'date'){
            $("input[name='table_fields[" + old_column_name + "]']").remove();
            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':''}";
            
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')

        }else {
            $("input[name='table_fields[" + old_column_name + "]']").remove();
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

    $('#editFieldForm')[0].reset();
    $("select option[value='required']").attr('disabled', false);
    $("select option[value='optional']").attr('disabled', false);
});


$('#edit_column_validation').on('change',function(){
    var values = $(this).val();
    if(values.includes('optional')){
        $("select option[value='required']").attr('disabled', true);
    }else{
        $("select option[value='required']").attr('disabled', false);
    }

    if(values.includes('required')){
        $("select option[value='optional']").attr('disabled', true);
    }else{
        $("select option[value='optional']").attr('disabled', false);
    }
});
