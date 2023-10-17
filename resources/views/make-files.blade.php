<!DOCTYPE html>
<html lang="en">
@include('shared.head')

<body>
    <div class="loading" style="display:none">Loading&#8230;</div>
    <div class="container">
        @include('render.main-form') <!-- Main generate form -->
        @include('render.add-field-modal') <!-- Add field modal -->
        @include('render.add-notification-modal') <!-- Add field modal -->
        @include('render.edit-field-modal') <!-- Edit field modal -->
        @include('render.add-scope-field-modal') <!-- Add scope field modal -->
        <h2>Generate files</h2>
        <p>Using this file generator you can generate the REST API files as well as GraphQL types, query and mutation.
        </p>

        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home">Rest API files</a></li>
            <li><a data-toggle="tab" href="#menu1">GraphQL Type</a></li>
            <li><a data-toggle="tab" href="#menu2">GraphQL Query</a></li>
            <li><a data-toggle="tab" href="#menu3">GraphQL Mutation</a></li>
        </ul>
        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <!-- <h3>Rest API files</h3> -->
                <br>
                <p style="color:red">To use this CRUD generator you first need to install <a
                        href="https://github.com/spatie/laravel-query-builder" target="_blank">spatie</a> package, as we
                    are using it in our BaseModel.php file.</p>
                @include('tabs.rest')
            </div>
            <div id="menu1" class="tab-pane fade">
                <!-- <h3>Menu 1</h3> -->
                @include('tabs.type')
            </div>
            <div id="menu2" class="tab-pane fade">
                <!-- <h3>Menu 2</h3> -->
                @include('tabs.query')
            </div>
            <div id="menu3" class="tab-pane fade">
                <!-- <h3>Menu 3</h3> -->
                @include('tabs.mutation')
            </div>
        </div>
    </div>
    @include('shared.footer')
</body>

</html>
