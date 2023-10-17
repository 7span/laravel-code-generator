$("body").on("click", ".type-field-edit", function () {
    row = $(this).closest("tr");
    var field = $(this).parents("tr").attr("data-field");
    var alias = $(this).parents("tr").attr("data-alias");
    var datatype = $(this).parents("tr").attr("data-type");
    var description = $(this).parents("tr").attr("data-description");

    $("#edit_field").val(field);
    $("#edit_alias").val(alias);
    $("#edit_datatype").val(datatype);
    $("#edit_description").val(description);

    $("#tr_row_for_edit_type_field").remove();
    $("#editTypeFieldForm #edit_field").before(
        '<input name="tr_row_for_edit_type_field" id="tr_row_for_edit_type_field" type="hidden" data-row="' +
            $(this).parents("tr").attr("data-row") +
            '" style="display:none" />'
    );

    $("#editTypeFieldModal").modal("toggle");
});

$("body").on("click", ".type-field-delete", function () {
    $(this).parents("tr").remove();
    var field = $(this).parents("tr").attr("data-field");
    $("input[name='type_fields[" + field + "]']").remove();
});