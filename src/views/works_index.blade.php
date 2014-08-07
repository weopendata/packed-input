<fieldset>
    <legend>Indexed</legend>

    <i class='fa fa-spin fa-spinner hidden'></i>

    <div ng:if='index_works'>
        <p>
            <kbd>@{{ index_works.count | resultCount }}</kbd>
        </p>

        <ul>
            <li ng:repeat='work in index_works.results' ng:click='viewDetails(work)'>
                @include('input::work_line')
            </li>
        </ul>
    </div>
</fieldset>