var addScopeButton = document.getElementById("addScopeButton");
document.getElementById("scope_fields").addEventListener('click', function() {        
    addScopeButton.style.display = (this.checked)? "block" : "none";
});

$('#addScopeFieldModal').on('shown.bs.modal', function () {
    var scopeArray = [];
    var input = document.getElementsByClassName('added_input');
    
    for (var i = 0; i < input.length; i++) {
        var a = input[i];
        scopeArray.push(a.name.replace("table_fields[", "").replace("]", ""));
    }
    console.log(scopeArray);

    $('#scope_fields_select').find("option").remove().end();

    $.each(scopeArray, function(key, value) {   
        $('#scope_fields_select')
        .append($("<option>jk</option>")
        .attr("value", value)
        .text(value)); 
    });
});

$('#addScopeFieldForm').on('submit', function(e){
    e.preventDefault();
    $('#makeFileForm #model_name').after('<input type="text" name="scope" value="'+$('#scope_fields_select').val()+'" style="display:none" />')

    $('#addScopeFieldModal').modal('toggle');
    $('#addScopeFieldForm').trigger('reset');
});