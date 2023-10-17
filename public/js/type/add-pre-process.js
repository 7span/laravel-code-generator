$('#pre_process').click(function(){
    if (this.checked == true) {
        $(".type_table").css("display", "block");
    } else {
        $(".type_table").css("display", "none");
    }
});