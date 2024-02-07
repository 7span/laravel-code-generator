<!DOCTYPE html>
<html lang="en">
@include('shared.head')

<body>
    <div class="loading" style="display:none">Loading&#8230;</div>
    <div class="container">

        @include('render.rest.add-notification-modal') <!-- Add field modal -->
        @include('render.rest.add-seeder-modal')
        <h2>Generate files</h2>
        <p>Using this file generator you can generate the REST API files as well as GraphQL Type, Query and Mutation.
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
            @yield('content')
        </div>
    </div>
    @include('shared.footer')
</body>

</html>
