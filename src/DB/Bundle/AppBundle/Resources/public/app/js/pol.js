var ftime = (new Date).getTime();
var primoPoll = {
		ran: false,
		frame: null,
		cid: null,
		apiKey: document.getElementById('primo-poll-js').getAttribute('data-key'),
		campaign: null,
		getUrl: function() {
			var surl = window.location.href.split('#')[0];
			surl = window.location.href.split('?')[0];
			
			if(document.getElementById('primo-campaign-url-frame')) {
				surl = window.location.hash.substr(1);
	    	}
			return surl;
		},
		init: function () {
	        if (primoPoll.ran) {return;}
	        primoPoll.ran = true;
	        var url = primoPoll.getUrl();
	    	url = document.getElementById('primo-poll-js').getAttribute('data-url') + 'api/v1/campaign?apiKey=' + primoPoll.apiKey + '&url=' + encodeURIComponent(url) + "&sessionId=" + primoPoll.getCid();
	    	
	        primoPoll.request(url, null, function(data) {
	        	campaign = eval("(" + data + ")");
	        	if(campaign.response.error){
	        		return;
	        	}
	        	if(campaign.response.campaign.sessionId && campaign.response.campaign.campaignStatus != 1){
	        		primoPoll.ran = true;
	        		primoPoll.cid = campaign.response.campaign.sessionId;
	        		primoPoll.campaign = campaign.response.campaign;
	        		
	        		//Set cookie
	        		primoPoll.createCookie('__cfghdekey', primoPoll.campaign.campaign);
	    			
	    			var __ppollsid = primoPoll.readCookie('__' + primoPoll.campaign.campaign + "cid");
	    			if(!__ppollsid || __ppollsid == '' || __ppollsid == null) {
	    				primoPoll.createCookie('__' + primoPoll.campaign.campaign + "cid", primoPoll.cid);
	    			}
	        	} else if(campaign.response.campaign.campaignStatus == 1) {
	        		return;
	        	}
	        	primoPoll.createPoll();
	        });
	    },

	    frameAttr: {
	        initialize: function () {
	            primoPoll.frame.style.width = "100%";
	            primoPoll.frameAttr.hide_sniply_bar();
	            primoPoll.frame.style.display = 'block';
	            primoPoll.frame.style.border = "0px";
	            primoPoll.frame.style.background = 'rgba(0,0,0,0)';
	            primoPoll.frame.style.borderRadius = '0px';
	            primoPoll.frame.style.boxShadow = 'none';
	            primoPoll.frame.style.color = 'rgba(0,0,0, 1)';
	            primoPoll.frame.style.cursor = 'auto';
	            primoPoll.frame.style.margin = '0px';
	            primoPoll.frame.style.lineHeight = 'normal';
	            primoPoll.frame.style.maxWidth = 'none';
	            primoPoll.frame.style.maxHeight = 'none';
	            primoPoll.frame.style.minWidth = '0px';
	            primoPoll.frame.style.minHeight = '0px';
	            primoPoll.frame.style.height = '55px';
	            primoPoll.frame.style.outlineWidth = '0px';
	            primoPoll.frame.style.padding = '0px';
	            primoPoll.frame.style.pointerEvents = 'auto';
	            primoPoll.frame.style.zIndex = '9999999999';
	            primoPoll.frame.style.zoom = '1';
	        },
	        hide_sniply_bar: function () {
	            if (primoPoll.frame != null) {
	                primoPoll.frame.style.position = 'absolute';
	                primoPoll.frame.style.left = '-99999999px';
	                primoPoll.frame.style.top = '-99999999px';
	            }
	        },
	        showPrimoBar: function () {
	            if (primoPoll.frame != null) {
	                primoPoll.frame.style.left = 0;
	                primoPoll.frame.style.right = 'auto';
	                
	                if(primoPoll.campaign.position && primoPoll.campaign.position === 'top') {
		                primoPoll.frame.style.top = 0;
		                primoPoll.frame.style.bottom = 'auto';
		                primoPoll.frame.style.position = 'absolute';		                
	                } else if(primoPoll.campaign.position && primoPoll.campaign.position === 'topfixed') {
		                primoPoll.frame.style.top = 0;
		                primoPoll.frame.style.bottom = 'auto';
		                primoPoll.frame.style.position = 'fixed';		                
	                } else {
		                primoPoll.frame.style.top = 'auto';
		                primoPoll.frame.style.bottom = 0;
		                primoPoll.frame.style.position = 'fixed';
	                }
	            }
	        },
	        make_fullwidth: function () {
	            primoPoll.frameAttr.set_width('100%');
	        },
	        set_width: function (width) {
	            if (typeof(width) == 'number') {
	                width = width.toString() + 'px';
	            }
	            primoPoll.frame.style.width = width;
	        },
	        set_height: function (height) {
	            if (typeof(height) == 'number') {
	                height = height.toString() + 'px';
	            }
	            primoPoll.frame.style.height = height;
	        },
	        position_right: function () {
	            primoPoll.frame.style.right = 0;
	            primoPoll.frame.style.left = 'auto';
	        },
	        position_top: function () {
	            primoPoll.frame.style.top = 0;
	            primoPoll.frame.style.bottom = 'auto';
	        },
	        get_window_width: function () {
	            if (self.innerWidth) {
	                return self.innerWidth;
	            }
	            if (document.documentElement && document.documentElement.clientWidth) {
	                return document.documentElement.clientWidth;
	            }
	            if (document.body) {
	                return document.body.clientWidth;
	            }
	        }
	    },
	    createPoll: function () {
	    	var url = primoPoll.getUrl();
	    	url = document.getElementById('primo-poll-js').getAttribute('data-url') + 'api/v2/render-poll?apiKey=' + primoPoll.apiKey + '&url=' + encodeURIComponent(url) + '&sessionId=' + primoPoll.cid;
	    	
	    	primoPoll.frame = document.createElement('iframe');
	    	primoPoll.frame.setAttribute('src', url);
	    	
	    	primoPoll.frame.setAttribute('id', 'id="primo-poll-bar"');
	    	
	    	document.body.appendChild(primoPoll.frame);
	    	primoPoll.frameAttr.initialize();

	    	if(typeof primoPoll.campaign.hideStatus != 'undefined' && primoPoll.campaign.hideStatus != 1) {
	    		if(primoPoll.isAnswered() === 'true') {
		    		return;
		    	}
	    	}
	    	
	    	if(primoPoll.campaign != null) {
	    		if(primoPoll.campaign.eventType == 1) {
	    			$.screentime({
	    				fields: [
	    			        { selector: 'body',
	    			          name: 'body'
	    			        }
	    			    ],
	    			    reportInterval: primoPoll.campaign.eventValue,
	    			    googleAnalytics: false,
	    			    callback: function(data, log) {
	    			    	primoPoll.frameAttr.showPrimoBar();
	    			    }
	    			});
	    			
	    		} else if(primoPoll.campaign.eventType == 0) {
	    			$.scrollDepth({
	    				elements: ['body', 'primo-campaign-url-frame'],
	    				userTiming: false,
	    				percentage: primoPoll.campaign.eventValue,
	    				pixelDepth: false,
	    				eventHandler: function(data) {
	    					primoPoll.frameAttr.showPrimoBar();
	    				}
	    			});
	    		} else {
	    			primoPoll.frameAttr.showPrimoBar();
	    		}
	    	} else {
	    		primoPoll.frameAttr.showPrimoBar();
	    	}
	    },
		ce: function(_cid) {
			var vtime = (new Date).getTime() - stime;
			stime = (new Date).getTime();
			
			//Convert milisecond into second
			vtime = Math.round(vtime / 1000);

			param = {'cid':_cid, 't': vtime};
			primoPoll.request("{{ path('db_api_v1_client_event') }}", param);
		},
		request: function(url, data, callback) {
			xmlhttp=new XMLHttpRequest();
			
			if(data) {
				xmlhttp.open("GET", url + '?' + primoPoll.params(data) ,true);
			} else {
				xmlhttp.open("GET", url ,true);
			}
			
			if(callback) {
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
						callback(xmlhttp.responseText);
					}
				}
			}
			
		    xmlhttp.send();
		},
		createCookie: function (name,value,days) {
		    if (days) {
		        var date = new Date();
		        date.setTime(date.getTime()+(days*24*60*60*1000));
		        var expires = "; expires="+date.toGMTString();
		    }
		    else var expires = "";
		    document.cookie = name+"="+value+expires+"; path=/";
		},
		readCookie: function (name) {
		    var nameEQ = name + "=";
		    var ca = document.cookie.split(';');
		    for(var i=0;i < ca.length;i++) {
		        var c = ca[i];
		        while (c.charAt(0)==' ') c = c.substring(1,c.length);
		        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		    }
		    return null;
		}, 
		eraseCookie: function(name) {
		    createCookie(name,"",-1);
		},
		params: function(source) {
		  var array = [];

		  for(var key in source) {
		     array.push(encodeURIComponent(key) + "=" + encodeURIComponent(source[key]));
		  }

		  return array.join("&");
		},
		isAnswered: function() {
			var ckey = primoPoll.readCookie('__cfghdekey');
			if(ckey) {
				var ans = primoPoll.readCookie('__' + ckey + 'ans');
				if(!ans) {
					ans = false;
				}
				return ans;
			}
			return false;
		},
		getCid : function() {
			var ckey = primoPoll.readCookie('__cfghdekey');
			
			if(ckey) {
				var cid = primoPoll.readCookie('__' + ckey + 'cid');
				return cid;
			}
			return null;
		},
		registerTimeEvent: function() {
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
		},
		registerScroolEvent: function() {
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
};

function ___pl() {
	var ltime = (new Date).getTime() - ftime;
	ltime = Math.round(ltime / 1000);
	
	var url = primoPoll.getUrl();
	url = document.getElementById('primo-poll-js').getAttribute('data-url') + 'api/v1/pl?apiKey=' + primoPoll.apiKey + '&url=' + encodeURIComponent(url) + '&t=' + ltime;
	
	primoPoll.request(url, null, function(data) {});
}

document.addEventListener("DOMContentLoaded", function() {
	if(document.getElementById('primo-campaign-url-frame')) {
		iframe = document.getElementById('primo-campaign-url-frame');
		console.log('Welcome to Primo iframe');
		iframe.onload = ___pl;
	} else {
		console.log('Welcome to Primo Pol');
		___pl();
	}
	primoPoll.init();
});

window.setTimeout(function () {
    if (!primoPoll.ran) {
    	primoPoll.init();
    }
}, 2000);