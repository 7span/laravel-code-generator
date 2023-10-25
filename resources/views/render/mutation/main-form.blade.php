<form id="makeMutationFileForm">
    @csrf
    <div class="form-group">
        <span style="color:blue">Please provide the folder name, such as 'Project' or 'Category' and the mutation name,
            like 'upsertProject' or 'deleteCategory.' <br> This format will result in a mutation stored in the
            respective folder, named 'Project/UpsertProject' or 'Category/DeleteCategory,' respectively.</span>
    </div>
    <div class="form-group">
        <label for="name">Folder name:</label>
        <input type="text" id="folder_name" name="folder_name" value="{!! old('folder_name') !!}">
        <span style="color:red" class="folderNameError"></span><br>
    </div>
    <div class="form-group">
        <label for="name">Mutation name:</label>
        <input type="text" id="mutation_name" name="mutation_name" value="{!! old('mutation_name') !!}">
        <span style="color:red" class="mutationNameError"></span><br>
    </div>

    <div class="form-group">
        <label for="mutation_text">Enter mutation text</label>
        <textarea class="form-control container" id="mutation_text" name="mutation_text" rows="3" cols="50"
            (focus)="func()" (blur)="otherFunc()" (keyup)="detectTextarea($event)"></textarea>
        <span style="color:red" class="mutationFormatError"></span><br>
        <span style="color:red" class="mutationTextError"></span><br>
    </div>
    <div class="form-group">
        <label for="mutation_text">Mutation sample object <span style="color:blue">(Add "!" if field is required) </span>
        </label>
        <textarea class="form-control container" id="mutation_text" name="mutation_text" rows="5" cols="50" disabled>
    mutation {
        insertUserCampaignMobile(
            mobileNumberWithCallingCode: String!
            campaignId: Int!
        ): User
    }
    </textarea>
    </div>



    {{-- Start HERE --}}

    <div class="inputs inout-boxes-wrap" style="display:none">
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
