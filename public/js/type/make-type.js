$('#makeTypeFileForm').on('submit',function(e){
    // alert('Hi');
    e.preventDefault();

    $('.loading').show();

    $('.typeObjectError').text('');

    var typeFields = [];
    var typeAlias = [];
    var input = document.getElementsByClassName('added_type_input');

    for (var i = 0; i < input.length; i++) {
        var a = input[i];
        console.log(a.value);
        typeFields.push(a.name.replace("type_fields[", "").replace("]", ""));
    }
    console.log(typeFields);

    $.ajax({
        url: "/make-type",
        type: "POST",
        data: $('#makeTypeFileForm').serialize(),
        success:function(response){
            if(response.file_path) {
                alert(response.file_path);
                window.location.href = response.file_path;
            }
        },
        complete: function(){
            $('.loading').hide();
        },
        error: function(response) {
            response.responseJSON.hasOwnProperty('type_name') ? $('.typeNameError').text(response.responseJSON.type_name[0]) : '';

            (typeof response.responseJSON.format != undefined) ? $('.typeObjectError').text(response.responseJSON.format) : '';
        },
    });
});
