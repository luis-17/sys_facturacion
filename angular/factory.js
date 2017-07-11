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
  }]); 