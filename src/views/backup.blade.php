<fieldset ng:if='works'>
    <legend>Simple</legend>

    <div ng:repeat='work in works'>
        <div class="row">
            <div class="large-6 columns">
                <h6>@{{ work.title[0] }} &ndash; <a href='?artist=@{{ work.creator[0] }}'>@{{ work.creator[0] }}</a></h6>
                <b></b>
            </div>
            <div class="large-6 columns text-right">
                <ul class="button-group">
                    <li ng:if='work.workPid[0]'>
                        <a href="@{{ work.workPid[0] }}" class="tiny button">Work Pid</a>
                    </li>
                    <li ng:if='work.dataPid '>
                        <a href="@{{ work.dataPid }}" class="tiny button">Data Pid</a>
                    </li>
                    <li ng:if='work.representationPid '>
                        <a href="@{{ work.representationPid }}" class="tiny button">Representatie</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="large-8 columns">
                 <table>
                    <tbody>
                        <tr>
                            <td>ID</td>
                            <td>@{{ work.objectNumber }}</td>
                        </tr>
                        <tr>
                            <td>Objectnaam</td>
                            <td>
                                @{{ work.objectName[0] }}
                            </td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td>
                                @{{ work.date }}
                            </td>
                        </tr>
                        <tr>
                            <td>Date ISO8601</td>
                            <td>
                                @{{ work.dateIso8601[0] }}
                            </td>
                        </tr>
                        <tr>
                            <td>Provider</td>
                            <td>@{{ work.dataprovider }}</td>
                        </tr>
                        <tr>
                            <td>Custodian</td>
                            <td>
                                @{{ work.custodian }}<br/>
                                <a href='@{{ work.custodianWikidataPid }}'>Wikidata</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                <a href='@{{ work.Wikidata.website }}' ng:if='work.Wikidata.website'>Site</a><br/>
                                @{{ work.Wikidata.geo }}
                            </td>
                        </tr>
                        <tr>
                            <td>Custodian Pid</td>
                            <td>@{{ work.custodianIsilPid }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="large-4 columns text-right">
                <img ng:if='work.representationUrl' src='@{{ work.representationUrl }}' />
            </div>
        </div>
        <hr/>
    </div>
</fieldset>