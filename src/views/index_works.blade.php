<fieldset>
    <legend>Indexed</legend>

    <p>
        <kbd>@{{ index_works.length | resultCount }}</kbd>
    </p>

    <div ng:if='index_works'>
        <ul>
            <li ng:repeat='work in index_works'>
                @include('input::work_line')
            </li>
        </ul>
    </div>
</fieldset>