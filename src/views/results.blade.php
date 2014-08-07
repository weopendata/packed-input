<div class="row">
    <div class="large-12 columns">
        <div id='results' ng:controller='ResultCtrl' class='hidden'>
            <div class="row">
                <div class="large-3 columns results results-simple">
                    @include('input::works_simple')
                </div>
                <div class="large-3 columns results results-index">
                    @include('input::works_index')
                </div>
                <div class="large-6 columns results results-normalised">
                    @include('input::works_normalised')
                </div>
            </div>
        </div>
    </div>
</div>