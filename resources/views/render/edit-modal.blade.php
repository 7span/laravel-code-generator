<div id="myEditModal" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Edit field</h1>
            </div>
            <div class="modal-body">
                <form role="form" id="myEditForm">
                    <div class="form-group">
                        <label class="control-label">Field data type</label>
                        <div>
                            <select id="edit_column_type" class="form-control" name="edit_column_type">
                                @include('render.select-options')
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Database column name</label>
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
                                <option value="unique">Required | Unique</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label edit_possible" style="display:none">Possible values<i class="fa fa-plus-circle fa-3x edit_more" aria-hidden="true"></i></label>
                        <div class="edit_cloning_div" style="display:none">
                            <input type="text" class="form-control input-lg" name="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>