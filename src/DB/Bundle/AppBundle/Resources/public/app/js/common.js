function isValidFile(obj) {	
    var fileUpload = null;
    if(typeof obj==="object") {
    	fileUpload = obj;
    } else {
    	fileUpload = document.getElementById(obj); 
    }
    
    if (typeof (fileUpload.files) != "undefined" && typeof (fileUpload.files[0]) != "undefined") {
        var size = parseFloat(fileUpload.files[0].size / 1024).toFixed(2);
        if(size > 1024) {
        	return false;
        } else {
        	return true;
        }
    }
    
    return false;
}