<!DOCTYPE html>
<html lang="en">
  @include('shared.head')
<body>

  <div class="loading" style="display:none">Loading&#8230;</div>
<div class="container">
  <h2>Generate files</h2>


  <!-- <div id='loading-image' style='display: none;'>
    <img src="{!! asset('images/loader.gif') !!}" width="500px" height="500px">
  </div> -->

  @include('render.make-file-form')
  
  <!-- Add field modal -->
  @include('render.add-field-modal')
  
  <!-- Edit field modal -->
  @include('render.edit-field-modal')
</div>
<script src="{!! asset('js/make-file.js') !!}"></script>
<script src="{!! asset('js/add-field.js') !!}"></script>
<script src="{!! asset('js/add-column-type-change.js') !!}"></script>
<script src="{!! asset('js/edit-field.js') !!}"></script>
<script src="{!! asset('js/edit-column-type-change.js') !!}"></script>
<script src="{!! asset('js/on-edit-or-delete-button.js') !!}"></script>
<script src="{!! asset('js/add-or-edit-more.js') !!}"></script>
</body>
</html>