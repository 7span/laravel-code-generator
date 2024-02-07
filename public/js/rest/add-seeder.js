$(document).ready(function () {
    $("#addSeederModal").on("shown.bs.modal", function () {
        $("#database-columns").empty("");
        var scopeArray = [];
        var input = document.getElementsByClassName("added_input");

        for (var i = 0; i < input.length; i++) {
            var a = input[i];
            scopeArray.push(
                a.name.replace("table_fields[", "").replace("]", "")
            );
        }
        console.log(scopeArray);

        $.each(scopeArray, function (key, value) {
            //     $('#scope_fields_select')
            //     .append($("<option>jk</option>")
            //     .attr("value", value)
            //     .text(value));
            $("#database-columns").append(value + "<br>");
        });
    });

    $("#addnotificationForm").validate({
        rules: {
            class_name: {
                required: true,
            },
            data: {
                required: true,
            },
        },

        submitHandler: function (form) {
            var classname = $("#addSeederForm #class_name").attr("name");
            var data = $("#addSeederForm #data").attr("name");
            var classvalue = $("#addSeederForm #class_name").val();
            var datavalue = $("#addSeederForm #data").val();

            $("#addSeederForm #model_name").after(
                '<input type="text" name="' +
                    classname +
                    '" value="' +
                    classvalue +
                    '"  style="display:none" />'
            );
            $("#addSeederForm #model_name").after(
                '<input type="text" name="' +
                    data +
                    '" value="' +
                    datavalue +
                    '"  style="display:none" />'
            );

            $("#addSeederForm").modal("toggle");
        },
    });

    $("#close-button,#closeModalBtn").click(function () {
        $("#addSeederForm")[0].reset(); // Reset form
        document.getElementById("seeder").checked = false; // Uncheck the checkbox
    });
});
