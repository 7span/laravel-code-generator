<!-- <form method="post" action="{{ url('make-files') }}" id="makeFileForm"> -->
<form id="makeFileForm">
    @csrf
    <fieldset>
    <label for="name">Model name:</label>
    <input type="text" id="model_name" name="model_name" value="{!! old('model_name') !!}">
    <!-- <input type="text" id="model_name" name="model_name" value="Project" required=""> -->
    <span style="color:blue">If possible please enter your model name like, Project OR ProjectCategory.</span><br><br>
    <span style="color:red" class="modelNameError"></span><br><br>

    <!-- <input type="text" name="table_fields[kajal]" value="{'type':'enum', 'validation':'optional', 'possible_values':'jenish,kajal'}" style="display:none"> -->
    <!-- <input type="text" name="table_fields[first_name]" value='{"type":"text","validation":"required", 'possible_values':''}' style="display:none">
    <input type="text" name="table_fields[mobile]" value='{"type":"integer","validation":"required","default":0, 'possible_values':''}' style="display:none"> -->
    <!-- <input type="text" name="table_fields[qaz]" value="{'type':'decimal', 'validation':'optional', 'possible_values':'', 'total_number':'4', 'decimal_precision':'2'}" style="display:none"> -->
    
    <input type="hidden" name="soft_delete" value="0" checked="checked">
    <input type="checkbox" id="soft_delete" name="soft_delete" value="1" @checked(old('soft_delete') ?? 'checked')><label class="light" for="development">Want to include soft delete?</label><br>
    
    <input type="hidden" name="admin_crud" value="0" checked="checked">
    <input type="checkbox" id="admin_crud" name="admin_crud" value="1" @checked(old('admin_crud'))><label class="light" for="development">Want to include admin CRUD?</label><br><br>
    
    <input type="hidden" name="migration_fields" value="0" checked="checked">
    <input type="checkbox" id="migration_fields" name="migration_fields" value="1" @checked(old('migration_fields'))><label class="light" for="development">Want to add more fields in the table?</label><br><br>

    <!-- Trigger the modal with a button -->
    <button type="button" class="btn btn-info btn-sm" id="hidden" style="display:none" data-toggle="modal" data-target="#myModal"><i class="fas fa-plus"> Add new field</i></button><br><br>
    <!-- <button type="button" class="btn btn-info btn-sm" id="hidden" data-toggle="modal" data-target="#myModal"><i class="fas fa-plus"> Add new field</i></button><br><br> -->

    <div class="row">
      <div class="col-12">
        <table class="table table-bordered" id="myTable">
          <thead>
            <tr>
              <th scope="col">Field Type</th>
              <th scope="col">Database Column</th>
              <th scope="col">Validation</th>
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
            <tr data-row="2">
              <th scope="row">date_time_picker</th>
              <td>created_at</td>
              <td>required</td>
            </tr>
            <tr data-row="3">
              <th scope="row">date_time_picker</th>
              <td>updated_at</td>
              <td>required</td>
            </tr>
            <tr data-row="4">
              <th scope="row">date_time_picker</th>
              <td>deleted_at</td>
              <td>optional</td>
            </tr>
            <tr data-row="5">
              <th scope="row">integer</th>
              <td>created_by</td>
              <td>optional</td>
            </tr>
            <tr data-row="6">
              <th scope="row">integer</th>
              <td>updated_by</td>
              <td>optional</td>
            </tr>
            <tr data-row="7">
              <th scope="row">integer</th>
              <td>deleted_by</td>
              <td>optional</td>
            </tr>
            <!-- <tr data-row="8" data-column-type="enum" data-column-name="kajal" data-column-validation="optional">
              <th scope="row">enum</th>
              <td>kajal</td>
              <td>optional</td>
              <td><button type="button" class="btn btn-success btn-edit"><i class="fas fa-edit"></i></button></td>
              <td><button type="button" class="btn btn-danger btn-delete"><i class="far fa-trash-alt btn-delete"></i></button></td>
            </tr> -->
            <!-- <tr data-row="8" data-column-type="decimal" data-column-name="qaz" data-column-validation="optional">
              <th scope="row">decimal</th>
              <td>qaz</td>
              <td>optional</td>
              <td><button type="button" class="btn btn-success btn-edit"><i class="fas fa-edit"></i></button></td><td><button type="button" class="btn btn-danger btn-delete"><i class="far fa-trash-alt btn-delete"></i></button></td>
            </tr> -->
          </tbody>
        </table>
      </div>
    </div>

    <label for="name">Which method do you want to include?</label>
    <input type="checkbox" id="create" name="method[]" value="create" @if(is_array(old('method')) && in_array('create', old('method'))) checked @endif>Create
    <!-- <input type="checkbox" id="create" name="method[]" value="create" checked>Create -->

    <input type="checkbox" id="update" name="method[]" value="update" @if(is_array(old('method')) && in_array('update', old('method'))) checked @endif>Update

    <input type="checkbox" id="destroy" name="method[]" value="destroy" @if(is_array(old('method')) && in_array('destroy', old('method'))) checked @endif>Destroy

    <input type="checkbox" id="show" name="method[]" value="show" @if(is_array(old('method')) && in_array('show', old('method'))) checked @endif>Show

    <input type="checkbox" id="index" name="method[]" value="index" @if(is_array(old('method')) && in_array('index', old('method'))) checked @endif>Index<br>

    <span style="color:red" class="methodsError"></span><br><br>

    <input type="hidden" name="service" value="0" checked="checked">
    <input type="checkbox" id="service" name="service" value="1" @checked(old('service') ?? 'checked')><label class="light" for="development">Do you want service file?</label><br>

    <input type="hidden" name="resource" value="0" checked="checked">
    <input type="checkbox" id="resource" name="resource" value="1" @checked(old('resource') ?? 'checked')><label class="light" for="development">Do you want resource file?</label><br>

    <input type="hidden" name="request" value="0" checked="checked">
    <input type="checkbox" id="request" name="request" value="1" @checked(old('request') ?? 'checked')><label class="light" for="development">Do you want request file?</label><br>

    </fieldset>  
    <button type="submit" class="btn btn-info btn-lg">Generate files</button>
</form>