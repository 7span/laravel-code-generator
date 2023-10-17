<div id="notificationModal" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title">Notification</h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" id="addnotificationForm">

                    <div class="form-group">
                        <label class="control-label">Class name</label>
                        <div>
                            <input type="text" class="form-control input-lg" name="class_name" id="class_name" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Data</label><p style="color:blue">
                        <p>Example : ['user_id' => 1];</p>
                        <div>
                            <textarea id="data" name="data" rows="4" cols="50" required=""></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Subject</label><p style="color:blue">
                        <div>
                            <textarea id="subject" name="subject" rows="4" cols="50" required=""></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Body</label><p style="color:blue">
                        <div>
                            <textarea id="body" name="body" rows="4" cols="50" required=""></textarea>


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
