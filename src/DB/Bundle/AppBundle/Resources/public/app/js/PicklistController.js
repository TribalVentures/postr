var poll = angular.module('POLL',['infinite-scroll']);

poll.controller('PicklistController', function($scope, $http, $location, $timeout) {
	$scope.currentPage = 1;
	$scope.picklistType = '';
	$scope.parentPicklistId = '0';
	$scope.previousParentPicklistId = [];
	$scope.childLevel = 0;
	$scope.listElement = '';
	$scope.list = [];
	$scope.pagination = [];
	$scope.requestState = false;
	
	//Function to get account from server
	$scope.getPicklist = function() {
		if($scope.requestState || $scope.currentPage === false) {
			$scope.hideLoader($timeout);
			return;
		}
		
		$scope.showLoader();
		
		$scope.list = [];
		
		var url = $('#picklistForm').attr('data-picklist-action') + '?picklistType=' + $scope.picklistType + '&parentPicklistId=' + $scope.parentPicklistId;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.picklist) {
	    		for($index = 0; $index < data.response.picklist.length; $index++) {
	    			//$scope.childLevel = data.response.picklist[$index].childLavel;
	    			$scope.list.push(data.response.picklist[$index]);
	    		}
	    		
	    		$scope.requestState = false;
	    		$scope.hideLoader($timeout);
	    	} else {
	    		$scope.requestState = false;
	    		$scope.hideLoader($timeout);
	    	}
        });
    };
    
    $scope.getChildPicklist = function(event) {
    	$scope.previousParentPicklistId.push($scope.parentPicklistId);
    	$scope.parentPicklistId = $(event.currentTarget).attr('data-picklistid');
    	$scope.childLevel = $scope.childLevel + 1
    	$scope.getPicklist();
    }
    
    $scope.getParentPicklist = function(event) {
    	$scope.childLevel = $scope.childLevel - 1;
    	$scope.parentPicklistId = $scope.previousParentPicklistId.pop()
    	$scope.getPicklist();
    }
    
    $scope.addPicklist = function () {
    	if($scope.listElement == '') {
    		return;
    	}
    	$scope.showLoader();
    	var request = {
    				picklistType: $scope.picklistType,
    				parentPicklistId: $scope.parentPicklistId,
    				listElement: $scope.listElement,
    				childLavel: $scope.childLevel
    			};
    	
    	var url = $('#picklistForm').attr('data-picklist-add-action');
		var httpRequest = $http({
            method: 'POST',
            url: url,
            data: request
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else {
	    		$scope.listElement = '';
	    		$scope.hideLoader($timeout);
	    		$scope.getPicklist();
	    	}
        });
    }
    
    $scope.removePicklist = function (event) {
    	var isConfirm = confirm("Do you want to delete Picklist.!");
    	if (isConfirm != true) {
    	    return;
    	}
    	var picklistId = $(event.currentTarget).attr('data-picklistid');
    	if(picklistId != '') {
    		
    	}
    	$scope.showLoader();
    	var request = {
    			picklistId: picklistId
			};
	
		var url = $('#picklistForm').attr('data-picklist-remove-action');
		var httpRequest = $http({
	        method: 'POST',
	        url: url,
	        data: request
	    }).success(function(data, status) {
	    	if(data.response.error) {
	    		window.location.href = data.response.error.action;
	    	} else {
	    		$scope.hideLoader($timeout);
	    		$scope.getPicklist();
	    	}
	    });
    }
    
    $scope.showPicklist = function(event) {
    	$scope.childLevel = 0;
    	$scope.previousParentPicklistId = [];
    	$scope.parentPicklistId = '0';
    	$scope.getPicklist();
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
    	if($scope.picklistType == '') {
    		$scope.picklistType = 'category';
    	}
    	$scope.getPicklist();
    }
    $scope.init();
});