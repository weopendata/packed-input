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
            console.log(data);
            $scope.normalised_works = data;
            resetForm();
        }).error(function(data, status, headers, config) {
            // Show errors in alert
            var error = JSON.parse(data.responseText);
            error = error.error;
            alertify.error('An error occurred: ' + data.status + ' - ' + error.message);

            resetForm();
        });

        var indexQueryObject = jQuery.extend({'type': 'index'}, queryObject);

        $http({
            url: baseURL + "api/query",
            method: 'GET',
            params: indexQueryObject
        }).success(function(data, status, headers, config) {
            console.log(data);
            $scope.index_works = data;
            resetForm();
        }).error(function(data, status, headers, config) {
            // Show errors in alert
            var error = JSON.parse(data.responseText);
            error = error.error;
            alertify.error('An error occurred: ' + data.status + ' - ' + error.message);

            resetForm();
        });


        var simpleQueryObject = jQuery.extend({'type': 'simple'}, queryObject);

        $http({
            url: baseURL + "api/query",
            method: 'GET',
            params: simpleQueryObject
        }).success(function(data, status, headers, config) {
            console.log(data);
            $scope.simple_works = data;
            resetForm();
        }).error(function(data, status, headers, config) {
            // Show errors in alert
            var error = JSON.parse(data.responseText);
            error = error.error;
            alertify.error('An error occurred: ' + data.status + ' - ' + error.message);

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
            status = 'Geen resultaten';
        }else if(count == 1){
            status = '1 resultaat';
        }else{
            status = count + ' resultaten';
        }

        return status + ' gevonden';
    };
});