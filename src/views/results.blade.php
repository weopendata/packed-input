<div class="row">
    <div class="large-12 columns" ng:app='PIDdemonstrator'>
        <div id='results' ng:controller='ResultCtrl'>
            <div class="row">
                <div class="large-4 columns">
                    @include('input::works')
                </div>
                <div class="large-4 columns">
                </div>
                <div class="large-4 columns">
                    @include('input::normalised_works')
                </div>
            </div>
        </div>
    </div>
</div>