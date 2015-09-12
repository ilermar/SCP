'use strict';

angular.module('scpApp')
  .controller('NewDoctorCtrl', function ($scope, $rootScope, $http, $timeout, $route, $routeParams, $location, formInitialization, NewStudyService, transformRequestAsFormPost, AppSettings) {

    var studyScope = NewStudyService.getStudyScope();

    if(studyScope){

      $scope.formInstance = formInitialization;

      $scope.dataReady = false;
      $scope.showAlert = false;
      $scope.working = false;

      $scope.titleForm = 'NUEVO ' + $scope.formInstance.title.toUpperCase();
      $scope.master = { 
        state: '24',
        name : studyScope.selectedDoctor
      };
      $scope.dataReady = true;

    	$scope.reset = function() {
          $scope.formObject = angular.copy($scope.master);
      };

      $scope.closeAlert = function(){
        $scope.showAlert = false;
      };

    	$scope.save = function(formObject){
    		
    		if($scope.htmlForm && $scope.htmlForm.$valid){
          $scope.working = true;
          var request = $http({
            method: 'POST',
            url: AppSettings.baseUrl + $scope.formInstance.baseUrl,
            transformRequest: transformRequestAsFormPost,
            data: formObject
          });

  	  		request.success(function(response, status) {
              if(status === 200){
                studyScope.selectedDoctor = {
                  id : response.data.id,
                  name : formObject.name
                };
                NewStudyService.setStudyScope(studyScope);
                $scope.formObject = angular.copy($scope.master);

                $timeout(function(){ $location.path('/newstudy'); }, 1000);
                $rootScope.showMessage($scope.formInstance.title + ' registrado exitosamente', 1000, 'success');
                $scope.htmlForm.$dirty = false;
                $scope.htmlForm.$pristine = true;
                $scope.htmlForm.$submitted = false;
              }else{
                $scope.working = false;
                $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
              }
  		  	})
  		  	.error(function(data){
              $scope.working = false;
              $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
  		  	});
    		}else{
          $rootScope.showMessage('Algunos campos obligatorios no estan presentes');
        }
    	};

      $scope.open = function($event) {
        $event.preventDefault();
        $event.stopPropagation();

        $scope.opened = true;
      };

      $scope.dateOptions = {
        formatYear: 'yyyy',
        startingDay: 1
      };  	

      $scope.reset();
    } else {
      $location.path('/startstudy'); 
    }
  	
  });