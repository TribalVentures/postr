var poll = angular.module('BaseService',[]);

poll.factory('mySplit', function(string) {
	var array = string.split(' ');
    return $scope.array[nb];
});

poll.factory('dbLoader', function () {
    return {
    	showLoader: function(x) {
    		$('#bt-loader').modal({backdrop: 'static', keyboard: false});
        },
        hideLoader: function() {
        	$('#bt-loader').modal('hide');
        }
    };
});

poll.directive('onErrorSrc', function() {
    return {
        link: function(scope, element, attrs) {
          element.bind('error', function() {
            if (attrs.src != attrs.onErrorSrc) {
              attrs.$set('src', attrs.onErrorSrc);
            }
          });
        }
    }
});

poll.filter('domain', function() {
	return function(input) {
		var matches, output = "", urls = /\w+:\/\/([\w|\.]+)/;

		matches = urls.exec(input);

		if (matches !== null)
			output = matches[1];

		return output;
	};
});