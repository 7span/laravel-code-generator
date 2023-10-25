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
            if (!response.responseJSON.status) {

                for (const error in response.responseJSON.errors) {
                    if (Object.hasOwnProperty.call(response.responseJSON.errors, error)) {
                        const element = response.responseJSON.errors[error];
                        if (error == 'type_name') {
                            // typeNameError.style.display = 'block';
                            $('.typeNameError').text(response.responseJSON.errors[error]);
                        }

                        if (error == 'type_text') {
                console.log('test');
                            // typeTextError.style.display = 'block';
                            $('.typeTextError').text(response.responseJSON.errors[error]);
                        }
                    }
                }
            }
            (typeof response.responseJSON.format != undefined) ? $('.typeObjectError').text(response.responseJSON.format) : '';
        },
    });
});
