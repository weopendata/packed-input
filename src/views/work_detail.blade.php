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
                        <h3>@{{ work_detail.title[0] }}</h3>
                        <p ng:if='enriched'>@{{ work_detail.workPid[0] }}</p>
                    </div>
                    <div class="large-4 columns text-right">
                        <ul class="button-group inline" ng:if='enriched'>
                            <li ng:if='work_detail.workPid[0]'>
                                <a href="@{{ work_detail.workPid[0] }}" class="tiny button" target='_blank' ng:tooltip title='@{{ work_detail.workPid[0] }}'>Work</a>
                            </li>
                            <li ng:if='work_detail.dataPid '>
                                <a href="@{{ work_detail.dataPid }}" class="tiny button" target='_blank' ng:tooltip title='@{{ work_detail.dataPid }}'>Data</a>
                            </li>
                            <li ng:if='work_detail.representationPid '>
                                <a href="@{{ work_detail.representationPid }}" class="tiny button" target='_blank' ng:tooltip title='@{{ work_detail.representationPid }}'>Representation</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="large-8 columns">
                         <table>
                            <tbody>
                                <tr>
                                    <td>Object nummer</td>
                                    <td>@{{ work_detail.objectNumber }}</td>
                                </tr>
                                <tr>
                                    <td>Objectnaam</td>
                                    <td>
                                        @{{ work_detail.objectName[0] }}
                                        <ul ng:if='enriched && work_detail.objects[0]'>
                                            <li ng:if='work_detail.objects[0].objectNameAatPid[0]'>AAT PID: @{{ work_detail.objects[0].objectNameAatPid[0] }}<br/></li>
                                            <li ng:if='work_detail.objects[0].AAT.note[0]'>
                                                @{{ work_detail.objects[0].AAT.note[0] }}
                                            </li>
                                            <li ng:if='work_detail.objects[0].AAT.preferredNames.de[0] || work_detail.objects[0].AAT.preferredNames.en[0] || work_detail.objects[0].AAT.preferredNames.fr[0] || work_detail.objects[0].AAT.preferredNames.nl[0]'>
                                                <span ng:if='work_detail.objects[0].AAT.preferredNames.nl[0]'>
                                                    nl: @{{ work_detail.objects[0].AAT.preferredNames.nl[0] }} <br/>
                                                </span>
                                                <span ng:if='work_detail.objects[0].AAT.preferredNames.en[0]'>
                                                    en: @{{ work_detail.objects[0].AAT.preferredNames.en[0] }} <br/>
                                                </span>
                                                <span ng:if='work_detail.objects[0].AAT.preferredNames.fr[0]'>
                                                    fr: @{{ work_detail.objects[0].AAT.preferredNames.fr[0] }} <br/>
                                                </span>
                                                <span ng:if='work_detail.objects[0].AAT.preferredNames.de[0]'>
                                                    de: @{{ work_detail.objects[0].AAT.preferredNames.de[0] }}
                                                </span>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Datering</td>
                                    <td>
                                        @{{ work_detail.date }}
                                        <div ng:if='enriched && work_detail.dateIso8601[0]'>ISO8601: @{{ work_detail.dateIso8601[0] }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Data uitgever</td>
                                    <td>@{{ work_detail.dataprovider }}</td>
                                </tr>
                                <tr>
                                    <td>Vervaardiger</td>
                                    <td>
                                        @{{ work_detail.creator[0] }}
                                        <ul ng:if='enriched && work_detail.artists[0]'>
                                            <li ng:if='work_detail.artists[0].creatorRkdPid[0]'>
                                                Rkd: @{{ work_detail.artists[0].creatorRkdPid[0] }}<br/>
                                                <span ng:if='work_detail.artists[0].RKD.dateOfBirth[0] || work_detail.artists[0].RKD.placeOfBirth[0]'>
                                                    ° @{{ work_detail.artists[0].RKD.dateOfBirth[0] }} @{{ work_detail.artists[0].RKD.placeOfBirth[0] }} &nbsp;&nbsp;&nbsp;&nbsp;
                                                </span>
                                                <span ng:if='work_detail.artists[0].RKD.dateOfDeath[0] || work_detail.artists[0].RKD.placeOfDeath[0]'>
                                                    &#8224; @{{ work_detail.artists[0].RKD.dateOfDeath[0] }} @{{ work_detail.artists[0].RKD.placeOfDeath[0] }}
                                                </span>
                                            </li>
                                            <li ng:if='work_detail.artists[0].creatorViafPid[0]'>
                                                Viaf: @{{ work_detail.artists[0].creatorViafPid[0] }}<br/>
                                                <span ng:if='work_detail.artists[0].VIAF.dateOfBirth[0] || work_detail.artists[0].VIAF.placeOfBirth[0]'>
                                                    ° @{{ work_detail.artists[0].VIAF.dateOfBirth[0] }} @{{ work_detail.artists[0].VIAF.placeOfBirth[0] }} &nbsp;&nbsp;&nbsp;&nbsp;
                                                </span>
                                                <span ng:if='work_detail.artists[0].VIAF.dateOfDeath[0] || work_detail.artists[0].VIAF.placeOfDeath[0]'>
                                                    &#8224; @{{ work_detail.artists[0].VIAF.dateOfDeath[0] }} @{{ work_detail.artists[0].VIAF.placeOfDeath[0] }}
                                                </span>
                                            </li>
                                            <li ng:if='work_detail.artists[0].creatorWikidataPid[0]'>
                                                Wikidata: @{{ work_detail.artists[0].creatorWikidataPid[0] }}<br/>
                                                <span ng:if='work_detail.artists[0].Wikidata.dateOfBirth[0] || work_detail.artists[0].Wikidata.placeOfBirth[0]'>
                                                    ° @{{ work_detail.artists[0].Wikidata.dateOfBirth[0] }} @{{ work_detail.artists[0].Wikidata.placeOfBirth[0] }} &nbsp;&nbsp;&nbsp;&nbsp;
                                                </span>
                                                <span ng:if='work_detail.artists[0].Wikidata.dateOfDeath[0] || work_detail.artists[0].Wikidata.placeOfDeath[0]'>
                                                    &#8224; @{{ work_detail.artists[0].Wikidata.dateOfDeath[0] }} @{{ work_detail.artists[0].Wikidata.placeOfDeath[0] }}
                                                </span>
                                            </li>
                                            <li ng:if='work_detail.artists[0].creatorOdisPid[0]'>
                                                Odis: @{{ work_detail.artists[0].creatorOdisPid[0] }}
                                            </li>
                                        </ul>

                                        <div ng:if='enriched && work_detail.artists[0].uniqueNameVariants[0]'>
                                            <br/>
                                            @{{ work_detail.artists[0].uniqueNameVariants.join('; ') }}
                                        </div>
                                    </td>
                                </tr>
                                <tr ng:if='enriched && work_detail.artists[0].RKD.literature[0]'>
                                    <td>Literatuurlijst</td>
                                    <td>
                                        <ul>
                                            <li ng:repeat='book in work_detail.artists[0].RKD.literature'>
                                                @{{ book }}
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Bewaarinstelling</td>
                                    <td>
                                        @{{ work_detail.custodian }}<br/>
                                        <span ng:if='enriched'>
                                            Wiki: <a href='@{{ work_detail.custodianWikidataPid }}' target='_blank'>@{{ work_detail.custodianWikidataPid }}</a><br/>
                                            ISIL: <a href='@{{ work_detail.custodianIsilPid }}' target='_blank'>@{{ work_detail.custodianIsilPid }}</a><br/>
                                            Site: <a href='@{{ work_detail.Wikidata.website }}' target='_blank' ng:if='work_detail.Wikidata.website'>@{{ work_detail.Wikidata.website }}</a><br/>
                                            @{{ work_detail.Wikidata.geo }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="large-4 columns text-right image-container">
                        <img ng:if='work_detail.representationUrl && enriched' ng:src='@{{ work_detail.representationUrl }}' />
                    </div>
                </div>
                <hr/>
            </div>
        </fieldset>
    </div>
</div>