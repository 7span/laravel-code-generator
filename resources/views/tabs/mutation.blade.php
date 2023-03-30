<form id="makeMutationFileForm">
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



    {{-- Start HERE --}}

    <div class="inputs inout-boxes-wrap" style="display:block">
        <div class="row add-more">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="name">Input Name:</label>
                    <input type="text" name="input_name[]" value="{!! old('input_name[]') !!}">
                    <span style="color:red" class="inputNameError"></span><br>
                </div>
            </div>

            <div class="col-md-2">
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

            <div class="col-md-2">
                <div class="form-group">
                    <label class="control-label">Is Required</label>
                        <select id="column_type" class="form-control" name="input_is_required[]">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                </div>
            </div>
            <div class="col-md-2">
                <input type="button" name="add_more" class="rowCls add_more_row" value="+">
            </div>

        </div>
        
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-code"> Generate code files</i></button>
</form>

<script type='text/javascript'>

    $('#makeMutationFileForm').on('submit',function(e){
        e.preventDefault();

        $('.loading').show();

        $.ajax({
            url: "/make-mutation",
            type: "POST",
            data: $('#makeMutationFileForm').serialize(),
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
    $(document).on('click','.add_more_row',function(){
        var obj = $('.inout-boxes-wrap');
        var $row = obj.children().first();
        var $clone = $row.clone();
        
        $clone.find('.rowCls').removeClass('add_more_row').addClass('remove_raw');
        $clone.find('input[type=text]').val('');
        $clone.find('.rowCls').val('-');
        obj.append($clone);
    });
    $(document).on('click','.remove_raw',function(){
        $(this).closest('.row').remove();
    });

</script>
