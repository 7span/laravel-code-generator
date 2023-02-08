var concession = document.getElementById("hidden");
document.getElementById("migration_fields").addEventListener('click', function() {        
    concession.style.display = (this.checked)? "block" : "none";
});

$('#makeFileForm').on('submit',function(e){
    e.preventDefault();

    $('#loading-image').show();

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
            $('#loading-image').hide();
        },
        error: function(response) {
            console.log(response.responseJSON);
            response.responseJSON.hasOwnProperty('model_name') ? $('.modelNameError').text(response.responseJSON.model_name[0]) : '';
            response.responseJSON.hasOwnProperty('method') ? $('.methodsError').text(response.responseJSON.method[0]) : '';
        },
    });
});
