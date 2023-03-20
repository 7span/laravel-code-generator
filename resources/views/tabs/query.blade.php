<form id="makeQueryFileForm">
    @csrf
    <div class="form-group">
        <label for="name">Query name:</label>
        <input type="text" id="query_name" name="query_name" value="{!! old('query_name') !!}">
        <span style="color:blue">If possible please enter your query name like, ProjectQuery OR ProjectCategoryQuery. If you select collection query then please entry query name like ProjectCollectionQuery,CategoryCollectionQuery.</span>
        <span style="color:red" class="queryNameError"></span><br>
    </div>

    <div class="form-group">
        <label for="queryText">Enter query text</label>
        <textarea class="form-control container" id="queryText" name="query_text" rows="3"  cols="50" (focus)="func()" (blur)="otherFunc()" (keyup)="detectTextarea($event)"></textarea>
    </div>

    <div class="form-check">
        <input type="hidden" name="pre_process" value="0" checked="checked">
        <input type="checkbox" id="pre_process" name="pre_process" value="1" @checked(old('pre_process') != null ?? 'checked')><label class="light" for="pre_process">Want to include pre-processed description?</label><br><br>
    </div>

    <div class="form-check">
        <label class="form-check-label" for="flexRadioDefault2">
            Query Resource <input class="form-check-input" type="radio" name="query_type" id="flexRadioDefault2" value="0" checked></label>

    </div>

    <div class="form-check">
        <label class="form-check-label" for="flexRadioDefault2">
            Query Collection <input class="form-check-input" type="radio" name="query_type" id="flexRadioDefault2" value="1" ></label>
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

    $('#makeQueryFileForm').on('submit',function(e){
        e.preventDefault();

        $('.loading').show();

        $.ajax({
            url: "/make-query",
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
</script>
