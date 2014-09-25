@extends('input::layouts.master')

@section('content')

<form id='searchForm'>
    <div class="row">
        <div class="small-12 large-8 columns">
            <fieldset>
                <legend>Zoeken</legend>

                <div class="row parent">
                    <div class="small-4 columns">
                        <div class="form-enabler-switch switch tiny left inline">
                            <input id="artist_enabled" type="checkbox" checked="checked">
                            <label for="artist_enabled"></label>
                        </div>
                        <label for="artist" class="right inline">Vervaardiger</label>
                    </div>
                    <div class="small-8 columns">
                        <input type="text" id="artist" placeholder="Vervaardiger" data-autocomplete='true' data-property='creator'>
                    </div>
                </div>
                <div class="row parent">
                    <div class="small-4 columns">
                        <div class="form-enabler-switch switch tiny left inline">
                            <input id="name_enabled" type="checkbox">
                            <label for="name_enabled"></label>
                        </div>
                        <label for="name" class="right inline">Objectnaam</label>
                    </div>
                    <div class="small-8 columns">
                        <input type="text" id="name" placeholder="Objectnaam" data-autocomplete='true' data-property='objectName'>
                    </div>
                </div>

                <div class="row parent">
                    <div class="small-4 columns">
                        <div class="form-enabler-switch switch tiny left inline">
                            <input id="institution_enabled" type="checkbox">
                            <label for="institution_enabled"></label>
                        </div>
                        <label for="institution" class="right inline">Bewaarinstelling</label>
                    </div>
                    <div class="small-8 columns">
                        <select id="institution" class='large-6'  data-property='institution'>
                            <option value="Groeningemuseum">Groeningemuseum</option>
                            <option value="Lukas">Lukas</option>
                            <option value="MHKA">MHKA</option>
                            <option value="SMAK">SMAK</option>
                            <option value="VKC">VKC</option>
                            <option value="CVG">CVG</option>
                            <option value="Middelheimmuseum">Middelheimmuseum</option>
                            <option value="MSKGent">MSKGent</option>
                            <option value="KMSKA">KMSKA</option>
                            <option value="Muzee">MuZee</option>
                        </select>
                    </div>
                </div>

                <div class="row parent">
                    <div class="small-4 columns">
                        <div class="form-enabler-switch switch tiny left inline">
                            <input id="startDate_enabled" type="checkbox">
                            <label for="startDate_enabled"></label>
                        </div>
                        <label for="name" class="right inline">Van</label>
                    </div>
                    <div class="small-8 columns">
                        <div class="row">
                            <div class="small-4 columns text-right">
                                <input type="text" id="startDate" placeholder="Jaar" data-property='startDate'>
                            </div>
                            <div class="small-8 columns">

                                <div class="row">
                                    <div class="small-4 columns">
                                        <label for="name" class="right inline">Tot</label>
                                    </div>
                                    <div class="small-8 columns">
                                        <input type="text" id="endDate" placeholder="Jaar" data-property='endDate'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row parent">
                    <div class="small-4 columns">
                        <div class="form-enabler-switch switch tiny left inline">
                            <input id="detail_enabled" type="checkbox">
                            <label for="detail_enabled"></label>
                        </div>
                        <label for="detail" class="right inline">Inventarisnummer of titel</label>
                    </div>
                    <div class="small-8 columns">
                        <input type="text" id="detail" placeholder="Nummer of titel" data-autocomplete='true' data-property='objectDetail'>
                    </div>
                </div>
                <div class="row parent">
                    <div class="small-8 small-offset-4 columns">
                        <button type="submit" class='button'>
                            Zoek
                        </button>
                        <span class='note' id='searchStatus'></span>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="small-6 large-4 columns">
            <fieldset>
                <legend>Opties</legend>

                <div class="row">
                    <div class="small-4 columns">
                        <label for="autocomplete_enabled" class="right inline">Autocomplete</label>
                    </div>
                    <div class="small-8 columns">
                        <div class="switch tiny round left inline">
                            <input id="autocomplete_enabled" type="checkbox" checked>
                            <label for="autocomplete_enabled"></label>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
@stop