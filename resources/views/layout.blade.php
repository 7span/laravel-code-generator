<!DOCTYPE html>
<html lang="en">
@include('shared.head')

<body>
    <div class="loading" style="display:none">Loading&#8230;</div>
    <div class="container">
        {{-- @include('render.main-form') <!-- Main generate form --> --}}
        {{-- @include('render.add-field-modal') <!-- Add field modal --> --}}
        @include('render.add-notification-modal') <!-- Add field modal -->
        {{-- @include('render.edit-field-modal') <!-- Edit field modal --> --}}
        {{-- @include('render.add-scope-field-modal') <!-- Add scope field modal --> --}}
        <h2>Generate files</h2>
        <p>Using this file generator you can generate the REST API files as well as GraphQL types, query and mutation.
        </p>

        <ul class="nav nav-tabs">
            @php
                $activeRoute = Route::currentRouteName();
            @endphp
            <li class="{{ $activeRoute == 'rest' ? 'active' : '' }}">
                <a href="{{ route('rest') }}">Rest API
                    files</a>
            </li>
            <li class="{{ $activeRoute == 'graphql-type' ? 'active' : '' }}">
                <a href="{{ route('graphql-type') }}">GraphQL Type</a>
            </li>
            <li class="{{ $activeRoute == 'graphql-query' ? 'active' : '' }}">
                <a href="{{ route('graphql-query') }}">GraphQL Query</a>
            </li>
            <li class="{{ $activeRoute == 'graphql-mutation' ? 'active' : '' }}">
                <a href="{{ route('graphql-mutation') }}">GraphQL Mutation</a>
            </li>
        </ul>
        <div class="tab-content">
            <br>
            <p style="color:red">To use this CRUD generator you first need to install <a
                    href="https://github.com/spatie/laravel-query-builder" target="_blank">spatie</a> package, as we
                are using it in our BaseModel.php file.</p>

            @yield('content')
        </div>
    </div>
    @include('shared.footer')
</body>

</html>
