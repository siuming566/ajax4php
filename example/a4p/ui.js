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

loadControl: function (control, id) {
	// request content
	var self = this;
	var event = this.newevent();
	jQuery.ajax({
		url: a4p.prefix + '/control.php',
		type: 'POST',
		data: {	control: control,
			time: (new Date()).getTime() },
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

