<fieldset>
    <legend>Normalised</legend>

    <i class='fa fa-spin fa-spinner hidden'></i>

    <div ng:if='normalised_works'>
        <p>
            <kbd>@{{ normalised_works.count | resultCount }}</kbd>
        </p>

        <div ng:repeat='pid in normalised_works.results'>

            <p><strong>@{{ pid[0].workPid[0] }}</strong></p>
            <ul>
                <li ng:repeat='work in pid' ng:click='viewDetails(work)'>
                    @include('input::work_line')
                    &ndash; @{{ work.dataPid }}
                </li>
            </ul>

            <hr/>
        </div>
    </div>
</fieldset>