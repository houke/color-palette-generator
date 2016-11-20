function cpg_showErrors(jqXHR, exception){
	var msg = "Something went wrong";

	if (jqXHR.status === 0) {
		msg = 'Not connected.\n Verify Network.';
	} else if (jqXHR.status == 404) {
		msg = 'Requested page not found. [404]';
	} else if (jqXHR.status == 500) {
		msg = 'Internal Server Error [500].';
	} else if (exception === 'parsererror') {
		msg = 'Requested JSON parse failed.';
	} else if (exception === 'timeout') {
		msg = 'Time out error.';
	} else if (exception === 'abort') {
		msg = 'Ajax request aborted.';
	} else {
		msg = 'Uncaught Error.\n' + jqXHR.responseText;
	}
	return msg;
}

function cpg_parseParams(str) {
	return str.split('&').reduce(function (params, param) {
		var paramSplit = param.split('=').map(function (value) {
			return decodeURIComponent(value.replace('+', ' '));
		});
		params[paramSplit[0]] = paramSplit[1];
		return params;
	}, {});
}
