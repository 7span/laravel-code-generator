<br>
<form id="makeQueryFileForm">
    @csrf

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <span style="color:blue">Please provide the query name, such as 'Project' or 'Category'.</span>
            </div>
            <div class="form-group">
                <label for="name">Query name:</label>
                <input type="text" id="query_name" name="query_name" value="{!! old('query_name') !!}">
                <span style="color:red;" class="queryNameError" id="queryNameError"></span><br>
            </div>

            <div>
                <div class="form-group">
                    <label for="queryText">Enter Query Text</label>
                    <textarea class="form-control container" id="queryText" name="query_text" rows="3" cols="50" (focus)="func()"
                        (blur)="otherFunc()" (keyup)="detectTextarea($event)"></textarea>
                    <span style="color:red;" class="queryTextError" id="queryTextError"></span><br>
                </div>
                <div class="form-group">
                        <label for="name">Query text Snippet
                            <span style="color:blue">(Add "!" if field is
                                required)
                            </span>
                        </label>

                    <textarea class="form-control container" rows="7" cols="50" disabled>
                        perPage: Int,
                        page: Int,
                        workspaceId: Int!,
                        campaignId: Int,
                        formId: Int,
                        search: String
                </textarea>
                </div>
            </div>
        </div>
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
