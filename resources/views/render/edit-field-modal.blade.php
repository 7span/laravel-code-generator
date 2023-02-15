<div id="editFieldModal" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Edit field</h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" id="editFieldForm">
                    <div class="form-group">
                        <label class="control-label">Field data type</label>
                        <div>
                            <select id="edit_column_type" class="form-control" name="edit_column_type">
                                @include('render.column-type-options')
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Database column name</label><p style="color:blue">Add without special characters</p>
                        <div>
                            <input type="text" class="form-control input-lg" name="edit_column_name" id="edit_column_name" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Validation for field</label>
                        <div>
                            <select id="edit_column_validation" class="form-control" name="edit_column_validation">
                                <option value="optional">Optional</option>
                                <option value="required">Required</option>
                                <!-- <option value="unique">Required | Unique</option> -->
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label edit_possible" style="display:none">Possible values<i class="fa fa-plus-circle fa-3x edit_more" aria-hidden="true"></i><p style="color:blue; margin-top:5px;">Add without special characters</p></label>
                        <div class="edit_clone_one" style="display:none">
                            <input type="text" class="form-control input-lg" name="">
                        </div>
                    </div>
                    <div class="form-group edit_decimal_div" style="display:none">
                        <label class="control-label">Total number and Decimal precision</label>
                        <div class="edit_clone_two" style="display:none">
                            <input type="number" class="form-control input-lg" name="" min="1" max="10">
                        </div>
                    </div>
                    <div class="form-group edit_character_div" style="display:none">
                        <label class="control-label">Character limit</label>
                        <div class="edit_clone_three" style="display:none">
                            <input type="number" class="form-control input-lg" name="" min="10" max="255">
                        </div>
                    </div>
                    <div class="form-group">
                        <div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" class="btn btn-danger"  data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>