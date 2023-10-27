$('#makeQueryFileForm').on('submit', function(e) {
    e.preventDefault();

    $('.loading').show();
    $('.queryObjectError').text();
    $('.queryNameError').hide();
    $('.queryTextError').hide();

    $.ajax({
        url: "/make-query",
        type: "POST",
        data: $('#makeQueryFileForm').serialize(),
        success: function(response) {
            if (response.file_path) {
                window.location.href = response.file_path;
                $('#makeQueryFileForm')[0].reset();
            }
        },
        complete: function() {
            $('.loading').hide();
        },
        error: function(response) {
            if (!response.responseJSON.status) {
                for (const error in response.responseJSON.errors) {
                    if (Object.hasOwnProperty.call(response.responseJSON.errors, error)) {
                        const element = response.responseJSON.errors[error];
                        if (error == 'query_name') {
                            queryNameError.style.display = 'block';
                            $('.queryNameError').text(response.responseJSON.errors[error]);
                        }

                        if (error == 'query_text') {
                            queryTextError.style.display = 'block';
                            $('.queryTextError').text(response.responseJSON.errors[error]);
                        }
                    }
                }
            }
            (typeof response.responseJSON.format != undefined) ? $('.queryObjectError').text(
                response.responseJSON.format): '';
        },
    });
});
