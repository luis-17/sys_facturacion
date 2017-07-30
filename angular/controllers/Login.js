'use strict';

/* Controllers */
  // signin controller
app.controller('LogInFormController', ['$scope', '$http', '$state','$timeout','blockUI','rootServices', function($scope, $http, $state, $timeout, blockUI, rootServices) {
    $scope.user = {};
    //$scope.authError = null;
    $scope.btnLoginToSystem = function() {  
      blockUI.start('Procesando información...');
      rootServices.sLoginToSystem($scope.fLogin).then(function (response) { 
        $scope.fAlert = {};
        $scope.fAlert.visible = true;
        if( response.flag == 1 ){ // SE LOGEO CORRECTAMENTE  
          $scope.fAlert.type= 'bg-success';
          $scope.fAlert.msg= response.message;
          $scope.fAlert.strStrong = 'OK.';
          $scope.getValidateSession();
          $scope.logIn(); 
        }else if( response.flag == 0 || response.flag == 'session_expired' ){ // NO PUDO INICIAR SESION 
          $scope.fAlert.type= 'bg-danger';
          $scope.fAlert.msg= response.message;
          $scope.fAlert.strStrong = 'Error.';
        }else if( response.flag == 2 ){  // CUENTA INACTIVA
          $scope.fAlert.type= 'bg-warning';
          $scope.fAlert.msg= response.message;
          $scope.fAlert.strStrong = 'Aviso.';
          // $scope.listaSedes = response.datos;
        }
        $scope.fAlert.flag = response.flag;
        blockUI.stop();
        $timeout(function() { 
          $scope.fAlert.visible = false;
        },12000)
      });
    };
}]);