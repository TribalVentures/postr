var poll = angular.module('POLL',['infinite-scroll']);

poll.controller('AccountController', function($scope, $http, $location, $timeout) {
	$scope.currentPage = 1;
	$scope.list = [];
	$scope.pagination = [];
	$scope.requestState = false;
	$scope.search 	= ''
	
	//Function to get account from server
	$scope.getAccountList = function() {
		if($scope.requestState || $scope.currentPage === false) {
			$scope.hideLoader($timeout);
			return;
		}
		$scope.requestState = true;
		$scope.showLoader();
		
		var url = $('#accountList').attr('data-url') + '?search=' + $scope.search + '&currentPage=' + $scope.currentPage;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.accountList.LIST) {
	    		for($index = 0; $index < data.response.accountList.LIST.length; $index++) {
	    			if(!data.response.accountList.LIST[$index].logo){
	    				data.response.accountList.LIST[$index].logo="uploads/logo/primo-logo.png";
	    			}
	    			$scope.list.push(data.response.accountList.LIST[$index]);
	    		}
	    		
	    		$scope.pagination = data.response.accountList.PAGING;
	    		$scope.currentPage = $scope.pagination.NEXT_PAGE;
	    		
	    		$scope.requestState = false;
	    		$scope.hideLoader($timeout);
	    	} else {
	    		$scope.requestState = false;
	    		$scope.hideLoader($timeout);
	    	}
        });
    };
	
    $scope.searchAccount = function() {
    	$scope.currentPage = 1;
    	$scope.list = [];
    	$scope.pagination = [];
    	$scope.requestState = false;
    		
    	$scope.getAccountList();
    }
    
    $scope.editAccountDetail = {};
    $scope.editAccount = function(e) {
		$scope.showLoader();
		$scope.editAccountDetail = $scope.initAccountDetail();
		$scope.editAccountDetail['accountId'] =$(e.target).data('id');
		
		if($scope.list.length > 0) {
			for(var index = 0; index < $scope.list.length; index ++) {        		
        		if($scope.list[index]['accountId'] === $scope.editAccountDetail['accountId']) {
        			$scope.editAccountDetail = $scope.list[index];
        			
        			console.log($scope.editAccountDetail);
        			
        			$('#editAccountModal').modal('show');
        			$scope.hideLoader();
        			break;
        		}
        	}
		}
    }
	
	$scope.initAccountDetail = function() {
		var editAccountDetail = {};
		
		editAccountDetail.account = 0;
		editAccountDetail.website = '';
		
		editAccountDetail.logo = '';
		editAccountDetail.creationDate = '';
		
		editAccountDetail.endDate = '';
		
		editAccountDetail.firstName = '';
		editAccountDetail.lastName = '';
		
		editAccountDetail.email = '';
		editAccountDetail.password = '';
		
		editAccountDetail.userType = '';
		editAccountDetail.userStatus = '';
		
		return editAccountDetail;
	}
	
	$scope.removeAccount = function(e) {
		var url = $('#accountList').attr('data-account-remove-url');
		var req = {
				method: 'POST',
				url: url,
				data: { accountId: $(e.target).data('id') }
		};

		var httpRequest = $http(req).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.accountId) { 
	    		if($scope.list.length > 0) {
	    			for(var index = 0; index < $scope.list.length; index ++) {        		
	            		if($scope.list[index]['accountId'] === data.response.accountId) {
	            			$scope.list.splice(index, 1);
	            			break;
	            		}
	            	}
	    		}
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
    	$scope.getAccountList();
    }
    //$scope.init();
});