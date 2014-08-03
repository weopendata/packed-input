@extends('input::layouts.master')

@section('content')

<form>
    <div class="row">
        <div class="small-12 large-8 columns">
            <fieldset>
                <legend>Zoeken</legend>

                <p>Geef mij alle beschrijvingen van de werken met:</p>

                <div class="row">
                    <div class="small-4 columns">
                        <div class="form-enabler-switch switch tiny left inline">
                            <input id="artist_enabled" type="checkbox" checked="checked">
                            <label for="artist_enabled"></label>
                        </div>
                        <label for="artist" class="right inline">Vervaardiger</label>
                    </div>
                    <div class="small-8 columns">
                        <input type="text" id="artist" placeholder="Artiest" data-autocomplete='true' data-property='creator'>
                    </div>
                </div>
                <div class="row">
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
                {{--
                <div class="row">
                    <div class="small-4 columns">
                        <div class="form-enabler-switch switch tiny left inline">
                            <input id="institute_enabled" type="checkbox">
                            <label for="institute_enabled"></label>
                        </div>
                        <label for="institute" class="right inline">Bewaarinstelling</label>
                    </div>
                    <div class="small-8 columns">
                        <select id="institute" class='large-6'>
                            <option value="husker">Husker</option>
                            <option value="starbuck">Starbuck</option>
                            <option value="hotdog">Hot Dog</option>
                            <option value="apollo">Apollo</option>
                        </select>
                    </div>
                </div>
                --}}
                <div class="row">
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
                <div class="row">
                    <div class="small-8 small-offset-4 columns">
                        <button type="submit" class='button'>
                            Zoek
                        </button>
                        <span class='note' id='searchStatus'></span>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="small-12 large-4 columns">
            <fieldset>
                <legend>Opties</legend>

                <div class="row">
                    <div class="small-5 columns">
                        <label for="index" class="right">Gebruik de index</label>
                    </div>
                    <div class="small-7 columns">
                        <div class="switch tiny left round inline">
                            <input id="index" type="checkbox" checked>
                            <label for="index"></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="small-5 columns">
                        <label for="normalized" class="right">Normalisatie</label>
                    </div>
                    <div class="small-7 columns">
                        <div class="switch tiny left round inline">
                            <input id="normalized" type="checkbox" checked>
                            <label for="normalized"></label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="small-12 columns">
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
@stop