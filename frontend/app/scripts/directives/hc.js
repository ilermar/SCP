'use strict';

angular.module('scpApp')
.directive('hcgeneral', [ function() {
    return {
        restrict: 'E',
        templateUrl: './views/hc/general.html',
        controller: 'HCGeneralCtrl',
        scope: {
            info: '=info'
        }
    };
} ] )
.directive('hcface', [ function() {
    return {
        restrict: 'E',
        templateUrl: './views/hc/face.html',
        controller: 'HCFaceCtrl',
        scope: {
            info: '=info'
        }
    };
} ] )
.directive('hclegs', [ function() {
    return {
        restrict: 'E',
        templateUrl: './views/hc/legs.html',
        controller: 'HCLegsCtrl',
        scope: {
            info: '=info'
        }
    };
} ] )
.directive('hchair', [ function() {
    return {
        restrict: 'E',
        templateUrl: './views/hc/hair.html',
        controller: 'HCHairCtrl',
        scope: {
            info: '=info'
        }
    };
} ] );