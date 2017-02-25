//var poll = angular.module('POLL',[]);

poll.controller('AdvertiserController', function($scope, $http, $location, $timeout) {
	$scope.currentPage = 1;
	$scope.list = [];
	$scope.pagination = [];
	
	//Function to get account from server
	$scope.getAdvertiserList = function() {
		$scope.showLoader();
		$scope.list = [];
		$scope.pagination = [];
		
		var url = $('#advertiserList').attr('data-url') + '?currentPage=' + $scope.currentPage;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.advertiserList.LIST) {
	    		for($index = 0; $index < data.response.advertiserList.LIST.length; $index++) {
	    			if(!data.response.advertiserList.LIST[$index].logo){
	    				data.response.advertiserList.LIST[$index].logo="uploads/logo/primo-logo.png";
	    			}
	    			$scope.list.push(data.response.advertiserList.LIST[$index]);
	    		}
	    		//$scope.currentPage = data.response.advertiserList.PAGING;
	    		$scope.pagination = data.response.advertiserList.PAGING;
	    		
	    		$timeout(function() {	    				
	    			$scope.setActivepage();
    			}, 200);
	    		
	    		$scope.hideLoader($timeout);
	    	}
        });
    };
    
    $scope.editAdvertiserDetail = {};
	$scope.editAdvertiser = function (e) {
		$scope.showLoader();
		$scope.editAdvertiserDetail = $scope.initAdvertiserDetail();
		$scope.editAdvertiserDetail['advertiserId'] =$(e.target).data('id');
		
		if($scope.list.length > 0) {
			for(var index = 0; index < $scope.list.length; index ++) {        		
        		if($scope.list[index]['advertiserId'] === $scope.editAdvertiserDetail['advertiserId']) {
        			$scope.editAdvertiserDetail = $scope.list[index];
        			
        			$('#editAdvertiserModal').modal('show');
        			$scope.hideLoader();
        			break;
        		}
        	}
		}
	};
	
	$scope.initAdvertiserDetail = function() {
		var editAdvertiserDetail = {};
		
		editAdvertiserDetail.advertiserId = 0;
		editAdvertiserDetail.advertiser = '';
		
		editAdvertiserDetail.phone = '';
		editAdvertiserDetail.email = '';
		
		editAdvertiserDetail.website = '';
		editAdvertiserDetail.logo = '';
		
		return editAdvertiserDetail;
	}
	
    $scope.show = function(e, currentPage) {
		$('.pagination-link').removeClass('active');
		$scope.currentPage = currentPage;
		$scope.getAdvertiserList();
		$(e.target).addClass('active');
    }
	
	$scope.showNext = function(e) {
		$('.pagination-link').removeClass('active');
		$scope.currentPage = $scope.currentPage + 1;
		$scope.getAdvertiserList();
		$('#pagination-link-' + $scope.currentPage).addClass('active');
    }
	
	$scope.showPrevious = function(e) {
		$('.pagination-link').removeClass('active');
		$scope.currentPage = $scope.currentPage - 1;
		$scope.getAdvertiserList();
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
    	$scope.getAdvertiserList();
    }
    $scope.init();
});