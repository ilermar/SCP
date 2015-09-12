'use strict';

angular.module('scpApp')
.directive('citology', [ function() {
    return {
        restrict: 'E',
        templateUrl: './views/studies/citology.html',
        controller: 'CitologyCtrl',
        scope: {
            info: '=info'
        }
    };
} ] )
.directive('androscopy', [ function() {
    return {
        restrict: 'E',
        templateUrl: './views/studies/androscopy.html',
        controller: 'AndroscopyCtrl',
        scope: {
            info: '=info'
        }
    };
} ] )
.directive('colposcopy', [ function() {
    return {
        restrict: 'E',
        templateUrl: './views/studies/colposcopy.html',
        controller: 'ColposcopyCtrl',
        scope: {
            info: '=info'
        }
    };
} ] )
.directive('histeroscopy', [ function() {
    return {
        restrict: 'E',
        templateUrl: './views/studies/histeroscopy.html',
        controller: 'HisteroscopyCtrl',
        scope: {
            info: '=info'
        }
    };
} ] )
.directive('histopatology', [ function() {
    return {
        restrict: 'E',
        templateUrl: './views/studies/histopatology.html',
        controller: 'HistopatologyCtrl',
        scope: {
            info: '=info'
        }
    };
} ] )
.directive('specials', [ function() {
    return {
        restrict: 'E',
        templateUrl: './views/studies/specials.html',
        controller: 'SpecialsCtrl',
        scope: {
            info: '=info'
        }
    };
} ] )
.directive('inmuno', [ function() {
    return {
        restrict: 'E',
        templateUrl: './views/studies/inmuno.html',
        controller: 'InmunoCtrl',
        scope: {
            info: '=info'
        }
    };
} ] )
.directive("regExpRequire", [function() {
    return {
        restrict: "A",
        link: function(scope, elem, attrs) {
            elem.on("keypress", function(event) {
                var regularExpresion = eval(elem.attr("reg-exp-require"));
                var char = String.fromCharCode(event.which)
                if(!regularExpresion.test(elem.val() + char)){
                    event.preventDefault();
                }
            })
        }
    }

}]);