<fieldset>
    <legend>Simple</legend>

    <p>
        <kbd>@{{ simple_works.length | resultCount }}</kbd>
    </p>

    <div ng:if='simple_works'>
        <ul>
            <li ng:repeat='work in simple_works'>
                @include('input::work_line')
            </li>
        </ul>
    </div>
</fieldset>