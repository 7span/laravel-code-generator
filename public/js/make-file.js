// $('#is_scope').on('change', function() {
//     if($(this).is(":checked")) {
//         $(".scope_div").css("display", "block");
//         $(".clone_four").find("input:last").clone().attr('name', 'possible_scope_values[]').addClass("scope_input").attr('placeholder', 'Scope field').attr("required", true).insertAfter(".clone_four");
//     } else {
//         $(".scope_input").remove();
//     }
// });

// $(".add_more_scope_field").on('click', function(){
//     $(".clone_four").find("input:last").clone().attr('name', 'possible_scope_values[]').addClass("scope_input").attr('placeholder', 'Scope field').attr("required", true).insertAfter(".clone_four");
// });

var scopeArray = [];
var input = document.getElementsByClassName('kajal');

for (var i = 0; i < input.length; i++) {
    var a = input[i];
    scopeArray.push(a.name.replace("table_fields[", "").replace("]", ""));
}
console.log(scopeArray);

var scope_input_name = $('#scope_input').val();
$('#makeFileForm #model_name').after('<input type="text" name="jenish" value="'+scopeArray.toString()+'" style="display:none" />')

$('#addScopeFieldModal').on('shown.bs.modal', function () {
    $.each(scopeArray, function(key, value) {   
        $('#scope_fields_select')
            .append($("<option>jk</option>")
                       .attr("value", value)
                       .text(value)); 
   });
});






var addFieldButton = document.getElementById("addFieldButton");
document.getElementById("migration_fields").addEventListener('click', function() {        
    addFieldButton.style.display = (this.checked)? "block" : "none";
});

var addScopeButton = document.getElementById("addScopeButton");
document.getElementById("scope_fields").addEventListener('click', function() {        
    addScopeButton.style.display = (this.checked)? "block" : "none";
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
