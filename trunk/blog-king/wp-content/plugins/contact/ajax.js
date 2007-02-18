/*  
    Author: VaamYob
    Website: http://www.xyooj.com

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

var AJAXSupported = (window.XMLHttpRequest != null || window.ActiveXObject != null);

function AJAXGet(url, fxCallMe) {        
    
    if (AJAXSupported) {
        var ajaxRequest = new AJAXRequest(url, fxCallMe);
        // this has to be externalized so that the method being called has access to the specific class instance's internal variables
        ajaxRequest.doIt(ajaxRequest.handleStateChange, 'GET', null);
    }
}

function AJAXPost(url, parms, fxCallMe) {
    if (AJAXSupported) {
        var ajaxRequest = new AJAXRequest(url, fxCallMe);
        // this has to be externalized so that the method being called has access to the specific class instance's internal variables
        ajaxRequest.doIt(ajaxRequest.handleStateChange, 'POST', parms);
    }
}

function AJAXRequest(newURL, fxCallMe) {
    var callMe = fxCallMe;
    var url = newURL;
    var requestObject;
    if (AJAXSupported) {
        if (window.XMLHttpRequest) {
            requestObject = new XMLHttpRequest();
        } else if (window.ActiveXObject) {
            requestObject = new ActiveXObject("Microsoft.XMLHTTP");
        }        
        // doesn't work in IE, and shouldn't be set anyway because sometimes we want html/sgml back
        // requestObject.overrideMimeType("text/xml");
    }
    
    this.doIt = function(callMeBack, request_type, post_parms) {
        requestObject.onreadystatechange = callMeBack;      
        if (request_type == 'GET') { 
	    requestObject.open("GET", url, true);
            requestObject.send(null);        
	} else {
	    requestObject.open("POST", url, true);
	    requestObject.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
	    if (post_parms == null) {
	        requestObject.send(null);
	    } else {
	        var post_str = ""; 
	        for (var i=0; i < post_parms.length; i++) {
		    if (post_str != "") { 
		        post_str += "&"; 
		    }
		    post_str += escape(post_parms[i][0]) + "=" + escape(post_parms[i][1]);
	        }
                requestObject.send(post_str);
	    }
	}
    }
    
    this.handleStateChange = function() {

        if (requestObject.readyState == 4) {            
            if (requestObject.status == 200) {     
            
                var mimeType = requestObject.getResponseHeader("Content-Type");
                
                if (mimeType != null && mimeType.indexOf("text/xml") != -1) {
                    var responseXMLDOM = requestObject.responseXML.getElementsByTagName("response")[0];
                    if (responseXMLDOM == null) {
                        alert('no response');
                    }
                    var respCode = responseXMLDOM.getElementsByTagName("code")[0];
                    if (respCode == null) {
                        alert('no response code');
                    } else {
                        // we only want the value
                        respCode = respCode.firstChild;
                        if (respCode != null) {
                            respCode = respCode.data;    
                        } 
                        
                    }
                    
                    var respMessage = responseXMLDOM.getElementsByTagName("message")[0];
                    if (respMessage == null) {
                        // message is optional
                        // alert('no response message');
                    } else {
                        // we only want the value
                        // respMessage = respMessage.firstChild.data;
                        respMessage = respMessage.firstChild;
                        if (respMessage != null) {
                            respMessage = respMessage.data;
                        }
                        
                    }
                    
                    var data = responseXMLDOM.getElementsByTagName("data")[0];
                    if (data == null) {
                        // data is optional
                        // alert('no data');
                    }
                    
                    callMe(respCode, respMessage, data);
                } else {
                    var html = requestObject.responseText;
                    callMe(html);
                }
            } else {
                alert('Server Returned a ' + requestObject.status);
            }
        }
        
    }
}
