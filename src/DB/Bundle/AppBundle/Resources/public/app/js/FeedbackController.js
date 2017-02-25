var poll = angular.module('POLL',['infinite-scroll']);

poll.controller('FeedbackController', function($scope, $http, $location, $timeout, $sce) {
	$scope.currentPage = 1;
	$scope.list = [];
	$scope.pagination = [];

	$scope.requestState = false;
	
	$scope.getFeedbackList = function() {
		if($scope.requestState || $scope.currentPage === false) {
			$scope.hideLoader($timeout);
			return;
		}
		
		$scope.showLoader();
		
		//$scope.list = [];
		$scope.requestState = true;
		var url = $('#feedbackList').attr('data-url') + '?currentPage=' + $scope.currentPage;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.userFeedbackList.LIST) {
	    		for($index = 0; $index < data.response.userFeedbackList.LIST.length; $index++) {
	    			data.response.userFeedbackList.LIST[$index].feedback = $sce.trustAsHtml(data.response.userFeedbackList.LIST[$index].feedback);
	    			data.response.userFeedbackList.LIST[$index].note = $sce.trustAsHtml(data.response.userFeedbackList.LIST[$index].note);
	    			$scope.list.push(data.response.userFeedbackList.LIST[$index]);
	    		}
	    		
	    		$scope.requestState = false;
	    		
	    		$scope.pagination = data.response.userFeedbackList.PAGING;
	    		$scope.currentPage = $scope.pagination.NEXT_PAGE;
	    		
	    		$scope.hideLoader($timeout);
	    	} else {
	    		$scope.requestState = false;
	    		$scope.hideLoader($timeout);
	    	}
        });
	}
	
	$scope.saveComment = function(e) {
		var request = {};
		request.feedbackId 	= $(e.currentTarget).attr('data-id');
		request.comment		= $('#comment_' + request.feedbackId).val();
		$(e.currentTarget).text('Saving...');
		var url = $('#feedbackList').attr('data-save-comment');
		var httpRequest = $http({
            method: 'POST',
            url: url,
            data: request
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	}
        	$(e.currentTarget).text('Save');
        });
	}
	
	$scope.deleteFeedback = function(e) {
		var request = {};
		request.feedbackId 	= $(e.currentTarget).attr('data-id');
		var url = $('#feedbackList').attr('data-delete');
		var httpRequest = $http({
            method: 'POST',
            url: url,
            data: request
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.feedbackForm.feedbackId) {
    			for(var jIndex = 0; jIndex < $scope.list.length; jIndex ++) {
    				if(data.response.feedbackForm.feedbackId == $scope.list[jIndex].feedbackId) {
    					$scope.list.splice(jIndex, 1);
    					break;
    				}
    			}
	    	} else {
	    		console.log(data);
	    	}
        });
	}
	
	$scope.showLoader = function() {
		$('.bt-ajax-fade').show();
		$('.bt-ajax-modal').show();
	}
	
	$scope.hideLoader = function() {
		$timeout(function() {	    				
			$('.bt-ajax-fade').hide();
			$('.bt-ajax-modal').hide();
		}, 500);
	}
	
    $scope.init = function() {
    	if($scope.categoryId == '') {
    		$scope.categoryId = 0;
    	}
    	
    	//$scope.getCategoryList();
    }
    $scope.init();
});