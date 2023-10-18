@extends('layout')

@section('content')
    <div class="container">
        @include('render.type.main-form') <!-- Main generate form -->
        @include('render.type.edit-type-field-modal') <!-- Edit field modal -->
    </div>
@endsection
