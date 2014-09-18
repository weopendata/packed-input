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
                    <div class="large-12 columns">
                        <h3>@{{ work_detail.title[0] }}</h3>
                        <p ng:if='enriched'>
                            [workPid] &ndash; <a href='@{{ work_detail.workPid[0] }}' target='_blank'>@{{ work_detail.workPid[0] }}</a><br/>
                            [dataPid] &ndash; <a href='@{{ work_detail.dataPid }}' target='_blank'>@{{ work_detail.dataPid }}</a><br/>
                            [representationPid] &ndash; <a href='@{{ work_detail.representationPid }}' target='_blank'>@{{ work_detail.representationPid }}</a>
                        </p>
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
                                        <span ng:if='!work_detail.objects[0].objectName[0] || !enriched'>
                                            @{{ work_detail.objectName.join('; ') }}
                                        </span>
                                        <div ng:if='enriched' ng:repeat='object in work_detail.objects' class='object'>
                                            <b>@{{ object.objectName[0] }}</b>
                                            <ul>
                                                <li ng:if='object.objectNameAatPid[0]'>AAT PID: <a href='@{{ object.objectNameAatPid[0] }}' target='_blank'>@{{ object.objectNameAatPid[0] }}</a><br/></li>
                                                <li ng:if='object.AAT.note[0]'>
                                                    @{{ object.AAT.note[0] }}
                                                </li>
                                                <li ng:if='object.AAT.preferredNames.de[0] || object.AAT.preferredNames.en[0] || object.AAT.preferredNames.fr[0] || object.AAT.preferredNames.nl[0]'>
                                                    <span ng:if='object.AAT.preferredNames.nl[0]'>
                                                        nl: @{{ object.AAT.preferredNames.nl[0] }} <br/>
                                                    </span>
                                                    <span ng:if='object.AAT.preferredNames.en[0]'>
                                                        en: @{{ object.AAT.preferredNames.en[0] }} <br/>
                                                    </span>
                                                    <span ng:if='object.AAT.preferredNames.fr[0]'>
                                                        fr: @{{ object.AAT.preferredNames.fr[0] }} <br/>
                                                    </span>
                                                    <span ng:if='object.AAT.preferredNames.de[0]'>
                                                        de: @{{ object.AAT.preferredNames.de[0] }}
                                                    </span>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Datering</td>
                                    <td>
                                        @{{ work_detail.dateStartPrecision[0] }} @{{ work_detail.dateStartValue[0] }}
                                        <span ng:if='work_detail.dateEndValue[0]'>- @{{ work_detail.dateEndPrecision[0] }} @{{ work_detail.dateEndValue[0] }}</span>
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
                                        <div ng:repeat='artist in work_detail.artists'>
                                            @{{ artist.creator[0] }}
                                            <ul ng:if='enriched && artist'>
                                                <li ng:if='artist.creatorRkdPid[0]'>
                                                    Rkd: <a href="@{{ artist.creatorRkdPid[0] }}" target='_blank'>@{{ artist.creatorRkdPid[0] }}</a><br/>
                                                    <span ng:if='artist.RKD.dateOfBirth[0] || artist.RKD.placeOfBirth[0]'>
                                                        ° @{{ artist.RKD.dateOfBirth[0] }} @{{ artist.RKD.placeOfBirth[0] }} &nbsp;&nbsp;&nbsp;&nbsp;
                                                    </span>
                                                    <span ng:if='artist.RKD.dateOfDeath[0] || artist.RKD.placeOfDeath[0]'>
                                                        &#8224; @{{ artist.RKD.dateOfDeath[0] }} @{{ artist.RKD.placeOfDeath[0] }}
                                                    </span>
                                                </li>
                                                <li ng:if='artist.creatorViafPid[0]'>
                                                    Viaf: <a href="@{{ artist.creatorViafPid[0] }}" target='_blank'>@{{ artist.creatorViafPid[0] }}</a><br/>
                                                    <span ng:if='artist.VIAF.dateOfBirth[0] || artist.VIAF.placeOfBirth[0]'>
                                                        ° @{{ artist.VIAF.dateOfBirth[0] }} @{{ artist.VIAF.placeOfBirth[0] }} &nbsp;&nbsp;&nbsp;&nbsp;
                                                    </span>
                                                    <span ng:if='artist.VIAF.dateOfDeath[0] || artist.VIAF.placeOfDeath[0]'>
                                                        &#8224; @{{ artist.VIAF.dateOfDeath[0] }} @{{ artist.VIAF.placeOfDeath[0] }}
                                                    </span>
                                                </li>
                                                <li ng:if='artist.creatorWikidataPid[0]'>
                                                    Wikidata: <a href="@{{ artist.creatorWikidataPid[0] }}" target='_blank'>@{{ artist.creatorWikidataPid[0] }}</a><br/>
                                                    <span ng:if='artist.Wikidata.dateOfBirth[0] || artist.Wikidata.placeOfBirth[0]'>
                                                        ° @{{ artist.Wikidata.dateOfBirth[0] }} @{{ artist.Wikidata.placeOfBirth[0] }} &nbsp;&nbsp;&nbsp;&nbsp;
                                                    </span>
                                                    <span ng:if='artist.Wikidata.dateOfDeath[0] || artist.Wikidata.placeOfDeath[0]'>
                                                        &#8224; @{{ artist.Wikidata.dateOfDeath[0] }} @{{ artist.Wikidata.placeOfDeath[0] }}
                                                    </span>
                                                </li>
                                                <li ng:if='artist.creatorOdisPid[0]'>
                                                    Odis: <a href="@{{ artist.creatorOdisPid[0] }}" target='_blank'>@{{ artist.creatorOdisPid[0] }}</a>
                                                </li>
                                            </ul>

                                            <div ng:if='enriched && artist.uniqueNameVariants[0]'>
                                                <br/>
                                                @{{ artist.uniqueNameVariants.join('; ') }}
                                            </div>

                                            <div ng:if='enriched && artist.RKD.literature[0]'>
                                                <br/>
                                                <b>Literatuurlijst</b>
                                                <ul>
                                                    <li ng:repeat='book in artist.RKD.literature'>
                                                        @{{ book }}
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
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