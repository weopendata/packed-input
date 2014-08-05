<fieldset>
    <legend>Normalised</legend>

    <p>
        <kbd>@{{ normalised_works.count | resultCount }}</kbd>
    </p>

    <div ng:if='normalised_works' ng:repeat='pid in normalised_works.results'>

        <p><strong>@{{ pid[0].workPid[0] }}</strong></p>
        <ul>
            <li ng:repeat='work in pid'>
                @include('input::work_line')
                &ndash; @{{ work.dataPid }}
            </li>
        </ul>

        <hr/>
    </div>
</fieldset>