var poll = angular.module('POLL',['infinite-scroll']);

poll.controller('AdminTrendController', function($scope, $http, $location, $timeout, $window) {
	$scope.noOfRecord = 10;
	$scope.search = '';
	$scope.time = '12';
	
	$scope.searchArticle = function(search, t, callback) {
		var url = $('#storyTrend').attr('data-trendsearch') + '?q=' + search + '&t=' + t + '&a=' + (new Date()).getMilliseconds();
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	callback(data, status);
        });
	}
	
	$scope.articleList = [];
	$scope.loadedArticleList = [];
	$scope.loadedStoryIndex = 0;
	$scope.velocityRange = 70;
	$scope.getStories = function(search) {
		$scope.showLoader('.story-trend-panel-loader');
		$scope.initStorie();
		$scope.searchArticle(search, $scope.time, function(data, status) {
			if(data.response.error) {
        		//window.location.href = data.response.error.action;
	    	} else if(data.response.articlesList) {
	    		$scope.articleList = data.response.articlesList;
	    		/*for($index = 0; $index < data.response.articlesList.length; $index++) {
	    			if(data.response.articlesList[$index]['velocity'] > $scope.velocityRange) {
	    				$scope.articleList.push(data.response.articlesList[$index]);
	    			}
	    		}*/
	    		$timeout(function() {
	    			$scope.loadStories();
	    		}, 200);
	    		
	    		$scope.hideLoader('.story-trend-panel-loader');
	    	}
		});
	}
	
	$scope.loadStories = function() {
		if (typeof $scope.articleList[$scope.loadedStoryIndex] == 'undefined') {
			return;
		}
		$scope.showLoader('.story-trend-panel-loader');
		if($scope.articleList.length == 0) {
			return;
		}
		
		for($jIndex = 0; ($jIndex < $scope.noOfRecord && $scope.loadedStoryIndex < $scope.articleList.length); $jIndex ++) {
			if($scope.articleList[$scope.loadedStoryIndex]['velocity'] > $scope.velocityRange) {
				$scope.loadedArticleList.push($scope.articleList[$scope.loadedStoryIndex]);
			}
			
			$scope.loadedStoryIndex ++;
		}
		
		$scope.hideLoader('.story-trend-panel-loader');
	}
	
	$scope.category = '';
	$scope.getCategoryStories = function() {
		$scope.getStories($scope.category);
	}
	
	$scope.initStorie = function() {
		$scope.articleList = [];
		$scope.loadedStoryIndex = 0;
		$scope.loadedArticleList = [];
	}
	
	$scope.resetStories = function() {
		$scope.loadedStoryIndex = 0;
		$scope.loadedArticleList = [];
		$scope.loadStories();
	}
	
	$scope.changeStoryTime = function(e) {
		$scope.time = $(e.target).attr('data-time');
		//Keep time selection
		$('.search-hour').removeClass('active-time');
		$(e.target).addClass('active-time');
	}
	
	$scope.removeSearchedCategory = function(postId, topicName, e) {
		for($index = 0; $index < $scope.loadedArticleList.length; $index++) {
			if(postId == $scope.loadedArticleList[$index]['postId']) {
				for(var jIndex = 0; jIndex < $scope.loadedArticleList[$index].topics.length; jIndex ++) {
					if(topicName == $scope.loadedArticleList[$index].topics[jIndex].name) {
						console.log($scope.loadedArticleList[$index].topics);
						$scope.loadedArticleList[$index].topics.splice(jIndex, 1);
						console.log($scope.loadedArticleList[$index].topics);
						break;
					}
				}
				break;
			}
		}
	}
	
	//For trending article
	$scope.trendingArticleList = [];
	$scope.currentPage = 1;
	$scope.trendingArticleCategory = '';
	$scope.isRequestRunning = false;
	
	$scope.getTrendingArticle = function() {
		if($scope.isRequestRunning || $scope.currentPage == false) {
			return;
		}
		
		$scope.isRequestRunning = true;
		var url = $('#trendingArticlePanel').attr('data-treding-article-url') + '?q=' + $scope.trendingArticleCategory + '&period=' + $scope.period + '&currentPage=' + $scope.currentPage;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.tredningArticleList.LIST) {
	    		for($index = 0; $index < data.response.tredningArticleList.LIST.length; $index++) {
	    			$scope.trendingArticleList.push(data.response.tredningArticleList.LIST[$index]);
	    		}
	    		
	    		if(data.response.tredningArticleList.PAGING) {
	    			$scope.currentPage = data.response.tredningArticleList.PAGING.NEXT_PAGE;
	    		}
	    	}
        	$scope.isRequestRunning = false;
        });
		
		$timeout(function() {	    				
			$scope.getCategoryNotification();
		}, 500);
	}
	
	$scope.changeTrendingCategory = function(search) {
		$scope.currentPage = 1;
		$scope.trendingArticleList = [];
		$scope.getTrendingArticle();
	}
	
	$scope.period = '';
	$scope.changePeriod = function() {
		$scope.currentPage = 1;
		$scope.trendingArticleList = [];
		$scope.getTrendingArticle();
	}
	
	$scope.removeStories = function(e) {
		var confirmMessage = 'Do you want to delete all stories';
		if($scope.trendingArticleCategory != '') {
			confirmMessage = 'Do you want to delete all stories of category : ' + $scope.trendingArticleCategory;
		} 
		
		if(confirm(confirmMessage)) {
			$(e.currentTarget).text('Deleting...');
			var url = $(e.currentTarget).attr('data-url') + '?category=' + $scope.trendingArticleCategory;
			var httpRequest = $http({
	            method: 'POST',
	            url: url
	        }).success(function(data, status) {
	        	if(data.response.error) {
	        		window.location.href = data.response.error.action;
		    	} else {
		    		$scope.changeTrendingCategory($scope.trendingArticleCategory);
		    		$(e.currentTarget).text('Remove Stories');
		    	}
	        });
		}
	}
	
	$scope.removeCategory = function(postId, category, e) {
		var url = $('#trendingArticlePanel').attr('data-remove-category-url') + '?postId=' + postId + '&category=' + category;
		var httpRequest = $http({
            method: 'POST',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.postDetail) {
	    		for($index = 0; $index < $scope.trendingArticleList.length; $index++) {
	    			if(data.response.postDetail.postId == $scope.trendingArticleList[$index]['postId']) {
	    				$scope.trendingArticleList[$index].category = data.response.postDetail.category;
	    				$scope.trendingArticleList[$index].categoryList = data.response.postDetail.categoryList;
	    			}
	    		}
	    	}
        });
	}
	
	$scope.removeStory = function(e) {
		$(e.currentTarget).text('Deleting...');
		var url = $('#removeAllCategoryBtn').attr('data-url') + '?postId=' + $(e.currentTarget).attr('data-postId');
		var httpRequest = $http({
            method: 'POST',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.postId) {
	    		for($index = 0; $index < $scope.trendingArticleList.length; $index++) {
	    			if(data.response.postId == $scope.trendingArticleList[$index]['postId']) {
	    				$scope.trendingArticleList.splice($index, 1);
	    			}
	    		}
	    	}
        });
	}
	
	$scope.addStory = function(e) {
		var postId = '';
		if(e) {
			postId = $(e.currentTarget).attr('data-postId')
		}
		
		var postList = [];
		if(postId !='') {
			for($index = 0; $index < $scope.loadedArticleList.length; $index++) {
				if(postId == $scope.loadedArticleList[$index]['postId']) {
					postList.push($scope.loadedArticleList[$index]);
				}
			}
		} else {
			postList = $scope.loadedArticleList;
		}
		
		//Check if post id is already exist
		for(var index = 0; index < postList.length; index ++) {
			for(var jIndex = 0; jIndex < $scope.trendingArticleList.length; jIndex ++) {
				if(postList[index].postId == $scope.trendingArticleList[jIndex].postId) {
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
            url: $('#addStories').attr('data-url'),
            data: JSON.stringify(postList),
            timeout: 50000
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.status) {
	    		if(data.response.status == "true") {
	    			$scope.changeTrendingCategory($scope.trendingArticleCategory);
	    		}
	    	}
        });
	}
	
	$scope.removeSearchedStory = function(e) {
		for($index = 0; $index < $scope.loadedArticleList.length; $index++) {
			if($(e.currentTarget).attr('data-postId') == $scope.loadedArticleList[$index]['postId']) {
				$scope.loadedArticleList.splice($index, 1);
			}
		}
	}
	
	$scope.changePostPeriod = function(postId, period) {
		var url = $('#trendingArticlePanel').attr('data-change-period-url') + '?postId=' + postId + '&period=' + period;
		var httpRequest = $http({
            method: 'POST',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.postDetail) {
	    		/*for($index = 0; $index < $scope.trendingArticleList.length; $index++) {
	    			if(data.response.postDetail.postId == $scope.trendingArticleList[$index]['postId']) {
	    				$scope.trendingArticleList[$index].category = data.response.postDetail.category;
	    				$scope.trendingArticleList[$index].categoryList = data.response.postDetail.categoryList;
	    			}
	    		}*/
	    	}
        });
	}
	
	$scope.showFilter = function() {
		$('#extraFilter').toggle();
	}
	
	$scope.newCategory = '';
	$scope.categoryList = [];
	$scope.addNewCategory = function() {
		var url = $('#extraFilter').attr('data-manage-picklist-url') + '?listElement=' + $scope.newCategory + '&picklistType=category';
		var httpRequest = $http({
            method: 'POST',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.picklist) {
	    		$scope.categoryList = data.response.picklist;
	    		$window.location.reload();
	    	}
        });
	}
	
	$scope.assignCategoryList = [];
	$scope.newAssignCategory = [];
	$scope.assignCategory = function(postId, e) {
		if($scope.newAssignCategory[postId] == '') {
			return;
		}
		
		//check category is already exist
		for($index = 0; $index < $scope.trendingArticleList.length; $index++) {
			if(postId == $scope.trendingArticleList[$index]['postId']) {
				for(var jIndex = 0; jIndex < $scope.trendingArticleList[$index].categoryList.length; jIndex ++) {
					if($scope.trendingArticleList[$index].categoryList[jIndex] == $scope.newAssignCategory[postId]) {
						return;
					}
				}
			}
		}
		
		var url = $('#trendingArticlePanel').attr('data-assign-category-url') + '?postId=' + postId + '&category=' + $scope.newAssignCategory[postId];
		var httpRequest = $http({
            method: 'POST',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.postDetail) {
	    		for($index = 0; $index < $scope.trendingArticleList.length; $index++) {
	    			if(data.response.postDetail.postId == $scope.trendingArticleList[$index]['postId']) {
	    				$scope.trendingArticleList[$index].category = data.response.postDetail.category;
	    				$scope.trendingArticleList[$index].categoryList = data.response.postDetail.categoryList;
	    			}
	    		}
	    	}
        });
	}
	
	$scope.morningCategoryList = [];
	$scope.afternoonCategoryList = [];
	$scope.eveningCategoryList = [];
	$scope.getCategoryNotification = function() {
		var url = $('#notification').attr('data-category-notification');
		var httpRequest = $http({
            method: 'POST',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response) {
	    		if(data.response.morningCategory) {
	    			$scope.morningCategoryList = data.response.morningCategory;
	    		}
	    		if(data.response.afternoonCategory) {
	    			$scope.afternoonCategoryList = data.response.afternoonCategory;
	    		}
	    		if(data.response.eveningCategory) {
	    			$scope.eveningCategoryList = data.response.eveningCategory;
	    		}
	    	}
        });
	}
	
	$scope.showLoader = function(loader) {
		$(loader).show();
	}
	
	$scope.hideLoader = function(loader) {
		$timeout(function() {	    				
			$(loader).hide();
		}, 500);
	}
	
	$scope.init = function() {
		$timeout(function() {	    				
			$scope.velocityRange = 70;
		}, 500);
		$scope.getStories('', null);
	}
	
	$scope.init();
});

poll.filter('abbreviateNumber', function() {
	return function(value) {
		var newValue = value;
	    if (value >= 1000) {
	        var suffixes = ["", "k", "m", "b","t"];
	        var suffixNum = Math.floor( (""+value).length/3 );
	        var shortValue = '';
	        for (var precision = 2; precision >= 1; precision--) {
	            shortValue = parseFloat( (suffixNum != 0 ? (value / Math.pow(1000,suffixNum) ) : value).toPrecision(precision));
	            var dotLessShortValue = (shortValue + '').replace(/[^a-zA-Z 0-9]+/g,'');
	            if (dotLessShortValue.length <= 2) { break; }
	        }
	        if (shortValue % 1 != 0)  shortNum = shortValue.toFixed(1);
	        newValue = shortValue+suffixes[suffixNum];
	    }
	    return newValue;
	  }
});

poll.filter('roundDecimal', function() {
	  return function(n) {
		  if(isNaN(n)) {
			  return 'NA';
		  } else {
			  var str = n.toString();
			  if(n > 0) {
				  num = str.match(/.{1,6}/g);
			  } else {
				  num = str.match(/.{1,7}/g);
			  }
			  return num[0];
			  //return parseFloat(Math.round(n * 100) / 100).toFixed(4);
		  }
	  }
});