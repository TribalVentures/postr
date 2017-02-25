var stime = (new Date).getTime();
var itime = 15;
var ceTimer = null;
var tmi = 30;
var etmi = 60;
var tmcounter = 0;

$(document).ready(function(){
	reder();
});

function reder() {
	var sid = readCookie('__ppollsid');
	var sidQs = '';
	if(sid) {
		sidQs = '&sessionId=' + sid;
	}
	
	var x = readCookie('__ppoll');
	if (x) {
		//handle session tracking here
		usession(sid);
		return;
	}
	
	$.get($('#primo-poll').attr('data-url') + "api/v1/render-poll?url=" + window.location.href + "&apiKey=" + $('#primo-poll').attr('data-key') + sidQs, function(data) {
		if(typeof data.response === 'undefined') {
			$('body').append(data);
			setTimeout(animate, 500);
			setTimeout(function() {
				$('.primo-poll-action-thumb-up').on('click', function() {
					request(1);
				});
				
				$('.primo-poll-action-thumb-down').on('click', function() {
					request(0)
				});
				
				//code to register event with tie or scroll
				if($('.primo-poll-event').attr('data-e') == 0) {
					registerScroolEvent();
				} else {
					registerTimeEvent();
				}
				
				//Set cookie with session id
				if($('.primo-poll-event').attr('data-clientid') > 0) {
					createCookie('__ppollsid', $('.primo-poll-event').attr('data-clientid'), 365);
				}
				
				usession($('.primo-poll-event').attr('data-clientid'));
			}, 100);
		}
	})
}

function animate() {
	$("#primo-poll-message-text-vote-number").animateNumbers($("#primo-poll-message-text-vote-number").attr('data-vote'));
}

function request(pollValue) {	
	var cid = readCookie('__ppollsid');
	if(!cid) {
		cid = 'T' + (new Date().getUTCMilliseconds());
	}
	
	$.post( $('#primo-poll').attr('data-url') + "api/v1/answer-poll?v=" + pollValue + "&campaign=" + $('.primo-poll-action').attr('data-campaign')+ "&cid=" + cid + "&url=" + window.location.href, function(data) {});
	
	$('#primo-poll').hide();
	$('#primo-poll-slide').slideDown("slow").show();
	
	//set cookie
	createCookie('__ppoll', pollValue, 365);
}

function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}

function registerTimeEvent() {
	$.screentime({
		fields: [
	        { selector: 'body',
	          name: 'body'
	        }
	    ],
	    reportInterval: $('.primo-poll-event').attr('data-v'),
	    googleAnalytics: false,
	    callback: function(data, log) {
	    	if($('.primo-poll-event').is(":visible")){
				//Code for visibility
			} else {
				$('.primo-poll-event').slideDown().show();
			}
	    }
	});
}

function registerScroolEvent() {
	$.scrollDepth({
		elements: ['body'],
		userTiming: false,
		percentage: $('.primo-poll-event').attr('data-v'),
		pixelDepth: false,
		eventHandler: function(data) {
			if($('.primo-poll-event').is(":visible")){
				//Code for visibility
			} else {
				$('.primo-poll-event').slideDown().show();
			}
		}
	});
}

function usession(cid) {
	/*window.onbeforeunload = function() {
	    var vtime = (new Date).getTime() - stime;
	    
	    xmlhttp=new XMLHttpRequest();
	    xmlhttp.open("GET", $('#primo-poll').attr('data-url') + "api/v1/ce?cid=" + $('.primo-poll-event').attr('data-clientid') + "&t=" + vtime ,true);
	    xmlhttp.send();
	};*/
	
	ceTimer = setInterval(function() {ce(cid);}, 1000 * itime);
}

function ce(cid) {
	var vtime = (new Date).getTime() - stime;
	stime = (new Date).getTime();
	
	//Convert milisecond into second
	vtime = Math.round(vtime / 1000);

	xmlhttp=new XMLHttpRequest();
    xmlhttp.open("GET", $('#primo-poll').attr('data-url') + "api/v1/ce?cid=" + cid + "&t=" + vtime ,true);
    xmlhttp.send();
    
    if(itime != tmi) {
    	itime = tmi;
    	clearInterval(ceTimer);
    	ceTimer = setInterval(function() {ce(cid);}, 1000 * itime);
    }
    
    if(tmcounter > 2 && itime != etmi) {
    	itime = etmi;
    	clearInterval(ceTimer);
    	ceTimer = setInterval(function() {ce(cid);}, 1000 * itime);	
    } else if(tmcounter <= 2) {
    	tmcounter ++;
    }
}

