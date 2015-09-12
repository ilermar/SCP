'use strict';

angular.module('scpApp')
  .controller('SimpleFormCtrl', function ($scope,$rootScope, $http, $routeParams, $location,formInitialization, transformRequestAsFormPost, AppSettings, NewStudyService) {

    $scope.formInstance = formInitialization;

    $scope.dataReady = false;
    $scope.working = false;

    if($rootScope.idObjectToEdit){
      $scope.titleForm = 'EDITAR ' + $scope.formInstance.title.toUpperCase();
      var idObject = $rootScope.idObjectToEdit;
      $rootScope.idObjectToEdit = undefined;
      $http.get(AppSettings.baseUrl + $scope.formInstance.baseUrl + '?id=' + idObject)
        .success(function(response, status) {
            if(status === 200){
              var data = response.data;
              $scope.master = data;
              $scope.reset();
              $scope.dataReady = true;
            }else{
              $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
            }
        })
        .error(function(data){
            $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
        });
    } else {
        $scope.titleForm = 'NUEVO ' + $scope.formInstance.title.toUpperCase();
        $scope.master = { 
          state: '24' 
        };
        $scope.dataReady = true;
    }

  	$scope.reset = function() {
        $scope.formObject = angular.copy($scope.master);
    };

  	$scope.save = function(formObject){
  		
  		if($scope.htmlForm && $scope.htmlForm.$valid){
        var httpMethod = $scope.master.id ? 'PUT' : 'POST';

        var formFields = $scope.formInstance.requiredFields;

        if(formFields && formFields.length){
          for(var index = 0; index < formFields.length ; index++){
            if(!formObject[formFields[index]]){
              formObject[formFields[index]] = '';
            }
          }
        }

        $scope.working = true;
        var request = $http({
            method: httpMethod,
            url: AppSettings.baseUrl + $scope.formInstance.baseUrl,
            transformRequest: transformRequestAsFormPost,
            data: formObject
        });

	  		request.success(function(response, status) {
          $scope.working = false;
          if(status === 200){
            if($scope.master.id){
              $scope.master = angular.copy($scope.formObject);
              $rootScope.showMessage($scope.formInstance.title + ' actualizado exitosamente', 5000, 'success');
            }else{
              $scope.formObject = angular.copy($scope.master);
              $rootScope.showMessage($scope.formInstance.title + ' registrado exitosamente', 5000, 'success');
            }
            $scope.htmlForm.$dirty = false;
            $scope.htmlForm.$pristine = true;
            $scope.htmlForm.$submitted = false;
          }else{
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

    $scope.showHC = function(){
      $scope.working = true;
      NewStudyService.setStudyScope({
        'patient_name' : $scope.formObject.name,
        'patient_age' : $scope.formObject.age,
        'patient_id' : $scope.formObject.id,
        'fromPatient' : true
      });
      $location.path('/hc');
    };

    $scope.dateOptions = {
      formatYear: 'yyyy',
      startingDay: 1
    };  	
  	
  });

angular.module('scpApp')
  .controller('SimpleSearchCtrl', function ($scope, $rootScope, $http, $location, $modal, formInitialization, AppSettings, NewStudyService) {
      $scope.formInstance = formInitialization;

      $scope.titleForm    = 'BUSCAR ' + $scope.formInstance.title.toUpperCase();

      $scope.prevCalendar = null;
      $scope.working      = false;
      
      $scope.instructions     = 'Use los filtros para simplicar su búsqueda. Ningún filtro es obligatorio';
      $scope.filterSelection  = true;


  		$scope.search = function(filters){
        if(!filters){
          filters = {};
        }
        $scope.working = true;
        $scope.selectedObject = null;
        filters.randomParam = Math.random();
        $http.get(AppSettings.baseUrl + $scope.formInstance.query, { params : filters})
        .success(function(response, status) {
            if(status === 200){
              $scope.instructions = 'Use doble click para editar un ' + $scope.formInstance.title.toLowerCase();
              $scope.filterSelection = false;
              $scope.objectList = response.data;
              $scope.working = false;
            }else{
              $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
            }
        })
        .error(function(data){
            $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
            $scope.working = false;
        });
  	   };

      $scope.selectObject = function(object){
        $scope.selectedObject = object;
      };

      $scope.edit = function(idObject){
        $rootScope.idObjectToEdit = idObject;
        $location.path($scope.formInstance.edit);
      };

      $scope.delete = function(filters, idObject, title, msg){
        $rootScope.idObjectToDelete = idObject;
        var confirmdialog = $modal.open({
          templateUrl: 'confirmdialog.html',
          controller: 'ModalConfirmCtrl',
          size: 'mm',
          resolve : {
            dialogInfo : function(){
              return {'title':title, 'message': '¿Confirma que desea eliminar el registro seleccionado' + msg + '?'};
            }
          }
        });

        confirmdialog.result.then(function (result) {
          if(result){
            $scope.working  = true;            
            var idObj       = $rootScope.idObjectToDelete;
            console.log( idObj );
            $rootScope.idObjectToDelete = undefined;
            $http.delete( AppSettings.baseUrl + $scope.formInstance.baseUrl + idObj )
            .success(function() {
              $scope.search(filters);
            })
            .error(function(){
              $scope.search(filters);  
            });
          }
        }, function () {
        });
      };

      $scope.dateOptions = {
        formatYear: 'yyyy',
        startingDay: 1
      };  

      $scope.openCalendar = function($event, index) {
        $event.preventDefault();
        $event.stopPropagation();
        if($scope.prevCalendar)
        {
          $scope[$scope.prevCalendar] = false;
        }
        $scope['calendar_' + index] = true;
        $scope.prevCalendar = 'calendar_' + index;
      };

      $scope.showHC = function(){
        NewStudyService.setStudyScope({
          'patient_name' : $scope.selectedObject.name,
          'patient_age' : $scope.selectedObject.age,
          'patient_id' : $scope.selectedObject.id
        });
        $location.path('/hc');
      };


  });