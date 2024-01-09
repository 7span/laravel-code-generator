$('#addnotificationForm').on('submit', function(e){
    e.preventDefault();

    var classname= $('#addnotificationForm #class_name').attr('name');
    var data = $('#addnotificationForm #data').attr('name');
    var subject = $('#addnotificationForm #subject').attr('name');
    var body = $('#addnotificationForm #body').attr('name');
    var classvalue = $('#addnotificationForm #class_name').val();
    var datavalue = $('#addnotificationForm #data').val();
    var subjectvalue = $('#addnotificationForm #subject').val();
    var bodyvalue = $('#addnotificationForm #body').val();

    $('#makeFileForm #model_name').after('<input type="text" name="'+classname+'" value="' + classvalue + '"  style="display:none" />')
    $('#makeFileForm #model_name').after('<input type="text" name="'+data+'" value="' + datavalue + '"  style="display:none" />')
    $('#makeFileForm #model_name').after('<input type="text" name="'+subject+'" value="' + subjectvalue + '"  style="display:none" />')
    $('#makeFileForm #model_name').after('<input type="text" name="'+body+'" value="' + bodyvalue + '"  style="display:none" />')
    $('#notificationModal').modal('toggle');
    document.getElementById("notification").checked = true;
});

$('#notificationModal').on('hide.bs.modal', function () {
    document.getElementById("notification").checked = false;
    $('#addnotificationForm').trigger('reset');
});


