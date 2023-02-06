var concession = document.getElementById("hidden");
document.getElementById("migration_fields").addEventListener('click', function() {        
    concession.style.display = (this.checked)? "block" : "none";
});

$('#makeFileForm').on('submit',function(e){
    e.preventDefault();

    $.ajax({
        url: "/make-files",
        type: "POST",
        data: $('#makeFileForm').serialize(),
        success:function(response){
            if(response.file_path) {
                // alert(response.file_path);
                // window.location.href = "file://" + response.file_path;
                window.location.href = response.file_path;
            }
        },
        error: function(response) {
            console.log(response.responseJSON);
            response.responseJSON.hasOwnProperty('model_name') ? $('.modelNameError').text(response.responseJSON.model_name[0]) : '';
            response.responseJSON.hasOwnProperty('method') ? $('.methodsError').text(response.responseJSON.method[0]) : '';
        },
    });
});

$('#myForm').on('submit', function(e){
    e.preventDefault();

    var values = [];
    $("input[name='possible_values[]']").each(function() {
        values.push($(this).val());
    });

    var column_type = $('#column_type').find(":selected").val();
    var column_name = $("input[name='column_name']").val();
    var column_validation = $('#column_validation').find(":selected").val();

    var new_tr_row = parseInt($('.table tbody tr:last').attr('data-row')) + 1;
    $(".table tbody").append("<tr data-row='"+new_tr_row+"' data-column-type='"+column_type+"' data-column-name='"+column_name+"' data-column-validation='"+column_validation+"'><th scope='row'>"+column_type+"</th><td>"+column_name+"</td><td>"+column_validation+"</td><td><button type='button' class='btn btn-success btn-edit'><i class='fas fa-edit'></i></button></td><td><button type='button' class='btn btn-danger btn-delete'><i class='far fa-trash-alt btn-delete'></i></button></td></tr>");

    if (column_type == 'enum') {
        var value = "{'type':'"+column_type+"', 'validation':'"+column_validation+"', 'possible_values':'"+values+"'}";
        $('#makeFileForm #model_name').after('<input type="text" name="table_fields['+column_name+']" value="'+value+'" style="display:none" />')
    } else if (column_type == 'decimal') {
        var value = "{'type':'"+column_type+"', 'validation':'"+column_validation+"', 'possible_values':'"+values+"'}";
        $('#makeFileForm #model_name').after('<input type="text" name="table_fields['+column_name+']" value="'+value+'" style="display:none" />')
    } else {
        var value = "{'type':'"+column_type+"', 'validation':'"+column_validation+"', 'possible_values':''}";
        $('#makeFileForm #model_name').after('<input type="'+column_type+'" name="table_fields['+column_name+']" value="'+value+'" style="display:none" />')
    }

    $('#myModal').modal('toggle');
    $('#myForm').trigger('reset');

    $(".possible").css("display", "none");
    $(".intro").remove();
});

$('#myEditForm').on('submit', function(e){
    e.preventDefault();

    var values = [];
    $("input[name='possible_values[]']").each(function() {
        values.push($(this).val());
    });

    var column_type = $('#edit_column_type').find(":selected").val();
    var column_name = $("input[name='edit_column_name']").val();
    var column_validation = $('#edit_column_validation').find(":selected").val();

    var which_row = $("#tr_row_for_edit").attr('data-row');

    $('.table tbody tr[data-row="'+which_row+'"]').attr('data-column-type', column_type).attr('data-column-name', column_name).attr('data-column-validation', column_validation);;

    $('.table tbody tr[data-row="'+which_row+'"]').find("th:eq(0)").text(column_type);  
    $('.table tbody tr[data-row="'+which_row+'"]').find("td:eq(0)").text(column_name);  
    $('.table tbody tr[data-row="'+which_row+'"]').find("td:eq(1)").text(column_validation);

    if (column_type == 'enum') {
        $("input[name='table_fields["+column_name+"]']").remove();
        var value = "{'type':'"+column_type+"', 'validation':'"+column_validation+"', 'possible_values':'"+values+"'}";
        $('#makeFileForm #model_name').after('<input type="text" name="table_fields['+column_name+']" value="'+value+'" style="display:none" />')
    } else {
        $("input[name='table_fields["+column_name+"]']").remove();
        var value = "{'type':'"+column_type+"', 'validation':'"+column_validation+"', 'possible_values':''}";
        $('#makeFileForm #model_name').after('<input type="text" name="table_fields['+column_name+']" value="'+value+'" style="display:none" />')
    }

    $('#myEditModal').modal('toggle');
    $('#myEditForm').trigger('reset');
    
    $(".edit_possible").css("display", "none");
    $(".intro").remove();
});

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
        var all_pos_values_to_array = JSON.parse(json_for_possible_values);
        var pos_values_string = all_pos_values_to_array['possible_values'];
        var pos_values_array = pos_values_string.split(",");

        pos_values_array.forEach(function(jk) {
            $('#myEditForm .edit_cloning_div').after('<input name="possible_values[]" class="form-control input-lg intro" type="text" value="' + jk + '" />')
        });

        $(".edit_possible").css("display", "block");
    }

    $("#tr_row_for_edit").remove();
    $('#myEditForm #edit_column_type').before('<input name="tr_row_for_edit" id="tr_row_for_edit" type="hidden" data-row="'+$(this).parents('tr').attr('data-row')+'" style="display:none" />')

    $('#myEditModal').modal('toggle');
});

$("body").on("click", ".btn-delete", function(){
    $(this).parents("tr").remove();
    var column_name = $(this).parents("tr").attr('data-column-name');
    $("input[name='table_fields["+column_name+"]']").remove();
});

$("#column_type").on('change', function(){
    var column_type = $('#column_type').find(":selected").val();
    if (column_type == 'enum') {
        $(".intro").remove();
        $(".possible").css("display", "block");
        $(".cloning_div").find("input:last").clone().attr('name', 'possible_values[]').addClass("intro").insertAfter(".cloning_div");
    } else if (column_type == 'decimal') {
        $(".intro").remove();
        $(".decimal_div").css("display", "block");
        $(".cloning_decimal_div").find("input:last").clone().attr('name', 'decimal_min_value[]').addClass("intro").insertAfter(".cloning_decimal_div");
        $(".cloning_decimal_div").find("input:last").clone().attr('name', 'decimal_max_value[]').addClass("intro").insertAfter(".cloning_decimal_div");
    } else {
        $(".possible").css("display", "none");
        $(".intro").remove();
    }
});

$("#edit_column_type").on('change', function(){
    var edit_column_type = $('#edit_column_type').find(":selected").val();
    if (edit_column_type == 'enum') {
        $(".intro").remove();
        $(".edit_possible").css("display", "block");
        $(".edit_cloning_div").find("input:last").clone().attr('name', 'possible_values[]').addClass("intro").insertAfter(".edit_cloning_div");
    } else if (edit_column_type == 'decimal') {
        $(".intro").remove();
        $(".edit_possible").css("display", "block");
        $(".edit_cloning_div").find("input:last").clone().attr('name', 'possible_values[]').addClass("intro").insertAfter(".edit_cloning_div");
    } else {
        $(".edit_possible").css("display", "none");
        $(".intro").remove();
    }
});

$(".add_more").on('click', function(){
    $(".cloning_div").find("input:last").clone().attr('name', 'possible_values[]').addClass("intro").insertAfter(".cloning_div");
});

$(".edit_more").on('click', function(){
    $(".edit_cloning_div").find("input:last").clone().attr('name', 'possible_values[]').addClass("intro").insertAfter(".edit_cloning_div");
});