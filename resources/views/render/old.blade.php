<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <a class="close" data-dismiss="modal">×</a>
                <h3>Add new field</h3>
            </div>
            <form class="form-inline" id="myForm">
                <div class="modal-body">				
                    <div class="form-group">
                        <label for="name">Field Data Type</label>
                        <select id="column_type" class="form-control" name="column_type">
                            @include('render.select-options')
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="email">Database column</label>
                        <input type="text" name="column_name" class="form-control" required="">
                    </div>
                    <div class="form-group">
                        <label for="message">Validation</label>
                        <select id="column_validation" class="form-control" name="column_validation">
                            <option value="optional">Optional</option>
                            <option value="required">Required</option>
                            <option value="unique">Required | Unique</option>
                        </select>
                    </div>
                    <label class="possible" style="display:none">Possible values<i class="fa fa-plus-circle fa-3x add_more" aria-hidden="true"></i></label>
                    <div class="cloning_div" style="display:none">
                        <input type="text" name="" class="form-control clone_input" value="" style="margin:5px;">
                    </div>
                </div>
                <div class="modal-footer">					
                    <input type="submit" class="btn btn-success" id="submit">
                </div>
            </form>
        </div>
    </div>
</div>