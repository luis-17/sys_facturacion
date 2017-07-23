angular.module('app')
  .factory('$bootbox', ['$uibModal', '$window', function($uibModal, $window) {
    'use strict';
    // NOTE: this is a workaround to make BootboxJS somewhat compatible with
    // Angular UI Bootstrap in the absence of regular bootstrap.js
    if (angular.element.fn.modal === undefined) {
      angular.element.fn.modal = function(directive) {
        var that = this;
        if (directive === 'hide') {
          if (this.data('bs.modal')) {
            this.data('bs.modal').close();
            angular.element(that).remove();
          }
          return;
        } else if (directive === 'show') {
          return;
        }

        var modalInstance = $uibModal.open({
          template: angular.element(this).find('.modal-content').html()
        });
        this.data('bs.modal', modalInstance);
        setTimeout(function() {
          angular.element('.modal.ng-isolate-scope').remove();
          angular.element(that).css({
            opacity: 1,
            display: 'block'
          }).addClass('in');
        }, 100);
      };
    } 
    return $window.bootbox;
  }])
  .factory('pinesNotifications', ['$window', function ($window) {
    'use strict';
    return {
      notify: function (args) {
        args.styling = 'fontawesome';
        args.mouse_reset = false;
        var notification = new $window.PNotify(args);
        notification.notify = notification.update;
        return notification;
      },
    }
  }])
  .factory("handleBehavior", function($bootbox,$location){ 
      var handleBehavior = {
        error: function (error) {
          return function () {
            return {success: false, message: Notification.warning({message: error})};
          };
        },
        success: function (response) {
            //console.log('response.data',response.data);
            if(response.data.flag == 'session_expired' && !($location.path() == '/access/login') ){ 
              $bootbox.alert({ 
                title: "Mensaje del Sistema",
                message: response.data.message,
                buttons: { 
                  ok: {
                    label: 'INICIAR SESIÓN',
                    className: 'btn-sm btn-primary' 
                  }
                },
                callback: function () {
                  // console.log('click me', $location.path() );
                  $location.path('/access/login');
                  return false; 
                }
              });
            }
            return( response.data );
        }
      }
      return handleBehavior;
    }); 