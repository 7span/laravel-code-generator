<form id="makeTypeFileForm">
    @csrf
    <div class="form-group">
        <label for="name">Type name:</label>
        <!-- <input type="text" id="type_name" name="type_name" value="{!! old('type_name') !!}"> -->
        <input type="text" id="type_name" name="type_name" value="ProjectType" required="">
        <span style="color:blue">If possible please enter your type name like, ProjectType OR ProjectCategoryType.</span>
        <span style="color:red" class="typeNameError"></span><br>
    </div>

    <div class="form-group">
        <label for="typeText">Enter type text</label>
        <textarea class="form-control container" id="typeText" name="type_text" rows="3"  cols="50" (focus)="func()" (blur)="otherFunc()" (keyup)="detectTextarea($event)"></textarea>
        <!-- <button type="button" id="fixTypeButton" class="btn btn-warning" onclick="handleFixTypeButtonClick()">Fix the type</button> -->
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
        // alert('hi');
        e.preventDefault();

        $('.loading').show();
        
        $.ajax({
            url: "/get-fields-and-datatypes",
            type: "POST",
            data: $('#makeTypeFileForm').serialize(),
            success:function(response){
                console.log(response['fields']);
                console.log(response['dataTypes']);
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

    // function handleFixTypeButtonClick(){
    //     btn = document.getElementById('fixTypeButton');
    //     btn.innerHTML = '<i class = "fa fa-spinner fa-spin"></i> Fixing the type ...';
        
    //     // Set this type fields ad type inputs
    //     setTimeout(function(){
    //         var type_text = $("#typeText").val();

            

    //         // alert('Hi');
    //         // When the work is done, reset the button to original state
    //         btn.innerHTML = 'Again fix the type';
    //     }, 3000); 
    // }
</script>