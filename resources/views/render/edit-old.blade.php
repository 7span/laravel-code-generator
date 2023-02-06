<div id="myEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a class="close" data-dismiss="modal">×</a>
                <h3>Edit field</h3>
            </div>
            <form class="form-inline" id="myEditForm">
                <div class="modal-body">				
                    <div class="form-group">
                        <label for="name">Field Data Type</label>
                        <select id="edit_column_type" class="form-control" name="edit_column_type">
                            @include('render.select-options')
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email">Database column</label>
                        <input type="text" name="edit_column_name" class="form-control" required="" id="edit_column_name">
                    </div>
                    <div class="form-group">
                        <label for="message">Validation</label>
                        <select id="edit_column_validation" class="form-control" name="edit_column_validation">
                            <option value="optional">Optional</option>
                            <option value="required">Required</option>
                            <option value="unique">Required | Unique</option>
                        </select>
                    </div>
                    <label class="edit_possible" style="display:none">Possible values<i class="fa fa-plus-circle fa-3x add_more" aria-hidden="true"></i></label>
                    <div class="edit_cloning_div" style="display:none">
                        <input type="text" name="" class="form-control" value="" style="margin:5px;">
                    </div>
                </div>
                <div class="modal-footer">					
                    <input type="submit" class="btn btn-success" id="submit">
                </div>
            </form>
        </div>
    </div>
</div>