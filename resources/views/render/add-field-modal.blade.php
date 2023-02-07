<div id="addFieldModal" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add new field</h1>
            </div>
            <div class="modal-body">
                <form role="form" id="addFieldForm">
                    <div class="form-group">
                        <label class="control-label">Field data type</label>
                        <div>
                            <select id="column_type" class="form-control" name="column_type">
                                @include('render.column-type-options')
                            </select>
                            <span style="color:red" class="columnTypeError"></span><br><br>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Database column name</label>
                        <div>
                            <input type="text" class="form-control input-lg" name="column_name" id="column_type" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Validation for field</label>
                        <div>
                            <select id="column_validation" class="form-control" name="column_validation">
                                <option value="optional">Optional</option>
                                <option value="required">Required</option>
                                <option value="unique">Required | Unique</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label possible" style="display:none">Possible values<i class="fa fa-plus-circle fa-3x add_more" aria-hidden="true"></i></label>
                        <div class="clone_one" style="display:none">
                            <input type="text" class="form-control input-lg" name="" required="">
                        </div>
                    </div>
                    <div class="form-group decimal_div" style="display:none">
                        <label class="control-label">Total number and Decimal precision</label>
                        <div class="clone_two" style="display:none">
                            <input type="number" class="form-control input-lg" name="" min="1" max="10" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label character_div" style="display:none">Character limit</label>
                        <div class="clone_three" style="display:none">
                            <input type="number" class="form-control input-lg" name="" value="10" min="10" max="255" required="">
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