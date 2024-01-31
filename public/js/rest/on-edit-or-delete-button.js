$("body").on("click", ".btn-edit", function () {
    row = $(this).closest("tr");
    var column_type = $(this).parents("tr").attr("data-column-type");
    var column_name = $(this).parents("tr").attr("data-column-name");
    var column_validation = $(this)
        .parents("tr")
        .attr("data-column-validation");

    $("#edit_column_type").val(column_type).attr("disabled", "disabled");
    $("#edit_column_name").val(column_name);
    $("#edit_column_validation").val(column_validation);

    if (column_type == "enum") {
        var json_for_possible_values = $("input[name='table_fields[" + column_name + "]']").attr("value").replace(/'/g, '"');
        var json_to_object = JSON.parse(json_for_possible_values);
        var pos_values_string = json_to_object["possible_values"];
        var pos_values_array = pos_values_string.split(",");

        pos_values_array.forEach(function (p_val) {
            $("#editFieldForm .edit_clone_one").after(
                '<input name="possible_values[]" class="form-control input-lg cloned_input" type="text" value="' +
                    p_val +
                    '" />'
            );
        });

        $(".edit_possible").css("display", "block"); // display clone div for enum
        $(".edit_decimal_div").css("display", "none"); // hide clone div for decimal/double/float
        $(".edit_character_div").css("display", "none"); // hide clone div for varchar
        $(".edit_table_name_div").css("display", "none"); // display clone div for table name
    } else if (column_type == "decimal" || column_type == "double" || column_type == "float") {
        var row = $("input[name='table_fields[" + column_name + "]']").attr("value").replace(/'/g, '"');
        var json_to_object = JSON.parse(row);
        var total_number = json_to_object["total_number"];
        var decimal_precision = json_to_object["decimal_precision"];

        $("#editFieldForm .edit_clone_two").after(
            '<input name="decimal_precision" class="form-control input-lg cloned_input" type="text" value="' +
                decimal_precision +
                '" />'
        );
        $("#editFieldForm .edit_clone_two").after(
            '<input name="decimal_total_number" class="form-control input-lg cloned_input" type="text" value="' +
                total_number +
                '" />'
        );

        $(".edit_decimal_div").css("display", "block"); // display clone div for decimal/double/float
        $(".edit_possible").css("display", "none"); // hide clone div for enum
        $(".edit_character_div").css("display", "none"); // hide clone div for varchar
        $(".edit_table_name_div").css("display", "none"); // display clone div for table name
    } else if (column_type == "string") {
        var row = $("input[name='table_fields[" + column_name + "]']").attr("value").replace(/'/g, '"');
        var json_to_object = JSON.parse(row);
        var character_limit = json_to_object["character_limit"];

        $("#character_limit_input").remove();
        $("#editFieldForm .edit_clone_three").after(
            '<input id="character_limit_input" name="character_limit" class="form-control input-lg cloned_input" type="text" value="' +
                character_limit +
                '" />'
        );

        $(".edit_character_div").css("display", "block"); // display clone div for varchar
        $(".edit_possible").css("display", "none"); // hide clone div for enum
        $(".edit_decimal_div").css("display", "none"); // hide clone div for decimal/double/float
        $(".edit_table_name_div").css("display", "none"); // display clone div for table name
    } else if (column_type == "foreignKey") {
        var row = $("input[name='table_fields[" + column_name + "]']").attr("value").replace(/'/g, '"');
        var json_to_object = JSON.parse(row);
        var table_name = json_to_object["table_name"];

        $("#editFieldForm .edit_clone_five").after(
            '<input name="table_name" class="form-control input-lg cloned_input" type="text" value="' +
                table_name +
                '" />'
        );

        $(".edit_table_name_div").css("display", "block"); // display clone div for varchar
        $(".edit_character_div").css("display", "none"); // hide clone div for varchar
        $(".edit_possible").css("display", "none"); // hide clone div for enum
        $(".edit_decimal_div").css("display", "none"); // hide clone div for decimal/double/float
    }

    $("#tr_row_for_edit").remove();
    $("#editFieldForm #edit_column_type").before(
        '<input name="tr_row_for_edit" id="tr_row_for_edit" type="hidden" data-row="' +
            $(this).parents("tr").attr("data-row") +
            '" style="display:none" />'
    );

    if ($(".added_input").length == 0) {
        $('#scope_fields').hide();
        $('#label_scope_fields').hide();
    } else {
        $('#scope_fields').show();
        $('#label_scope_fields').show();
    }

    $("#editFieldModal").modal("toggle");
});

$("body").on("click", ".btn-delete", function () {
    $(this).parents("tr").remove();
    var column_name = $(this).parents("tr").attr("data-column-name");
    if(column_name == 'deleted_by'){
        $('#makeFileForm #model_name').after('<input type="text" name="deleted_by" value="1" class="" style="display:none" />');
    }
    $("input[name='table_fields[" + column_name + "]']").remove();

    if ($(".added_input").length == 0) {
        $('#scope_fields').hide();
        $('#label_scope_fields').hide();
        $('#addScopeButton').hide();
    } else {
        $('#scope_fields').show();
        $('#label_scope_fields').show();
    }
});
