var poll = angular.module('POLL',['infinite-scroll']);

poll.controller('CategoryController', function($scope, $http, $location, $timeout) {
	$scope.currentPage = 1;
	$scope.list = [];
	$scope.pagination = [];

	$scope.requestState = false;
	
	$scope.categoryId = 0;
	$scope.getCategoryList = function() {
		if($scope.requestState || $scope.currentPage === false) {
			$scope.hideLoader($timeout);
			return;
		}
		
		var hash = $location.hash();
		var pos = hash.indexOf("id-");
		if(pos == 0) {
			hash = hash.replace('id-', '');
			if(isNaN(hash) == false) {
				$scope.categoryId = hash;
			}
			$location.hash('');
		}
		
		$scope.showLoader();
		
		//$scope.list = [];
		$scope.requestState = true;
		var url = $('#categoryList').attr('data-url') + '?categoryId=' + $scope.categoryId + '&currentPage=' + $scope.currentPage;
		var httpRequest = $http({
            method: 'GET',
            url: url
        }).success(function(data, status) {
        	if(data.response.error) {
        		window.location.href = data.response.error.action;
	    	} else if(data.response.categoryList.LIST) {
	    		for($index = 0; $index < data.response.categoryList.LIST.length; $index++) {
					if(data.response.categoryList.LIST[$index].image == '') {
						data.response.categoryList.LIST[$index].image = 'bundles/dbapp/postreachv1/img/popculture.jpg';
					}
	    			$scope.list.push(data.response.categoryList.LIST[$index]);
	    		}
	    		
	    		$scope.requestState = false;
	    		
	    		$timeout(function() {	    				
	    			var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));	    			
	    			
	    			// Success color: #10CFBD
	    			elems.forEach(function(html) {
	    				var isLoad = $(html).attr('data-load');
	    				if(isLoad == 'true') {
		    				$(html).attr('data-load', false);
	    					var switchery = new Switchery(document.getElementById($(html).attr('id')), {color: '#10CFBD', size: 'small'});
		    				Switchery.prototype.handleOnchange = function(state) {
		    					$scope.changeCategoryStatus($(this.element).val(), state);
		    				}
	    				}
	    			});
    			}, 200);
	    		
	    		$scope.pagination = data.response.categoryList.PAGING;
	    		$scope.currentPage = $scope.pagination.NEXT_PAGE;
	    		
	    		$scope.hideLoader($timeout);
	    	} else {
	    		$scope.requestState = false;
	    		$scope.hideLoader($timeout);
	    	}
        });
	}
	
	$scope.show = function(e) {
		$scope.categoryId = $(e.target).data('id');
		$scope.currentPage = 1;
		$scope.list = [];
		$scope.pagination = [];
		$scope.getCategoryList();
	}
	
	$scope.changeCategoryStatus = function(categoryId, state) {
		var url = $('#categoryList').attr('data-change-url');
		var httpRequest = $http({
            method: 'POST',
            url: url,
            data: { 'categoryId' : categoryId, 'categoryStatus': ((state == false) ? 1 : 0)}
        }).success(function(data, status) {});
	}
	
	$scope.editCategoryDetail = {'categoryId': -1, 'category':''};
    $scope.changeCategory = function(e) {
		$scope.showLoader();
		$scope.editCategoryDetail = {'categoryId': -1, 'category':''};
		$scope.editCategoryDetail.categoryId = $(e.target).data('id');
		
		if($scope.list.length > 0) {
			for(var index = 0; index < $scope.list.length; index ++) {        		
        		if($scope.list[index]['categoryId'] === $scope.editCategoryDetail.categoryId) {
        			$scope.editCategoryDetail.categoryId = $scope.list[index].categoryId;
        			$scope.editCategoryDetail.category = $scope.list[index].category;
        			
        			$('#editCategoryModel').modal('show');
        			$scope.hideLoader();
        			break;
        		}
        	}
		} else {
			$scope.hideLoader();
		}
    }
	
    $scope.saveChangeCategory = function(e) {
		$scope.showLoader();
		
		var categoryId = $('#categoryForm_categoryId').val();
		var category = $('#categoryForm_category').val();
		
		var url = $('#changeCategoryForm').attr('data-url');
		var httpRequest = $http({
            method: 'POST',
            url: url,
            data: { 'categoryId' : categoryId, 'category': category}
        }).success(function(data, status) {
        	if(categoryId > 0 && $scope.list.length > 0) {
    			for(var index = 0; index < $scope.list.length; index ++) {        		
            		if($scope.list[index]['categoryId'] == categoryId) {
            			$scope.list[index].category = category;
            			break;
            		}
            	}
    		}
        	$('#editCategoryModel').modal('hide');$scope.hideLoader();
        });
    }
    
    $scope.deleteCategory = function(e) {
    	var isConfirm = confirm("Do you want to delete User type or category.!");
    	if (isConfirm != true) {
    	    return;
    	}
    	
    	var categoryId =  $(e.target).data('id');
    	if(categoryId == '') {
    		return;
    	}
    	$scope.showLoader();
    	var request = {
    			categoryId: categoryId
			};
	
    	var url = $('#categoryList').attr('data-remove-url');
		var httpRequest = $http({
	        method: 'POST',
	        url: url,
	        data: request
	    }).success(function(data, status) {
	    	if(data.response.error) {
	    		window.location.href = data.response.error.action;
	    	} else if(data.response.category.categoryId) {
	    		if($scope.list.length > 0) {
	    			for(var index = 0; index < $scope.list.length; index ++) {        		
	            		if($scope.list[index]['categoryId'] === data.response.category.categoryId) {
	            			$scope.list.splice(index, 1);
	            			break;
	            		}
	            	}
	    		}
	    		$scope.hideLoader($timeout);
	    	} else {
	    		$scope.hideLoader($timeout);
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
    	if($scope.categoryId == '') {
    		$scope.categoryId = 0;
    	}
    	
    	//$scope.getCategoryList();
    }
    $scope.init();
});