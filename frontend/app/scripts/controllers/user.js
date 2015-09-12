'use strict';

angular.module('scpApp')
  .controller('UserFormCtrl', function ($scope, $rootScope, $http, $timeout, $route, $routeParams, $modal, transformRequestAsFormPost, AppSettings) {
    var doctorModalInstance = null;
    var patientModalInstance = null;

    $scope.dataReady = false;


    if($rootScope.idObjectToEdit){
      $scope.titleForm = 'EDITAR USUARIO';
      var idObject = $rootScope.idObjectToEdit;
      $rootScope.idObjectToEdit = undefined;
      $http.get(AppSettings.baseUrl + 'users_drv/user/?id=' + idObject)
        .success(function(response, status) {
          if(status === 200){
            $scope.master = response.data;
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
        $scope.titleForm = 'NUEVO USUARIO';
        $scope.master = { 
          profile : '',
          status : '1'
        };
        $scope.dataReady = true;
    }

  	$scope.reset = function() {
        $scope.formObject = angular.copy($scope.master);
    };

  	$scope.save = function(formObject){
  		
  		if($scope.htmlForm && $scope.htmlForm.$valid){

        var httpMethod = $scope.master.id ? 'PUT' : 'POST';

        var request = $http({
            method: httpMethod,
            url: AppSettings.baseUrl + 'users_drv/user/',
            transformRequest: transformRequestAsFormPost,
            data: formObject
        });

	  		request.success(function(response, status) {
          if(status === 200){
            if($scope.master.id){
              $scope.master = angular.copy($scope.formObject);
              $rootScope.showMessage('Usuario actualizado exitosamente', 5000, 'success');
            }else{
              $scope.formObject = angular.copy($scope.master);
              $rootScope.showMessage('Usuario registrado exitosamente', 5000, 'success');
            }
            $scope.htmlForm.$dirty = false;
            $scope.htmlForm.$pristine = true;
            $scope.htmlForm.$submitted = false;
          }else{
            $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
          }
		  	})
		  	.error(function(data){
            $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
		  	});
  		}else{
        $rootScope.showMessage( 'Algunos campos obligatorios no estan presentes');
      }
  	};

    $scope.userBaseSelection = function () {
      $scope.formObject.idExternalEntity = null;
      if($scope.formObject.profile === '4' ){
        $scope.selectDoctor();
      } else if($scope.formObject.profile === '5' ){
        $scope.selectPatient();
      }
    };

     $scope.selectDoctor = function () {
        doctorModalInstance = $modal.open({
          templateUrl: 'selectdoctor.html',
          controller: 'ModalDoctorCtrl'
        });

        doctorModalInstance.result.then(function (selectedItem) {
          if(selectedItem){
            if(selectedItem.id){
              $http.get(AppSettings.baseUrl + 'doctors_drv/doctor/?id=' + selectedItem.id)
              .success(function(response) {
                  var data = response.data;
                  $scope.formObject.idExternalEntity = data.id;
                  $scope.formObject.name = data.name;
                  $scope.formObject.email = data.email;
                  $scope.formObject.phone_number = '';
                  if(data.phone_number_1){
                    $scope.formObject.phone_number += data.phone_number_1 + ';';
                  }
                  if(data.phone_number_2){
                    $scope.formObject.phone_number += data.phone_number_2 + ';';
                  }
                  if(data.phone_number_3){
                    $scope.formObject.phone_number += data.phone_number_3 + ';';
                  }
              });
            } else {
              $scope.formObject.profile = '';
            }
          }
        }, function () {
          $scope.formObject.profile = '';
        });
      };

      $scope.selectPatient = function () {
        patientModalInstance = $modal.open({
          templateUrl: 'selectpatient.html',
          controller: 'ModalPatientCtrl'
        });

        patientModalInstance.result.then(function (selectedItem) {
          if(selectedItem){
            if(selectedItem.id){
              $http.get(AppSettings.baseUrl + 'patients_drv/patient/?id=' + selectedItem.id)
              .success(function(response) {
                  var data = response.data;
                  $scope.formObject.idExternalEntity = data.id;
                  $scope.formObject.name = data.name;
                  $scope.formObject.email = data.email;
                  $scope.formObject.phone_number = '';
                  if(data.phone_number_1){
                    $scope.formObject.phone_number += data.phone_number_1 + ';';
                  }
                  if(data.phone_number_2){
                    $scope.formObject.phone_number += data.phone_number_2 + ';';
                  }
                  if(data.phone_number_3){
                    $scope.formObject.phone_number += data.phone_number_3 + ';';
                  }
                
              });
            } else {
              $scope.formObject.profile = '';
            }
          }
        }, function () {
          $scope.formObject.profile = '';
        });
      };

      $scope.reset();
  	
  });

angular.module('scpApp')
  .controller('ChangePwdCtrl', function ($scope, $rootScope, $timeout, $http, $location, transformRequestAsFormPost, AppSettings) {

    $scope.change = function(formObject){
      
      if($scope.htmlForm && $scope.htmlForm.$valid){

        if(formObject.newpwd !== formObject.newpwd2){
          $rootScope.showMessage( 'La contraseña nueva y su confirmación no coinciden');
        }else if(formObject.newpwd === formObject.currentpwd){
          $rootScope.showMessage( 'La contraseña nueva y la actual deben ser diferentes');
        }else {
          var request = $http({
              method: 'POST',
              url: AppSettings.baseUrl + 'login_drv/change/',
              transformRequest: transformRequestAsFormPost,
              data: {
                'currentpwd' : formObject.currentpwd,
                'newpwd' : formObject.newpwd,
                'email' : $rootScope.currentUser.email
              }
          });

          request.success(function(response, status) {
            if(status === 200){
              $rootScope.showMessage('Cambio de contraseña exitoso', 1000, 'success', function(){
                $location.path('/main');
              });
            }else{
              $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
            }
          })
          .error(function(data){
              $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
          });
        }
      }else{
        $rootScope.showMessage( 'Algunos campos obligatorios no estan presentes');
      }
    };

    $scope.dataReady = true;
});
