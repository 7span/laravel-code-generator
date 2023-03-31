<form id="makeTypeFileForm">
    @csrf
    <div class="form-group">
        <label for="name">Type Object:</label>
        <!-- <input type="text" id="type_name" name="type_name" value="{!! old('type_name') !!}"> -->
        <textarea class="form-control container" id="type_obj" name="type_obj" rows="3"  cols="50"></textarea>
        <span style="color:red" class="typeObjectError"></span><br>
    </div>

    <div class="form-group col-lg-6">
        <label for="name">Type Object Snippet :</label>
        <textarea class="form-control container"  rows="7"  cols="50" disabled>
            type FormField {
                id: Int
                name: String
                type: String
                validations: String
                createdAt: String
            }
        </textarea>
    </div>

    <div class="form-group col-lg-6">
        <label for="name">Type Input Object Snippet :</label>
        <textarea class="form-control container" rows="7"  cols="50" disabled>
            type FormFieldInput {
                id: Int
                name: String
                type: String
                validations: String
             }
        </textarea>
    </div>

    <div class="form-group ">
        <label for="name">Type name:</label>
        <!-- <input type="text" id="type_name" name="type_name" value="{!! old('type_name') !!}"> -->
        <input type="text" id="type_name" name="type_name" value="ProjectType" required="">
        <span style="color:blue">If possible please enter your type name like  Project,Category.</span>
        <span style="color:red" class="typeNameError"></span><br>
    </div>


    <input type="String" name="type_fields[name]" value="{'field':name', 'alias':name', 'type':'String', 'description':'name of xyz'}" class="added_type_input" style="display:none">
    <input type="int" name="type_fields[id]" value="{'field':id', 'alias':id', 'type':'int', 'description':'id of xyz'}" class="added_type_input" style="display:none">

    <div class="form-group col-lg-6">
        <label for="typeText">Enter type text</label>
        <textarea class="form-control container" id="typeText" name="type_text" rows="3"  cols="50" (focus)="func()" (blur)="otherFunc()" (keyup)="detectTextarea($event)"></textarea>
        <button type="button" id="fixTypeButton" class="btn btn-warning" onclick="handleFixTypeButtonClick()">Fix the type</button>
    </div>

    <div class="form-group col-lg-6">
        <label for="name">Type Text Snippet :</label>
        <textarea class="form-control container" rows="3"  cols="50" disabled>
            id: int,name: String
        </textarea>
    </div>

    <!-- <button type="button" class="btn btn-warning" id="editTypeFieldModal" data-toggle="modal" data-target="#editTypeFieldModal"><i class="fas fa-plus"> Add new field</i></button><br><br> -->

    {{-- <div class="form-check">
        <input type="hidden" name="pre_process" value="0" checked="checked">
        <input type="checkbox" id="pre_process" name="pre_process" value="1" @checked(old('pre_process') != null ?? 'checked')><label class="light" for="pre_process">Want to include pre-processed description?</label><br><br>
    </div> --}}

    <div class="row table-responsive type_table" style="display:none">
        <table class="table table-bordered type_table_table" id="myTable">
            <thead>
                <tr>
                    <th scope="col">Field</th>
                    <th scope="col">Alias</th>
                    <th scope="col">Datatype</th>
                    <th scope="col">Description</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody id="tbody">
            </tbody>
        </table>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fas fa-code"> Generate type file</i></button>
</form>
