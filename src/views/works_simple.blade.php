<fieldset>
    <legend>Simple</legend>

    <i class='fa fa-spin fa-spinner hidden'></i>

    <div ng:if='simple_works'>
        <p>
            <kbd>@{{ simple_works.count | resultCount }}</kbd>
        </p>

        <ul>
            <li ng:repeat='work in simple_works.results' ng:click='viewDetails(work)'>
                @include('input::work_line')
            </li>
        </ul>
    </div>
</fieldset>