var poll = angular.module('POLL',['BaseService']);

poll.controller('PostController', function($scope, $http, $location, dbLoader, $timeout) {
	$scope.searchArticle = function(search, callback) {
		var url = $('#base-path').attr('data-url') + '/get-post?currentPage=' + currentPage;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	callback(data, status);
        });
	}
	
	$scope.articleList = [];
	$scope.pagination = [];
	
	$scope.requestStatus = false;
	$scope.isValid = true;
	
	$scope.getArticleList = function(currentPage) {
		if(currentPage == false) {
			$('#load-more-post').prop('disabled', true);
			return;
		}
		$scope.requestStatus = true;
		$('#load-more-post').hide();
		$('#load-more-post-spinner').show();
		
		var url = $('#base-path').attr('data-url') + 'get-post?currentPage=' + currentPage;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.trendingArticleList) {
	    		if(data.response.trendingArticleList.LIST) {
	    			for(var index = 0; index < data.response.trendingArticleList.LIST.length; index ++) {
	    				$scope.articleList.push(data.response.trendingArticleList.LIST[index]);
	    			}
	    		}
	    		if(data.response.trendingArticleList.PAGING) {
	    			$scope.pagination = data.response.trendingArticleList.PAGING;
	    		}
	    	}
        	
        	$scope.requestStatus = false;
    		$('#load-more-post-spinner').hide();
    		$('#load-more-post').show();
        }).error(function (error, status) {
        	$scope.requestStatus = false;
    		$('#load-more-post-spinner').hide();
    		$('#load-more-post').show();
        });
	}
	
	$scope.initArticle = function() {
		$scope.articleList = [];
		$scope.pagination = [];
	}
	
	$scope.shareArticleDetail = {};
	$scope.caption = '';
	$scope.trendingArticleId = '';
	
	$scope.fbProfile = '';
	$scope.twProfile = '';
	$scope.showArticleModal = function(articleDetail, social) {
		$scope.shareArticleDetail = articleDetail;
		$scope.caption = $scope.shareArticleDetail.caption;
		$scope.trendingArticleId = $scope.shareArticleDetail.trendingArticleId;
		
		$('#icon-fb').hide();
		$('#icon-tw').hide();
		
		$('#post-now').removeClass('btn-info');
		$('#post-now').removeClass('btn-success');
		if(typeof social != undefined && 'fb' == social) {
			$('input[type="checkbox"][name="fbProfile"]').prop('checked', true).iCheck('update');
			$('#icon-fb').show();
			$('#post-now').addClass('btn-success');
		} else {
			$('input[type="checkbox"][name="fbProfile"]').prop('checked', false).iCheck('update');
		}
		
		if(typeof social != undefined && 'tw' == social) {
			$('input[type="checkbox"][name="twProfile"]').prop('checked', true).iCheck('update');
			$('#icon-tw').show();
			$('#post-now').addClass('btn-info');
		} else {
			$('input[type="checkbox"][name="twProfile"]').prop('checked', false).iCheck('update');
		}
		
		/*if($('input[type="checkbox"][name="fbProfile"]').length > 0) {
			$('input[type="checkbox"][name="fbProfile"]').prop('checked', true).iCheck('update');
		}
		
		if($('input[type="checkbox"][name="twProfile"]').length > 0) {
			
		}*/
		
		$("#shareModal").modal()
	}
	
	$scope.postArticle = function() {
		if($('input[type="checkbox"][name="fbProfile"]').length > 0 && $('input[type="checkbox"][name="fbProfile"]').is(':checked')) {
			$scope.fbProfile = '1';
		} else {
			$scope.fbProfile = '';
		}
		
		if($('input[type="checkbox"][name="twProfile"]').length > 0 && $('input[type="checkbox"][name="twProfile"]').is(':checked')) {
			$scope.twProfile = '1';
		} else {
			$scope.twProfile = '';
		}
		
		dbLoader.showLoader();
		
		$timeout(function() {
			$("#shareModal").modal('hide');
    	}, 500);
		
		var url = $('#base-path').attr('data-url') + 'share-post';
		var httpRequest = $http({
            method: 'POST',
            url: url,
            data: { caption: $scope.caption, trendingArticleId:$scope.trendingArticleId, twProfile:$scope.twProfile, fbProfile:$scope.fbProfile}
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else {
	    		if(data.response.response.trendingArticleId) {
	    			$scope.removeArticle(data.response.response.trendingArticleId);
	    			
	    			//Redirect to dashboard page after successful
	    			window.location.href = $('.recommended-posts').attr('data-url');
	    		}
	    		
	    		if(data.response.response.isException && data.response.response.isException == true) {
	    			$("#shareSettingDialog").modal()
	    		}
	    	}
        	dbLoader.hideLoader();
        }).error(function (error, status) {
        	dbLoader.hideLoader();
        	$scope.removeArticle($scope.trendingArticleId);
        });
	}
	
	$scope.hideArticle = function(articleDetail) {
		if(typeof articleDetail.trendingArticleId == 'undefined') {
			return;
		}
		
		var url = $('#base-path').attr('data-url') + 'hide-post';
		var httpRequest = $http({
            method: 'POST',
            url: url,
            data: { trendingArticleId:articleDetail.trendingArticleId}
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else {
	    		if(data.response.status) {
	    			$scope.removeArticle(articleDetail.trendingArticleId);
	    		}
	    	}
        });
	}
	
	$scope.removeArticle = function(trendingArticleId) {
		for(var index = 0; index < $scope.articleList.length; index ++) {
			if($scope.articleList[index].trendingArticleId == trendingArticleId) {
				$scope.articleList.splice(index, 1);
				break;
			}
		}
	}
	
    $scope.init = function() {
    	if($scope.isValid == true) {
    		$scope.getArticleList(1);
    	}
    }
    
    $scope.init();
});