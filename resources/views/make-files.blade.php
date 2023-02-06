<!DOCTYPE html>
<html lang="en">
  @include('shared.head')
<body>

<div class="container">
  <h2>Generate files</h2>
  @include('render.make-file-form')
  
  <!-- Add field modal -->
  @include('render.add-modal')
  
  <!-- Edit field modal -->
  @include('render.edit-modal')
</div>
<script src="{!! asset('js/custom.js') !!}"></script>
</body>
</html>