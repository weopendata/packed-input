<!DOCTYPE html>
<html lang='en'>
    <head profile="http://dublincore.org/documents/dcq-html/">
        <title>PID demonstrator</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/jquery-ui.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.21/angular.min.js"></script>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel='stylesheet' href='{{ URL::to("packages/packed-input/css/style.css") }}?v={{ Config::get('app.version', 4) }}' type='text/css'/>
    </head>

    <body ng:app='PIDdemonstrator'>
        <div class="row header">
            <div class="large-6 columns">
                <img src='{{ URL::to("packages/packed-input/img/logo.png") }}' width="215" class=''/>
            </div>
            <div class="large-6 columns text-right">
                Statistics:
                <a href="/api/stats/artists" class="tiny button">Artists</a>
                <a href="/api/stats/objects" class="tiny button">Objects</a>
                <a href="/api/stats/works" class="tiny button">Works</a>
                <a href="/api/stats/normalisedworks" class="tiny button">Normalised works</a>
            </div>
        </div>

        @yield('content')

        @include('input::results')
        @include('input::work_detail')
    </body>
    <script>
        var baseURL = '{{ URL::to('api') }}/';
    </script>
    <script src="{{ URL::to("packages/packed-input/js/script.min.js") }}"></script>
</html>