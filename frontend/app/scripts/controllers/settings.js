'use strict';

angular.module('scpApp')
  .controller('MailServerCtrl', function ($scope,$http, $timeout, $rootScope, transformRequestAsFormPost, AppSettings) {

    $scope.dataReady = false;
    $scope.showAlert = false;
    //obtener los datos al cargar la vista
    $http.get(AppSettings.baseUrl + 'settings_drv/settings/?rnd' + Math.random())
      .success(function(response, status) {
        var data = response.data;
        if(status === 200){
          $scope.master = data;
          $scope.master.port = parseInt(data.port);
          $scope.master.use_ssl = data.use_ssl === 1;
          $scope.master.password2 = data.password;
          $scope.dataReady = true;
          $scope.reset();
        }else if(status === 404){
          $scope.dataReady = true;
          $scope.master = {}; 
          if(!data){
            data = response;
          }
          $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
        }else{
          $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
        }
      })
      .error(function(data, status){
        if(status === 404){
          $scope.dataReady = true;
          $scope.master = {};
        }else{
          $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
        }
      });

  	$scope.reset = function() {
        $scope.formObject = angular.copy($scope.master);
        $scope.htmlForm.$setPristine();
    };

    $scope.closeAlert = function(){
      $scope.showAlert = false;
    };

  	$scope.save = function(formObject){  

      if($scope.htmlForm && $scope.htmlForm.$valid){
        if(formObject.password !== formObject.password2){
          $rootScope.showMessage('Las contraseñas no coinciden');
        }else{
          var request = $http({
            method: 'PUT',
            url: AppSettings.baseUrl + 'settings_drv/settings/',
            transformRequest: transformRequestAsFormPost,
            data: {
              'smtp_server' : formObject.smtp_server,
              'port' : formObject.port,
              'use_ssl' : formObject.use_ssl ? true : false,
              'user' : formObject.user,
              'password' : formObject.password
            }
          });

      		request.success(function(response) {
            if(response.id){
              $scope.master = angular.copy($scope.formObject);
              $scope.htmlForm.$setPristine();
              $rootScope.showMessage('Configuración actualizada exitosamente', 5000, 'success');
            }else{
              $rootScope.showMessage('Falla al guardar configuración, reintente más tarde');
            }
    	  	})
    	  	.error(function(data){
              $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
    	  	});
        }
      }else{
        $rootScope.showMessage('Algunos campos obligatorios no están presentes');
      }
  	};  	
});