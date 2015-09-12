'use strict';

angular.module('scpApp').factory('StudyNames', function(){
  var studyNames = {
        '1' : 'Citología',
        '2' : 'Androscopía', 
        '4' : 'Colposcopía', 
        '8' : 'Histeroscopía', 
        '16' : 'Histopatología', 
        '32' : 'Especiales', 
        '64': 'Inmunológicos'};

  function getStudyName(type){
    return studyNames.hasOwnProperty(type) ? studyNames[type] : 'Desconocido';
  }

  return getStudyName;
});

angular.module('scpApp').factory('LabTestData', ['$http', 'AppSettings', 'transformRequestAsFormPost', '$rootScope', function($http, AppSettings, transformRequestAsFormPost, $rootScope) {  
    return {
        save : function(labTestDataObject){

          var method = labTestDataObject.id ? (labTestDataObject.force_post ? 'POST' : 'PUT') : 'POST';

          if(!labTestDataObject.id){
            var now = new Date();
            
            labTestDataObject.id = now.getTime() + '$' + Math.random();
          }

          if(!labTestDataObject.json_data && labTestDataObject.formObject){
            labTestDataObject.json_data = angular.toJson(labTestDataObject.formObject);
          }

          if(labTestDataObject.json_data){
            var request = $http({
              method: method,
              url: AppSettings.baseUrl + 'labtestdata_drv/labtestdata/',
              transformRequest: transformRequestAsFormPost,
              data: {
                'id' : labTestDataObject.id,
                'json_data' : labTestDataObject.json_data,
                'id_study' : labTestDataObject.id_study
              }
            });

            request.success(function(response, status) {
              if(status === 200){
                $rootScope.showMessage('Estudio registrado exitosamente', 5000, 'success');
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
          
          return labTestDataObject;
        },

        get: function(idObject, fnPostBack, fnError) {
          
          $http.get(AppSettings.baseUrl + 'labtestdata_drv/labtestdata/?id=' + idObject + '&rnd=' + Math.random())
          .success(function(response, status) {
              if(status === 200){
                var labTestDataObject = {};
                labTestDataObject.id = response.data.id;
                labTestDataObject.json_data = response.data.json_data;
                labTestDataObject.formObject = angular.fromJson(response.data.json_data);
                fnPostBack(labTestDataObject);
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
  .controller('StartStudyCtrl', function ($scope, $location, NewStudyService) {
    NewStudyService.setStudyScope(null);
    $location.path('/newstudy');
  });


angular.module('scpApp')
  .controller('StudiesFormCtrl', function ($scope,$rootScope,   $http, $timeout, $modal, $location, NewStudyService, AppSettings, transformRequestAsFormPost) {
    $scope.dataReady = false;
    $scope.waitForSave = false;
  
  	$scope.reset = function() {
        $scope.formObject = angular.copy($scope.master);
        $scope.selectedPatient = null;
        $scope.selectedDoctor = null;
    };

  	$scope.save = function(formObject){
  		if($scope.htmlForm && $scope.htmlForm.$valid){
        NewStudyService.setStudyScope(null);
        if($scope.selectedPatient && $scope.selectedPatient.id && $scope.selectedDoctor  && $scope.selectedDoctor.id)
        {
            $scope.formObject.patient_id = $scope.selectedPatient.id;
            $scope.formObject.doctor_id = $scope.selectedDoctor.id;
            $scope.finalSave(formObject);
        }  		
  		}else{
        $rootScope.showMessage('Algunos campos obligatorios no estan presentes');
      }
  	};

    $scope.finalSave = function(formObject){

      if(!formObject.patient_age){
        formObject.patient_age = 0;
      }

      var request = $http({
          method: 'POST',
          url: AppSettings.baseUrl + 'labtest_drv/labtest/',
          transformRequest: transformRequestAsFormPost,
          data: formObject
      });

      request.success(function(response, status) {
        if(status === 200){
          $rootScope.showMessage('Estudio registrado exitosamente', 5000, 'success');
          $scope.reset();
        }else{
          $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
        }
      })
      .error(function(data){
          $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
      });
    };

    $scope.checkForPatient = function(){
      if($scope.selectedPatient && !($scope.selectedPatient.id)){
        var confirmdialog = $modal.open({
            templateUrl: 'confirmdialog.html',
            controller: 'ModalConfirmCtrl',
            size: 'mm',
            resolve : {
              dialogInfo : function(){
                return {'title':'¡NUEVO PACIENTE!', 
                'message': 'El nombre de PACIENTE (' + $scope.selectedPatient + ') no está registrado. ¿Desea registrarlo?'
                };
              }
            }
          });

        confirmdialog.result.then(function (result) {
            if(result){
              NewStudyService.setStudyScope({
                owners : $scope.owners,
                form : $scope.formObject,
                selectedPatient : $scope.selectedPatient,
                selectedDoctor : $scope.selectedDoctor
              });
              $location.path('/newpatientfromservice');
            }else{
              $scope.selectedPatient = null;
            }
          }, function () {
            $scope.selectedPatient = null;
          });

      }
    };

    $scope.checkForDoctor = function(){
      if($scope.selectedDoctor && !($scope.selectedDoctor.id)){
        var confirmdialog = $modal.open({
            templateUrl: 'confirmdialog.html',
            controller: 'ModalConfirmCtrl',
            size: 'mm',
            resolve : {
              dialogInfo : function(){
                return {'title':'¡NUEVO DOCTOR!', 
                'message': 'El nombre de DOCTOR (' + $scope.selectedDoctor + ') no está registrado. ¿Desea registrarlo?'
                };
              }
            }
          });

        confirmdialog.result.then(function (result) {
            if(result){
              NewStudyService.setStudyScope({
                owners : $scope.owners,
                form : $scope.formObject,
                selectedPatient : $scope.selectedPatient,
                selectedDoctor : $scope.selectedDoctor
              });
              $location.path('/newdoctorfromservice');
            }else{
              $scope.selectedDoctor = null;
            }
          }, function () {
            $scope.selectedDoctor = null;
          });

      }
    };

    $scope.updatePatientAge = function(){
      if($scope.savePatientAge && $scope.formObject.patient_age !== '' && $scope.formObject.patient_age > 0){
        var now = new Date();
        var fixedDate = new Date(now.getTime() - ($scope.formObject.patient_age * 366 * 24 * 60 * 60 * 1000));
        fixedDate.setDate(1);
        fixedDate.setMonth(0);
        $http({
            method: 'PUT',
            url: AppSettings.baseUrl + 'patients_drv/patient/',
            transformRequest: transformRequestAsFormPost,
            data: {
              'id' : $scope.selectedPatient.id,
              'birth_date' : fixedDate.getFullYear() + '-' + (fixedDate.getMonth() + 1) + '-' + fixedDate.getDate()
            }
        });
      }
    };

    $scope.getNextKeyNumber = function(){
      $scope.waitForSave = true;
      $http.get(AppSettings.baseUrl + 'labtest_drv/labtest/?next_number=true&key_prefix=' + $scope.formObject.key_prefix + '&rnd=' + Math.random())
        .success(function(response, status) {
          if(status === 200){
            $scope.waitForSave = false;
            $scope.formObject.key_number = response.data;
          }else{
            $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
          }
        })
        .error(function(data){
            $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
            $scope.working = false;
        });
    };

    $scope.loadForm = function(){
        var prevScope = NewStudyService.getStudyScope();
        NewStudyService.setStudyScope(null);

        if(prevScope){
          $scope.dataReady = false;
          $scope.master = prevScope.form;
          $scope.owners = prevScope.owners;
          $timeout(function(){ 
            $scope.dataReady = true;
            $scope.selectedPatient = prevScope.selectedPatient;
            $scope.selectedDoctor = prevScope.selectedDoctor;
            $scope.formObject.patient_age = $scope.selectedPatient.age;
            $scope.savePatientAge = parseInt($scope.selectedPatient.age) === 0;
          }, 1000);
        }else{
          $http.get(AppSettings.baseUrl + 'users_drv/users/?profile=6&rnd=' + Math.random())
          .then(function(response) {
            if(response.status === 200){
              $scope.owners = response.data.data;
              if($scope.owners.length > 0){
                $scope.formObject.main_doctor_id = $scope.owners[0].id;  
              }
            }
          });
          $scope.dataReady = true;
          $scope.master = {};
          $scope.selectedPatient = null;
          $scope.selectedDoctor = null;
        }
        
        $scope.reset();
    };

    $scope.getDoctors = function(doctorName) {

      return $http.get(AppSettings.baseUrl + 'doctors_drv/doctors/?autocomplete=TRUE&name=' + doctorName + '&rnd=' + Math.random())
        .then(function(response) {
          return response.status === 200 ? response.data.data : [];
        });
    };

    $scope.getPatients = function(patientName) {

      return $http.get(AppSettings.baseUrl + 'patients_drv/patients/?name=' + patientName + '&rnd=' + Math.random())
        .then(function(response) {
          return response.status === 200 ? response.data.data : [];
        });
    };

    $scope.onPatientSelected = function ($item) {
      $scope.formObject.patient_age = $item.age ? $item.age : 0;

      $scope.savePatientAge = parseInt($scope.formObject.patient_age) === 0;
    };

    $scope.loadForm();
  	
  });

angular.module('scpApp')
  .controller('StudiesSearchCtrl', function ($scope, $http, $location, $rootScope, $modal, formInitialization, AppSettings, NewStudyService, StudyNames, Auth) {
      
      var statusNames = ['No atendido', 'Inconcluso', 'Firmado'];

      $scope.formInstance = formInitialization;

      $scope.titleForm = 'BUSCAR ' + $scope.formInstance.title.toUpperCase();

      $scope.prevCalendar = null;
      $scope.working = false;
      
      $scope.instructions = 'Use los filtros para simplicar su búsqueda. Ningún filtro es obligatorio';
      $scope.filterSelection = true;
      $scope.showAlert = false;

      $scope.search = function(filters){
        if( !filters ){
          filters = {};
        }
        $scope.working = true;
        $scope.selectedObject = null;
        filters.randomParam = Math.random();
        $http.get(AppSettings.baseUrl + 'labtest_drv/labtests/', { params : filters})
        .success(function(response, status) {
          if(status === 200){
            $scope.instructions = 'Use doble click para editar un estudio';
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
        $http.get(AppSettings.baseUrl + 'labtest_drv/labtest/?id=' + idObject + '&rnd=' + Math.random())
        .success(function(response, status){
          if(status === 200){
            NewStudyService.setStudyScope(response.data);
            $location.path($scope.formInstance.edit);
          }else{
            $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
          }
        }).error(function(data){
          $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
        });        
      };

      $scope.delete = function(filters, idObject, title, msg){
        $rootScope.idObjectToDelete = idObject;
        var confirmdialog = $modal.open({
          templateUrl: 'confirmdialog.html',
          controller: 'ModalConfirmCtrl',
          size: 'mm',
          resolve : {
            dialogInfo : function(){
              return {'title':title, 'message': '¿Confirma que desea eliminar el estudio seleccionado' + msg + '? Esta operación no podrá deshacerse!!'};
            }
          }
        });

        confirmdialog.result.then(function (result) {
          if(result){
            $scope.working = true;
            var idObj       = $rootScope.idObjectToDelete;
            $rootScope.idObjectToDelete = undefined;
            $http.delete( AppSettings.baseUrl + 'labtest_drv/labtest/' + idObj )
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

      $scope.getStudyName = function(type){
        return StudyNames(type);
      };

      $scope.getStatusName = function(status){
          return status >= 0 && status < statusNames.length ? statusNames[status] : 'Desconocido';
      };

      $scope.updateType = function(checked, trueValue, falseValue){
        var trueTypeValue = $scope.formObject.type | trueValue;
        var falseTypeValue = $scope.formObject.type & falseValue;

        $scope.formObject.type = checked ? trueTypeValue : falseTypeValue;
      };

      $scope.loadForm = function(){
        $scope.formObject = { 'type' : 0};
        $scope.formObject.register_date_end = new Date();
        $scope.formObject.register_date_start = new Date($scope.formObject.register_date_end.getTime()- (8 * 24 * 60 * 60 * 1000));
        $http.get(AppSettings.baseUrl + 'users_drv/users/?profile=6&rnd=' + Math.random()).then(function(response) {
          if(response.status === 200){
            $scope.owners = response.data.data;
            var user = Auth.isLoggedIn();
            if(user && user.profile === 6){
              $scope.formObject.main_doctor = user.id;
            }
          }
        });
      };

      $scope.loadForm();
  });

angular.module('scpApp')
  .controller('EditStudyFormCtrl', function ($scope, $location, $modal, StudyNames, $http, AppSettings, NewStudyService, $rootScope, LabTestData, $window) {
    $scope.dataReady = true;
    $scope.showAlert = false;

    $scope.master = {
    };

    $scope.tabs = [
    ];

    $scope.study = {

    };

    $scope.currentTab = null;
    $scope.currentTabIndex = -1;

    $scope.reset = function() {
        $scope.labtests = angular.copy($scope.master);
    };

    $scope.closeAlert = function(){
      $scope.showAlert = false;
    };

    $scope.selectTab = function(tab, index){
        for(var i = 0; i < $scope.tabs.length; i++){
          $scope.tabs[i].active = false;
        }
        tab.active = true;
        $scope.currentTab = tab;
        $scope.currentTabIndex = index;
    };
    

    $scope.newTab = function(type) {
      $scope.tabs[$scope.tabs.length] =
      { 
        'type':type 
      };

      $scope.selectTab($scope.tabs[$scope.tabs.length - 1], $scope.tabs.length - 1);
    };

    $scope.save = function(){
      var labtests = $scope.labtests;

      labtests.tabs = $scope.tabs;

      var savedLabTest = LabTestData.save({
        'id' : $scope.study.main_json_data,
        'formObject' : labtests,
        'id_study' : $scope.study.id
      });

      $scope.study.main_json_data = savedLabTest.id;
      $scope.master = angular.copy($scope.labtests);
    };

    $scope.buildPrintView = function(){
      var win = $window.open('../printerviews/views/citology.html');
      win.info = {
        'study' : $scope.study,
        'labtests' : $scope.labtests
      };
    };

    $scope.showHC = function(){
      NewStudyService.setStudyScope($scope.study);

      $location.path('/hc');
    };

    $scope.showImages = function(){
      
      var confirmdialog = $modal.open({
          templateUrl: 'imagesdialog.html',
          controller: 'ModalImagesCtrl',
          size: 'mm',
          resolve : {
            dialogInfo : function(){
              return {
                'title':'Imagenes', 
                'message': '¿Confirma que desea eliminar el registro seleccionado?',
                'id' : $scope.study.id + '$imgs$' + $scope.currentTabIndex
              };
            }
          }
        });

        confirmdialog.result.then(function (result) {
          if(result){
          }
        }, function () {
        });
    };

    $scope.loadForm = function(){
      var currentStudy = NewStudyService.getStudyScope();
      NewStudyService.setStudyScope(null);
      if(currentStudy && currentStudy.id){
        $scope.study = currentStudy;
        $scope.labtests = {};
        if($scope.study.main_json_data === null){
          $scope.newTab($scope.study.type);
        }else{
          LabTestData.get($scope.study.main_json_data, function(response){
            $scope.labtests = response.formObject;
            $scope.tabs = $scope.labtests.tabs;
            if($scope.tabs.length > 0){
              $scope.selectTab($scope.tabs[0], 0);
            }
            $scope.master = angular.copy(response.formObject);
          }, function(){
            $scope.newTab($scope.study.type);
          });

        }
      }else{
        $location.path('/main');
      }
      
    };

    $scope.getStudyName = function(type){
      return StudyNames(type);
    };

    $scope.loadForm();
    
  });