<!DOCTYPE html>
<html>
<style>
input[type=text], select {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

input[type=submit] {
  width: 100%;
  background-color: #4CAF50;
  color: white;
  padding: 14px 20px;
  margin: 8px 0;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

input[type=submit]:hover {
  background-color: #45a049;
}

div {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
}
</style>
<body>

<h2>Make files</h2>

<div>
<!-- @if (count($errors) >= 1)

@foreach ($errors->all() as $error)
{{ $error }}
@endforeach
@endif -->

  <form method="post" action="{{ url('make-files') }}">
    @csrf
    <b>Note: Model, Controller, Migration files and routes will be default generated.</b><br><br>

    <label for="model_name">Model name</label>
    <input type="text" id="model_name" name="model_name" placeholder="Your model name.." value="{{ old('model_name') }}">
    @if($errors->has('model_name'))<span style="color:red">{{ $errors->first('model_name') }}</span>@endif<br><br>

    <label for="soft_delete">Want to include soft delete?</label>
    <input type="hidden" name="soft_delete" value="0" checked="checked">
    <input type="checkbox" id="soft_delete" name="soft_delete" value="1" @checked(old('soft_delete') ?? 'checked')><br><br>

    <label for="admin_crud">Want to include admin CRUD?</label>
    <input type="hidden" name="admin_crud" value="0" checked="checked">
    <input type="checkbox" id="admin_crud" name="admin_crud" value="1" @checked(old('admin_crud'))><br><br>

    <b>Below questions are for API</b><br><br>
    <div>
        <label for="lname">Which method do you want to include?</label><br>
        <!-- <input type="hidden" name="create" value="0" checked="checked">
        <input type="checkbox" id="create" name="create" value="1">Create

        <input type="hidden" name="update" value="0" checked="checked">
        <input type="checkbox" id="update" name="update" value="1">Update

        <input type="hidden" name="destroy" value="0" checked="checked">
        <input type="checkbox" id="destroy" name="destroy" value="1">Delete

        <input type="hidden" name="show" value="0" checked="checked">
        <input type="checkbox" id="show" name="show" value="1">Show

        <input type="hidden" name="index" value="0" checked="checked">
        <input type="checkbox" id="index" name="index" value="1">List<br><br> -->

        <input type="checkbox" id="create" name="method[]" value="create" @if(is_array(old('method')) && in_array('create', old('method'))) checked @endif>Create

        <input type="checkbox" id="update" name="method[]" value="update" @if(is_array(old('method')) && in_array('update', old('method'))) checked @endif>Update

        <input type="checkbox" id="destroy" name="method[]" value="destroy" @if(is_array(old('method')) && in_array('destroy', old('method'))) checked @endif>Destroy

        <input type="checkbox" id="show" name="method[]" value="show" @if(is_array(old('method')) && in_array('show', old('method'))) checked @endif>Show

        <input type="checkbox" id="index" name="method[]" value="index" @if(is_array(old('method')) && in_array('index', old('method'))) checked @endif>Index<br>

        @if($errors->has('method'))<span style="color:red">{{ $errors->first('method') }}</span>@endif<br><br>

        <label for="lname">Do you want service file?</label>
        <input type="hidden" name="service" value="0" checked="checked">
        <input type="checkbox" id="service" name="service" value="1" @checked(old('service') ?? 'checked')><br><br>

        <label for="lname">Do you want resource file?</label>
        <input type="hidden" name="resource" value="0" checked="checked">
        <input type="checkbox" id="resource" name="resource" value="1" @checked(old('resource') ?? 'checked')><br><br>

        <label for="lname">Do you want request file?</label>
        <input type="hidden" name="request" value="0" checked="checked">
        <input type="checkbox" id="request" name="request" value="1" @checked(old('request') ?? 'checked')><br><br>
    </div>
    <!-- <label for="country">Country</label>
    <select id="country" name="country">
      <option value="australia">Australia</option>
      <option value="canada">Canada</option>
      <option value="usa">USA</option>
    </select> -->
  
    <input type="submit" value="Submit" class="btn btn-sm">
  </form>
</div>
<!-- <div>
    <b>Please create below files in respective folder as defined:</b><br>

    app/Models/Project.php<br>
    app/Http/Controllers/Api/V1/ProjectController.php<br>
    app/Http/Controllers/Api/V1/Admin/ProjectController.php<br>
    app/Http/Requests/Project/Upsert.php<br>
    app/Http/Resources/Project/Collection.php<br>
    app/Http/Resources/Project/Resource.php<br>
    app/Services/ProjectService.php<br>
    database/migrations/2023_01_20_000000_create_projects_table.php<br><br>

    <b>Please write below routes in api-v1.php</b><br>

    Route::apiResource('projects', V1\ProjectController::class)->only(['create', 'destroy', 'index']);<br><br>

    <b>Please write below routes in api-admin-v1.php</b><br>

    Route::apiResource('projects', Admin\ProjectController::class);<br><br>

</div> -->
</body>
</html>