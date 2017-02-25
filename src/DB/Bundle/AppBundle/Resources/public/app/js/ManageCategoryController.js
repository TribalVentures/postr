var poll = angular.module('POLL',[]);

poll.controller('ManageCategoryController', function($scope, $http, $location, $timeout) {
	$scope.addInList = function(field, listField, newField) {
		if($('#' + newField).is("select")) {
			var newValue = $('#' + newField).val();
			var newText = '';
			$("#" + newField + " > option").each(function() {
				if(newValue == this.value) {
					newText = $(this).text();
			    }
			});
			
			if(newValue == '') {
				return;
			}
			var isExist = false;
			$("#" + listField + " > option").each(function() {
			    if(newValue == this.value) {
			    	isExist = true;
			    }
			});
			
			if(isExist == false) {
				$('#' + listField).append($('<option>', {
				    value: newValue,
				    text: newText
				}));
			}
			var resetValue = '';
			$("#" + listField + " > option").each(function() {
			    if(resetValue == '') {
			    	resetValue = this.value;
			    } else {
			    	resetValue = resetValue + ',' + this.value;
			    }
			});
			
			if(resetValue != '') {
				$('#' + field).val(resetValue);
			}
			$('#' + newField).val('')
		} else {
			var newValue = $('#' + newField).val();
			newValue = newValue.trim();
			if(newValue != '') {
				var newList = newValue.split(",")
				if(newList.length > 0) {
					for(var index = 0; index < newList.length; index ++) {
						$('#' + listField).append($('<option>', {
						    value: newList[index].trim(),
						    text: newList[index].trim()
						}));
					}
				}
				var resetValue = '';
				$("#" + listField + " > option").each(function() {
				    if(resetValue == '') {
				    	resetValue = this.value;
				    } else {
				    	resetValue = resetValue + ',' + this.value;
				    }
				});
				
				if(resetValue != '') {
					$('#' + field).val(resetValue);
				}
			}
			
			$('#' + newField).val('')
		}
	}
	
	$scope.removeFromList = function(listField, field) {
		var isChange = false;
		$('#' + listField + ' option:selected').each( function() {
            $(this).remove();
			isChange = true;
        });
	 
		var resetValue = '';
		$("#" + listField + " > option").each(function() {
		    if(resetValue == '') {
		    	resetValue = this.value;
		    } else {
		    	resetValue = resetValue + ',' + this.value;
		    }
		});
		
		if(isChange) {
			$('#' + field).val(resetValue);
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
    	$('#addIncludeKeywords').val('')
    }
    $scope.init();
});