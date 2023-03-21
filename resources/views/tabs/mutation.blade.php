<form id="makeQueryFileForm">
    @csrf
    <div class="form-group">
        <label for="name">Mutation name:</label>
        <input type="text" id="query_name" name="mutation_name" value="{!! old('mutation_name') !!}">
        <span style="color:blue">If possible please enter your mutation name like, ProjectUpsert.</span>
        <span style="color:red" class="queryNameError"></span><br>
    </div>

    <div class="form-group">
        <label for="queryText">Enter mutation text</label>
        <textarea class="form-control container" id="queryText" name="mutation_text" rows="3"  cols="50" (focus)="func()" (blur)="otherFunc()" (keyup)="detectTextarea($event)"></textarea>
    </div>

    <div class="form-check">
        <input type="hidden" name="pre_process" value="0" checked="checked">
        <input type="checkbox" id="pre_process" name="pre_process" value="1" @checked(old('pre_process') != null ?? 'checked')><label class="light" for="pre_process">Want to include pre-processed description?</label><br><br>
    </div>


    {{-- Start HERE --}}

    <div class="inputs" style="display:block">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name">Input Name:</label>
                    <input type="text" name="input_name[]" value="{!! old('input_name[]') !!}">
                    <span style="color:red" class="inputNameError"></span><br>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label">Input data type</label>
                        <select id="column_type" class="form-control" name="input_type[]">
                            <option value="Int">Int</option>
                            <option value="String">String</option>
                            <option value="GraphQL">GraphQL</option>
                        </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="name">Input Alias:</label>
                    <input type="text" name="input_alias[]" value="{!! old('input_alias[]') !!}">
                    <span style="color:red" class="inputNameError"></span><br>
                </div>
            </div>

        </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-code"> Generate code files</i></button>
</form>

<script type='text/javascript'>

    $('#makeQueryFileForm').on('submit',function(e){
        e.preventDefault();

        $('.loading').show();

        $.ajax({
            url: "/make-mutation",
            type: "POST",
            data: $('#makeQueryFileForm').serialize(),
            success:function(response){
                if(response.file_path) {
                    alert(response.file_path);
                    // window.location.href = "file://" + response.file_path;
                    window.location.href = response.file_path;
                }
            },
            complete: function(){
                $('.loading').hide();
            },
            error: function(response) {
                console.log(response.responseJSON);
                response.responseJSON.hasOwnProperty('model_name') ? $('.modelNameError').text(response.responseJSON.model_name[0]) : '';
                response.responseJSON.hasOwnProperty('method') ? $('.methodsError').text(response.responseJSON.method[0]) : '';
            },
        });
    });

var room = 1;
function education_fields() {

    room++;
    var objTo = document.getElementById('education_fields')
    var divtest = document.createElement("div");
	divtest.setAttribute("class", "form-group removeclass"+room);
	var rdiv = 'removeclass'+room;
    divtest.innerHTML = '<div class="col-sm-3 nopadding"><div class="form-group"> <input type="text" class="form-control" id="Schoolname" name="Schoolname[]" value="" placeholder="School name"></div></div><div class="col-sm-3 nopadding"><div class="form-group"> <input type="text" class="form-control" id="Major" name="Major[]" value="" placeholder="Major"></div></div><div class="col-sm-3 nopadding"><div class="form-group"> <input type="text" class="form-control" id="Degree" name="Degree[]" value="" placeholder="Degree"></div></div><div class="col-sm-3 nopadding"><div class="form-group"><div class="input-group"> <select class="form-control" id="educationDate" name="educationDate[]"><option value="">Date</option><option value="2015">2015</option><option value="2016">2016</option><option value="2017">2017</option><option value="2018">2018</option> </select><div class="input-group-btn"> <button class="btn btn-danger" type="button" onclick="remove_education_fields('+ room +');"> <span class="glyphicon glyphicon-minus" aria-hidden="true"></span> </button></div></div></div></div><div class="clear"></div>';

    objTo.appendChild(divtest)
}
   function remove_education_fields(rid) {
	   $('.removeclass'+rid).remove();
   }
</script>
