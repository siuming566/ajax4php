/* A simple PHP AJAX Toolkit */

var a4p = {

prefix: '',
phpself: '',
phpquery: '',
controller: '',
token: '',

busy_func: function() {},

idle_func: function() {},

init: function (prefix, phpself, phpquery, controller, token) {
	this.prefix = prefix;
	this.phpself = phpself;
	this.phpquery = phpquery;
	this.controller = controller;
	this.token = token;
},

setup: function (prefix, phpself, phpquery, controller, token) {
	var type = function() {};
	type.prototype = a4p;
	var obj = new type;
	obj.init(prefix, phpself, phpquery, controller, token);
	return obj;
},

newevent: function () {
	var type = function() {};
	type.prototype = _event;
	var obj = new type;
	return obj;
},

_ajaxPoll: function (poll_id, pos, feed) {
	jQuery.ajax({
		url: this.prefix + '/poll.php',
		type: 'POST',
		data: { poll_id: poll_id, pos: pos, feed: feed, time: (new Date()).getTime() },
		success: function(response) {
			if (response != '@END@') {
				feed = eval(response);
				setTimeout(function() { a4p._ajaxPoll(poll_id, pos + response.length, response.length > 0 ? feed : ''); }, 100);
			}
		}
	});
},

ajaxCall: function (arg) {
	var formname = typeof arg.formname == 'string' ? arg.formname : 'form1';
	var element = document.getElementById(formname);
	if (element == null)
		document.forms[0].id = formname;
	if (typeof arg.controller == 'undefined' || arg.controller == '') {
		arg.controller = this.controller;
		arg.token += this.token;
	}
	return this._ajaxCall(arg.token, arg.controller, arg.method, arg.param, formname, arg.rerender, arg.push);
},

_ajaxCall: function (token, controller, method, param, formname, rerender, push) {
	if (typeof param == 'undefined')
		param = '';
	var target = this;
	var event = this.newevent();
	var poll_id = '';
	var poll_str = '';
	if (push == true) {
		poll_id = a4p_sec.randomString(32);
		poll_str = '&poll_id=' + poll_id;
		setTimeout(function() { a4p._ajaxPoll(poll_id, 0, ''); }, 100);
	}
	a4p.busy_func();
	jQuery.ajax({
		url: this.prefix + '/ajaxcall.php?controller=' + controller + '&method=' + method + '&param=' + escape(param) + '&token=' + token + '&time=' + (new Date()).getTime() + poll_str + '&' + this.phpquery,
		type: 'POST',
		data: jQuery('#' + formname).serialize(),
		success: function(response) {
			a4p.ajaxResponse(response, target, rerender, event);
		}
	});
	return event;
},

ajaxResponse: function (response, target, rerender, event) {
	if (response.startsWith('@')) {
		if (response.length > 1)
			window.location = response.substring(1);
	}
	else
		document.body.innerHTML = response;

	if (typeof rerender == 'string' && rerender.length > 0)
		target._ajaxRerender(rerender, event);
	else {
		event._onComplete();
		a4p.idle_func();		
	}
},

ajaxRerender: function (id) {
	var event = this.newevent();
	this._ajaxRerender(id, event);
	return event;
},

_ajaxRerender: function (id, event) {
	jQuery.ajax({
		url: this.prefix + '/rerender.php?' + this.phpquery,
		type: 'POST',
		data: {	page: this.phpself,
			id: id,
			time: (new Date()).getTime() },
		success: function (response) {
			if (response.startsWith('@')) {
				if (response.length > 1)
					a4p.ajaxDisplay(response.substring(1), id);
			}
			else
				document.body.innerHTML = response;
			event._onComplete();
			a4p.idle_func();		
		}
	});
},

onBusy: function (func) {
	a4p.busy_func = func;
},

onIdle: function (func) {
	a4p.idle_func = func;
},

setInnerHTML: function (element, html) {
	jQuery(element).replaceWith(html);
	layout.resize();
},

ajaxDisplay: function (response, id) {
	if (response != '')	{
		var contents = this.JSONDecode(response);
		for (var id in contents) {
			var element = document.getElementById(id);
			this.setInnerHTML(element, contents[id]);
		}
	}
},

JSONDecode: function (json) {
	return eval('(' + json + ')');
},

JSONEncode: function (obj) {
	return jQuery.toJSON(obj);
},

phpCall: function (arg) {
	if (typeof arg.controller == 'undefined' || arg.controller == '') {
		arg.controller = this.controller;
		arg.token += this.token;
	}
	return this._phpCall(arg.token, arg.controller, arg.method, arg.param);
},

_phpCall: function (token, controller, method, param) {
	var result = '';
	if (typeof param == 'undefined')
		param = '';
	jQuery.ajax({
		url: this.prefix + '/ajaxcall.php?controller=' + controller + '&method=' + method + '&param=' + escape(param) + '&token=' + token + '&time=' + (new Date()).getTime() + '&' + this.phpquery,
		type: 'POST',
		async: false,
		success: function (response) {
			if (response.startsWith('@')) {
				if (response.length > 1)
					result = response.substring(1);
			}
			else
				document.body.innerHTML = response;
		}
	});
	return result;
},

action: function (arg) {
	return this.ajaxCall(arg);
},

rerender: function (arg) {
	return this.ajaxRerender(arg);
},

call: function (arg) {
	return this.phpCall(arg);
},

get: function (url) {
	var result = '';
	jQuery.ajax({
		url: url,
		type: 'GET',
		async: false,
		success: function (response) {
			result = response;
		}
	});
	return result;
}

};

var _event = {

_onComplete: function () {},

_onLoad: function () {},

_onClose: function () {},

onComplete: function (f) {
	this._onComplete = f;
	return this;
},

onLoad: function (f) {
	this._onLoad = f;
	return this;
},

onClose: function (f) {
	this._onClose = f;
	return this;
}

};

var a4p_sec = {

alphabet: 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789',

randomString: function (length) {
	var pass = '';
	var alphaLength = this.alphabet.length;
	for (var i = 0; i < length; i++) {
		var n = Math.floor(Math.random() * alphaLength);
		pass = pass + this.alphabet.charAt(n);
	}
	return pass;
}

};

if (typeof String.prototype.startsWith != 'function') {
	String.prototype.startsWith = function (str) {
		return this.slice(0, str.length) == str;
	};
}

if (typeof String.prototype.endsWith != 'function') {
	String.prototype.endsWith = function (str) {
		return this.slice(-str.length) == str;
	};
}
