<form id="makeTypeFileForm">
    @csrf
    <div class="form-group">
        <label for="name">Type name:</label>
        <input type="text" id="type_name" name="type_name" value="{!! old('type_name') !!}">
        <!-- <input type="text" id="type_name" name="type_name" value="Project" required=""> -->
        <span style="color:blue">If possible please enter your type name like, ProjectType OR ProjectCategoryType.</span>
        <span style="color:red" class="typeNameError"></span><br>
    </div>

    <div class="form-group">
        <label for="typeText">Enter type text</label>
        <textarea class="form-control container" id="typeText" name="type_text" rows="3"  cols="50" (focus)="func()" (blur)="otherFunc()" (keyup)="detectTextarea($event)"></textarea>
    </div>

    <div class="form-check">
        <input type="hidden" name="pre_process" value="0" checked="checked">
        <input type="checkbox" id="pre_process" name="pre_process" value="1" @checked(old('pre_process') != null ?? 'checked')><label class="light" for="pre_process">Want to include pre-processed description?</label><br><br>
    </div>

    <div class="row table-responsive type_table" style="display:none">
        <table class="table table-bordered" id="myTable">
        <thead>
            <tr>
            <th scope="col">Field</th>
            <th scope="col">Alias</th>
            <th scope="col">Description</th>
            <th scope="col">Edit</th>
            <th scope="col">Delete</th>
            </tr>
        </thead>
        <tbody id="tbody">
            <tr data-row="1">
            <th scope="row">auto_increment</th>
            <td>id</td>
            <td>required</td>
            </tr>
        </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-code"> Generate code files</i></button>
</form>

<script type='text/javascript'>

    $('#makeTypeFileForm').on('submit',function(e){
        e.preventDefault();

        $('.loading').show();
        
        $.ajax({
            url: "/make-type",
            type: "POST",
            data: $('#makeTypeFileForm').serialize(),
            success:function(response){
                if(response.file_path) {
                    // alert(response.file_path);
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
</script>