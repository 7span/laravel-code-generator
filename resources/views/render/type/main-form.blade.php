<form id="makeTypeFileForm">
    @csrf

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <span style="color:blue">Please provide the type name, such as 'Project' or 'Category'.</span>
            </div>
            <div class="form-group ">
                <label for="name">Type name:</label>
                <input type="text" id="type_name" name="type_name" value="">
                <p style="color:red;" class="typeNameError"></p>
            </div>


            <input type="String" name="type_fields[name]"
                value="{'field':name', 'alias':name', 'type':'String', 'description':'name of xyz'}"
                class="added_type_input" style="display:none">
            <input type="int" name="type_fields[id]"
                value="{'field':id', 'alias':id', 'type':'int', 'description':'id of xyz'}" class="added_type_input"
                style="display:none">

            <div class="form-group">
                <label for="typeText">Enter type text</label>
                <textarea class="form-control container" id="typeText" name="type_text" rows="3" cols="50" (focus)="func()"
                    (blur)="otherFunc()" (keyup)="detectTextarea($event)"></textarea>
                {{-- <button type="button" id="fixTypeButton" class="btn btn-warning" onclick="handleFixTypeButtonClick()">Fix the type</button> --}}
                <p style="color:red" class="typeFormatError"></p><br>
                <p style="color:red;" class="typeTextError"></p>
            </div>
            <div class="form-group">
                <label for="name">Type Text Snippet :</label>
                <textarea class="form-control container" rows="3" cols="50" disabled>
                    id: int,
                    name: String
                </textarea>
            </div>
        </div>

    </div>
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
