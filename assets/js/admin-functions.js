function cpg_showErrors(jqXHR, exception){
	var msg = cpg.error;

	if (jqXHR.status === 0) {
		msg = cpg.error_0;
	} else if (jqXHR.status == 404) {
		msg = cpg.error_1;
	} else if (jqXHR.status == 500) {
		msg = cpg.error_2;
	} else if (exception === 'parsererror') {
		msg = cpg.error_3;
	} else if (exception === 'timeout') {
		msg = cpg.error_4;
	} else if (exception === 'abort') {
		msg = cpg.error_5;
	} else {
		msg = cpg.error_x + '\n' + jqXHR.responseText;
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
