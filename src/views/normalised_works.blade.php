<fieldset ng:if='normalised_works'>
    <legend>Normalised</legend>

    <p>
        <kbd>@{{ normalised_works.count | resultCount }}</kbd>
    </p>

    <div ng:repeat='pid in normalised_works.results'>

        <b>@{{ pid[0].workPid[0] }}</b>
        <ul>
            <li ng:repeat='work in pid'>
                @{{ work.objectNumber }} &ndash; @{{ work.title[0] }}  &ndash; @{{ work.dataprovider }}<br/>
                <a href='?artist=@{{ work.creator[0] }}'>@{{ work.creator[0] }}</a>
            </li>
        </ul>

        <hr/>
    </div>
</fieldset>