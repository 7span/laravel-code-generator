<div id="myModal" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Add new field</h1>
            </div>
            <div class="modal-body">
                <form role="form" id="myForm">
                    <div class="form-group">
                        <label class="control-label">Field data type</label>
                        <div>
                            <select id="column_type" class="form-control" name="column_type">
                                @include('render.select-options')
                            </select>
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
                        <div class="cloning_div" style="display:none">
                            <input type="text" class="form-control input-lg" name="">
                        </div>
                    </div>
                    <div class="form-group decimal_div" style="display:none">
                        <label class="control-label">Total number and Decimal precision</label>
                        <div class="cloning_decimal_div" style="display:none">
                            <input type="number" class="form-control input-lg" name="" min="1" max="10">
                            <!-- <input type="text" class="form-control input-lg" name="" pattern="[0-9]+"> -->
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