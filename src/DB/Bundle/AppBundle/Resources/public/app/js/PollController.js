var poll = angular.module('POLL',['infinite-scroll']);

poll.controller('PollController', function($scope, $http, $location, $timeout) {
	$scope.currentPage = 1;
	$scope.list = [];
	$scope.pagination = [];

	$scope.requestState = false;
	
	//Function to get account from server
	$scope.getPollList = function() {
		if($scope.requestState || $scope.currentPage === false) {
			$scope.hideLoader($timeout);
			return;
		}
		
		$scope.showLoader();
		//$scope.list = [];
		//$scope.pagination = [];
		$scope.requestState = true;
		var url = $('#pollList').attr('data-url') + '?currentPage=' + $scope.currentPage;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.pollList.LIST) {
	    		for($index = 0; $index < data.response.pollList.LIST.length; $index++) {
	    			if(!data.response.pollList.LIST[$index].logo){
	    				data.response.pollList.LIST[$index].logo="uploads/logo/primo-logo.png";
	    			}
	    			$scope.list.push(data.response.pollList.LIST[$index]);
	    		}
	    		//$scope.currentPage = data.response.pollList.PAGING;
	    		$scope.pagination = data.response.pollList.PAGING;
	    		$scope.currentPage = $scope.pagination.NEXT_PAGE;
	    		
	    		$timeout(function() {	    				
	    			$scope.setActivepage();
    			}, 200);
	    		
	    		$scope.requestState = false;
	    		$scope.hideLoader($timeout);
	    	}
        });
    };
    
    $scope.editPollDetail = {};
    $scope.editPoll = function(e) {
    	$scope.showLoader();
		$scope.editPollDetail = $scope.initPollDetail();
		$scope.editPollDetail['pollId'] =$(e.target).data('id');
		
		if($scope.list.length > 0) {
			for(var index = 0; index < $scope.list.length; index ++) {        		
        		if($scope.list[index]['pollId'] === $scope.editPollDetail['pollId']) {
        			$scope.editPollDetail = $scope.list[index];
        			
        			//Show logo images 
        			$timeout(function() {	    				
        				$('.hover-img-edit').attr('src', $('.hover-img-edit').attr('data-url') + $scope.editPollDetail.logo);
            			$('.logo-change-edit').attr('src', $('.hover-img-edit').attr('data-url') + $scope.editPollDetail.thanksImage);
        			}, 200);
        			
        			$('#editPollModal').modal('show');
        			$scope.hideLoader();
        			break;
        		}
        	}
		}
    }
	
	$scope.initPollDetail = function() {
		var pollDetail = {};
		
		pollDetail.pollId = 0;
		pollDetail.pollQuestion = '';
		pollDetail.logo = '';
		
		pollDetail.thanksImage = '';
		pollDetail.thanksMessage = '';
		pollDetail.thanksUrl = '';
		
		return pollDetail;
	}
	
	$scope.pollDetail = {pollId:'', pollQuestion:'', logo:'', thanksMessage:'', thanksImage:''};
	$scope.showPoll = function(e) {
		$scope.pollDetail = {pollId:'', message:'', logo:'', thanksMessage:'', thanksImage:''};
		$scope.pollDetail['pollId'] =$(e.target).data('id');
		if($scope.list.length > 0) {
			for(var index = 0; index < $scope.list.length; index ++) {        		
        		if($scope.list[index]['pollId'] === $scope.pollDetail['pollId']) {
        			$scope.pollDetail = $scope.list[index];
        			
        			//Show logo images 
        			$timeout(function() {	    				
        				$('.logo-change-preview').attr('src', $('.logo-change-preview').attr('data-url') + $scope.pollDetail.logo);
            			$('.hover-preview').attr('src', $('.hover-preview').attr('data-url') + $scope.pollDetail.thanksImage);
        			}, 200);
        			
        			$('#pollPreview').modal('show');
        			break;
        		}
        	}
		}
	}
    
    $scope.show = function(e, currentPage) {
		$('.pagination-link').removeClass('active');
		$scope.currentPage = currentPage;
		$scope.getPollList();
		$(e.target).addClass('active');
    }
	
	$scope.showNext = function(e) {
		$('.pagination-link').removeClass('active');
		$scope.currentPage = $scope.currentPage + 1;
		$scope.getPollList();
		$('#pagination-link-' + $scope.currentPage).addClass('active');
    }
	
	$scope.showPrevious = function(e) {
		$('.pagination-link').removeClass('active');
		$scope.currentPage = $scope.currentPage - 1;
		$scope.getPollList();
		$('#pagination-link-' + $scope.currentPage).addClass('active');
    }
	
	$scope.setActivepage = function() {
		$('#pagination-link-' + $scope.currentPage).addClass('active');
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
    	$scope.getPollList();
    }
    //$scope.init();
});