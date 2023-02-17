<div id="editTypeFieldModal" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Edit type field and it's property</h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p style="color:blue">Add all text without special characters</p>
                <form role="form" id="editTypeFieldForm">
                    <div class="form-group">
                        <label class="control-label">Field</label>
                        <div>
                            <input type="text" class="form-control input-lg" name="edit_field" id="edit_field" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Alias</label>
                        <div>
                            <input type="text" class="form-control input-lg" name="edit_alias" id="edit_alias" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Datatype</label>
                        <div>
                            <input type="text" class="form-control input-lg" name="edit_datatype" id="edit_datatype" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <div>
                            <input type="text" class="form-control input-lg" name="edit_description" id="edit_description" required="">
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