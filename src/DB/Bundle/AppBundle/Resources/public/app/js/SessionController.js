//var poll = angular.module('POLL',[]);

poll.controller('SessionController', function($scope, $http, $location, $timeout) {
	$scope.currentPage = 1;
	$scope.list = [];
	$scope.pagination = [];
	$scope.campaignId ;
	$scope.startDate = new Date().toISOString().slice(0, 10).replace('T', ' ');
	$scope.endDate = new Date().toISOString().slice(0, 10).replace('T', ' ');
	
	//Function to get account from server
	$scope.getCampaignSessionList = function() {
		$scope.showLoader();
		if(!$scope.campaignId) {
			$scope.campaignId = $('#campaignId').attr('data-default');			
		}
		
		$scope.list = [];
		$scope.pagination = [];
		
		var url = $('#campaignSessionList').attr('data-url') + '?campaignId=' + $scope.campaignId + '&startDate=' + $scope.startDate + '&endDate=' + $scope.endDate + '&currentPage=' + $scope.currentPage;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.campaignSessionList.LIST) {
	    		for($index = 0; $index < data.response.campaignSessionList.LIST.length; $index++) {
	    			data.response.campaignSessionList.LIST[$index].formatedTotalTime = $scope.millisecondsToStr(data.response.campaignSessionList.LIST[$index].totalTime);
	    			$scope.list.push(data.response.campaignSessionList.LIST[$index]);
	    		}
	    		
	    		$scope.pagination = data.response.campaignSessionList.PAGING;
	    		$timeout(function() {	    				
	    			$scope.setActivepage();
    			}, 200);
	    		$scope.hideLoader();
	    	}
        });
    };
	
	$scope.millisecondsToStr = function(milliseconds) {
	    // TIP: to find current time in milliseconds, use:
	    // var  current_time_milliseconds = new Date().getTime();

	    function numberEnding (number) {
	        return (number > 1) ? 's' : '';
	    }

	    //Comment this line becasue we store time in second in DB
	    //var temp = Math.floor(milliseconds / 1000);
	    var temp = milliseconds;
	    var years = Math.floor(temp / 31536000);
	    if (years) {
	        return years + ' year' + numberEnding(years);
	    }
	    //TODO: Months! Maybe weeks? 
	    var days = Math.floor((temp %= 31536000) / 86400);
	    if (days) {
	        return days + ' day' + numberEnding(days);
	    }
	    var hours = Math.floor((temp %= 86400) / 3600);
	    if (hours) {
	        return hours + ' hour' + numberEnding(hours);
	    }
	    var minutes = Math.floor((temp %= 3600) / 60);
	    if (minutes) {
	        return minutes + ' minute' + numberEnding(minutes);
	    }
	    var seconds = temp % 60;
	    if (seconds) {
	        return seconds + ' second' + numberEnding(seconds);
	    }
	    return 'less than a second'; //'just now' //or other string you like;
	}
    
    $scope.show = function(e, currentPage) {
		$('.pagination-link').removeClass('active');
		$scope.currentPage = currentPage;
		$scope.getCampaignSessionList();
		$(e.target).addClass('active');
    }
	
	$scope.showNext = function(e) {
		$('.pagination-link').removeClass('active');
		$scope.currentPage = $scope.currentPage + 1;
		$scope.getCampaignSessionList();
		$('#pagination-link-' + $scope.currentPage).addClass('active');
    }
	
	$scope.showPrevious = function(e) {
		$('.pagination-link').removeClass('active');
		$scope.currentPage = $scope.currentPage - 1;
		$scope.getCampaignSessionList();
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
    	$scope.getCampaignSessionList();
    }
    $scope.init();
});