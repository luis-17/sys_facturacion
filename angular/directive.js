angular.module('app')
  .directive('inputMask', function() {
    return {
      restrict:'A',
      scope: true,
      link: function (scope, element, attrs) {
        var options;
        if (attrs.inputMask) {
          options = scope.$eval(attrs.inputMask);
        } else if (attrs.maskOptions) {
          options = scope.$eval(attrs.maskOptions);
        }
        angular.element(element).inputmask(options);
      }
    };
  })
  .directive('uiToggleClass', ['$timeout', '$document', function($timeout, $document) {
    return {
      restrict: 'AC',
      link: function(scope, el, attr) {
        el.on('click', function(e) {
          e.preventDefault();
          var classes = attr.uiToggleClass.split(','),
              targets = (attr.target && attr.target.split(',')) || Array(el),
              key = 0;
          angular.forEach(classes, function( _class ) {
            var target = targets[(targets.length && key)];            
            ( _class.indexOf( '*' ) !== -1 ) && magic(_class, target);
            $( target ).toggleClass(_class);
            key ++;
          });
          $(el).toggleClass('active');

          function magic(_class, target){
            var patt = new RegExp( '\\s' + 
                _class.
                  replace( /\*/g, '[A-Za-z0-9-_]+' ).
                  split( ' ' ).
                  join( '\\s|\\s' ) + 
                '\\s', 'g' );
            var cn = ' ' + $(target)[0].className + ' ';
            while ( patt.test( cn ) ) {
              cn = cn.replace( patt, ' ' );
            }
            $(target)[0].className = $.trim( cn );
          }
        });
      }
    };
  }])
  .directive('ngEnter', function() {
    return function(scope, element, attrs) {
      element.bind("keydown", function(event) { 
          if(event.which === 13) {
            scope.$apply(function(){
              scope.$eval(attrs.ngEnter);
            });
          }
      });
    };
  })
  .directive('scroller', function() {
    return {
      restrict: 'A',
      link: function(scope,elem,attrs){
          $(elem).on('scroll', function(evt){ 
            // PROGRAMACION DE AMBIENTES 
            $('.planning .sidebar .table').css('margin-top', -$(this).scrollTop());
            $('.planning .header .table').css('margin-left', -$(this).scrollLeft());
            // PROGRAMACION DE MEDICOS 
            $('.planning-medicos .fixed-row').css('margin-left', -$(this).scrollLeft());
            $('.planning-medicos .fixed-column').css('margin-top', -$(this).scrollTop()); 

            $('.planning-medicos .fixed-row .cell-planing.ambiente').css('left', $(this).scrollLeft()); 
            
          });
      }
    }
  })
  .directive('resetscroller', function() {
    return {
      restrict: 'A',
      link: function(scope,elem,attrs){
          $(elem).on('click', function(evt){ 
            // PROGRAMACION DE AMBIENTES 
            $('.planning .sidebar .table').css('margin-top', 0);
            $('.planning .header .table').css('margin-left', 0);
            $('.planning .body').scrollLeft(0);
            $('.planning .body').scrollTop(0);
            // PROGRAMACION DE MEDICOS 
            $('.planning-medicos .fixed-row').css('margin-left', -$(this).scrollLeft());
            $('.planning-medicos .fixed-column').css('margin-top', -$(this).scrollTop()); 

            $('.planning-medicos .fixed-row .cell-planing.ambiente').css('left', $(this).scrollLeft()); 
            
          });
      }
    }
  })
  .directive('fileModel', ['$parse', function ($parse) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
          var model = $parse(attrs.fileModel);
          var modelSetter = model.assign;
          element.bind('change', function(){
            scope.$apply(function(){
                modelSetter(scope, element[0].files[0]);
            });
          });
        }
    };
  }])
  .directive('focusMe', function($timeout, $parse) {
    return {
      link: function(scope, element, attrs) {
        var model = $parse(attrs.focusMe);

        scope.$watch(model, function(pValue) {
            value = pValue || 0;
            $timeout(function() {
              element[value].focus();
              // console.log(element[value]);
            });
        });
      }
    };
  })
  .directive('stringToNumber', function() {
    return {
      require: 'ngModel',
      link: function(scope, element, attrs, ngModel) {
        // console.log(scope);
        ngModel.$parsers.push(function(value) {
          // console.log('p '+value);
          return '' + value;
        });
        ngModel.$formatters.push(function(value) {
          // console.log('f '+value);
          return parseFloat(value, 10);
        });
      }
    };
  })
  .directive('enterAsTab', function () {
    return function (scope, element, attrs) {
      element.bind("keydown keypress", function (event) {
        if(event.which === 13 || event.which === 40) {
          event.preventDefault();
          var fields=$(this).parents('form:eq(0),body').find('input, textarea, select');
          var index=fields.index(this);
          if(index > -1 &&(index+1) < (fields.length - 1))
            fields.eq(index+1).focus();
        }
        if(event.which === 38) {
          event.preventDefault();
          var fields=$(this).parents('form:eq(0),body').find('input, textarea, select');
          var index=fields.index(this);
          if((index-1) > -1 && index < fields.length)
            fields.eq(index-1).focus();
        }
      });
    };
  })
  .directive('smartFloat', function() {
    var FLOAT_REGEXP = /^\-?\d+((\.|\,)\d+)?$/;
    return {
      require: 'ngModel',
      link: function(scope, elm, attrs, ctrl) {
        ctrl.$parsers.unshift(function(viewValue) {
          if (FLOAT_REGEXP.test(viewValue)) {
            ctrl.$setValidity('float', true);
            if(typeof viewValue === "number")
              return viewValue;
            else
              return parseFloat(viewValue.replace(',', '.'));
          } else {
            ctrl.$setValidity('float', false);
            return undefined;
          }
        });
      }
    };
  });