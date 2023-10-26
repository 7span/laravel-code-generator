<form id="makeFileForm">
    <div>
        <p style="color:red">To use this CRUD generator you first need to install <a
            href="https://github.com/spatie/laravel-query-builder" target="_blank">spatie</a> package, as we
        are using it in our BaseModel.php file.</p>
    </div>
    @csrf
    <fieldset>
      <label for="name">Model name:</label>
      <input type="text" id="model_name" name="model_name" value="{!! old('model_name') !!}">

      <table class="relation_model">
        <tr class="relationdata">
            <td>
              <input type="text" name="relation_model[]" class="relation_model_name" placeholder="Relation model name">
            </td>
            <td>
              <select name="relation_ship[]" class="relation_ship">
                  <option value="hasOne">One To One</option>
                  <option value="hasMany">One To Many</option>
                  <option value="belongsToMany">Many To Many</option>
                  <option value="hasOneThrough">Has One Through</option>
                  <option value="hasManyThrough">Has many Through</option>
                  <option value="morphOne">One to One(Polymorphic)</option>
                  <option value="morphMany">One To Many(Polymorphic)</option>
                  <option value="morphToMany">Many To Many(Polymorphic)</option>
              </select>
            </td>
            <td>
              <input type="text" name="relation_another_model[]" class="relation_another_model relation_model_name" disabled placeholder="Relational second model">
            </td>
            <td>
              <input type="text" name="foreign_key[]" class="foreign_key relation_model_name" placeholder="Foreign key">
            </td>
            <td>
              <input type="text" name="local_key[]" class="local_key relation_model_name" placeholder="Local key">
            </td>
            <td>
              <button name="add_more" id="add_more" class="add_new_row add_more">+</button>
            </td>
        </tr>
      </table>
      <!-- <input type="text" id="model_name" name="model_name" value="Project" required=""> -->
      <span style="color:blue">If possible please enter your model name like, Project OR ProjectCategory.</span><br><br>
      <span style="color:red" class="modelNameError"></span><br><br>

      <input type="checkbox" id="add_model" name="add_model" value="1" @checked(old('add_model') ?? 'checked')><label class="light" for="add_model">Want to include model?</label><br>
      <input type="checkbox" id="add_migration" name="add_migration" value="1" @checked(old('add_migration') ?? 'checked')><label class="light" for="add_migration">Want to include migration?</label><br>

      <input type="hidden" name="soft_delete" value="0" checked="checked">
      <input type="checkbox" id="soft_delete" name="soft_delete" value="1" @checked(old('soft_delete') ?? 'checked')><label class="light" for="soft_delete">Want to include soft delete?</label><br>

      <input type="hidden" name="admin_crud" value="0" checked="checked">
      <input type="checkbox" id="admin_crud" name="admin_crud" value="1" @checked(old('admin_crud'))><label class="light" for="admin_crud">Want to include admin CRUD?</label><br>

      <input type="hidden" name="service" value="0" checked="checked">
      <input type="checkbox" id="service" name="service" value="1" @checked(old('service') ?? 'checked')><label class="light" for="service">Do you want service file?</label><br>

      <input type="hidden" name="notification" value="0" checked="checked">
      <input type="checkbox" id="notification" name="notification" value="1" @checked(old('notification'))><label class="light" for="notification">Do you want notification file?</label><br>


      <input type="hidden" name="resource" value="0" checked="checked">
      <input type="checkbox" id="resource" name="resource" value="1" @checked(old('resource') ?? 'checked')><label class="light" for="resource">Do you want resource file?</label><br>

      <input type="hidden" name="request" value="0" checked="checked">
      <input type="checkbox" id="request" name="request" value="1" @checked(old('request') ?? 'checked')><label class="light" for="request">Do you want request file?</label><br>

      <input type="hidden" name="trait" value="0" checked="checked">
      <input type="checkbox" id="trait" name="trait" value="1" @checked(old('trait') ?? 'checked')><label class="light" for="trait">Do you want below trait files?</label><br>
      <ul>
        <li>ApiResponse.php</li>
        <li>BaseModel.php</li>
        <li>BootModel.php</li>
        <li>PaginationTrait.php</li>
        <li>ResourceFilterable.php</li>
      </ul>

      <label for="name">Which method do you want to include?</label>

      <input type="checkbox" id="index" name="method[]" value="index" @if(is_array(old('method')) && in_array('index', old('method'))) checked @endif><label class="light" for="index">Index</label>
      <!-- <input type="checkbox" id="index" name="method[]" value="index" checked><label class="light" for="index">Index</label> -->

      <input type="checkbox" id="store" name="method[]" value="store" @if(is_array(old('method')) && in_array('store', old('method'))) checked @endif><label class="light" for="store">Store</label>

      <input type="checkbox" id="show" name="method[]" value="show" @if(is_array(old('method')) && in_array('show', old('method'))) checked @endif><label class="light" for="show">Show</label>

      <input type="checkbox" id="update" name="method[]" value="update" @if(is_array(old('method')) && in_array('update', old('method'))) checked @endif><label class="light" for="update">Update</label>

      <input type="checkbox" id="destroy" name="method[]" value="destroy" @if(is_array(old('method')) && in_array('destroy', old('method'))) checked @endif><label class="light" for="destroy">Destroy</label><br>

      <span style="color:red" class="methodsError"></span><br><br>

      <input type="hidden" name="migration_fields" value="0" checked="checked">
      <input type="checkbox" id="migration_fields" name="migration_fields" value="1" @checked(old('migration_fields'))><label class="light" for="migration_fields">Want to add more fields in the table?</label><br><br>

      <button type="button" class="btn btn-warning" id="addFieldButton" style="display:none" data-toggle="modal" data-target="#addFieldModal"><i class="fas fa-plus"> Add new field</i></button><br><br>
      <!-- <button type="button" class="btn btn-info btn-sm" id="hidden" data-toggle="modal" data-target="#addFieldModal"><i class="fas fa-plus"> Add new field</i></button><br><br> -->

      <input type="hidden" name="scope_fields" value="0" checked="checked">
      <input type="checkbox" id="scope_fields" name="scope_fields" @checked(old('scope'))><label class="light" for="scope_fields" id="label_scope_fields">Want to add scope in the model file?</label><br><br>

      <button type="button" class="btn btn-warning" id="addScopeButton" style="display:none" data-toggle="modal" data-target="#addScopeFieldModal"><i class="fas fa-plus"> Add new scope field</i></button><br><br>

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
                <td></td>
                <td></td>
              </tr>
              <tr data-row="2">
                <th scope="row">date_time_picker</th>
                <td>created_at</td>
                <td>required</td>
                <td></td>
                <td></td>
              </tr>
              <tr data-row="3">
                <th scope="row">date_time_picker</th>
                <td>updated_at</td>
                <td>required</td>
                <td></td>
                <td></td>
              </tr>
              <tr data-row="4">
                <th scope="row">date_time_picker</th>
                <td>deleted_at</td>
                <td>optional</td>
                <td></td>
                <td></td>
              </tr>
              <tr data-row="5">
                <th scope="row">integer</th>
                <td>created_by</td>
                <td>optional</td>
                <td></td>
                <td></td>
              </tr>
              <tr data-row="6">
                <th scope="row">integer</th>
                <td>updated_by</td>
                <td>optional</td>
                <td></td>
                <td></td>
              </tr>
              <tr data-row="7" data-column-name="deleted_by">
                <th scope="row">integer</th>
                <td>deleted_by</td>
                <td>optional</td>
                <td></td>
                <td><button type='button' class='btn btn-danger btn-delete'><i class='far fa-trash-alt btn-delete'></i></button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </fieldset>
    <button type="submit" class="btn btn-primary"><i class="fas fa-code"> Generate REST API files</i></button>
</form>
