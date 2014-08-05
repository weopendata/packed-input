<div class="row">
    <div class="large-12 columns" ng:app='PIDdemonstrator'>
        <div id='results' ng:controller='ResultCtrl' class='hidden'>
            <div class="row">
                <div class="large-3 columns results results-simple">
                    @include('input::simple_works')
                </div>
                <div class="large-3 columns results results-index">
                    @include('input::index_works')
                </div>
                <div class="large-6 columns results results-normalised">
                    @include('input::normalised_works')
                </div>
            </div>
        </div>
    </div>
</div>