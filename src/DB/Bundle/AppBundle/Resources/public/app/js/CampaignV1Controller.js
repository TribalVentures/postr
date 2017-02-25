var poll = angular.module('POLL',['infinite-scroll']);

poll.controller('CampaignV1Controller', function($scope, $http, $location, $timeout) {
	$scope.currentPage = 1;
	$scope.list = [];
	$scope.pagination = [];
	$scope.campaignStatus = [];
	$scope.requestState = false;
	
	$scope.campaignSummary = {totalLink:0, totalPageViews:0, totalPollClicks:0};
	
	//Function to get account from server
	$scope.getCampaignList = function() {
		//$scope.list = [];
		//$scope.pagination = [];
		if($scope.requestState || $scope.currentPage === false) {
			$scope.hideLoader($timeout);
			return;
		}
		
		$scope.showLoader();
		$scope.requestState = true;
		var url = $('#campaignList').attr('data-url') + '?currentPage=' + $scope.currentPage;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.campaignList.LIST) {
	    		for($index = 0; $index < data.response.campaignList.LIST.length; $index++) {
	    			if(!data.response.campaignList.LIST[$index].logo){
	    				data.response.campaignList.LIST[$index].logo = "uploads/logo/primo-logo.png";
	    			}
	    			
	    			if(!data.response.campaignList.LIST[$index].thanksImage){
	    				data.response.campaignList.LIST[$index].thanksImage = "bundles/dbapp/app/images/increase.gif";
	    			}
	    			
	    			if(!data.response.campaignList.LIST[$index].pollLike) {
	    				data.response.campaignList.LIST[$index].pollLike = 0;
	    			}
	    			
	    			if(!data.response.campaignList.LIST[$index].pollDisLike) {
	    				data.response.campaignList.LIST[$index].pollDisLike = 0;
	    			}
	    			
	    			if(!data.response.campaignList.LIST[$index].session) {
	    				data.response.campaignList.LIST[$index].session = {cnt:0, tTime: 0};
	    			}
	    			
	    			if(data.response.campaignList.LIST[$index].articleImageUrl.length <= 0){
	    				data.response.campaignList.LIST[$index].articleImageUrl = $('#campaignList').attr('data-asset-url') + "bundles/dbapp/v1poll/images/blank.png";
	    			}
	    			
	    			//Create summery
	    			if(data.response.campaignList.LIST[$index].pollCount > 0) {
	    				$scope.campaignSummary.totalPollClicks = $scope.campaignSummary.totalPollClicks + parseInt(data.response.campaignList.LIST[$index].pollCount);
	    			}
	    			if(data.response.campaignList.LIST[$index].session.cnt > 0) {
	    				$scope.campaignSummary.totalPageViews = $scope.campaignSummary.totalPageViews + parseInt(data.response.campaignList.LIST[$index].session.cnt);
	    			}
	    			
	    			$scope.campaignSummary.totalLink ++;
	    			
	    			data.response.campaignList.LIST[$index].session.totalTime = $scope.millisecondsToStr(data.response.campaignList.LIST[$index].session.tTime);
	    			data.response.campaignList.LIST[$index].pageLoadTime = $scope.millisecondsToStr(data.response.campaignList.LIST[$index].pageLoadTime);
	    			
	    			$scope.list.push(data.response.campaignList.LIST[$index]);
	    		}
	    		
	    		$scope.pagination = data.response.campaignList.PAGING;
	    		$scope.currentPage = $scope.pagination.NEXT_PAGE;
	    		
	    		$timeout(function() {	    				
	    			$scope.setActivepage();
	    			
	    			var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));	    			
	    			
	    			// Success color: #10CFBD
	    			elems.forEach(function(html) {
	    				var isLoad = $(html).attr('data-load');
	    				if(isLoad == 'true') {
	    					var switchery = new Switchery(document.getElementById($(html).attr('id')), {color: '#10CFBD', size: 'small'});
		    				Switchery.prototype.handleOnchange = function(state) {
		    					$scope.changeStatus($(this.element).val(), state);
		    				}
		    				$(html).attr('data-load', false);
	    				} else {
	    					return;
	    				}
	    				
	    			});
	    			
	    			//Render bar chart for answers
	    			 $('.primo-campaign-bar').each(function() {
	    				 var data = $.parseJSON($(this).attr('data-feedback'));
	    				 $(this).sparkline(data, { type: 'pie' });
	    			 });
	    			 
	    			 //$scope.$watch('list', $scope.updateList(), true);
    			}, 200);
	    		
	    		/*$timeout(function() {
	    			for($index = 0; $index < $scope.list.length; $index++) {
	    				$scope.list[$index].pollLike = 50;
	    				$scope.list[$index].pollDisLike = 50;
	    				$('.primo-campaign-bar').each(function() {
	    					$(this).html('');
		    				$(this).sparkline([$(this).attr('data-like'),$(this).attr('data-dislike')], { type: 'pie' });
		    				console.log($index + ' === ' + $(this).attr('data-like') + ' === ' + $(this).attr('data-dislike'));
	    				});
	    			}
	    		}, 3000);*/
	    		$scope.requestState = false;
	    		$scope.hideLoader($timeout);
	    	}
        });
    };
    
    $scope.updateList = function() {
    	console.log('change the array');
    }
    
    $scope.intervalIndex = 0;
    $scope.interval = null;
    $scope.copyClipbard = function(e) {
    	$('#clipBoardTextArea').text($(e.target).data('url'));
    	
    	target = document.getElementById('clipBoardTextArea');
    	target.focus();
    	target.setSelectionRange(0, target.value.length);
    	var succeed;
    	try {
    		succeed = document.execCommand("copy");
    		$scope.interval = setInterval(function() {
    			$(e.target).fadeTo(100, 0.1).fadeTo(200, 1.0);
    			if($scope.intervalIndex > 10) {
    				$scope.intervalIndex = 0;
    				clearInterval($scope.interval);
    			} else {
    				$scope.intervalIndex ++;
    			}
    		}, 100);
    		
    	} catch(e) {
    		succeed = false;
    	}
    }
    
    $scope.editCampaignDetail = {};
    $scope.editCampaign = function(e) {
    	$scope.showLoader();
		$scope.editCampaignDetail = $scope.initCampaignDetail();
		$scope.editCampaignDetail['campaignId'] = $(e.target).data('id');
		
		if($scope.list.length > 0) {
			for(var index = 0; index < $scope.list.length; index ++) {        		
        		if($scope.list[index]['campaignId'] === $scope.editCampaignDetail['campaignId']) {
        			$scope.editCampaignDetail = $scope.list[index];
        			
        			$('#editCampaignModal').modal('show');
        			
        			$timeout(function() {
        				$('#campaignForm_advertiserId').val($scope.editCampaignDetail.advertiserId);
        				$('#campaignForm_theme').val($scope.editCampaignDetail.theme);
        				$('#campaignForm_position').val($scope.editCampaignDetail.position);
        				$('#campaignForm_eventType').val($scope.editCampaignDetail.eventType);
        				$('#campaignForm_pollId').val($scope.editCampaignDetail.pollId);
        				$('#campaignForm_pollId').trigger('change');
        			}, 200);
        			
        			$scope.hideLoader();
        			break;
        		}
        	}
		}
    }
	
	$scope.initCampaignDetail = function() {
		var campaignDetail = {};
		
		campaignDetail.campaignId = 0;
		campaignDetail.accountId = '';
		campaignDetail.advertiserId = '';
		
		campaignDetail.campaignKey = '';
		campaignDetail.campaign = '';
		campaignDetail.url = '';
		
		campaignDetail.pollId = '';
		campaignDetail.eventType = '';
		campaignDetail.eventValue = '';
		
		campaignDetail.campaignStatus = '';
		campaignDetail.userId = '';
		campaignDetail.creationDate = '';
		
		return campaignDetail;
	}
	
	$scope.millisecondsToStr = function(milliseconds) {
	    // TIP: to find current time in milliseconds, use:
	    // var  current_time_milliseconds = new Date().getTime();
		if(isNaN(milliseconds)) {
			return 'NA'
		}

	    function numberEnding (number) {
	        return (number > 1) ? 's' : '';
	    }

	    //Comment this line becasue we store second in databse
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
	
	$scope.showShareModel = function(e) {
		e.preventDefault();
		$('.share-modal:visible').fadeOut(300);
		$('.content:hidden').delay(300).fadeIn(300);
		
		$($(e.target)).parents('.content').fadeOut(300);
		$($(e.target)).parents('.poll-mobile')
			.find('.share-modal').delay(300)
			.fadeIn(300);
	}
	
	$scope.closeShareModel = function(e) {
		$($(e.target)).parent().fadeOut(300);
		$($(e.target)).parents('.poll-mobile').find('.content').delay(300)
				.fadeIn(300);
	}
	
	$scope.showSession = function(e) {
		var url = $('#campaignList').attr('data-session-url') + '?campaignId=' + $(e.target).data('id');
		window.location.href = url;
	}
	
	$scope.changeStatus = function(campaignId, state) {
		var url = $('#campaignList').attr('data-status-url');
		var httpRequest = $http({
            method: 'POST',
            url: url,
            data: { 'campaignId' : campaignId, 'campaignStatus': ((state == false) ? 1 : 0)}
        }).success(function(data, status) {});
	}
    
    $scope.show = function(e, currentPage) {
		$('.pagination-link').removeClass('active');
		$scope.currentPage = currentPage;
		$scope.getCampaignList();
		$(e.target).addClass('active');
    }
	
	$scope.showNext = function(e) {
		$('.pagination-link').removeClass('active');
		$scope.currentPage = $scope.currentPage + 1;
		$scope.getCampaignList();
		$('#pagination-link-' + $scope.currentPage).addClass('active');
    }
	
	$scope.showPrevious = function(e) {
		$('.pagination-link').removeClass('active');
		$scope.currentPage = $scope.currentPage - 1;
		$scope.getCampaignList();
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
    	$scope.getCampaignList();
    }
    //$scope.init();
});