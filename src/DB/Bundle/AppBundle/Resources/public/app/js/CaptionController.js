var poll = angular.module('POLL',['infinite-scroll']);

poll.controller('CaptionController', function($scope, $http, $location, $timeout) {
	$scope.currentPage = 1;
	$scope.list = [];
	$scope.pagination = [];

	$scope.requestState = false;
	
	$scope.period = '';
	$scope.trendingArticleCategory = '';
	
	$scope.loadStories = function() {
		if($scope.requestState || $scope.currentPage === false) {
			$scope.hideLoader($timeout);
			return;
		}
		
		$scope.showLoader();
		
		//$scope.list = [];
		$scope.requestState = true;
		var url = $('#story-trend-panel').attr('data-url') + '?period=' + $scope.period + '&trendingArticleCategory=' + $scope.trendingArticleCategory + '&currentPage=' + $scope.currentPage;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.tredningArticleList.LIST) {
	    		for($index = 0; $index < data.response.tredningArticleList.LIST.length; $index++) {
	    			$scope.list.push(data.response.tredningArticleList.LIST[$index]);
	    		}
	    		
	    		$timeout(function() {	    				
	    			var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));	    			
	    			
	    			// Success color: #10CFBD
	    			elems.forEach(function(html) {
	    				var isLoad = $(html).attr('data-load');
	    				if(isLoad == 'true') {
		    				$(html).attr('data-load', false);
	    					var switchery = new Switchery(document.getElementById($(html).attr('id')), {color: '#10CFBD'});
		    				Switchery.prototype.handleOnchange = function(state) {
		    					$scope.changeApproveStatus($(this.element).val(), state);
		    				}
	    				}
	    			});
    			}, 200);
	    		
	    		$scope.pagination = data.response.tredningArticleList.PAGING;
	    		$scope.currentPage = $scope.pagination.NEXT_PAGE
	    	}
        	$scope.requestState = false;
        	$scope.hideLoader();
        });
	}
	
	$scope.changeApproveStatus = function(trendingArticleId, approveStatus) {
		var url = $('#story-trend-panel').attr('data-approve-url');
		var httpRequest = $http({
            method: 'POST',
            url: url,
            data: { 'trendingArticleId' : trendingArticleId, 'approveStatus': ((approveStatus == false) ? 0 : 1)}
        }).success(function(data, status) {});
	}
	
	$scope.checkSelectd = function(trendingArticleId) {
		
	}
	
	$scope.changeTrendingCategory = function(trendingArticleCategory) {
		$scope.currentPage = 1;
		$scope.list = [];
		$scope.pagination = [];
		
		$scope.loadStories();
	}
	
	$scope.changePeriod = function(period) {
		$scope.currentPage = 1;
		$scope.list = [];
		$scope.pagination = [];
		
		$scope.loadStories();
	}
	
	$scope.saveCaption = function(trendingArticleId) {
		$scope.showLoader();
		
		var caption = $('#caption'+ trendingArticleId).val();
		
		var url = $('#story-trend-panel').attr('data-caption-url');
		var httpRequest = $http({
            method: 'POST',
            url: url,
            data: { 'trendingArticleId' : trendingArticleId, 'caption': caption}
        }).success(function(data, status) {
        	$scope.hideLoader();
        });
	}
	
	$scope.spikeArticleList = [];
	$scope.categoryId = '';
	$scope.spikePeriod = '';
	$scope.spikeRequestState = false;
	
	$scope.searchSpikeTrend = function() {
		if($scope.spikeRequestState) {
			return;
		}
		
		if($scope.categoryId == '') {
			alert('Please select category to get spike article');
			return;
		}
		
		if($scope.spikePeriod == '') {
			alert('Please select period to get spike article');
			return;
		}
		
		$('.spike-story-trend-panel').show();
		$scope.spikeArticleList = [];
		$scope.spikeRequestState = true;
		var url = $('#spike-story-trend-panel').attr('data-url') + '?period=' + $scope.spikePeriod + '&categoryId=' + $scope.categoryId;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.tredningArticleList) {
	    		$scope.spikeArticleList = data.response.tredningArticleList;
	    	}
        	$scope.spikeRequestState = false;
        	
        	$('.spike-story-trend-panel').hide();
        });
	}
	
	$scope.addStory = function(e) {
		var postId = '';
		if(e) {
			postId = $(e.currentTarget).attr('data-postId')
		}
		
		var postList = [];
		if(postId !='') {
			for($index = 0; $index < $scope.spikeArticleList.length; $index++) {
				if(postId == $scope.spikeArticleList[$index]['postId']) {
					postList.push($scope.spikeArticleList[$index]);
				}
			}
		} else {
			postList = $scope.spikeArticleList;
		}
		
		//Check if post id is already exist
		for(var index = 0; index < postList.length; index ++) {
			for(var jIndex = 0; jIndex < $scope.list.length; jIndex ++) {
				if(postList[index].postId == $scope.list[jIndex].postId) {
					postList.splice(index, 1);
					break;
				}
			}
		}
		
		if(postList.length < 1) {
			return;
		}
		
		var httpRequest = $http({
            method: 'POST',
            url: $('#spike-story-trend-panel').attr('data-add-url'),
            data: JSON.stringify(postList),
            timeout: 50000
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.status) {
	    		if(data.response.status == "true" || data.response.status == true) {
	    			$scope.currentPage = 1;
	    			$scope.list = [];
	    			$scope.pagination = [];
	    			$scope.loadStories();
	    		}
	    	}
        });
	}
	
	$scope.removeSearchedStory = function(e) {
		var postId = $(e.currentTarget).attr('data-postId');
		for($index = 0; $index < $scope.spikeArticleList.length; $index++) {
			if(postId == $scope.spikeArticleList[$index]['postId']) {
				$scope.spikeArticleList.splice($index, 1);
			}
		}
	}
	
	$scope.removeStory = function(e) {
		var postId = $(e.currentTarget).attr('data-postId');
		var request = {'postId': postId};
		var httpRequest = $http({
            method: 'POST',
            url: $('#story-trend-panel').attr('data-remove-url'),
            data: JSON.stringify(request),
            timeout: 50000
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.postId) {
    			for(var jIndex = 0; jIndex < $scope.list.length; jIndex ++) {
    				if(data.response.postId == $scope.list[jIndex].postId) {
    					$scope.list.splice(jIndex, 1);
    					break;
    				}
    			}
	    	} else {
	    		console.log(data);
	    	}
        });
	}
	
	$scope.newArticleToolBarToggle = function(event) {
		if($("#new-article-panel").hasClass('col-md-6')) {
			$("#new-article-panel").removeClass('col-md-6').addClass('col-md-1');
			$("#trending-article_panel").removeClass('col-md-6').addClass('col-md-11');
			
			$('.new-article-tool-bar-toggle').hide();
			$(event.currentTarget).find('span').removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-right');
		} else {
			$("#new-article-panel").removeClass('col-md-1').addClass('col-md-6');
			$("#trending-article_panel").removeClass('col-md-11').addClass('col-md-6');
			
			$('.new-article-tool-bar-toggle').show();
			$(event.currentTarget).find('span').removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-left');
		}
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