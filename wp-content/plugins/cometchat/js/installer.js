jqcc = jQuery;

jqcc(document).ready(function(){
	jqcc('#license').keyup(function(){
        jqcc("#error").hide();
    });
});

/**
 * ccInstall: validate license key and begin installation process
*/
function cometchatInstall(){

	var licensekey= jqcc("#license").val().trim();
	jqcc("#error").hide();

	if(licensekey == ''){
		alert("Please enter valid license key.");
		return;
	}
	checkCometChatLicenseKey(licensekey);
};

/**
 * checkCometChatLicenseKey
 * @param licenseKey
 * @return status of license and it's details
 */
function checkCometChatLicenseKey(licensekey){
	var data = {
		'action': 'cc_action',
		'api': 'checkCometChatLicenseKey',
		'licensekey': licensekey
	};

	jqcc.post(ajaxurl, data, function(response) {
		if(response.success == 1){
			if(response.hasOwnProperty('cloud') && response.cloud != 0){
				setCookie ("cc_cloud", response.cloud, 365);
				location.reload();
			}else{
				setCookie ("cc_cloud", 0, 365);
				jqcc("#license-form").fadeOut();
				jqcc("#installer-process").fadeIn(function(){
					jqcc("#progressbar").css('width','5%');
					response = JSON.parse(response['cc_api_response']);
					isCometChatPackageAlreadyExists(response);
				});
			}
		}else{
			jqcc("#error").show().html(response.error);
		}
	}).fail(function(data) {
	    jqcc("#error").show().html(data.message);
	});
}

/**
 * isCometChatPackageAlreadyExists
 * @return status of cometchat.zip
 */
function isCometChatPackageAlreadyExists(params){
	var width = 5;
	var progressInterval = setInterval(function(){
		width += 2;
		jqcc("#progressbar").css('width', width+'%');

		if(width >= 20){
			clearInterval(progressInterval);
		}
	}, 300);

	var data = {
		'action': 'cc_action',
		'api': 'isCometChatPackageAlreadyExists',
		'cc_api_response': params
	};

	jqcc.post(ajaxurl, data, function(response) {
		if(response.zip == 1){
			jqcc("#progressbar").css('width','70%');
			extractCometChatZip();
		}else{
			if(response.success == 0){
				clearInterval(progressInterval);
				jqcc("#progressbar").css('width','20%');
				getCometChatTokenKey(response['cc_api_response']);
			} else{
				jqcc("#installer-process").hide();
				jqcc("#license-form").show();
				jqcc("#error").show().html(response.message);
			}
		}
	}).fail(function(data) {
		clearInterval(progressInterval);
	    jqcc("#error").show().html(data.message);
	});
}

/**
 * getCometChatTokenKey
 * @return token key
 */
function getCometChatTokenKey(params){
	var width = 20;
	var progressInterval = setInterval(function(){
		width += 1;
		jqcc("#progressbar").css('width', width+'%');

		if(width >= 35){
			clearInterval(progressInterval);
		}
	}, 500);

	var data = {
		'action': 'cc_action',
		'api': 'getCometChatTokenKey',
		'cc_api_response':params
	};

	jqcc.post(ajaxurl, data, function(response) {
		if (response.hasOwnProperty('data') && response.data.hasOwnProperty('token')) {
			clearInterval(progressInterval);
			jqcc("#progressbar").css('width','35%');
			downloadLatestCometChatPackage(response);
		} else {
			jqcc("#installer-process").fadeOut();
			jqcc("#license-form").fadeIn();
			jqcc("#error").show().html(response.message);
		}
	}).fail(function(data) {
		clearInterval(progressInterval);
		jqcc("#progressbar").css('width','35%');
	    jqcc("#error").show().html(data.message);
	});
}

/**
 * downloadLatestCometChatPackage
 * @param token key
 * @return cometchat.zip
 */
function downloadLatestCometChatPackage(params){
	var token = params.data.token || '',
	download_link = params.download_link || '';

	var width = 35;
	var progressInterval = setInterval(function(){
		width += 1;
		jqcc("#progressbar").css('width', width+'%');

		if(width >= 70){
			clearInterval(progressInterval);
		}
	}, 2500);

	var data = {
		'action': 'cc_action',
		'api': 'downloadLatestCometChatPackage',
		'token': token,
		'download_link': download_link
	};

	jqcc.post(ajaxurl, data, function(response) {
		if (response.error == 1) {
			jqcc("#error").show().html(response.message);
		} else {
			clearInterval(progressInterval);
			jqcc("#progressbar").css('width','70%');
			extractCometChatZip();
		}
	}).fail(function(data) {
		clearInterval(progressInterval);
		jqcc("#progressbar").css('width','70%');
	    jqcc("#error").show().html(data.message);
	});
}

/**
 * extractCometChatZip
 * @return extracted cometchat directory
 */
function extractCometChatZip(){
	var width = 70;
	var progressInterval = setInterval(function(){
		width += 1;
		jqcc("#progressbar").css('width', width+'%');

		if(width >= 95){
			clearInterval(progressInterval);
		}
	}, 2500);

	var data = {
		'action': 'cc_action',
		'api': 'extractCometChatZip'
	};

	jqcc.post(ajaxurl, data, function(response) {
		if (response.error == 1) {
			jqcc("#error").show().html(response.message);
		} else {
			clearInterval(progressInterval);
			jqcc("#progressbar").css('width','100%');
			location.reload();
		}
	}).fail(function(data) {
		clearInterval(progressInterval);
	    jqcc("#error").show().html(data.message);
	});
}

/**
 * getCookie
 * @param cname = name of cookie
 * @return cookie value
 */
function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return '';
}

/**
 * setCookie
 * @param key = name , value = cookie value, days = expiry period of cookie
 * creating new cookie
 */
function setCookie (key, value, days) {
    var date = new Date();
    // Default at 365 days.
    days = days || 365;
    // Get unix milliseconds at current time plus number of days
    date.setTime(+ date + (days * 86400000)); //24 * 60 * 60 * 1000
    window.document.cookie = key + "=" + value + "; expires=" + date.toGMTString() + "; path=/";
}