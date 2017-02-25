var poll = angular.module('POLL',['infinite-scroll']);

poll.controller('TrendController', function($scope, $http, $location, $timeout) {
	$scope.noOfRecord = 10;
	
	$scope.searchArticle = function(search, t, callback) {
		var url = $('#storyTrend').attr('data-trendsearch') + '?q=' + search + '&t=' + t;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	callback(data, status);
        });
	}
	
	$scope.articleList = [];
	$scope.search = '';
	$scope.time = 12;
	$scope.searchType = 'stories';
	$scope.getStories = function(search, e) {
		$scope.showLoader('.story-trend-panel-loader');
		$scope.initStorie();
		$scope.searchArticle(search, $scope.time, function(data, status) {
			if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.articlesList) {
	    		$scope.articleList = data.response.articlesList;
	    		$timeout(function() {
	    			$scope.loadStories();
	    		}, 200);
	    		
	    		$scope.hideLoader('.story-trend-panel-loader');
	    	}
		});
	}
	
	$scope.initStorie = function() {
		$scope.articleList = [];
		$scope.loadedStoryIndex = 0;
		$scope.loadedArticleList = [];
	}
	
	$scope.changeStoryTime = function(e) {
		$scope.time = $(e.target).attr('data-time');
		$scope.initSearchFilter($scope.searchType);
	}
	
	$scope.loadedStoryIndex = 0;
	$scope.loadedArticleList = [];
	$scope.loadStories = function() {
		if (typeof $scope.articleList[$scope.loadedStoryIndex] == 'undefined') {
			return;
		}
		$scope.showLoader('.story-trend-panel-loader');
		if($scope.articleList.length == 0) {
			return;
		}
		
		for($jIndex = 0; ($jIndex < $scope.noOfRecord && $scope.loadedStoryIndex < $scope.articleList.length); $jIndex ++) {
			$scope.loadedArticleList.push($scope.articleList[$scope.loadedStoryIndex]);
			$scope.loadedStoryIndex ++;
		}
		
		$scope.hideLoader('.story-trend-panel-loader');
	}
	
	$scope.changeSearchType = function(searchType, e) {
		e.preventDefault();
		$scope.searchType = searchType;
		$scope.initSearchFilter($scope.searchType);
	}
	
	$scope.initSearchFilter = function(searchType) {
		if(searchType == 'stories') {
			$('#storyTrend').show();
			$('#keywordsTrend').hide();
			
			$('.search-type').removeClass('active-time');
			$('#storyTrend').find('.search-type-stories').addClass('active-time');
			
			//Keep time selection
			$('.search-hour').removeClass('active-time');
			$('#storyTrend').find('.search-hour-' + $scope.time).addClass('active-time');
		} else {
			$('#storyTrend').hide();
			$('#keywordsTrend').show();
			
			$('.search-type').removeClass('active-time');
			$('#keywordsTrend').find('.search-type-keywords').addClass('active-time');
			
			//Keep time selection
			$('.search-hour').removeClass('active-time');
			$('#keywordsTrend').find('.search-hour-' + $scope.time).addClass('active-time');
			
			//Call api if no keywords 
			if($scope.keywordsList.length <= 0) {
				$scope.getKeywords($scope.search, null);
			}
		}
	}
	
	//Coading to handle keywords searchs
	$scope.keywordsList = [];
	$scope.keywordsArticleList = [];
	$scope.getKeywords = function(search, e) {
		$scope.showLoader('.menu-trend-loader');
		
		$scope.initKeywords();
		$scope.searchArticle(search, $scope.time, function(data, status) {
			if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.articlesList) {
	    		var articleList = data.response.articlesList;
	    		//calculate the keywords
	    		var keywordsList = [];
	    		for(var index = 0; index < articleList.length; index ++) {
	    			var artile = articleList[index];
					if(artile.keywords && artile.velocity > 60) {
						var keywordsTempList = artile.keywords.split(",");
			    		for(var tempKIndex = 0; tempKIndex < keywordsTempList.length; tempKIndex ++) {
			    			keywordsTempList[tempKIndex] = keywordsTempList[tempKIndex].trim();
			    			
			    			if(keywordsTempList[tempKIndex] == '' || keywordsTempList[tempKIndex].length > 30) {
			    				continue;
			    			}
			    			
			    			//Check is keywords is already exist in keywords list
			    			var isKeywordNotFound = true;
			    			for(var kIndex = 0; kIndex < keywordsList.length; kIndex ++) {
			    				if(keywordsList[kIndex]) {
				    				if(keywordsTempList[tempKIndex].toLowerCase() == keywordsList[kIndex].keyword.toLowerCase()) {
				    					isKeywordNotFound = false;
				    					keywordsList[kIndex].count = keywordsList[kIndex] + 1;
				    					
				    					//Change other parameter if multiple post having same keywords
				    					if(keywordsList[kIndex].postIds.indexOf(artile.postId) < 0) {
					    					keywordsList[kIndex].postIds = keywordsList[kIndex].postIds + ',' + artile.postId;
					    					
					    					keywordsList[kIndex].totalSocial = keywordsList[kIndex].totalSocial + artile.totalSocial;
					    					
					    					keywordsList[kIndex].velocity = keywordsList[kIndex].velocity + artile.velocity;
					    					
					    					keywordsList[kIndex].postCount = keywordsList[kIndex].postCount + 1;
					    					
					    					keywordsList[kIndex].avgVelocity = keywordsList[kIndex].velocity / keywordsList[kIndex].postCount;
					    					
					    					$scope.keywordsArticleList[artile.postId] = artile;
					    				}
				    				}
			    				}
			    			}
			    			
			    			if(isKeywordNotFound) {
			    				var keywordDetail = {};
		    					keywordDetail.keyword = keywordsTempList[tempKIndex];
		    					keywordDetail.count = 1;
		    					
		    					keywordDetail.totalSocial = artile.totalSocial;
		    					keywordDetail.postIds = artile.postId;

		    					keywordDetail.velocity = artile.velocity;
		    					keywordDetail.avgVelocity = artile.velocity;
		    					
		    					keywordDetail.postCount = 1;
		    					
		    					keywordDetail.sentiment = 0;
		    					keywordDetail.avgSentiment = 0;
		    					keywordDetail.sentimentPostCount = 0;
		    					
		    					keywordsList.push(keywordDetail);
		    					
		    					$scope.keywordsArticleList[artile.postId] = artile;
			    			}
			    		}
					}
	    		}
	    		
	    		keywordsList = keywordsList.sort(function(a, b) {
	        		return b.avgVelocity - a.avgVelocity;
	        	});
	    		
	    		$scope.keywordsList = keywordsList;
	    		
	    		$timeout(function() {
	    			$scope.loadKeywords();
	    		}, 200);
	    		
	    		$scope.hideLoader('.menu-trend-loader');
	    	}
		});
	}
	
	$scope.initKeywords = function() {
		$scope.keywordsList = [];
		$scope.keywordsArticleList = [];
		
		$scope.loadedKeywordList = [];
		
		$scope.loadedKeywordIndex = 0;
	}
	
	$scope.loadedKeywordIndex = 0;
	$scope.loadedKeywordList = [];
	$scope.loadKeywords = function() {
		if (typeof $scope.keywordsList[$scope.loadedKeywordIndex] == 'undefined') {
			return;
		}
		
		$scope.showLoader();
		if($scope.keywordsList.length == 0) {
			return;
		}
		
		for($jIndex = 0; ($jIndex < $scope.noOfRecord && $scope.loadedKeywordIndex < $scope.keywordsList.length); $jIndex ++) {
			$scope.loadedKeywordList.push($scope.keywordsList[$scope.loadedKeywordIndex]);
			$scope.loadedKeywordIndex ++;
		}
		
		$timeout(function() {
			//hide columns if alrady keywords articles shows
			
			if($('#stories-trend-panel').is(':visible')) {
				$('.keyword-field').hide();
				$('.trend-menu').addClass('col-md-3').removeClass('col-md-12');
				$('.trend-menu-topic').removeClass('col-md-4').removeClass('primo_inline_col');
			}
		}, 200);
		
		$scope.hideLoader();
	}
	
	$scope.keywordsArticleList = [];
	$scope.searchKyeowrdsStories = function(stories, e) {
		$scope.showLoader('.keywords-trend-loader');
		
		if(e) {
			$('.trend-menu-topic').find('.pull-right').removeClass('glyphicon').removeClass('glyphicon-chevron-right');
			$(e.currentTarget).find('.pull-right').addClass('glyphicon').addClass('glyphicon-chevron-right');
		}
		
		$scope.initKeywordsStories();
		$scope.showKeywordsStories(true);
		$scope.searchArticle(stories, $scope.time, function(data, status) {
			if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.articlesList) {
	    		$scope.keywordsArticleList = data.response.articlesList;
	    		$timeout(function() {
	    			$scope.loadKyeowrdsStories();
	    		}, 200);
	    		
	    		$scope.hideLoader('.keywords-trend-loader');
	    	}
		});
	}
	
	$scope.loadedStoryStoriesIndex = 0;
	$scope.loadedStoryArticleList = [];
	$scope.loadKyeowrdsStories = function() {
		if (typeof $scope.keywordsArticleList[$scope.loadedStoryStoriesIndex] == 'undefined') {
			return;
		}
		$scope.showLoader('.keywords-trend-loader');
		if($scope.keywordsArticleList.length == 0) {
			return;
		}
		
		for($jIndex = 0; ($jIndex < $scope.noOfRecord && $scope.loadedStoryStoriesIndex < $scope.keywordsArticleList.length); $jIndex ++) {
			$scope.loadedStoryArticleList.push($scope.keywordsArticleList[$scope.loadedStoryStoriesIndex]);
			$scope.loadedStoryStoriesIndex ++;
		}
		
		$scope.hideLoader('.keywords-trend-loader');
	}
	
	$scope.initKeywordsStories = function() {
		$scope.loadedStoryStoriesIndex = 0;
		$scope.loadedStoryArticleList = [];
		$scope.keywordsArticleList = [];
	}
	
	$scope.closeTrend = function() {
		$scope.showKeywordsStories(false);
	}
	
	$scope.showKeywordsStories = function(status) {
		if(status == true) {
			$('#stories-trend-panel').show();
			$('.keyword-field').hide();
			
			$('.trend-menu').addClass('col-md-3').removeClass('col-md-12');
			$('.trend-menu-topic').removeClass('col-md-4').removeClass('primo_inline_col');
		} else {
			$('#stories-trend-panel').hide();
			$('.keyword-field').show();
			
			$('.trend-menu').addClass('col-md-12').removeClass('col-md-3');
			$('.trend-menu-topic').addClass('col-md-4').addClass('primo_inline_col');
			
			$('.trend-menu-topic').find('.pull-right').removeClass('glyphicon').removeClass('glyphicon-chevron-right');
		}
	}
	
	$scope.sentimentalRequestCount = 0;
	$scope.noOfAjaxRequest = 5;
	$scope.loadedStoryStoriesIndex = 0;
	$scope.loadsentiment = function() {
		
	}
	
	//================================================================================
	/*
	$scope.keywordsList = [];
	$scope.keywordsArticleList = [];
	$scope.keywordsArticle = {};
	$scope.sentimentPostIndex = 0;
	$scope.loadedSentimentPostId = '';
	$scope.currentSearch = {trend:'', e:null, keywordStatus:true};
	$scope.noOfAjaxRequest = 5;
	
	$scope.getArticle_old = function(trend, e, keywordStatus) {
		$scope.currentSearch = {trend:trend, e:e, keywordStatus:keywordStatus};
		
		$scope.showLoader();
		$('.trending-keyword').removeClass('active-trend');
		if(e) {
			$(e.currentTarget).addClass('active-trend');
		}
		
		if(keywordStatus) {
			$scope.keywordsList = [];
			$scope.keywordsArticleList = [];
			$scope.keywordsArticle = {};
			
			$scope.initKeywordSearch();
			
			$('.trend-menu').addClass('col-md-12').removeClass('col-md-3');
			$('#trend-panel').hide();
			$('.trend-menu-sentimental').show();
			$('.trend-menu-score').show();
			$('.trend-menu-social').show();
			$('.trend-menu-topic').addClass('col-md-4').addClass('primo_inline_col');
			$('.menu-trend-loader').show();
		} else {
			$('#trend-panel').show();
			$scope.shortKeyword(keywordStatus);
			$('.menu-trend-loader').hide();
		}
		
		$scope.articleList = [];
		$scope.trendList = [];
		$scope.index = 0;
		var t = 12;
		if($(".active-time").length > 0) {
			t = $(".active-time").attr('data-time');
		}
		var url = $('#trend-panel').attr('data-trendsearch') + '?q=' + trend + '&t=' + t;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.articlesList) {
	    		$scope.articleList = data.response.articlesList;
	    		
	    		if(keywordStatus) {
	    			$scope.getKeywords($scope.articleList);
	    			$scope.loadedSentimentPostId = '';
	    			$scope.sentimentPostIndex = 0;
	    		}
	    		
	    		$timeout(function() {
	    			$scope.loadArticleList();
	    			$scope.loadKeywordsList();
	    		}, 200);
	    	}
        	$scope.hideLoader();
        });
	}
	
	$scope.index = 0;
	$scope.noOfRecord = 10;
	$scope.trendList = [];
	$scope.loadArticleList = function() {
		if (typeof $scope.articleList[$scope.index] == 'undefined') {
			return;
		}
		
		$scope.showLoader();
		if($scope.articleList.length == 0) {
			return;
		}
		
		for($jIndex = 0; ($jIndex < $scope.noOfRecord && $scope.index < $scope.articleList.length); $jIndex ++) {
			$scope.trendList.push($scope.articleList[$scope.index]);
			$scope.index ++;
		}
		
		$scope.hideLoader();
	}
	
	$scope.getKeywords_old = function(articleList) {
		$scope.keywordsList = [];
		var tempArticleList = [];
		$scope.keywordsArticleList = [];
		$scope.keywordsArticle = {};
		if(articleList && articleList.length > 0) {
			for(var index = 0; index < articleList.length; index ++) {
				var artile = articleList[index];
				var isArticleHavingKeyword = false;
				if(artile.keywords && artile.velocity > 60) {
		    		var keywordsTempList = artile.keywords.split(",");
		    		for(var tempKIndex = 0; tempKIndex < keywordsTempList.length; tempKIndex ++) {
		    			keywordsTempList[tempKIndex] = keywordsTempList[tempKIndex].trim();
		    			
		    			if(keywordsTempList[tempKIndex] == '' || keywordsTempList[tempKIndex].length > 30) {
		    				continue;
		    			}
		    			
		    			var keywordIndex = null;
		    			for(var kIndex = 0; kIndex < $scope.keywordsList.length; kIndex ++) {
		    				if($scope.keywordsList[kIndex]) {
			    				if(keywordsTempList[tempKIndex].toLowerCase() == $scope.keywordsList[kIndex].keyword.toLowerCase()) {
			    					keywordIndex = kIndex;
			    				}
		    				}
		    			}
		    			
		    			if(keywordIndex == null) {
		    				var keywordDetail = {};
	    					keywordDetail.keyword = keywordsTempList[tempKIndex];
	    					keywordDetail.count = 1;
	    					keywordDetail.totalSocial = artile.totalSocial;
	    					keywordDetail.postIds = artile.postId;
	    					keywordDetail.indexs = index;
	    					keywordDetail.velocity = artile.velocity;
	    					keywordDetail.postCount = 1;
	    					keywordDetail.avgVelocity = artile.velocity;
	    					keywordDetail.sentiment = 0;
	    					keywordDetail.avgSentiment = 0;
	    					keywordDetail.sentimentPostCount = 0;
	    					
	    					$scope.keywordsList.push(keywordDetail);
	    					
	    					isArticleHavingKeyword = true;
	    					
	    					$scope.keywordsArticleList[artile.postId] = artile;
	    					
		    			} else {
		    				$scope.keywordsList[keywordIndex].count = $scope.keywordsList[keywordIndex].count + 1;
		    				if($scope.keywordsList[keywordIndex].postIds.indexOf(artile.postId) < 0) {
		    					$scope.keywordsList[keywordIndex].postIds = $scope.keywordsList[keywordIndex].postIds + ',' + artile.postId;
		    					$scope.keywordsList[keywordIndex].indexs = $scope.keywordsList[keywordIndex].indexs + ',' + index;
		    					
		    					$scope.keywordsList[keywordIndex].totalSocial = $scope.keywordsList[keywordIndex].totalSocial + artile.totalSocial;
		    					$scope.keywordsList[keywordIndex].velocity = $scope.keywordsList[keywordIndex].velocity + artile.velocity;
		    					$scope.keywordsList[keywordIndex].postCount = $scope.keywordsList[keywordIndex].postCount + 1;
		    					
		    					$scope.keywordsList[keywordIndex].avgVelocity = $scope.keywordsList[keywordIndex].velocity / $scope.keywordsList[keywordIndex].postCount;
		    					
		    					isArticleHavingKeyword = true;
		    					
		    					$scope.keywordsArticleList[artile.postId] = artile;
		    				}
	    				}
		    		}
				}
			}
		}
		
		$scope.keywordsList = $scope.keywordsList.sort(function(a, b) {
    		return b.avgVelocity - a.avgVelocity;
    	});
	}
	
	$scope.searchTrend = function(trend, e) {
		$scope.getArticle(trend, e, false);
	}
	
	$scope.closeTrend = function() {
		$('.trend-menu').addClass('col-md-12').removeClass('col-md-3');
		$('#trend-panel').hide();
		$('.trend-menu-sentimental').show();
		$('.trend-menu-score').show();
		$('.trend-menu-social').show();
		$('.trend-menu-topic').addClass('col-md-4').addClass('primo_inline_col');
		$('.menu-trend-loader').show();
	}
	
	$scope.shortKeyword = function(keywordStatus) {
		$('.trend-menu-sentimental').hide();
		$('.trend-menu-score').hide();
		$('.trend-menu-social').hide();
		$('.trend-menu').addClass('col-md-3').removeClass('col-md-12');
		$('.trend-menu-topic').removeClass('col-md-4').removeClass('primo_inline_col');
	}
	
	$scope.loadsentiment = function() {
		if($scope.sentimentalRequestCount > $scope.noOfAjaxRequest) {
			return;
		}
		
		if($scope.sentimentPostIndex > $scope.loadedKeywordsList.length) {
			return;
		}
		
		for(var index = 0; index < 5; index ++) {
			//var article = $scope.keywordsArticleList[$scope.sentimentPostIndex];
			$keyworlDetail = $scope.loadedKeywordsList[$scope.sentimentPostIndex];
			if (typeof $keyworlDetail == 'undefined') {
				continue;
			}
			
			var postIdList = $keyworlDetail.postIds.split(",");
			if(typeof postIdList == 'undefined') {
				continue;
			}
			
			for(var postIndex = 0; postIndex < postIdList.length; postIndex ++) {
				article = $scope.keywordsArticleList[postIdList[postIndex]];
				if (typeof article == 'undefined') {
					continue;
				}
				
				if($scope.loadedSentimentPostId.indexOf(article.postId) > -1) {
					continue;
				} else {
					$scope.loadedSentimentPostId = $scope.loadedSentimentPostId + ',' + article.postId;
				}
				
				if(article.sentiment == 0) {
					$scope.getSentiment(article);
				} else {
					console.log(article);
				}
			}
			
			$scope.sentimentPostIndex ++;
		}
	}
	
	$scope.sentimentalRequestCount = 0;
	$scope.getSentiment = function(article) {
		$scope.sentimentalRequestCount ++;
		var serntimentaPost = {};
		serntimentaPost.postId = article.postId;
		serntimentaPost.title = article.title;
		serntimentaPost.description = article.description;
		serntimentaPost.publisher = article.publisher;
		
		var postList = [serntimentaPost];
		var httpRequest = $http({
            method: 'POST',
            url: $('.trend-menu').attr('data-sentiment-url'),
            data: JSON.stringify(postList),
            timeout: 50000
        }).success(function(data, status) {
        	if(data.response.sentimentPostList.length > 0) {
        		for(var index = 0; index < data.response.sentimentPostList.length; index ++) {
    				$post = data.response.sentimentPostList[index];
    				
    				$scope.keywordsArticleList[$post.postId].sentiment + $post.sentiment;
    				
    				var isArticleFound = false;
    				for(var kIndex = 0; kIndex < $scope.loadedKeywordsList.length; kIndex ++) {
    					if(typeof $scope.loadedKeywordsList[kIndex] != 'undefined' && $scope.loadedKeywordsList[kIndex].keyword != '') {
    						if($scope.loadedKeywordsList[kIndex].postIds.indexOf($post.postId) > -1) {
    							$scope.loadedKeywordsList[kIndex].sentiment = $scope.loadedKeywordsList[kIndex].sentiment + $post.sentiment;
    							$scope.loadedKeywordsList[kIndex].sentimentPostCount = $scope.loadedKeywordsList[kIndex].sentimentPostCount + 1;
    							$scope.loadedKeywordsList[kIndex].avgSentiment = $scope.loadedKeywordsList[kIndex].sentiment / $scope.keywordsList[kIndex].sentimentPostCount ;
    							
    							isArticleFound = true;
    						}
    					}
    				}
    				
    				$scope.sentimentalRequestCount --;
    				if($scope.sentimentalRequestCount < $scope.noOfAjaxRequest) {
    					$scope.loadsentiment();
    				}
    			}
    		}
        }).error(function(data) {
        	$scope.sentimentalRequestCount --;
			if($scope.sentimentalRequestCount < $scope.noOfAjaxRequest) {
				$scope.loadsentiment();
			}
        });
	}
	
	$scope.loadKeywordIndex = 0;
	$scope.loadKeywordNoOfRecord = 8;
	$scope.loadedKeywordsList = [];
	$scope.loadKeywordsList = function() {
		if (typeof $scope.keywordsList[$scope.loadKeywordIndex] == 'undefined') {
			return;
		}
		
		$scope.showLoader();
		if($scope.keywordsList.length == 0) {
			return;
		}
		
		for($jIndex = 0; ($jIndex < $scope.noOfRecord && $scope.index < $scope.keywordsList.length); $jIndex ++) {
			$scope.loadedKeywordsList.push($scope.keywordsList[$scope.loadKeywordIndex]);
			$scope.loadKeywordIndex ++;
		}
		
		if($scope.sentimentalRequestCount < $scope.noOfAjaxRequest) {
			$scope.loadsentiment();
		}
		
		$timeout(function() {
			if($('#trend-panel').is(':visible')) {
				$scope.shortKeyword(false);
			}
		}, 100);
		
		$scope.hideLoader();
	}
	
	$scope.initKeywordSearch = function () {
		$scope.keywordsList = [];
		$scope.keywordsArticleList = [];

		$scope.loadedKeywordsList = [];
		
		$scope.keywordsArticle = {};
		
		$scope.sentimentPostIndex = 0;
		$scope.loadedSentimentPostId = '';
		$scope.sentimentalRequestCount = 0;
		$scope.loadKeywordIndex = 0;
	}
	*/
	$scope.changeTime = function() {
		$scope.getArticle($scope.currentSearch.trend, $scope.currentSearch.e, $scope.currentSearch.keywordStatus);
	}
	
	$scope.publlish = function(e) {
		$('#quickCampaignUrl').val($(e.target).attr('data-url'));
		$('.create-btn').trigger('click');
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