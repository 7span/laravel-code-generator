$('#addFieldForm').on('submit', function(e){
    e.preventDefault();
        
    var column_type = $('#column_type').find(":selected").val();
    var column_name = $("input[name='column_name']").val();
    var column_validation = $('#column_validation').find(":selected").val();
        
    var new_tr_row = parseInt($('.table tbody tr:last').attr('data-row')) + 1;
    $(".table tbody").append("<tr data-row='"+new_tr_row+"' data-column-type='"+column_type+"' data-column-name='"+column_name+"' data-column-validation='"+column_validation+"'><th scope='row'>"+column_type+"</th><td>"+column_name+"</td><td>"+column_validation+"</td><td><button type='button' class='btn btn-success btn-edit'><i class='fas fa-edit'></i></button></td><td><button type='button' class='btn btn-danger btn-delete'><i class='far fa-trash-alt btn-delete'></i></button></td></tr>");

    if (column_type == 'enum') {
        var values = [];
        $("input[name='possible_values[]']").each(function() {
            values.push($(this).val());
        });

        var value = "{'type':'"+column_type+"', 'validation':'"+column_validation+"', 'possible_values':'"+values+"'}";
       
        $('#makeFileForm #model_name').after('<input type="text" name="table_fields['+column_name+']" value="'+value+'" style="display:none" />')
    } else if (column_type == 'decimal' || column_type == 'double' || column_type == 'float') {
        var total_number = $("input[name='decimal_total_number']").val();
        var decimal_precision = $("input[name='decimal_precision']").val();
        
        var value = "{'type':'"+column_type+"', 'validation':'"+column_validation+"', 'possible_values':'', 'total_number':'"+total_number+"', 'decimal_precision':'"+decimal_precision+"'}";
        
        $('#makeFileForm #model_name').after('<input type="text" name="table_fields['+column_name+']" value="'+value+'" style="display:none" />')
    } else if (column_type == 'varchar') {
        var character_limit = $("input[name='character_limit']").val();
        
        var value = "{'type':'"+column_type+"', 'validation':'"+column_validation+"', 'possible_values':'', 'character_limit':'"+character_limit+"'}";
        
        $('#makeFileForm #model_name').after('<input type="text" name="table_fields['+column_name+']" value="'+value+'" style="display:none" />')
    } else {
        var value = "{'type':'"+column_type+"', 'validation':'"+column_validation+"', 'possible_values':''}";
        
        $('#makeFileForm #model_name').after('<input type="'+column_type+'" name="table_fields['+column_name+']" value="'+value+'" style="display:none" />')
    }

    if (column_type == "blank") {
        $('.columnTypeError').text('Please select any one data type.')
    } else {
        $('#addFieldModal').modal('toggle');
        $('#addFieldForm').trigger('reset');
    
        $(".possible").css("display", "none");
        $(".cloned_input").remove();
    }
});