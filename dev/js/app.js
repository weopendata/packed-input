var app = angular.module('PIDdemonstrator', []);

app.controller('ResultCtrl', ['$scope', '$http', function($scope, $http) {

    $scope.simple_works = [];
    $scope.index_works = [];
    $scope.normalised_works = [];

    $scope.loadWorks = function(queryObject) {
        // Query
        $http({
            url: baseURL + "api/query",
            method: 'GET',
            params: queryObject
        }).success(function(data, status, headers, config) {
            $scope.normalised_works = data;

            $('.results-normalised .fa-spin').fadeOut();
            resetForm();
        }).error(function(data, status, headers, config) {
            // Show errors in alert
            var error = JSON.parse(data.responseText);
            error = error.error;
            alertify.error('An error occurred: ' + data.status + ' - ' + error.message);

            $('.results-normalised .fa-spin').fadeOut();
            resetForm();
        });

        var indexQueryObject = jQuery.extend({'type': 'index'}, queryObject);

        $http({
            url: baseURL + "api/query",
            method: 'GET',
            params: indexQueryObject
        }).success(function(data, status, headers, config) {

            var object = {};
            object.count = data.length;
            object.results = data;

            $scope.index_works = object;

            $('.results-index .fa-spin').fadeOut();
            resetForm();
        }).error(function(data, status, headers, config) {
            // Show errors in alert
            var error = JSON.parse(data.responseText);
            error = error.error;
            alertify.error('An error occurred: ' + data.status + ' - ' + error.message);

            $('.results-index .fa-spin').fadeOut();
            resetForm();
        });


        var simpleQueryObject = jQuery.extend({'type': 'simple'}, queryObject);

        $http({
            url: baseURL + "api/query",
            method: 'GET',
            params: simpleQueryObject
        }).success(function(data, status, headers, config) {
            var object = {};
            object.count = data.length;
            object.results = data;

            $scope.simple_works = object;

            $('.results-simple .fa-spin').fadeOut();
            resetForm();
        }).error(function(data, status, headers, config) {
            // Show errors in alert
            var error = JSON.parse(data.responseText);
            error = error.error;
            alertify.error('An error occurred: ' + data.status + ' - ' + error.message);

            $('.results-simple .fa-spin').fadeOut();
            resetForm();
        });

        $scope.$apply();
    };

    $scope.reset = function() {
        $scope.simple_works = [];
        $scope.index_works = [];
        $scope.normalised_works = [];
        $scope.$apply();
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