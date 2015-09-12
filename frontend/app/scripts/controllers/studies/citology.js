angular.module('scpApp')
  .controller('CitologyCtrl', function ($scope, CitoTemplate, $modal) {
    $scope.templates = [
    ];

    $scope.loadForm = function(){
    	CitoTemplate.getAll(function(data){
    		$scope.templates = data;
    	});
    }

    $scope.loadTemplate = function(){
    	if($scope.templateSelection === '-1'){
    		$scope.startSaveTemplate();
    	}else if($scope.templateSelection !== ''){
    		CitoTemplate.get($scope.templateSelection, function(data){
	    		$scope.info = data.formObject;
	    	});
    	}
    }

    $scope.startSaveTemplate = function(){
    	var confirmdialog = $modal.open({
          templateUrl: 'savetemplate.html',
          controller: 'CitoTemplateDlgCtrl',
          size: 'mm'
        });

        confirmdialog.result.then(function (result) {
			$scope.templateSelection = '';
        	if(result){
            	$scope.working  = true;
            	CitoTemplate.save({
            		'key' : result,
            		'formObject' : $scope.info
            	}, $scope.loadForm);            
          	}
        }, function () {
        	$scope.templateSelection = '';
        });
    }

    $scope.loadForm();

});

angular.module('scpApp').factory('CitoTemplate', ['$http', 'AppSettings', 'transformRequestAsFormPost', '$rootScope', function($http, AppSettings, transformRequestAsFormPost, $rootScope) {  
    return {
        save : function(citoTemplateObject, fnPostBack){

          var method = citoTemplateObject.id ? (citoTemplateObject.force_post ? 'POST' : 'PUT') : 'POST';

          if(!citoTemplateObject.key){
          	$rootScope.showMessage('Error de aplicación, falta clave de template');
          	return;
          }

          if(!citoTemplateObject.json_data && citoTemplateObject.formObject){
            citoTemplateObject.json_data = angular.toJson(citoTemplateObject.formObject);
          }
          var data = {
            'json_data' : citoTemplateObject.json_data,
            'key' : citoTemplateObject.key
          };

          if(citoTemplateObject.id)
          {
          	data.id = citoTemplateObject.id;
          }

          if(citoTemplateObject.json_data){
            var request = $http({
              method: method,
              url: AppSettings.baseUrl + 'citologycaltemplates_drv/citologycaltemplate/',
              transformRequest: transformRequestAsFormPost,
              data: data
            });

            request.success(function(response, status) {
              if(status === 200){
                $rootScope.showMessage('Machote registrado exitosamente', 5000, 'success');
                if(fnPostBack){
                	fnPostBack();
                }
              }else{
                $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
              }
            })
            .error(function(data){
                $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
            });
          }else{
            $rootScope.showMessage('Error de aplicación, petición mal formada');
          }
          
          return citoTemplateObject;
        },

        get: function(idObject, fnPostBack, fnError) {
          
          $http.get(AppSettings.baseUrl + 'citologycaltemplates_drv/citologycaltemplate/?id=' + idObject + '&rnd=' + Math.random())
          .success(function(response, status) {
              if(status === 200){
                var citoTemplateObject = {};
                var data = response.data.json_data;
                citoTemplateObject.id = response.data.id;
                citoTemplateObject.json_data = response.data.json_data;
                citoTemplateObject.formObject = angular.fromJson(response.data.json_data);
                fnPostBack(citoTemplateObject);
              }else{
                if(fnError){
                  fnError(response, status)
                }else{
                  $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
                }
              }
          })
          .error(function(data){
            if(fnError){
              fnError(data)
            }else{
              $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
            }
          });
        },
        getAll: function(fnPostBack, fnError) {
          
          $http.get(AppSettings.baseUrl + 'citologycaltemplates_drv/citologycaltemplates/?rnd=' + Math.random())
          .success(function(response, status) {
              if(status === 200){
                var data = response.data;
                fnPostBack(data);
              }else{
                if(fnError){
                  fnError(response, status)
                }else{
                  $rootScope.showMessage('No fue posible cargar los templates disponibles');
                }
              }
          })
          .error(function(data){
            if(fnError){
              fnError(data)
            }else{
              $rootScope.showMessage('No fue posible cargar los templates disponibles');
            }
          });
        },
        delete: function(idObject, fnPostBack, fnError) {
          $http.delete(AppSettings.baseUrl + 'citologycaltemplates_drv/citologycaltemplate/?id=' + idObject)
          .success(function(response, status) {
              if(status === 200){
                fnPostBack(citoTemplateObject);
              }else{
                if(fnError){
                  fnError(response, status)
                }else{
                  $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
                }
              }
          })
          .error(function(data){
            if(fnError){
              fnError(data)
            }else{
              $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
            }
          });
        }
    };
  }]);