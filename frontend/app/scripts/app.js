'use strict';

/**
 * @ngdoc overview
 * @name scpApp
 * @description 
 * # scpApp
 *
 * Main module of the application.
 */
angular
  .module('scpApp', [
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngRoute',
    'ngSanitize',
    'ngTouch',
    'ui.bootstrap',
    'filters',
    'textAngular'
  ])
  .config(function ($routeProvider, $httpProvider) {
    $routeProvider
      .when('/', {
        templateUrl: 'views/main.html',
        controller: 'MainCtrl'
      })
      .when('/main', {
        templateUrl: 'views/main.html',
        controller: 'MainCtrl'
      })
      .when('/about', {
        templateUrl: 'views/about.html',
        controller: 'AboutCtrl'
      })
      .when('/contact', {
        templateUrl: 'views/contact.html',
        controller: 'ContactCtrl'
      })
      .when('/startstudy', {
        templateUrl: 'views/empty.html',
        controller: 'StartStudyCtrl'
      })
      .when('/newstudy', {
        templateUrl: 'views/studies/study-form.html',
        controller: 'StudiesFormCtrl'
      })
      .when('/editstudy', {
        templateUrl: 'views/studies/study.html',
        controller: 'EditStudyFormCtrl'
      })
      .when('/searchstudy', {
        templateUrl: 'views/studies/study-filters.html',
        controller: 'StudiesSearchCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('searchstudy');
            }
          }
      })
      .when('/citotemplates', {
        templateUrl: 'views/studies/citotemplates.html',
        controller: 'CitoTemplatesCtrl'
      })
      .when('/newpatient', {
        templateUrl: 'views/patients/patient-form.html',
        controller: 'SimpleFormCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('newpatient');
            }
        }
      })
      .when('/newpatientfromservice', {
        templateUrl: 'views/patients/patient-form.html',
        controller: 'NewPatientCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('newpatient');
            }
        }
      })
      .when('/editpatient', {
        templateUrl: 'views/patients/patient-form.html',
        controller: 'SimpleFormCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('editpatient');
            }
        }
      })
      .when('/searchpatient', {
        templateUrl: 'views/patients/patient-filters.html',
        controller: 'SimpleSearchCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('searchpatient');
            }
        }
      })
      .when('/hc', {
        templateUrl: 'views/patients/hc.html',
        controller: 'HCCtrl'
      })
      .when('/newdoctor', {
        templateUrl: 'views/doctors/doctor-form.html',
        controller: 'SimpleFormCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('newdoctor');
            }
        }
      })
      .when('/newdoctorfromservice', {
        templateUrl: 'views/doctors/doctor-form.html',
        controller: 'NewDoctorCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('newdoctor');
            }
        }
      })
      .when('/editdoctor', {
        templateUrl: 'views/doctors/doctor-form.html',
        controller: 'SimpleFormCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('editdoctor');
            }
        }
      })
      .when('/searchdoctor', {
        templateUrl: 'views/doctors/doctor-filters.html',
        controller: 'SimpleSearchCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('searchdoctor');
            }
        }
      })
      .when('/newclinical', {
        templateUrl: 'views/clinicals/clinical-form.html',
        controller: 'SimpleFormCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('newclinical');
            }
        }
      })
      .when('/editclinics', {
        templateUrl: 'views/clinicals/clinical-form.html',
        controller: 'SimpleFormCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('editclinical');
            }
        }
      })
      .when('/searchclinical', {
        templateUrl: 'views/clinicals/clinical-filters.html',
        controller: 'SimpleSearchCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('searchclinical');
            }
        }
      })
      .when('/newreminder', {
        templateUrl: 'views/reminders/reminder-form.html',
        controller: 'SimpleFormCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('newreminder');
            }
        }
      })
      .when('/editreminder', {
        templateUrl: 'views/reminders/reminder-form.html',
        controller: 'SimpleFormCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('editreminder');
            }
        }
      })
      .when('/searchreminder', {
        templateUrl: 'views/reminders/reminder-filters.html',
        controller: 'SimpleSearchCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('searchreminder');
            }
        }
      })
      .when('/newuser', {
        templateUrl: 'views/users/user-form.html',
        controller: 'UserFormCtrl'
      })
      .when('/edituser', {
        templateUrl: 'views/users/user-form.html',
        controller: 'UserFormCtrl'
      })
      .when('/searchuser', {
        templateUrl: 'views/users/user-filters.html',
        controller: 'SimpleSearchCtrl',
        resolve: {
            formInitialization: function(simpleFormService){
                return simpleFormService.prepareForm('searchuser');
            }
        }
      })
      .when('/mailserver', {
        templateUrl: 'views/settings/mailserver.html',
        controller: 'MailServerCtrl'
      })
      .when('/changepwd', {
        templateUrl: 'views/users/changepwd.html',
        controller: 'ChangePwdCtrl'
      })
      .otherwise({
        redirectTo: '/main'
      });

      $httpProvider.interceptors.push('httpInterceptor');

  }).factory('simpleFormService', function() {
    return {
      prepareForm: function(formid){
        switch(formid)
        {
          //Estudios
          case 'searchstudy':
            return {
                'title':'Estudio', 
                'baseUrl': '/dummyresponses/studies/', 
                'query' : 'list.json', 
                'edit' : '/editstudy/'
              };
          //Pacientes
          case 'newpatient':
          case 'editpatient':
            return {
                'title':'Paciente', 
                'baseUrl': 'patients_drv/patient/',
                'requiredFields' : ['name', 'birth_date', 'address', 'city', 
                                'phone_number_1', 'phone_number_2', 'phone_number_3','email']
              };
          case 'searchpatient':
            return {
              'title':'Paciente', 
              'query' : 'patients_drv/patients/', 
              'baseUrl': 'patients_drv/patient/',
              'edit' : '/editpatient/'
            };
          //Doctores
          case 'newdoctor':
          case 'editdoctor':
            return {
                'title':'Doctor', 
                'baseUrl': 'doctors_drv/doctor/',
                'requiredFields' : ['name', 'birth_date', 'address', 'city', 'fax', 'specialty',
                                'phone_number_1', 'phone_number_2', 'phone_number_3','email']
              };
          case 'searchdoctor':
            return {
              'title':'Doctor', 
              'query' : 'doctors_drv/doctors/', 
              'baseUrl': 'doctors_drv/doctor/',
              'edit' : '/editdoctor/'
            };
          //Clínicas
          case 'newclinical':
          case 'editclinical':
            return {
                'title':'Clínica', 
                'baseUrl': 'clinics_drv/clinic/',
                'requiredFields' : ['name', 'address', 'city', 'fax', 'notes',
                                'phone_number', 'email']
              };
          case 'searchclinical':
            return {
              'title':'Clínica', 
              'query' : 'clinics_drv/clinics/', 
              'baseUrl': 'clinics_drv/clinic/',
              'edit' : '/editclinics/'
            };
          //Recordatorios
          case 'newreminder':
          case 'editreminder':
            return {
                'title':'Recordatorio', 
                'baseUrl': 'reminders_drv/reminder/',
                'requiredFields' : ['reminder_date', 'notes']
              };
          case 'searchreminder':
            return {
              'title':'Recordatorio', 
              'query' : 'reminders_drv/reminders/', 
              'baseUrl': 'reminders_drv/reminder/',
              'edit' : '/editreminder/'
            };
          case 'searchuser':
            return {
              'title':'Usuario', 
              'query' : 'users_drv/users/', 
              'baseUrl': 'users_drv/user/',
              'edit' : '/edituser/',
              'getProfileName' : function(profileId){
                var profileNames = [ 'Caja', 'Recepción', 'Ayudante general', 'Doctor asociado', 'Paciente', 'Doctor a cargo'];

                return profileId > 0 && profileId <= profileNames.length ? profileNames[profileId - 1] : 'Desconocido';
              },
              'requiredFields' : ['profile', 'name', 'email', 'phone_number', 'status']
            };
        }
        
        return null;
      }
    };
  }).factory('Auth', function(){
    var user = null;

    return{
        setUser : function(aUser){
            user = aUser;
        },
        isLoggedIn : function(){
            return (user) ? user : false;
        }
      };
  }).factory('AppSettings', function() {
      return {
        baseUrl : 'api/index.php/'
    };
  }).factory('transformRequestAsFormPost', function() {
      function transformRequest(data, getHeaders){
        var headers = getHeaders();

        headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=utf-8';

        return serializeData(data);
      }


      function serializeData(data){

        if(!angular.isObject(data)){

          return data ? data.toString() : '';

        }

        var buffer = [];

        for(var name in data){

          if(!data.hasOwnProperty(name)){
            continue;
          }

          var value = data[name];

          if(value !== null){
            if(value instanceof Date){
              value = formatDate(value);
            }
          }

          buffer.push(encodeURIComponent(name) + '=' + encodeURIComponent( ( value === null ) ? '' : value ));
        }

        var source = buffer.join( '&' ).replace( /%20/g, '+' );

        return( source );

      }

      function formatDate(dateObject){
        var day = dateObject.getDate();
        var month = dateObject.getMonth() + 1;
        var year = dateObject.getFullYear();

        if(day < 10){
          day = '0' + day;
        }

        if(month < 10){
          month = '0' + month;
        }

        return year + '-' + month + '-' + day;
      }

      return( transformRequest );

  }).factory('httpInterceptor', ['$rootScope', function($rootScope) {  
    return {
        request : function(request){
          $rootScope.dataFormLoading = true;
          return request;
        },
        response: function(response) {
            $rootScope.dataFormLoading = false;
            return response;
        },
        responseError: function(response) {
            $rootScope.dataFormLoading = false;
            if (response.status ===403 || response.status === 419){
                $rootScope.closeSession();
            }
            return response;
        }
    };
  }]).service('NewStudyService', function() {
      var currentData;

      return {

        setStudyScope : function(data){
          currentData = data;
        },

        getStudyScope : function(){
          return currentData;
        }
    };
  });

angular.module('scpApp').directive("regExpRequire", function() {

    var regexp;
    return {
        restrict: "A",
        link: function(scope, elem, attrs) {
            regexp = eval(attrs.regExpRequire);
F
            var char;
            elem.on("keypress", function(event) {
                char = String.fromCharCode(event.which)
                if(!regexp.test(elem.val() + char))
                    event.preventDefault();
            })
        }
    }

})

angular.module('scpApp').run(['$rootScope', '$location', '$timeout', 'Auth', function ($rootScope, $location, $timeout, Auth) {
    $rootScope.$on('$routeChangeStart', function () {
        
        if (!Auth || !(Auth.isLoggedIn())) {
            $location.path('/main');
        } else {
            
        }
        $rootScope.closeAlert();
    });

    $rootScope.closeAlert = function(){
      $rootScope.showAlert = false;
    };

    $rootScope.showMessage = function(message, timeoutValue, alertType, fnAfterTimeout){
      if($rootScope.currentTimeoutAlert){
        $timeout.cancel($rootScope.currentTimeoutAlert);
        $rootScope.currentTimeoutAlert = null;
      }
      if($rootScope.currentUser){
        $rootScope.showAlert = true;
        $rootScope.alertType = alertType ? alertType : 'danger';
        $rootScope.serverMessage = message;
        $rootScope.currentTimeoutAlert = $timeout(function(){ 
          $rootScope.showAlert = false; 
          if(fnAfterTimeout){
            fnAfterTimeout();
          }
        }, timeoutValue ? timeoutValue : 10000);
      }else{
        $rootScope.showLoginAlert = true;
        $rootScope.loginAlertType = alertType ? alertType : 'danger';
        $rootScope.loginMessage = message;
        $rootScope.currentTimeoutAlert = $timeout(function(){ 
          $rootScope.showLoginAlert = false;
          if(fnAfterTimeout){
            fnAfterTimeout();
          } 
        }, timeoutValue ? timeoutValue : 10000);
      }
    };
}]);

angular.module('filters', []).filter('zpad', function() {
  return function(input, n) {
    var padding = '';

    if(input === undefined){
      input = '';
    }
      
    while(padding.length + input.length < n){
      padding = '0' + padding;
    }
    
    return padding + input;
  };
});
