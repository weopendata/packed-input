<div class="row">
    <div class="large-12 columns" ng:app='PIDdemonstrator'>
        <div id='results' ng:controller='ResultCtrl'>
            <div class="row">
                <div class="large-3 columns">
                    @include('input::simple_works')
                </div>
                <div class="large-3 columns">
                    @include('input::index_works')
                </div>
                <div class="large-6 columns">
                    @include('input::normalised_works')
                </div>
            </div>
        </div>
    </div>
</div>