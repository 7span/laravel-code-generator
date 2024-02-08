$(document).ready(function () {
    $("#addSeederModal").on("shown.bs.modal", function () {
        $("#database-columns").empty("");
        var seederArray = [];
        var input = document.getElementsByClassName("added_input");

        for (var i = 0; i < input.length; i++) {
            var a = input[i];
            seederArray.push(
                a.name.replace("table_fields[", "").replace("]", "")
            );
        }
        console.log(seederArray);
        $.each(seederArray, function (key, value) {
            $("#database-columns").append(value + "<br>");
        });
    });

    $("#addSeederForm").validate({
        rules: {
            seeder_class: {
                required: true,
            },
            seeder_data: {
                required: true,
            },
        },

        submitHandler: function (form) {
            var classname = $("#addSeederForm #seeder_class").attr("name");
            var data = $("#addSeederForm #seeder_data").attr("name");
            var classvalue = $("#addSeederForm #seeder_class").val();
            var datavalue = $("#addSeederForm #seeder_data").val();
            console.log(classname, data, classvalue, datavalue);

            $("#makeFileForm #model_name").after('<input type="text" name="' + classname +'" value="' + classvalue +'" style="display:none" />');
            $("#makeFileForm #model_name").after('<input type="text" name="' + data +'" value="' + datavalue +'"  style="display:none" />');

            $("#addSeederModal").modal("toggle");
        },
    });

    $("#close-button,#closeModalBtn").click(function () {
        $("#addSeederForm")[0].reset(); // Reset form
        document.getElementById("seeder").checked = false; // Uncheck the checkbox
    });
});
