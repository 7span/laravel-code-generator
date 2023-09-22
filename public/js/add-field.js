$('#addFieldForm').on('submit', function(e){
    e.preventDefault();

    var column_type = $('#column_type').find(":selected").val();
    var column_name = $("input[name='column_name']").val().replace(/[^\w\s]/gi, "");


   

    var column_validation = $('#column_validation').val().join("|");

    let names = [];

    jQuery('#myTable > tbody > tr').each(function(index, value) {
        names.push($('td:eq(0)', this).text());
    });

    if($.inArray(column_name,names) != -1){
        alert('Column already exists.');
        return true;
    }

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
            var character_limit_minimum = $("input[name='character_limit_minimum']").val();

            console.log('character_limit_minimum_add'+character_limit_minimum);
            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':'', 'character_limit':'" + character_limit + "', 'character_limit_minimum':'" + character_limit_minimum + "' }";
            
            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')


            // var character_limit_minimum = $("input[name='character_limit_minimum']").val();
            

            // var minvalue = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':'', 'character_limit_minimum':'" + character_limit_minimum + "'}";
          
            // $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + minvalue + '" class="added_input" style="display:none" />')
        } else if (column_type == 'foreignKey') {
            var table_name = $("input[name='table_name']").val();

            var value = "{'type':'" + column_type + "', 'validation':'optional', 'possible_values':'', 'table_name':'" + table_name + "'}";

            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        }else if(column_type == 'date'){
            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':''}";

            $('#makeFileForm #model_name').after('<input type="text" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        }else {
            var value = "{'type':'" + column_type + "', 'validation':'" + column_validation + "', 'possible_values':''}";

            $('#makeFileForm #model_name').after('<input type="' + column_type + '" name="table_fields[' + column_name + ']" value="' + value + '" class="added_input" style="display:none" />')
        }

        if ($(".added_input").length == 0) {
            $('#scope_fields').hide();
            $('#label_scope_fields').hide();
            $('#addScopeButton').hide();
        } else {
            $('#scope_fields').show();
            $('#label_scope_fields').show();
        }

        $('#addFieldModal').modal('toggle');
        //$('#addFieldForm').trigger('reset');

        $(".possible").css("display", "none"); // hide clone div for enum
        $(".table_name_div").css("display", "none"); // display clone div for table name
        $(".character_div").css("display", "none"); // hide clone div for varchar
        $(".character_div_minimum").css("display", "none"); // hide clone div for varchar
        $(".decimal_div").css("display", "none"); // hide clone div for decimal/double/float

        $(".cloned_input").remove();
    }

    $('#addFieldForm')[0].reset();
    $("select option[value='required']").attr('disabled', false);
    $("select option[value='optional']").attr('disabled', false);

});

$('#column_validation').on('change',function(){
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
