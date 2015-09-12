'use strict';

angular.module('scpApp').factory('MedicalRecord', ['$http', 'AppSettings', 'transformRequestAsFormPost', '$rootScope', function($http, AppSettings, transformRequestAsFormPost, $rootScope) {  
  return {
        save : function(dataObject){

          if(!dataObject.id){
            $rootScope.showMessage('Error de aplicación. Falta indicar id de historia clínica.');
            return;
          }

          if(!dataObject.json_data && dataObject.formObject){
            dataObject.json_data = angular.toJson(dataObject.formObject);
          }

          if(dataObject.json_data){
            var request = $http({
              method: 'POST',
              url: AppSettings.baseUrl + 'medicalrecords_drv/medicalrecord/',
              transformRequest: transformRequestAsFormPost,
              data: {
                'id' : dataObject.id,
                'json_data' : dataObject.json_data
              }
            });

            request.success(function(response, status) {
              if(status === 200){
                $rootScope.showMessage('Hisoria clínica registrada exitosamente', 5000, 'success');
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
          
          return dataObject;
        },

        get: function(idObject, fnPostBack, fnError) {
          
          $http.get(AppSettings.baseUrl + 'medicalrecords_drv/medicalrecord/?id=' + idObject + '&rnd=' + Math.random())
          .success(function(response, status) {
              var dataObject = {};
              if(status === 200){
                dataObject.id = response.data.id;
                dataObject.json_data = response.data.json_data;
                dataObject.formObject = angular.fromJson(response.data.json_data);
                fnPostBack(dataObject);
              }else if(status === 404){
                dataObject.id = idObject;
                dataObject.formObject = {};
                fnPostBack(dataObject);
              }else{
                if(fnError){
                  fnError(response, status);
                }else{
                  $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
                }
              }
          })
          .error(function(data){
            if(fnError){
              fnError(data);
            }else{
              $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
            }
          });
        }
    };
  }]);

angular.module('scpApp')
.controller('HCCtrl', function ($scope,  $window, $rootScope, $http, $timeout, MedicalRecord, $location, NewStudyService) {

    $scope.patientName = '';
    $scope.patientAge = '';

    $scope.dataReady = false;

    $scope.master = {
    };

    $scope.selectTab = function(tabIndex){
      $scope.tabActive = tabIndex;
    };

    $scope.reset = function() {
        $scope.medrecord = angular.copy($scope.master);
    };

    $scope.backToStudy = function(){
      
    };
    
    $scope.backToPatient = function(){
      $rootScope.idObjectToEdit = $scope.patient_id;
      $location.path('/editpatient');
    };

    $scope.save = function(){
        MedicalRecord.save({
          'id' : $scope.patient_id,
          'formObject' : $scope.medrecord
        });
    };

    $scope.loadForm = function(){
        var study = NewStudyService.getStudyScope();
        if(study){
          $scope.patientName = study.patient_name;
          $scope.patientAge = study.patient_age;
          $scope.patient_id = study.patient_id;
          $scope.fromStudy = study.fromStudy;
          $scope.fromPatient = study.fromPatient;
        }

        if($scope.fromPatient){
          NewStudyService.setStudyScope(null);
        }

        if($scope.patient_id){
            MedicalRecord.get($scope.patient_id, function(medicalRecordObject){
              $scope.patient_id = medicalRecordObject.id;
              $scope.medrecord = medicalRecordObject.formObject;
              $scope.master = angular.copy($scope.medrecord);
              $scope.dataReady = true;
              $scope.tabActive = 1;
            });
        }else{
            $location.path('/main');
        }
    };


    $scope.loadForm();
});
