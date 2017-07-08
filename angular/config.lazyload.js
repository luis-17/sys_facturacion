// lazyload config

angular.module('app')
    /**
   * jQuery plugin config use ui-jq directive , config the js and css files that required
   * key: function name of the jQuery plugin
   * value: array of the css js file located
   */
  .constant('JQ_CONFIG', {
      easyPieChart:   [   'assets/libs/jquery/jquery.easy-pie-chart/dist/jquery.easypiechart.fill.js'],
      sparkline:      [   'assets/libs/jquery/jquery.sparkline/dist/jquery.sparkline.retina.js'],
      plot:           [   'assets/libs/jquery/flot/jquery.flot.js',
                          'assets/libs/jquery/flot/jquery.flot.pie.js', 
                          'assets/libs/jquery/flot/jquery.flot.resize.js',
                          'assets/libs/jquery/flot.tooltip/js/jquery.flot.tooltip.min.js',
                          'assets/libs/jquery/flot.orderbars/js/jquery.flot.orderBars.js',
                          'assets/libs/jquery/flot-spline/js/jquery.flot.spline.min.js'],
      moment:         [   'assets/libs/jquery/moment/moment.js'],
      screenfull:     [   'assets/libs/jquery/screenfull/dist/screenfull.min.js'],
      slimScroll:     [   'assets/libs/jquery/slimscroll/jquery.slimscroll.min.js'],
      sortable:       [   'assets/libs/jquery/html5sortable/jquery.sortable.js'],
      nestable:       [   'assets/libs/jquery/nestable/jquery.nestable.js',
                          'assets/libs/jquery/nestable/jquery.nestable.css'],
      filestyle:      [   'assets/libs/jquery/bootstrap-filestyle/src/bootstrap-filestyle.js'],
      slider:         [   'assets/libs/jquery/bootstrap-slider/bootstrap-slider.js',
                          'assets/libs/jquery/bootstrap-slider/bootstrap-slider.css'],
      chosen:         [   'assets/libs/jquery/chosen/chosen.jquery.min.js',
                          'assets/libs/jquery/chosen/bootstrap-chosen.css'],
      TouchSpin:      [   'assets/libs/jquery/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js',
                          'assets/libs/jquery/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css'],
      wysiwyg:        [   'assets/libs/jquery/bootstrap-wysiwyg/bootstrap-wysiwyg.js',
                          'assets/libs/jquery/bootstrap-wysiwyg/external/jquery.hotkeys.js'],
      dataTable:      [   'assets/libs/jquery/datatables/media/js/jquery.dataTables.min.js',
                          'assets/libs/jquery/plugins/integration/bootstrap/3/dataTables.bootstrap.js',
                          'assets/libs/jquery/plugins/integration/bootstrap/3/dataTables.bootstrap.css'],
      vectorMap:      [   'assets/libs/jquery/bower-jvectormap/jquery-jvectormap-1.2.2.min.js', 
                          'assets/libs/jquery/bower-jvectormap/jquery-jvectormap-world-mill-en.js',
                          'assets/libs/jquery/bower-jvectormap/jquery-jvectormap-us-aea-en.js',
                          'assets/libs/jquery/bower-jvectormap/jquery-jvectormap.css'],
      footable:       [   'assets/libs/jquery/footable/v3/js/footable.min.js',
                          'assets/libs/jquery/footable/v3/css/footable.bootstrap.min.css'],
      fullcalendar:   [   'assets/libs/jquery/moment/moment.js',
                          'assets/libs/jquery/fullcalendar/dist/fullcalendar.min.js',
                          'assets/libs/jquery/fullcalendar/dist/fullcalendar.css',
                          'assets/libs/jquery/fullcalendar/dist/fullcalendar.theme.css'],
      daterangepicker:[   'assets/libs/jquery/moment/moment.js',
                          'assets/libs/jquery/bootstrap-daterangepicker/daterangepicker.js',
                          'assets/libs/jquery/bootstrap-daterangepicker/daterangepicker-bs3.css'],
      tagsinput:      [   'assets/libs/jquery/bootstrap-tagsinput/dist/bootstrap-tagsinput.js',
                          'assets/libs/jquery/bootstrap-tagsinput/dist/bootstrap-tagsinput.css']
                      
    }
  )
  .constant('MODULE_CONFIG', [ 
      {
          name: 'ui.select',
          files: [
              'assets/libs/angular/angular-ui-select/dist/select.min.js',
              'assets/libs/angular/angular-ui-select/dist/select.min.css'
          ]
      },
      {
          name:'angularFileUpload',
          files: [
            'assets/libs/angular/angular-file-upload/angular-file-upload.js'
          ]
      },
      {
          name:'ui.calendar',
          files: ['assets/libs/angular/angular-ui-calendar/src/calendar.js']
      },
      {
          name: 'ngImgCrop',
          files: [
              'assets/libs/angular/ngImgCrop/compile/minified/ng-img-crop.js',
              'assets/libs/angular/ngImgCrop/compile/minified/ng-img-crop.css'
          ]
      },
      {
          name: 'angularBootstrapNavTree',
          files: [
              'assets/libs/angular/angular-bootstrap-nav-tree/dist/abn_tree_directive.js',
              'assets/libs/angular/angular-bootstrap-nav-tree/dist/abn_tree.css'
          ]
      },
      {
          name: 'toaster',
          files: [
              'assets/libs/angular/angularjs-toaster/toaster.js',
              'assets/libs/angular/angularjs-toaster/toaster.css'
          ]
      },
      {
          name: 'textAngular',
          files: [
              'assets/libs/angular/textAngular/dist/textAngular-sanitize.min.js',
              'assets/libs/angular/textAngular/dist/textAngular.min.js'
          ]
      },
      {
          name: 'vr.directives.slider',
          files: [
              'assets/libs/angular/venturocket-angular-slider/build/angular-slider.min.js',
              'assets/libs/angular/venturocket-angular-slider/build/angular-slider.css'
          ]
      },
      {
          name: 'com.2fdevs.videogular',
          files: [
              'assets/libs/angular/videogular/videogular.min.js'
          ]
      },
      {
          name: 'com.2fdevs.videogular.plugins.controls',
          files: [
              'assets/libs/angular/videogular-controls/controls.min.js'
          ]
      },
      {
          name: 'com.2fdevs.videogular.plugins.buffering',
          files: [
              'assets/libs/angular/videogular-buffering/buffering.min.js'
          ]
      },
      {
          name: 'com.2fdevs.videogular.plugins.overlayplay',
          files: [
              'assets/libs/angular/videogular-overlay-play/overlay-play.min.js'
          ]
      },
      {
          name: 'com.2fdevs.videogular.plugins.poster',
          files: [
              'assets/libs/angular/videogular-poster/poster.min.js'
          ]
      },
      {
          name: 'com.2fdevs.videogular.plugins.imaads',
          files: [
              'assets/libs/angular/videogular-ima-ads/ima-ads.min.js'
          ]
      },
      {
          name: 'xeditable',
          files: [
              'assets/libs/angular/angular-xeditable/dist/js/xeditable.min.js',
              'assets/libs/angular/angular-xeditable/dist/css/xeditable.css'
          ]
      },
      {
          name: 'smart-table',
          files: [
              'assets/libs/angular/angular-smart-table/dist/smart-table.min.js'
          ]
      },
      {
          name: 'angular-skycons',
          files: [
              'assets/libs/angular/angular-skycons/angular-skycons.js'
          ]
      }
    ]
  )
  // oclazyload config
  .config(['$ocLazyLoadProvider', 'MODULE_CONFIG', function($ocLazyLoadProvider, MODULE_CONFIG) {
      // We configure ocLazyLoad to use the lib script.js as the async loader
      $ocLazyLoadProvider.config({
          debug:  false,
          events: true,
          modules: MODULE_CONFIG
      });
  }])
;
