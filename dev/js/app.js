var app = angular.module('PIDdemonstrator', []);

app.controller('ResultCtrl', ['$scope', '$http', function($scope, $http) {

    $scope.artists = [];
    $scope.works = [];

    $scope.loadArtists = function(data) {
        console.log(data);
        $scope.artists = data;

        $scope.$apply();
    };

    $scope.loadWorks = function(data) {
        console.log(data);
        $scope.works = data;

        $scope.$apply();
    };

    $scope.reset = function() {
        $scope.artists = [];
        $scope.works = [];
        $scope.$apply();
    }

}]);