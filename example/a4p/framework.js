/* A simple PHP AJAX Toolkit */

var a4p = {

prefix: '',
phpself: '',
phpquery: '',

init: function (prefix, phpself, phpquery) {
	this.prefix = prefix;
	this.phpself = phpself;
	this.phpquery = phpquery;
},

setup: function (prefix, phpself, phpquery) {
	var type = function() {};
	type.prototype = a4p;
	var obj = new type;
	obj.init(prefix, phpself, phpquery);
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
	else
		event._onComplete();
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
			a4p.ajaxDisplay(response, id);
			event._onComplete();
		}
	});
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

var ui = {
		
target: null,

init: function (a4p) {
	this.target = a4p;
},

setup: function (a4p) {
	var type = function() {};
	type.prototype = ui;
	var obj = new type;
	obj.init(a4p);
	return obj;
},

closePopup: null,

newevent: function () {
	var type = function() {};
	type.prototype = _event;
	var obj = new type;
	return obj;
},

popup: function (url, width, height, rerender) {
	// overlay masking
	var overlay = jQuery('<div id="modal-overlay"></div>');
	// modal window that placed at center
	var modalWindow = jQuery('<div id="modal-window"></div>');
	// the content that will be shown
	var modalContent = jQuery('<div id="modal-content"></div>');
	// close button
	var modalControl = jQuery('<div id="modal-control">Close</div>');
	// Re-render after popup
	var modalRerender = rerender;
	// load the require modal window according to the parameters
	modalWindow.css({
		'margin-left': -width / 2,
		'margin-top': -height / 2 - 20
	});
	modalContent.css({
		'width': width + 'px',
		'height': height + 'px'
	});
	overlay.css('opacity', 0.75);

	var self = this;
	var event = this.newevent();
	modalControl.click(function () {
		self.modalHide(overlay, modalWindow, rerender);
		event._onClose();
	});
	
	this.closePopup = function () { modalControl.click(); };
	
	// set content to modalWindow
	modalWindow.append(modalContent);
	modalWindow.append(modalControl);
	
	// append the ModalPanel to body
	jQuery('body').append(overlay);
	jQuery('body').append(modalWindow);
	
	// IE fix
	if (typeof document.body.style.maxHeight == 'undefined') { //if IE 6
		jQuery('body','html').css({height: '100%', width: '100%'});
	}
	
	// request content
	jQuery.ajax({
		url: url,
		type: 'POST',
		success: function (response) {
			modalContent.append(response);
			layout.resize();
			event._onLoad();
		}
	});
	
	return event;
},

setInnerHTML: function (element, html) {
	jQuery(element).replaceWith(html);
	layout.resize();
},

loadControl: function (url, id) {
	// request content
	var self = this;
	var event = this.newevent();
	jQuery.ajax({
		url: url,
		type: 'POST',
		success: function (response) {
			var element = document.getElementById(id);
			self.setInnerHTML(element, response);
			event._onComplete();
		}
	});
	
	return event;
},

// hide the ModalPanel
modalHide: function (overlay, modalWindow, modalRerender) {
	overlay.remove();
	modalWindow.remove();
	if (typeof modalRerender == 'string')
		this.target.rerender(modalRerender);
},

sortBy: function (table, column) {
	this.target.action({controller: 'ui', method: 'sortBy', param: this.target.JSONEncode({name: table, sortBy: column}), rerender: table});
},

firstPage: function (table, pager) {
	this.target.action({controller: 'ui', method: 'firstPage', param: table, rerender: table + ',' + pager});
},

previousPage: function(table, pager) {
	this.target.action({controller: 'ui', method: 'previousPage', param: table, rerender: table + ',' + pager});
},

nextPage: function (table, pager) {
	this.target.action({controller: 'ui', method: 'nextPage', param: table, rerender: table + ',' + pager});
},

lastPage: function (table, pager) {
	this.target.action({controller: 'ui', method: 'lastPage', param: table, rerender: table + ',' + pager});
},

gotoPage: function (table, pager, page) {
	this.target.action({controller: 'ui', method: 'gotoPage', param: this.target.JSONEncode({name: table, page: page}), rerender: table + ',' + pager});
},

fileupload: function(arg) {
	var response = arg.frame.contentDocument.body.innerHTML;
	if (response.length > 1) {
		arg.frame.contentDocument.body.innerHTML = '';
		if (response.startsWith('@'))
			this.target.action({token: arg.token, controller: arg.controller, method: arg.method, param: response.substring(1), rerender: arg.rerender});
		else
			document.body.innerHTML = response;
	}
}

};

var layout = {

_resize: function(table, width, height) {
	var element = document.getElementById(table.id);
	if (table.width.endsWith('%'))
		element.style.width = Math.round(width * parseInt(table.width, 10) / 100) + 'px';
	if (table.width.endsWith('px'))
		element.style.width = table.width;
	if (table.height.endsWith('%'))
		element.style.height = Math.round(height * parseInt(table.height, 10) / 100) + 'px';
	if (table.height.endsWith('px'))
		element.style.height = table.height;

	if (table.type == 'vertical')
		layout._resizeVertical(table, width, height);
	if (table.type == 'horizontal')
		layout._resizeHorizontal(table, width, height);
},

_resizeControl: function(table) {
	var element = document.getElementById(table.id).parentNode;
	while (element.style.width == '' && element.style.height == '')
		element = element.parentNode;
	var width = element.clientWidth;
	var height = element.clientHeight;
	if (table.width.endsWith('%'))
		element.style.width = Math.round(width * parseInt(table.width, 10) / 100) + 'px';
	if (table.width.endsWith('px'))
		element.style.width = table.width;
	if (table.height.endsWith('%'))
		element.style.height = Math.round(height * parseInt(table.height, 10) / 100) + 'px';
	if (table.height.endsWith('px'))
		element.style.height = table.height;

	if (table.type == 'vertical')
		layout._resizeVertical(table, width - layout_padding.bodypadding, height - layout_padding.bodypadding - 14);
	if (table.type == 'horizontal')
		layout._resizeHorizontal(table, width - layout_padding.bodypadding, height - layout_padding.bodypadding - 14);
},

_resizeVertical: function(table, width, height) {
	var total_height = 0
	var total_auto_rows = 0;
	var rows = table.rows;
	for (var i = 0; i < rows.length; i++) {
		var row = rows[i];
		var actualHeight = 0;
		if (row.height.endsWith('%'))
			actualHeight = Math.round(height * parseInt(row.height, 10) / 100);
		if (row.height.endsWith('px'))
			actualHeight = parseInt(row.height, 10);
		if (row.height == '*')
			total_auto_rows++;
		if (actualHeight > 0) {
			var element = document.getElementById(row.id);
			element.style.width = width;
			element.style.height = actualHeight + 'px';
			if (row.nested != null)
				layout._resize(layout_info[row.nested], width - layout_padding.cellpadding, actualHeight - layout_padding.cellpadding);
		}
		total_height += actualHeight;
	}

	if (total_auto_rows > 0) {
		var auto_height = Math.round((height - total_height) / total_auto_rows);
		for (var i = 0; i < rows.length; i++) {
			var row = rows[i];
			if (row.height == '*') {
				var element = document.getElementById(row.id);
				element.style.width = width;
				element.style.height = auto_height + 'px';
				if (row.nested != null)
					layout._resize(layout_info[row.nested], width - layout_padding.cellpadding, auto_height - layout_padding.cellpadding);
			}
		}
	}
},

_resizeHorizontal: function(table, width, height) {
	var total_width = 0;
	var total_auto_columns = 0;
	var columns = table.columns;
	for (var i = 0; i < columns.length; i++) {
		var column = columns[i];
		var actualWidth = 0;
		if (column.width.endsWith('%'))
			actualWidth = Math.round(width * parseInt(column.width, 10) / 100);
		if (column.width.endsWith('px'))
			actualWidth = parseInt(column.width, 10);
		if (column.width == '*')
			total_auto_columns++;
		if (actualWidth > 0) {
			var element = document.getElementById(column.id);
			element.style.width = actualWidth + 'px';
			element.style.height = height;
			if (column.nested != null)
				layout._resize(layout_info[column.nested], actualWidth - layout_padding.cellpadding, height - layout_padding.cellpadding);
		}
		total_width += actualWidth;
	}

	if (total_auto_columns > 0) {
		var auto_width = Math.round((width - total_width) / total_auto_columns);
		for (var i = 0; i < columns.length; i++) {
			var column = columns[i];
			if (column.width == '*') {
				var element = document.getElementById(column.id);
				element.style.width = auto_width + 'px';
				element.style.height = height;
				if (column.nested != null)
					layout._resize(layout_info[column.nested], auto_width - layout_padding.cellpadding, height - layout_padding.cellpadding);
			}
		}
	}
},

resize: function() {
	if (typeof layout_info != 'undefined') {
		if (typeof window.innerWidth != 'undefined') {
			width = window.innerWidth;
			height = window.innerHeight - 14;
		} else if (typeof document.documentElement != 'undefined') {
			width = document.documentElement.clientWidth;
			height = document.documentElement.clientHeight;
		} else {
			width = document.getElementsByTagName('body')[0].clientWidth;
			height = document.getElementsByTagName('body')[0].clientHeight;
		}
	 	layout._resize(layout_info[0], width - layout_padding.bodypadding, height - layout_padding.bodypadding);
 	}

	if (typeof control_layout_info != 'undefined') {
 		for (var i = 0; i < control_layout_info.length; i++) {
 			layout._resizeControl(control_layout_info[i]);
 		}
 	}
}

}

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

jQuery(window).resize(function() { layout.resize(); });
jQuery(document).ready(function() { layout.resize(); });
