var addFieldButton = document.getElementById("addFieldButton");
document.getElementById("migration_fields").addEventListener('click', function() {
    addFieldButton.style.display = (this.checked)? "block" : "none";
});

document.getElementById("notification").addEventListener('click', function() {
    let checkbox = document.getElementById("notification");
    if ( checkbox.checked ) {
        $("#notificationModal").modal('show');
    }

});

if ($(".added_input").length == 0) {
    $('#scope_fields').hide();
    $('#label_scope_fields').hide();
    $('#addScopeButton').hide();
} else {
    $('#scope_fields').show();
    $('#label_scope_fields').show();
}
$(document).on('click','.add_more',function(e){
    e.preventDefault();
    var cloneObj = $('.relationdata').clone();

    cloneObj.find('.add_new_row').removeClass('add_more');
    cloneObj.find('.add_new_row').addClass('remove_row');
    cloneObj.addClass('clonerelation');
    cloneObj.removeClass('relationdata');
    cloneObj.find('.relation_model_name').val('');
    cloneObj.find('.add_new_row').text('-');
    $('.relation_model').append(cloneObj);
});
$(document).on('change','.relation_ship',function(e){
    var relationShipType = $(this).val();
    if(relationShipType == "belongsToMany" || relationShipType == "morphToMany"){
        $(this).closest('tr').find('.relation_another_model').prop('disabled', false);
    }else {
        $(this).closest('tr').find('.relation_another_model').prop('disabled', true);
    }
});
$(document).on('click','.remove_row',function(e){
    e.preventDefault();
    $(this).closest('tr').remove();
});
$('#makeFileForm').on('submit',function(e){
    e.preventDefault();
    console.log('formdata'+$('#makeFileForm').serialize());

    $('.loading').show();

    $.ajax({
        url: "/make-files",
        type: "POST",
        data: $('#makeFileForm').serialize(),
        success:function(response){
            if(response.file_path) {
                window.location.href = response.file_path;
                // setTimeout(function() {
                //     location.reload();
                // }, 5000);
            }
        },
        complete: function(){
            $('.loading').hide();
        },
        error: function(response) {
            console.log(response.responseJSON);
            response.responseJSON.hasOwnProperty('model_name') ? $('.modelNameError').text(response.responseJSON.model_name[0]) : '';
            response.responseJSON.hasOwnProperty('method') ? $('.methodsError').text(response.responseJSON.method[0]) : '';
        },
    });
});
