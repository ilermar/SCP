'use strict';

angular.module('scpApp').controller('ModalDoctorCtrl', function ($scope, $modalInstance, $http, AppSettings) {

  //$scope.doctors = [];

  $scope.ok = function () {

    $modalInstance.close($scope.selectedDoctor);
  };

  $scope.cancel = function () {
    $modalInstance.dismiss();
  };

  $scope.getDoctors = function(doctorName) {

    return $http.get(AppSettings.baseUrl + 'doctors_drv/doctors/?autocomplete=TRUE&name=' + doctorName + '&rnd=' + Math.random())
      .then(function(response) {
        return response.status === 200 ? response.data.data : [];
      });
  };

}); 

angular.module('scpApp').controller('ModalPatientCtrl', function ($scope, $modalInstance, $http, AppSettings) {

  $scope.ok = function () {
    $modalInstance.close($scope.selectedPatient);
  };

  $scope.cancel = function () {
    $modalInstance.dismiss();
  };

  $scope.getPatients = function(patientName) {

    return $http.get(AppSettings.baseUrl + 'patients_drv/patients/?autocomplete=TRUE&name=' + patientName + '&rnd=' + Math.random())
      .then(function(response) {
        return response.status === 200 ? response.data.data : [];
      });
  };

});

angular.module('scpApp').controller('ModalConfirmCtrl', function ($scope, $modalInstance, $http, dialogInfo) {

  $scope.dialog = dialogInfo;

  $scope.ok = function () {
    $modalInstance.close(true);
  };

  $scope.cancel = function () {
    $modalInstance.dismiss();
  };
});

angular.module('scpApp').controller('RecoverPasswordCtrl', ['$scope', '$http', '$modalInstance', function ($scope, $http, $modalInstance) {
  $scope.ok = function () {
    if( $scope.useremail ){
       $modalInstance.close($scope.useremail);
    }else{
      $scope.message = 'Ingresa un correo electrónico válido';
    }
  };

  $scope.cancel = function () {
    $modalInstance.dismiss();
  };
  
}]);

angular.module('scpApp').controller('CitoTemplateDlgCtrl', ['$scope', '$http', '$modalInstance', function ($scope, $http, $modalInstance) {
  $scope.ok = function () {
    if( $scope.key ){
       $modalInstance.close($scope.key);
    }else{
      $scope.message = 'La clave del machote no puede estar vacía';
    }
  };

  $scope.cancel = function () {
    $modalInstance.dismiss();
  };
  
}]);


angular.module('scpApp').controller('ModalImagesCtrl', function ($scope, $modalInstance, $http, dialogInfo, LabTestData) {

  $scope.dialog = dialogInfo;
  $scope.currentIndex = 0;
  $scope.currentText = null;
  $scope.currentLink = null;
  $scope.slides = [];
  $scope.noSavedData = false;
  $scope.userMessage = '';

  $scope.dropboxChooser = function(){
    var options = {
        success: function(files) {
            for(var i = 0; i < files.length; i++){
              if(files[i].thumbnailLink){
                var indexParams = files[i].thumbnailLink.indexOf('?');
                var fixedLink = files[i].thumbnailLink.substring(0, indexParams);
                $scope.addSlide(fixedLink, files[i].name);  
              } 
            }
            $scope.refresh();
        },
        cancel: function() {
          $scope.userMessage = null;
        },
        linkType: 'preview', 
        multiselect: true
    };
    $scope.userMessage = 'Haga click para refrescar, después de cerrar Dropbox!';
    Dropbox.choose(options);
  };

  $scope.ok = function () {
    LabTestData.save({
        'id' : $scope.dialog.id,
        'formObject' : $scope.slides,
        'force_post' : $scope.noSavedData
      });
    $modalInstance.close(true);
  };

  $scope.cancel = function () {
    $modalInstance.dismiss();
  };

  $scope.deleteCurrent = function () {
    if($scope.currentIndex >= 0 && $scope.currentIndex < $scope.slides.length){
      $scope.slides.splice($scope.currentIndex, 1);
      if($scope.currentIndex >= $scope.slides.length){
        $scope.currentIndex = $scope.slides.length - 1;
      }
      $scope.refresh();
    }
  };

  $scope.moveSlider = function(delta){
    var newIndex = $scope.currentIndex + delta;
    
    if(newIndex >= 0 && newIndex < $scope.slides.length){
      $scope.currentIndex = newIndex;
    }
    $scope.refresh();
  };

  $scope.refresh = function(){
    if($scope.slides.length > 0){
      $scope.currentLink = $scope.slides[$scope.currentIndex].link;
      $scope.currentText = $scope.slides[$scope.currentIndex].text;
      $scope.userMessage = null;
    }else{
      $scope.currentLink = null;
      $scope.currentText = null;
      $scope.userMessage = 'No hay imágenes guardadas';
    }
    
  };

  $scope.addSlide = function(imageLink, textImage) {
    $scope.slides.push({
      link: imageLink + '?mode=fit&bounding_box=256',
      text :textImage
    });

    if(!$scope.currentLink){
      $scope.currentIndex = 0;
      $scope.refresh();
    }
  };

  $scope.userMessage = 'Cargando...';
  LabTestData.get($scope.dialog.id, function(response){
      $scope.slides = response.formObject;
      $scope.refresh();
  }, function(){
    $scope.noSavedData = true;
    $scope.refresh();
  });
});

