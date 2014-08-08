var app = angular.module('PIDdemonstrator', []);

$('#detailCtrl').hide();

app.controller('ResultCtrl', ['$scope', '$http', '$q', function($scope, $http, $q) {

    // Ability to cancel requests
    var canceler = $q.defer();

    $scope.simple_works = [];
    $scope.index_works = [];
    $scope.normalised_works = [];

    $scope.loadWorks = function(queryObject) {
        // Cancel current requests
        canceler.resolve();
        canceler = $q.defer();

        // Query
        $http({
            url: baseURL + "query",
            method: 'GET',
            params: queryObject,
            timeout: canceler.promise
        }).success(function(data, status, headers, config) {
            // console.log(data);
            $scope.normalised_works = data;

            $('.results-normalised .fa-spin').stop().fadeOut();
            resetForm();
        }).error(function(data, status, headers, config) {
            if(data){
                // Show errors in alert
                var error = JSON.parse(data.responseText);
                error = error.error;
                alertify.error('An error occurred: ' + data.status + ' - ' + error.message);

                $('.results-normalised .fa-spin').stop().fadeOut();
                resetForm();
            }
        });

        var indexQueryObject = jQuery.extend({'type': 'index'}, queryObject);

        $http({
            url: baseURL + "query",
            method: 'GET',
            params: indexQueryObject,
            timeout: canceler.promise
        }).success(function(data, status, headers, config) {
            var object = {};
            object.count = data.length;
            object.results = data;

            $scope.index_works = object;

            $('.results-index .fa-spin').stop().fadeOut();
            resetForm();
        }).error(function(data, status, headers, config) {
            if(data){
                // Show errors in alert
                var error = JSON.parse(data.responseText);
                error = error.error;
                alertify.error('An error occurred: ' + data.status + ' - ' + error.message);

                $('.results-index .fa-spin').stop().fadeOut();
                resetForm();
            }
        });


        var simpleQueryObject = jQuery.extend({'type': 'simple'}, queryObject);

        $http({
            url: baseURL + "query",
            method: 'GET',
            params: simpleQueryObject,
            timeout: canceler.promise
        }).success(function(data, status, headers, config) {
            var object = {};
            object.count = data.length;
            object.results = data;

            $scope.simple_works = object;

            $('.results-simple .fa-spin').stop().fadeOut();
            resetForm();
        }).error(function(data, status, headers, config) {
            if(data){
                // Show errors in alert
                var error = JSON.parse(data.responseText);
                error = error.error;
                alertify.error('An error occurred: ' + data.status + ' - ' + error.message);

                $('.results-simple .fa-spin').stop().fadeOut();
                resetForm();
            }
        });

        $scope.$apply();
    };

    $scope.viewDetails = function(e) {
        angular.element($('#detailCtrl')).scope().viewDetails(e);
    }

    $scope.reset = function() {
        $('#detailCtrl').hide();

        // Clear results
        $scope.simple_works = [];
        $scope.index_works = [];
        $scope.normalised_works = [];
        $scope.$apply();
    }

}]);


app.controller('DetailCtrl', ['$scope', function($scope) {

    $scope.enriched = true;
    $scope.work_detail = {};

    $scope.goBack = function() {
        $('#detailCtrl').hide();
        $('#results').show();
        $('#searchForm').show();
        $scope.work_detail = {};
    }

    $scope.viewDetails = function(e) {
        console.log(e);
        $('#searchForm').hide();
        $('#results').hide();
        $('#detailCtrl').show();
        $scope.work_detail = e;
    }
}]);

/**
 * Result count filter
 */
app.filter('resultCount', function() {
    return function(count) {

        if(count == 0 || count == undefined){
            status = 'Geen werken';
        }else if(count == 1){
            status = '1 werk';
        }else{
            status = count + ' werken';
        }

        return status + ' gevonden';
    };
});