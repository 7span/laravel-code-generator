$('#makeMutationFileForm').on('submit', function(e) {
    e.preventDefault();

    $('.mutationFormatError').text('');
    $('.folderNameError').text('');
    $('.mutationNameError').text('');
    $('.mutationTextError').text('');
    $('.methodsError').text('');
    $('.loading').show();

    var xhr = $.ajax({
        url: "/make-mutation",
        type: "POST",
        data: $('#makeMutationFileForm').serialize(),
        success: function(response) {
            if (response.file_path) {
                let text = response.file_path;

                window.location.href = response.file_path;
                $('#makeMutationFileForm')[0].reset();
            }
        },
        complete: function() {
            $('.loading').hide();
        },
        error: function(response) {
            console.log(response.responseJSON.format);
            response.responseJSON.hasOwnProperty('format') ? $('.mutationFormatError').text(
                response.responseJSON.format) : '';
            response.responseJSON.hasOwnProperty('folder_name') ? $('.folderNameError').text(
                response.responseJSON.folder_name[0]) : '';
            response.responseJSON.hasOwnProperty('mutation_text') ? $('.mutationTextError')
                .text(
                    response.responseJSON.mutation_text[0]) : '';
            response.responseJSON.hasOwnProperty('mutation_name') ? $('.mutationNameError').text(
                response.responseJSON.mutation_name[0]) : '';
            response.responseJSON.hasOwnProperty('method') ? $('.methodsError').text(response
                .responseJSON.method[0]) : '';
        },
    });
});
$(document).on('click', '.add_more_row', function() {
    var obj = $('.inout-boxes-wrap');
    var $row = obj.children().first();
    var $clone = $row.clone();

    $clone.find('.rowCls').removeClass('add_more_row').addClass('remove_raw');
    $clone.find('input[type=text]').val('');
    $clone.find('.rowCls').val('-');
    obj.append($clone);
});
$(document).on('click', '.remove_raw', function() {
    $(this).closest('.row').remove();
});
