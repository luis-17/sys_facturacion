'use strict'; 
if (!window.location.origin) {
  window.location.origin = window.location.protocol+"//"+window.location.host;
}
var dirWebRoot =  window.location.origin + '/'+directoryApp+'/';
angular.patchURL = dirWebRoot;
angular.patchURLCI = dirWebRoot+'ci.php/';
angular.dirViews = angular.patchURL+'/application/views/';
function handleError( response ) {
    if ( ! angular.isObject( response.data ) || ! response.data.message ) {
        return( $q.reject( "An unknown error occurred." ) );
    }
    return( $q.reject( response.data.message ) );
}
function handleSuccess( response ) {
    return( response.data );
}

/* Controllers */ 
angular.module('app')
  .controller('AppCtrl', ['$scope', '$translate', '$localStorage', '$location', '$window',  '$timeout', 'rootServices',  'blockUI', 'pinesNotifications', 
      '$state', 
    function(              $scope,   $translate,   $localStorage,   $location,   $window,    $timeout,   rootServices,    blockUI ,  pinesNotifications   
        ,$state) { 
      // add 'ie' classes to html
      var isIE = !!navigator.userAgent.match(/MSIE/i);
      if(isIE){ angular.element($window.document.body).addClass('ie');}
      if(isSmartDevice( $window ) ){ angular.element($window.document.body).addClass('smart')};

      // config
      $scope.app = {
        name: 'CONEHOST.PE',
        version: '2.2.0',
        // for chart colors
        color: {
          primary: '#7266ba',
          info:    '#23b7e5',
          success: '#27c24c',
          warning: '#fad733',
          danger:  '#f05050',
          light:   '#e8eff0',
          dark:    '#3a3f51',
          black:   '#1c2b36'
        },
        settings: {
          themeID: 1,
          navbarHeaderColor: 'bg-black',
          navbarCollapseColor: 'bg-white-only',
          asideColor: 'bg-black',
          headerFixed: true,
          asideFixed: false,
          asideFolded: false,
          asideDock: false,
          container: false
        }
      }
      $scope.reloadPage = function() { 
        $state.reload();
      }
      $scope.goToUrl = function(param) { 
        $location.path(param); // Ejm: '/access/login'
      }
      $scope.listaEmpresaAdminSession = []; 
      // save settings to local storage
      if ( angular.isDefined($localStorage.settings) ) {
        $scope.app.settings = $localStorage.settings;
      } else {
        $localStorage.settings = $scope.app.settings;
      }
      $scope.$watch('app.settings', function(){
        if( $scope.app.settings.asideDock  &&  $scope.app.settings.asideFixed ){
          // aside dock and fixed must set the header fixed.
          $scope.app.settings.headerFixed = true;
        }
        // for box layout, add background image
        $scope.app.settings.container ? angular.element('html').addClass('bg') : angular.element('html').removeClass('bg');
        // save to local storage
        $localStorage.settings = $scope.app.settings;
      }, true);

      // angular translate
      $scope.lang = { isopen: false };
      $scope.langs = {es_SP:'Spanish', en:'English', de_DE:'German', it_IT:'Italian'};
      $scope.selectLang = $scope.langs[$translate.proposedLanguage()] || "Spanish";
      $scope.setLang = function(langKey, $event) { 
        // set the current lang
        $scope.selectLang = $scope.langs[langKey];
        // You can change the language during runtime
        $translate.use(langKey);
        $scope.lang.isopen = !$scope.lang.isopen;
      };
      $scope.setLang('es_SP');
      $scope.lang.isopen = false;
      function isSmartDevice( $window )
      {
          // Adapted from http://www.detectmobilebrowsers.com
          var ua = $window['navigator']['userAgent'] || $window['navigator']['vendor'] || $window['opera'];
          // Checks for iOs, Android, Blackberry, Opera Mini, and Windows mobile devices
          return (/iPhone|iPod|iPad|Silk|Android|BlackBerry|Opera Mini|IEMobile/).test(ua);
      }

      /* SESSION */
      $scope.arrMain = {};
      $scope.arrMain.empresaadmin = {};
      $scope.arrMain.listaEmpresaAdminSession = [];
      $scope.fSessionCI = {};
      $scope.fConfigSys = {};

      $scope.$on('$routeChangeStart', function() {
        console.log('change me');
      });
      $scope.isLoggedIn = false;
      $scope.logOut = function() {
        $scope.isLoggedIn = false; 
      }

      $scope.logIn = function() {
        $scope.isLoggedIn = true;
      };
      $scope.getListaEmpresasSession = function(){ 
        $timeout(function() { 
          rootServices.sListarEmpresaAdminSession().then(function (rpta) { 
            $scope.arrMain.listaEmpresaAdminSession = rpta.datos; 
            var objIndex = $scope.arrMain.listaEmpresaAdminSession.filter(function(obj) { 
              return obj.idempresaadmin == $scope.fSessionCI.idempresaadmin;
            }).shift(); 
            $scope.arrMain.empresaadmin = objIndex; 
          });
        }, 50);
      }
      $scope.getValidateSession = function () { 
        rootServices.sGetSessionCI().then(function (response) { 
          if(response.flag == 1){
            $scope.fSessionCI = response.datos;
            $scope.getListaEmpresasSession();
            $scope.logIn();
            // $scope.CargaMenu();
            if( $location.path() == '/access/login' ){ 
              $location.path('/');
            }else{
              $scope.reloadPage(); 
            }
            
          }else{
            $scope.fSessionCI = {};
            $scope.fConfigSys = {};
            $scope.logOut();
            $location.path('/access/login'); 
            return false; 
          }
        });
      }
      $scope.getConfiguracionSys = function() {
        rootServices.sGetConfiguracionSys().then(function (response) { 
          if(response.flag == 1){
            $scope.fConfigSys = response.datos;
          }else{
            $scope.fSessionCI = {};
            $scope.fConfigSys = {};
            $scope.logOut();
            $location.path('/access/login'); 
            return false; 
          }
        }); 
      }
      $scope.onChangeEmpresaSession = function() {
        var arrData = { 
          'datos' : $scope.arrMain.empresaadmin,
          'session' : $scope.fSessionCI
        }
        blockUI.start('Ejecutando proceso...');
        rootServices.sCambiarEmpresaSession(arrData).then(function (rpta) {
          if(rpta.flag == 1){
            var pTitle = 'OK!';
            var pType = 'success';
            $scope.getValidateSession();
          }else if(rpta.flag == 0){
            var pTitle = 'Error!';
            var pType = 'warning';
          }else{
            alert('Contacte con el Área de Sistemas');
          }
          blockUI.stop();
          pinesNotifications.notify({ title: pTitle, text: rpta.message, type: pType, delay: 2000 });
        });
      }
      $scope.btnLogoutToSystem = function () { 
        blockUI.start('Cerrando sesión...');
        rootServices.sLogoutSessionCI().then(function () {
          blockUI.stop();
          $scope.fSessionCI = {};
          $scope.arrMain = {};
          $scope.arrMain.empresaadmin = {};
          $scope.arrMain.listaEmpresaAdminSession = [];
          $scope.logOut();
          $location.path('/access/login');
        });
      } 
      $timeout(function() {
        $scope.getValidateSession();
        $scope.getConfiguracionSys();
      }, 400);
      
  }])
  .service('rootServices', function($http, $q, handleBehavior) { 
    return({
        sLogoutSessionCI: sLogoutSessionCI,
        sGetSessionCI: sGetSessionCI,
        sGetConfiguracionSys : sGetConfiguracionSys,
        sListarEmpresaAdminSession: sListarEmpresaAdminSession, 
        sCambiarEmpresaSession: sCambiarEmpresaSession,
        sLoginToSystem: sLoginToSystem 
    });
    function sLogoutSessionCI(pDatos) {
      var datos = pDatos || {};
      var request = $http({
            method : "post",
            url :  angular.patchURLCI + "Acceso/logoutSessionCI",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sGetSessionCI(pDatos) {
      var datos = pDatos || {};
      var request = $http({
            method : "post",
            url :  angular.patchURLCI + "Acceso/getSessionCI",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sGetConfiguracionSys(datos) {
      var datos = datos || {};
      var request = $http({
            method : "post",
            url :  angular.patchURLCI + "Configuracion/getConfigSys",
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sListarEmpresaAdminSession(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Acceso/lista_empresa_admin_session", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sCambiarEmpresaSession(datos) {
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Acceso/cambiar_empresa_admin_session", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    function sLoginToSystem(datos) { 
      var request = $http({
            method : "post",
            url : angular.patchURLCI+"Acceso/", 
            data : datos
      });
      return (request.then(handleBehavior.success,handleBehavior.error));
    }
    
  });