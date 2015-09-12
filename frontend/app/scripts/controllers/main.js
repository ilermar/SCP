'use strict';

/**
 * @ngdoc function
 * @name scpApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the scpApp
 */
angular.module('scpApp')
  .controller('MainCtrl', function ($rootScope, $scope,$http, $location, $modal, Auth, transformRequestAsFormPost, AppSettings, $timeout, StudyNames, NewStudyService) {
  	
    $scope.isMainDoctor = false;

    $scope.loadDashboard = function(){

      
      if($rootScope.currentUser){

        if($rootScope.currentUser.profile === 6){
          $http.get(AppSettings.baseUrl + 'labtest_drv/labtests/?status=0&main_doctor=' + $rootScope.currentUser.id + '&rnd='+ Math.random())
            .success(function(response, status) {
              if( status === 200 ){
                $scope.studyList = response.data;
              }else{
                $scope.studyList = [];
              }
          });
          $scope.isMainDoctor = true;
        }

        $http.get(AppSettings.baseUrl + 'reminders_drv/reminders/?rnd=' + Math.random()).success(
          function(response, status) {
            if(status === 200){
              $scope.rememberList = response.data;
            }else{
              $scope.rememberList = [];
            }
        });
      }
    };

    $scope.editStudy = function(idObject){
      $http.get(AppSettings.baseUrl + 'labtest_drv/labtest/?id=' + idObject + '&rnd=' + Math.random())
      .success(function(response, status){
        if(status === 200){
          NewStudyService.setStudyScope(response.data);
          $location.path('/editstudy');
        }else{
          $rootScope.showMessage(response.rm ? '[' + response.supportCode +'] - ' + response.rm : 'Falla operación. Consulte a soporte.');
        }
      }).error(function(data){
        $rootScope.showMessage(data.rm ? '[' + data.supportCode +'] - ' + data.rm : 'Falla operación. Consulte a soporte.');
      });
    };

    $scope.getStudyName = function(type){
      return StudyNames(type);
    };
    
    $rootScope.login = function () { 
      $rootScope.currentUser = null;
      $rootScope.showLoginAlert = false;
      $rootScope.dataLoading = true;
      var request = $http({
            method: 'POST',
            url: AppSettings.baseUrl + 'login_drv/log_in',
            transformRequest: transformRequestAsFormPost,
            data: {
              'email' : $rootScope.username,
              'password' : $rootScope.password
            }
      });
      
      request.success(function(response, status) {
        $rootScope.dataLoading = false;
        if(status === 200){
            $rootScope.currentUser = {
              fullName : response.name ? response.name : 'Nombre no disponible',
              profile : response.profile,
              email : $rootScope.username
            };
            Auth.setUser($rootScope.currentUser);
            $location.path('/main');

            $scope.loadDashboard();
        }else{
          $rootScope.showMessage(response.rm); 
        }
      }).error(function(msg){
         $rootScope.showMessage(msg.rm);
      });
    };

    $rootScope.closeSession = function () {      
      $http({
            method: 'POST',
            url: AppSettings.baseUrl + 'login_drv/log_out',
            transformRequest: transformRequestAsFormPost
      });

      $rootScope.currentUser = null;
      Auth.setUser($rootScope.currentUser);
      $location.path('/login');
    };

    $rootScope.recoverPassword = function(){
        var dialog = $modal.open({
          templateUrl: 'requestuser.html',
          controller: 'RecoverPasswordCtrl'
        });
        dialog.opened.then(function() {
            $timeout(function(){
              var element = document.getElementById('useremail');
              if(element){
                element.focus();
              }
            }, 500 );
            
        });
        dialog.result.then(function(userEmail){
          $rootScope.dataLoading = true;
          var request = $http({
            method: 'POST',
            url: AppSettings.baseUrl + 'login_drv/recover/',
            transformRequest: transformRequestAsFormPost,
            data: {
              'email' : userEmail
            }
          });
          
          request.then(function(response) {
            $rootScope.dataLoading = false;
            if(response.status === 200){
              $rootScope.showMessage('Se envió una nueva contraseña a su correo electrónico. La contraseña anterior ha sido eliminada.', 5000, 'success'); 
            }else if(response.data && response.data.rm){
              $rootScope.showMessage(response.data.rm); 
            }else{
              $rootScope.showMessage('Falla operación, consulte a soporte'); 
            }
          });          
        });
    };
    $scope.loadDashboard();
});