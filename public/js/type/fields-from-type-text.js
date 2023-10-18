function handleFixTypeButtonClick(){
    $('.loading').show();

    btn = document.getElementById('fixTypeButton');
    btn.innerHTML = '<i class = "fa fa-spinner fa-spin"></i> Fixing the type ...';
    
    // Set the type fields, alias, data types and it's description
    setTimeout(function(){
        var type_text = $("#typeText").val();

        $.ajax({
            url: "/type-fields-and-datatypes",
            type: "POST",
            data: $('#makeTypeFileForm').serialize(),
            success:function(response){
                var fields = response['fields'];
                var dataTypes = response['dataTypes'];

                $(".type_table_table tbody tr").remove();

                $(fields).each(function(key, val) {
                    // alert(key + '--' + value);
                    $(".type_table_table tbody").append("<tr data-row='" + key + "' data-field='" + val + "' data-alias='" + val + "' data-type='" + dataTypes[key] + "' data-description='" + val + " of xyz'><td>" + val + "</td><td>" + val + "</td><td>" + dataTypes[key] + "</td><td>" + val + " of xyz</td><td><button type='button' class='btn btn-primary btn-edit type-field-edit'><i class='fas fa-edit'></i></button></td><td><button type='button' class='btn btn-danger btn-delete type-field-delete'><i class='far fa-trash-alt btn-delete'></i></button></td></tr>");

                    var value = "{'field':" + val + "', 'alias':" + val + "', 'type':'" + dataTypes[key] + "', 'description':'" + val + " of xyz'}";
                    $('#makeTypeFileForm #type_name').after('<input type="' + dataTypes[key] + '" name="type_fields[' + val + ']" value="' + value + '" class="added_type_input" style="display:none" />')
                });
            },
            complete: function(){
                $('.loading').hide();
            },
            error: function(response) {
                console.log(response.responseJSON);
            },
        });            

        // alert('Hi');
        // When the work is done, reset the button to original state
        btn.innerHTML = 'Again fix the type';
    }, 3000);
    
};





// var typeTextArea = document.getElementById('typeText');

// document.addEventListener('click', function(event) {
//     var isClickInsideElement = typeTextArea.contains(event.target);
//     if (!isClickInsideElement) {
//         alert('Bhar');
//     } else {
//         alert('Andar');
//     }
// });

// $(document).mouseup(function (e) {
    //         if ($(e.target).closest(".container").length
    //                     === 0) {
    //             $(".container").hide();
    //         }
    //     });

    // $(document).ready(function() {<!--from  ww  w . ja v  a  2s.c om-->
    //     var $text = $("textarea#typeText"),
    //         $btn = $("input.submit");
    //     $(document).on("click focus", function(e) {
    //         alert('Hii');
    //         if (!$text.is(e.target) && !$btn.is(e.target) && $text.val() == "") {
    //             alert('Hi');
    //             // $btn.hide();
    //         }
    //         else {
    //             alert('Bye');
    //             // $btn.show();
    //         }
            
    //     });
    // });