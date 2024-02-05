<div id="addFieldModal" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add new field</h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" id="addFieldForm">
                    <div class="form-group">
                        <label class="control-label">Field data type</label>
                        <div>
                            <select id="column_type" class="form-control" name="column_type">
                                @include('render.rest.column-type-options')
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Database column name</label><p style="color:blue">Add without special characters</p>
                        <div>
                            <input type="text" class="form-control input-lg" name="column_name" id="column_type" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Validation for field</label>
                        <div>
                            <select id="column_validation" class="form-control" name="column_validation" multiple>
                                <option value="optional">Optional</option>
                                <option value="required">Required</option>
                                <option value="unique">Unique</option>
                                <option value="email">Email</option>
                                <!-- <option value="unique">Required | Unique</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label possible" style="display:none">Possible values<i
                                class="fa fa-plus-circle fa-3x add_more_enum" aria-hidden="true"></i>
                            <p style="color:blue; margin-top:5px;">Add without special characters</p>
                        </label>
                        <div class="clone_one" style="display:none">
                            <input type="text" class="form-control input-lg" name="">
                        </div>
                    </div>
                    <div class="form-group decimal_div" style="display:none">
                        <label class="control-label">Total number and Decimal precision</label>
                        <div class="clone_two" style="display:none">
                            <input type="number" class="form-control input-lg" name="" min="1" max="10">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label character_div" style="display:none">Character limit</label>
                        <div class="clone_three" style="display:none">
                            <input type="number" class="form-control input-lg" name="" value="10" min="10" max="255">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label table_name_div" style="display:none">Table name</label>
                        <div class="clone_five" style="display:none">
                            <input type="text" class="form-control input-lg" name="">
                        </div>
                        <div class="is_index_div" style="display:none">
                            <input type="hidden" name="is_index" value="0" checked="checked">
                            <input type="checkbox" id="is_index" name="is_index" value="1" @checked(old('is_index') ?? 'checked')><label class="light" for="is_index">Is this index?</label><br>
                        </div>
                    </div>
                    <div class="form-group">
                        <div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" id="close-button" class="btn btn-danger"  data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
