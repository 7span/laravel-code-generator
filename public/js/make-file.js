var addFieldButton = document.getElementById("addFieldButton");
document.getElementById("migration_fields").addEventListener('click', function() {        
    addFieldButton.style.display = (this.checked)? "block" : "none";
});

if ($(".added_input").length == 0) {
    $('#scope_fields').hide();
    $('#label_scope_fields').hide();
    $('#addScopeButton').hide();
} else {
    $('#scope_fields').show();
    $('#label_scope_fields').show();
}

$('#makeFileForm').on('submit',function(e){
    e.preventDefault();

    $('.loading').show();
    
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
