'use strict';

/**
 * Config for the router
 */
angular.module('app')
  .run(
    [          
      '$rootScope', '$state', '$stateParams',
      function ($rootScope,   $state,   $stateParams) {
          $rootScope.$state = $state;
          $rootScope.$stateParams = $stateParams;        
      }
    ]
  )
  .config(
    [
      '$stateProvider', '$urlRouterProvider', 'JQ_CONFIG', 'MODULE_CONFIG', 
      function ($stateProvider, $urlRouterProvider, JQ_CONFIG, MODULE_CONFIG) { 
        var layout = "tpl/app.html";
        if(window.location.href.indexOf("material") > 0){
          layout = "tpl/blocks/material.layout.html";
          $urlRouterProvider
            .otherwise('/app/dashboard');
        }else{
          $urlRouterProvider
            .otherwise('/app/dashboard');
        }
          
        $stateProvider
          .state('app', {
            abstract: true,
            url: '/app',
            templateUrl: layout
          })
          .state('app.dashboard', {
            url: '/dashboard',
            templateUrl: 'tpl/app_dashboard.html',
            resolve: load(['angular/controllers/chart.js'])
          })
          .state('app.persona-natural', {
            url: '/persona-natural',
            templateUrl: 'tpl/persona-natural.html',
            resolve: load([
              'angular/controllers/ClientePersonaCtrl.js',
              'angular/controllers/CategoriaClienteCtrl.js',
              'angular/controllers/ColaboradorCtrl.js'
            ]) 
          })
          .state('app.persona-juridica', {
            url: '/persona-juridica',
            templateUrl: 'tpl/persona-juridica.html',
            resolve: load([
              'angular/controllers/ClienteEmpresaCtrl.js',
              'angular/controllers/CategoriaClienteCtrl.js',
              'angular/controllers/ContactoEmpresaCtrl.js',
              'angular/controllers/ColaboradorCtrl.js'
            ])
          })
          .state('app.nueva-cotizacion', {
            url: '/nueva-cotizacion',
            templateUrl: 'tpl/nueva-cotizacion.html',
            resolve: load([
              'angular/controllers/NuevaCotizacionCtrl.js',
              'angular/controllers/ClienteEmpresaCtrl.js',
              'angular/controllers/ClientePersonaCtrl.js',
              'angular/controllers/ElementoCtrl.js',
              'angular/controllers/ProductoCtrl.js',
              'angular/controllers/ServicioCtrl.js',
              'angular/controllers/ClienteCtrl.js',
              'angular/controllers/ColaboradorCtrl.js',
              'angular/controllers/CategoriaClienteCtrl.js',
              'angular/controllers/CategoriaElementoCtrl.js',
              'angular/controllers/CaracteristicaCtrl.js',
              'angular/controllers/TipoDocumentoClienteCtrl.js',
              'angular/controllers/SedeCtrl.js',
              'angular/controllers/FormaPagoCtrl.js',
              'angular/controllers/UnidadMedidaCtrl.js',
              'angular/controllers/ContactoEmpresaCtrl.js' 
            ])
          })
          .state('app.historial-cotizacion', {
            url: '/historial-cotizacion',
            templateUrl: 'tpl/historial-cotizacion.html',
            resolve: load([
              'angular/controllers/HistorialCotizacionCtrl.js',
              'angular/controllers/NuevaCotizacionCtrl.js',
              'angular/controllers/SedeCtrl.js',
            ])
          })
          .state('app.producto', {
            url: '/producto',
            templateUrl: 'tpl/producto.html',
            resolve: load([
              'angular/controllers/ProductoCtrl.js',
              'angular/controllers/CategoriaElementoCtrl.js',
              'angular/controllers/UnidadMedidaCtrl.js'
            ]) 
          })
          .state('app.servicio', {
            url: '/servicio',
            templateUrl: 'tpl/servicio.html',
            resolve: load([
              'angular/controllers/ServicioCtrl.js',
              'angular/controllers/CategoriaElementoCtrl.js' 
            ]) 
          })
          .state('app.unidad-medida', {
            url: '/unidad-medida',
            templateUrl: 'tpl/unidad-medida.html',
            resolve: load([
              'angular/controllers/UnidadMedidaCtrl.js'
            ]) 
          })
          .state('app.caracteristica', {
            url: '/caracteristica',
            templateUrl: 'tpl/caracteristica.html',
            resolve: load([
              'angular/controllers/CaracteristicaCtrl.js'
            ]) 
          })
          .state('app.banco', {
            url: '/banco',
            templateUrl: 'tpl/banco.html',
            resolve: load([
              'angular/controllers/BancoCtrl.js'
            ]) 
          })     
          .state('app.empresa-admin', {
            url: '/empresa-admin',
            templateUrl: 'tpl/empresa-admin.html',
            resolve: load([
              'angular/controllers/EmpresaAdminCtrl.js',
              'angular/controllers/BancoCtrl.js',
              'angular/controllers/BancoEmpresaAdminCtrl.js'
            ]) 
          })  
          .state('app.categoria-elemento', {
            url: '/categoria-elemento',
            templateUrl: 'tpl/categoria-elemento.html',
            resolve: load([
              'angular/controllers/CategoriaElementoCtrl.js'         
            ]) 
          })  
          .state('app.colaborador', {
            url: '/colaborador',
            templateUrl: 'tpl/colaborador.html',
            resolve: load([
              'angular/controllers/ColaboradorCtrl.js',
              'angular/controllers/UsuarioCtrl.js'         
            ]) 
          })    
          .state('app.sede', {
            url: '/sede',
            templateUrl: 'tpl/sede.html',
            resolve: load([
              'angular/controllers/SedeCtrl.js'      
            ]) 
          })  
          .state('app.usuario', {
            url: '/usuario',
            templateUrl: 'tpl/usuario.html',
            resolve: load([
              'angular/controllers/UsuarioCtrl.js',   
              'angular/controllers/ColaboradorCtrl.js',
              'angular/controllers/UsuarioEmpresaAdminCtrl.js',
              'angular/controllers/EmpresaAdminCtrl.js'               
            ]) 
          })    
          .state('app.contacto', {
            url: '/contacto',
            templateUrl: 'tpl/contacto.html',
            resolve: load([      
              'angular/controllers/ContactoEmpresaCtrl.js', 
              'angular/controllers/ClienteEmpresaCtrl.js',
              'angular/controllers/ClienteCtrl.js'   
            ]) 
          })    
          .state('app.variable-car', {
            url: '/variable-car',
            templateUrl: 'tpl/variable-car.html',
            resolve: load([
              'angular/controllers/VariableCarCtrl.js'      
            ]) 
          })                                                       
          .state('lockme', {
              url: '/lockme',
              templateUrl: 'tpl/page_lockme.html'
          })
          .state('access', {
              url: '/access',
              template: '<div ui-view class="fade-in-right-big smooth"></div>'
          })
          .state('access.login', {
              url: '/login',
              templateUrl: 'tpl/login.html',
              resolve: load( ['angular/controllers/Login.js'] )
          })
          // others
          .state('access.signup', {
              url: '/signup',
              templateUrl: 'tpl/page_signup.html',
              resolve: load( ['angular/controllers/signup.js'] )
          })
          .state('access.404', {
              url: '/404',
              templateUrl: 'tpl/page_404.html'
          })
          // mail
          .state('app.mail', {
              abstract: true,
              url: '/mail',
              templateUrl: 'tpl/mail.html',
              // use resolve to load other dependences
              resolve: load( ['angular/app/mail/mail.js','angular/app/mail/mail-service.js','moment'] )
          })
          .state('app.mail.list', {
              url: '/inbox/{fold}',
              templateUrl: 'tpl/mail.list.html'
          })
          .state('app.mail.detail', {
              url: '/{mailId:[0-9]{1,4}}',
              templateUrl: 'tpl/mail.detail.html'
          })
          .state('app.mail.compose', {
              url: '/compose',
              templateUrl: 'tpl/mail.new.html'
          });

        function load(srcs, callback) {
          return {
              deps: ['$ocLazyLoad', '$q',
                function( $ocLazyLoad, $q ){
                  var deferred = $q.defer();
                  var promise  = false;
                  srcs = angular.isArray(srcs) ? srcs : srcs.split(/\s+/);
                  if(!promise){
                    promise = deferred.promise;
                  }
                  angular.forEach(srcs, function(src) {
                    promise = promise.then( function(){
                      if(JQ_CONFIG[src]){
                        return $ocLazyLoad.load(JQ_CONFIG[src]);
                      }
                      angular.forEach(MODULE_CONFIG, function(module) {
                        if( module.name == src){
                          name = module.name;
                        }else{
                          name = src;
                        }
                      });
                      return $ocLazyLoad.load(name);
                    } );
                  });
                  deferred.resolve();
                  return callback ? promise.then(function(){ return callback(); }) : promise;
              }]
          }
        }
      }
    ]
  );
