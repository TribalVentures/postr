var poll = angular.module('POLL',['BaseService']);
poll.directive('onFinishRender', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            if (scope.$last === true) {
                $timeout(function () {
                    scope.$emit(attr.onFinishRender);
                });
            }
        }
    }
});

poll.controller('TrendingController', function($scope, $http, $location, $timeout) {
		
	$scope.articleList = [];
	$scope.pagination = [];
	$scope.categoryId = '';
	$scope.parentCategoryId = '';
	$scope.approveStatus = '';
	
	$scope.type		= 'LU';
	$scope.fromDate = new Date().toISOString().slice(0, 10);;
	$scope.toDate 	= new Date().toISOString().slice(0, 10);;
	
	$scope.requestStatus = false;
	
	$scope.getArticleList = function(currentPage) {
		if(currentPage == false) {
			$('#load-more-post').prop('disabled', true);
			return;
		}
		
		$scope.requestStatus = true;
		$('#load-more-post-spinner').show();
		$('#load-more-post').hide();
		
		var url = $('#base-path').attr('data-url') + '/get-trending-article?categoryId=' + $scope.categoryId + 
		'&currentPage=' + currentPage + '&type=' + $scope.type +
				'&approveStatus=' + $scope.approveStatus +
				'&fromDate=' + $scope.fromDate + '&toDate=' + $scope.toDate;
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
	    			$('#load-more-post').prop('disabled', false);
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
	
	$scope.$on('ngRepeatFinished', function(ngRepeatFinishedEvent) {
		//$('.chosen-select-new').chosen({});
		/*$('.new-checkbox').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
        });
		
		$('.new-checkbox').removeClass('new-checkbox');*/
	});
	
	$scope.search = function() {
		$scope.initArticle();
		$scope.getArticleList(1);
	}
	
	$scope.categoryList = [];
	$scope.changeCategory = function() {
		var url = $('#base-path').attr('data-url') + '/getCategory?parentCategoryId=' + $scope.parentCategoryId;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.categoryList) {
	    		$scope.categoryList = data.response.categoryList;
	    	}
        	
        	if($scope.articleList.length < 1) {
        		$scope.getArticleList(1);
        	}
        });
		
	}
	
	$scope.removeTrendingArticalCategory = function(categoryId, trendingArticleId) {
		var url = $('#base-path').attr('data-url') + '/remove-category-trending-article';
		var httpRequest = $http({
	           method: 'POST',
	           url: url,
	           data: { categoryId: categoryId, trendingArticleId: trendingArticleId }
		}).success(function(data, status) {
			if(data.response.error) {
       			window.location.href = data.response.error.action;
       		} else {
       			//Load data for selected articles
       			if(typeof data.response.response.deletedIdsCategoryList != 'undefined' && data.response.response.deletedIdsCategoryList.length > 0) {
       				for(var index = 0; index < data.response.response.deletedIdsCategoryList.length; index ++) {
       					var deleteCategory = data.response.response.deletedIdsCategoryList[index];
       					for(var jindex = 0; jindex < $scope.articleList.length; jindex ++) {
       						if($scope.articleList[jindex].trendingArticleId == deleteCategory.trendingArticleId) {
       							//Update article category list
       							if(typeof $scope.articleList[jindex].categoryList != 'undefined' && $scope.articleList[jindex].categoryList.length > 0) {
       								for(var kindex = 0; kindex < $scope.articleList[jindex].categoryList.length; kindex ++) {
       									var articleCategory = $scope.articleList[jindex].categoryList[kindex];
           								if(articleCategory.categoryId == deleteCategory.categoryId) {
           									$scope.articleList[jindex].categoryList.splice(kindex, 1);
           								}
       								}
       							}
       						}
       					}
       				}
       			}
       		}
		});
	}
	
	$scope.deleteTrendingArtical = function(trendingArticleId) {
		var list = [];
		if(typeof trendingArticleId != 'undefined' && trendingArticleId != null) {
			list.push({ trendingArticleId: trendingArticleId, 'type':'ARTICLE', 'status':'1'});
		} else {
			$('.category-check:checkbox:checked').each(function () {
				var id = $(this).val();
				for(var index = 0; index < $scope.articleList.length; index ++) {
		   			if($scope.articleList[index].trendingArticleId == id) {
		   				list.push({ trendingArticleId: $scope.articleList[index].trendingArticleId, 'type':'ARTICLE', 'status':'1'});
		   				break;
		   			}
	   			}
			});
		}
		
		var url = $('#base-path').attr('data-url') + '/manage-trending-article-status';
		var httpRequest = $http({
	           method: 'POST',
	           url: url,
	           data: list
	       }).success(function(data, status) {
	    	   if(data.response.error) {
	       			window.location.href = data.response.error.action;
	       		} else if(data.response.response.status) {
	       			if(data.response.response.articleDetailList) {
	       				for(var aIndex = 0; aIndex < data.response.response.articleDetailList.length; aIndex ++ ) {
		       				for(var index = 0; index < $scope.articleList.length; index ++) {
					   			if($scope.articleList[index].trendingArticleId == data.response.response.articleDetailList[aIndex].trendingArticleId) {
					   				$scope.articleList.splice(index, 1);
					   				break;
					   			}
				   			}
	       				}
	       			}
	       			
	       			$('.category-check').attr('checked', false);
	       			$scope.trendingCategoryList = [];
	       			$scope.allCategory = false;
	    	   }
	       });
	}
	
	$scope.approveTrendingArtical = function(articleDetail, status) {
		var list = [];
		if(typeof articleDetail != 'undefined' && articleDetail != null) {
			list.push({ trendingArticleId: articleDetail.trendingArticleId, type: 'APPROVE', status:articleDetail.approveStatus});
		} else {
			$('.category-check:checkbox:checked').each(function () {
				var id = $(this).val();
				for(var index = 0; index < $scope.articleList.length; index ++) {
		   			if($scope.articleList[index].trendingArticleId == id) {
		   				list.push({ trendingArticleId: $scope.articleList[index].trendingArticleId, type: 'APPROVE', 'status':$scope.articleList[index].approveStatus, 'op':status});
		   				break;
		   			}
	   			}
			});
		}
		
		var url = $('#base-path').attr('data-url') + '/manage-trending-article-status';
		var httpRequest = $http({
	           method: 'POST',
	           url: url,
	           data: list
	       }).success(function(data, status) {
	    	   if(data.response.error) {
	       			window.location.href = data.response.error.action;
	       		} else if(data.response.response.status) {
	       			if(data.response.response.articleDetailList) {
	       				for(var aIndex = 0; aIndex < data.response.response.articleDetailList.length; aIndex ++ ) {
		       				for(var index = 0; index < $scope.articleList.length; index ++) {
					   			if($scope.articleList[index].trendingArticleId == data.response.response.articleDetailList[aIndex].trendingArticleId) {
					   				$scope.articleList[index].approveStatus = data.response.response.articleDetailList[aIndex].approveStatus;
					   				break;
					   			}
				   			}
	       				}
	       			}
	       			
	       			$('.category-check').attr('checked', false);
	       			$scope.trendingCategoryList = [];
	       			$scope.allCategory = false;
	    	   }
	       }
	   );
	}
	
	$scope.updateCaption = function(trendingArticleId, postId) {
		$('#spinner-' + postId).show();
		$('#caption-' + postId).hide();
		
		var url = $('#base-path').attr('data-url') + '/update-caption';
		var caption =  $('#' + postId). val();
		var httpRequest = $http({
			method: 'POST',
	        url: url,
	        data: { trendingArticleId: trendingArticleId, caption: caption }
		}).success(function(data, status) {
			if(data.response.error) {
				window.location.href = data.response.error.action;
	       	}
			$('#spinner-' + postId).hide();
			$('#caption-' + postId).show();
		});
	}	
	
	$scope.trendingCategoryList=[];
	$scope.allCategory = false;
	$scope.toggleSelection = function toggleSelection(trendingArticleId) {
		if(typeof trendingArticleId != 'undefined') {
			var isChecked = false;
			if($('.category-check[value="' + trendingArticleId + '"]:checkbox:checked').length > 0) {
				isChecked = true;
			}
			
			//Check if already exist, If yes then ignore otherwise add in list
			var isNotFound = true;
			for(var index = 0; index < $scope.trendingCategoryList.length; index ++) {
				if($scope.trendingCategoryList[index] == trendingArticleId) {
					isNotFound = false;
					if(isChecked == false) {
						$scope.trendingCategoryList.splice(index, 1);
					}
					break;
				}
			}
			
			if(isChecked == true && isNotFound == true) {
				$scope.trendingCategoryList.push(trendingArticleId);
			}
		} else {
			$scope.trendingCategoryList = [];
			$('.category-check:checkbox:checked').each(function () {
				var id = $(this).val();
				if(!isNaN(id)) {
					$scope.trendingCategoryList.push(Number(id));
				}
			});
		}
		if($scope.trendingCategoryList.length == 0) {
			$scope.allCategory = false;
		}
	};
	
	$scope.manageCategory = function() {
		var url = $('#base-path').attr('data-url') + '/manage-category';
		var httpRequest = $http({
	           method: 'POST',
	           url: url,
	           data: {trendingArticleIdList: $scope.trendingCategoryList, categoryId: $scope.categoryId}
	    }).success(function(data, status) {
       		if(data.response.error) {
       			window.location.href = data.response.error.action;
       		} else {
       			//Load data for selected articles
       			if(typeof data.response.response.updatedIdsCategoryList != 'undefined' && data.response.response.updatedIdsCategoryList.length > 0) {
       				//First get category names
       				for(var index = 0; index < data.response.response.updatedIdsCategoryList.length; index ++) {
       					for(var kindex = 0; kindex < $scope.categoryList.length; kindex ++) {
       						if(data.response.response.updatedIdsCategoryList[index].categoryId == $scope.categoryList[kindex].categoryId) {
       							data.response.response.updatedIdsCategoryList[index].category = $scope.categoryList[kindex].category;
       						}
       					}
       				}
       				
       				for(var index = 0; index < data.response.response.updatedIdsCategoryList.length; index ++) {
       					var updatedCategory = data.response.response.updatedIdsCategoryList[index];
       					for(var jindex = 0; jindex < $scope.articleList.length; jindex ++) {
       						if($scope.articleList[jindex].trendingArticleId == updatedCategory.trendingArticleId) {
       							//Update article category list
       							if(typeof $scope.articleList[jindex].categoryList != 'undefined' && $scope.articleList[jindex].categoryList.length > 0) {
       								var isFound = false;
       								for(var kindex = 0; kindex < $scope.articleList[jindex].categoryList.length; kindex ++) {
       									var articleCategory = $scope.articleList[jindex].categoryList[kindex];
       									//console.log(articleCategory.categoryId + ' == ' +  updatedCategory.categoryId + ' == ' + (articleCategory.categoryId == updatedCategory.categoryId));
           								if(articleCategory.categoryId == updatedCategory.categoryId) {
           									isFound = true;
           									break;
           								}
       								}
       								
       								if(isFound == false) {
       									$scope.articleList[jindex].categoryList.push(updatedCategory);
       								}
       							}
       						}
       					}
       				}
       			}
       			
       			//Make all chekcboxes uncheck
       			$('.category-check').attr('checked', false);
       			$scope.trendingCategoryList = [];
       			$scope.allCategory = false;
       		}
	    });
	}	
	
	$scope.initArticle = function() {
		$scope.articleList = [];
		$scope.pagination = [];
	}
	
    $scope.init = function() {
    	if($scope.parentCategoryId == '') {
    		$($('#trendingArticleForm_parentCategoryId').children()[1]).attr('selected',true);
    		$scope.parentCategoryId = $('#trendingArticleForm_parentCategoryId').val();
    		//$scope.changeCategory()
    	}
    	
    	$scope.changeCategory();
    }
    
    $scope.init();
	
});

