<fieldset ng:if='artists'>
    <legend>Resultaten</legend>

    <div ng:repeat='artist in artists'>
        <div class="row">
            <div class="large-6 columns">
                <b>@{{ artist.creator[0] }}</b>
                <p>@{{ artist.works.length }} werk(en)</p>
            </div>
            <div class="large-6 columns text-right">
                <ul class="button-group">
                    <li ng:if='artist.creatorViafPid[0]'>
                        <a href="@{{ artist.creatorViafPid[0] }}" class="tiny button">VIAF</a>
                    </li>
                    <li ng:if='artist.creatorRkdPid[0]'>
                        <a href="@{{ artist.creatorRkdPid[0] }}" class="tiny button">RKD</a>
                    </li>
                    <li ng:if='artist.creatorWikidataPid[0]'>
                        <a href="@{{ artist.creatorWikidataPid[0] }}" class="tiny button">WIKIDATA</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="large-4 columns">
                 <table>
                    <tbody>
                        <tr>
                            <td>ID</td>
                            <td>@{{ artist.creatorId[0] }}</td>
                        </tr>
                        <tr>
                            <td>Provider</td>
                            <td>@{{ artist.dataprovider }}</td>
                        </tr>
                        <tr>
                            <td>Naam varianten</td>
                            <td>
                                <div ng:repeat='name in artist.uniqueNameVariants'>
                                    @{{ name }}<br/>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="large-8 columns">
                <fieldset ng:if='artist.works'>
                    <legend>Werken</legend>
                    <div ng:repeat='work in artist.works'>
                        <h6>@{{ work.title[0] }}</h6>
                        <img ng:if='work.representationUrl' src='@{{ work.representationUrl }}' />
                        <hr/>
                    </div>
                </fieldset>
            </div>
        </div>
        <hr/>
    </div>
</fieldset>