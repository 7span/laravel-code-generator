<!DOCTYPE html>
<html lang="en">
  @include('shared.head')
  <body>
    <div class="loading" style="display:none">Loading&#8230;</div>
    <div class="container">
      @include('render.main-form') <!-- Main generate form -->
      @include('render.add-field-modal') <!-- Add field modal -->
      @include('render.edit-field-modal') <!-- Edit field modal -->
    </div>
    @include('shared.footer')
  </body>
</html>