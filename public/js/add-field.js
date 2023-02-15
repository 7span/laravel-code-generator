$('#addFieldForm').on('submit', function(e){
    e.preventDefault();
        
    var column_type = $('#column_type').find(":selected").val();
    var column_name = $("input[name='column_name']").val().replace(/[^\w\s]/gi, "");
    var column_validation = $('#column_validation').find(":selected").val();

    if (column_type == "blank") {
        $('#column_type').after("<span style='color:red' class='columnTypeError'>Please select any one data type.</span>")
    } else {
        var new_tr_row = parseInt($('.table tbody tr:last').attr('data-row')) + 1;
        $(".table tbody").append("<tr data-row='" + new_tr_row + "' data-column-type='" + column_type + "' data-column-name='" + column_name + "' data-column-validation='" + column_validation + "'><th scope='row'>" + column_type + "</th><td>" + column_name + "</td><td>" + column_validation + "</td><td><button type='button' class='btn btn-primary btn-edit'><i class='fas fa-edit'></i></button></td><td><button type='button' class='btn btn-danger btn-delete'><i class='far fa-trash-alt btn-delete'></i></button></td></tr>");

        if (column_type == 'enum') {
            var values = [];
            $("input[name='possible_values[]']").each(function() {
                values.push($(this).val().replace(/[^\w\s]/gi, ""));
            });

            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':'" + values + "'}";
        
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        } else if (column_type == 'decimal' || column_type == 'double' || column_type == 'float') {
            var total_number = $("input[name='decimal_total_number']").val();
            var decimal_precision = $("input[name='decimal_precision']").val();
            
            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':'', 'total_number':'" + total_number + "', 'decimal_precision':'" + decimal_precision + "'}";
            
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        } else if (column_type == 'string') {
            var character_limit = $("input[name='character_limit']").val();
            
            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':'', 'character_limit':'" + character_limit + "'}";
            
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        } else if (column_type == 'foreignKey') {
            var table_name = $("input[name='table_name']").val();
            var is_index = $('#is_index').is(':checked');
            
            var value = "{'type':'" + column_type + "', 'validation':'optional', 'possible_values':'', 'table_name':'" + table_name + "', 'is_index':'" + is_index + "'}";
            
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        } else {
            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':''}";
            
            $('#makeFileForm #model_name').after('<input type="' + column_type + '" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        }
        
        $('#addFieldModal').modal('toggle');
        $('#addFieldForm').trigger('reset');
    
        $(".possible").css("display", "none"); // hide clone div for enum
        $(".table_name_div").css("display", "none"); // display clone div for table name
        $(".is_index_div").css("display", "none"); // display index div
        $(".character_div").css("display", "none"); // hide clone div for varchar
        $(".decimal_div").css("display", "none"); // hide clone div for decimal/double/float
        
        $(".cloned_input").remove();
    }
});