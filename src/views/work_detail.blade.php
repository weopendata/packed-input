<div id='detailCtrl' class="row" ng:controller='DetailCtrl'>
    <div class="large-12 columns">
        <fieldset>
            <legend>Detail</legend>

            <div ng:if='work_detail'>
                <div class="row">
                    <div class="large-6 columns">
                        <button class="tiny button" ng:click='goBack()'>&larr; Terug</button>
                    </div>
                    <div class="large-6 columns text-right">
                        <div class="switch round tiny inline right">
                            <input id="enriched" type="checkbox" ng:model='enriched'>
                            <label for="enriched"></label>
                        </div>
                        <label class='switchlabel'>Verrijkte data</label>
                    </div>
                </div>

                <div class="row">
                    <div class="large-8 columns">
                        <h3>@{{ work_detail.title[0] }} <small>@{{ work_detail.creator[0] }}</small></h3>
                        <p ng:if='enriched'>@{{ work_detail.workPid[0] }}</p>
                    </div>
                    <div class="large-4 columns text-right">
                        <ul class="button-group inline" ng:if='enriched'>
                            <li ng:if='work_detail.workPid[0]'>
                                <a href="@{{ work_detail.workPid[0] }}" class="tiny button">Work Pid</a>
                            </li>
                            <li ng:if='work_detail.dataPid '>
                                <a href="@{{ work_detail.dataPid }}" class="tiny button">Data Pid</a>
                            </li>
                            <li ng:if='work_detail.representationPid '>
                                <a href="@{{ work_detail.representationPid }}" class="tiny button">Representatie</a>
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
                                    <td>@{{ work_detail.objectNumber }}</td>
                                </tr>
                                <tr>
                                    <td>Objectnaam</td>
                                    <td>
                                        @{{ work_detail.objectName[0] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Date</td>
                                    <td>
                                        @{{ work_detail.date }}
                                    </td>
                                </tr>
                                <tr ng:if='enriched'>
                                    <td>Date ISO8601</td>
                                    <td>
                                        @{{ work_detail.dateIso8601[0] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Provider</td>
                                    <td>@{{ work_detail.dataprovider }}</td>
                                </tr>
                                <tr ng:if='enriched && work_detail.artists[0]'>
                                    <td>Artist</td>
                                    <td>
                                        <ul>
                                            <li ng:if='work_detail.artists[0].creatorRkdPid'>
                                                Rkd: @{{ work_detail.artists[0].creatorRkdPid[0] }}
                                            </li>
                                            <li ng:if='work_detail.artists[0].creatorViafPid'>
                                                Viaf: @{{ work_detail.artists[0].creatorViafPid[0] }}
                                            </li>
                                            <li ng:if='work_detail.artists[0].creatorWikidataPid'>
                                                Wikidata: @{{ work_detail.artists[0].creatorWikidataPid[0] }}
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr ng:if='enriched && work_detail.artists[0].RKD'>
                                    <td>Artist RKD</td>
                                    <td>
                                        <span ng:if='work_detail.artists[0].RKD.dateOfBirth[0] || work_detail.artists[0].RKD.placeOfBirth[0]'>
                                            ° @{{ work_detail.artists[0].RKD.dateOfBirth[0] }} @{{ work_detail.artists[0].RKD.placeOfBirth[0] }} <br/>
                                        </span>
                                        <span ng:if='work_detail.artists[0].RKD.dateOfDeath[0] || work_detail.artists[0].RKD.placeOfDeath[0]'>
                                            &#8224; @{{ work_detail.artists[0].RKD.dateOfDeath[0] }} @{{ work_detail.artists[0].RKD.placeOfDeath[0] }}
                                        </span>
                                    </td>
                                </tr>
                                <tr ng:if='enriched && work_detail.artists[0].uniqueNameVariants[0]'>
                                    <td>Artist alternative names</td>
                                    <td>@{{ work_detail.artists[0].uniqueNameVariants.join('; ') }}</td>
                                </tr>
                                <tr>
                                    <td>Custodian</td>
                                    <td>
                                        @{{ work_detail.custodian }}<br/>
                                        <span ng:if='enriched'>
                                            <a href='@{{ work_detail.custodianWikidataPid }}'>Wikidata</a>&nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href='@{{ work_detail.Wikidata.website }}' ng:if='work_detail.Wikidata.website'>Site</a><br/>
                                            @{{ work_detail.Wikidata.geo }}
                                        </span>
                                    </td>
                                </tr>
                                <tr ng:if='enriched'>
                                    <td>Custodian Pid</td>
                                    <td>@{{ work_detail.custodianIsilPid }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="large-4 columns text-right">
                        <img ng:if='work_detail.representationUrl && enriched' ng:src='@{{ work_detail.representationUrl }}' />
                    </div>
                </div>
                <hr/>
            </div>
        </fieldset>
    </div>
</div>