$(".add_more").on('click', function(){
    $(".clone_one").find("input:last").clone().attr('name', 'possible_values[]').addClass("cloned_input").attr('placeholder', 'Possible value').attr("required", true).insertAfter(".clone_one");
});

$(".edit_more").on('click', function(){
    $(".edit_clone_one").find("input:last").clone().attr('name', 'possible_values[]').addClass("cloned_input").attr('placeholder', 'Possible value').attr("required", true).insertAfter(".edit_clone_one");
});