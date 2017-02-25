var poll = angular.module('POLL',['BaseService']);

poll.controller('DashboardController', function($scope, $http, $location, $timeout) {
	$scope.postList = [];
	$scope.pagination = [];
	
	$scope.getSocialPostList = function(currentPage) {
		if(currentPage == false) {
			$('#load-more-post').prop('disabled', true);
			return;
		}
		$('#load-more-post-spinner').show();
		$('#load-more-post').hide();
		
		var url = $('#base-path').attr('data-url') + 'get-social-post?currentPage=' + currentPage;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.socialPostList) {
	    		if(data.response.socialPostList.LIST) {
	    			for(var index = 0; index < data.response.socialPostList.LIST.length; index ++) {
	    				$scope.postList.push(data.response.socialPostList.LIST[index]);
	    			}
	    		}
	    		if(data.response.socialPostList.PAGING) {
	    			$scope.pagination = data.response.socialPostList.PAGING;
	    		}
	    	}
        	
        	$('#load-more-post-spinner').hide();
    		$('#load-more-post').show();
        });
	}
	
	$scope.initArticle = function() {
		$scope.postList = [];
		$scope.pagination = [];
	}
	
	$scope.summary = {"post":"0","postIn":"0","reach":"0","reachIn":"0","share":"0","shareIn":"0","visit":"0","visitIn":"0"};
	$scope.loadQuiclSummary = function() {
		$('#dashboard-spinner').show();
		$('#dashboard').hide();
		
		var url = $('#base-path').attr('data-url') + 'get-summary';
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.summary) {
	    		$scope.summary = data.response.summary;
	    	}
        	
        	$('#dashboard-spinner').hide();
    		$('#dashboard').show();
        });
	}
	
    $scope.init = function() {
    	$scope.loadQuiclSummary();
    	$scope.getSocialPostList(1);
    }
    $scope.init();
});