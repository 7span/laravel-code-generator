var addFieldButton = document.getElementById("addFieldButton");
document.getElementById("migration_fields").addEventListener('click', function() {        
    addFieldButton.style.display = (this.checked)? "block" : "none";
});

$('#makeFileForm').on('submit',function(e){
    e.preventDefault();

    $('.loading').show();
    
    var scopeArray = [];
    var input = document.getElementsByName('possible_scope_values[]');

    for (var i = 0; i < input.length; i++) {
        var a = input[i];
        scopeArray.push(a.value);
    }

    var scope_input_name = $('#scope_input').val();
    $('#makeFileForm #model_name').after('<input type="text" name="' + scope_input_name + '" value="'+scopeArray.toString()+'" style="display:none" />')

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
