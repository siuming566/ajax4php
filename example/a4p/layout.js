
var layout = {

_resizeElement: function(table, width, height) {
	var element = document.getElementById(table.id);
	if (table.width.endsWith('%'))
		element.style.width = Math.floor(width * parseInt(table.width, 10) / 100) + 'px';
	if (table.width.endsWith('px'))
		element.style.width = table.width;
	if (table.height.endsWith('%'))
		element.style.height = Math.floor(height * parseInt(table.height, 10) / 100) + 'px';
	if (table.height.endsWith('px'))
		element.style.height = table.height;

	width = parseInt(element.style.width) - table.pad_width;
	height = parseInt(element.style.height) - table.pad_height;

	if (table.type == 'vertical')
		layout._resizeVertical(table, width, height);
	else if (table.type == 'horizontal')
		layout._resizeHorizontal(table, width, height);
},

_resize: function(table) {
	var element = document.getElementById(table.id).parentNode;
	while (element.nodeName != 'BODY' && !(element.style.width.endsWith('px') && element.style.width.endsWith('px')))
		element = element.parentNode;

	var width = element.clientWidth;
	var height = element.clientHeight;

	if (element.nodeName == 'BODY') {
		if (typeof window.innerWidth != 'undefined') {
			width = window.innerWidth;
			height = window.innerHeight;
		} else if (typeof document.documentElement != 'undefined' &&
					document.documentElement.clientWidth > 0 &&
					document.documentElement.clientHeight > 0) {
			width = document.documentElement.clientWidth;
			height = document.documentElement.clientHeight;
		} else {
			width = document.body.clientWidth;
			height = document.body.clientHeight;
		}

		// browser body margin
		width -= layout_margin.bodymargin * 2;
		height -= layout_margin.bodymargin * 2;
	}

	layout._resizeElement(table, width, height);
},

_resizeVertical: function(table, width, height) {
	var total_height = 0
	var total_auto_rows = 0;
	var rows = table.rows;
	for (var i = 0; i < rows.length; i++) {
		var row = rows[i];
		var actualHeight = 0;
		if (row.height.endsWith('%'))
			actualHeight = Math.floor(height * parseInt(row.height, 10) / 100);
		if (row.height.endsWith('px'))
			actualHeight = parseInt(row.height, 10);
		if (row.height == '*')
			total_auto_rows++;
		if (actualHeight > 0) {
			var element = document.getElementById(row.id);
			element.style.width = width + 'px';
			element.style.height = actualHeight + 'px';
		}
		total_height += actualHeight;
	}

	if (total_auto_rows > 0) {
		var auto_height = Math.floor((height - total_height) / total_auto_rows);
		if (auto_height < 0) return;
		for (var i = 0; i < rows.length; i++) {
			var row = rows[i];
			if (row.height == '*') {
				var element = document.getElementById(row.id);
				element.style.width = width + 'px';
				element.style.height = auto_height + 'px';
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
			actualWidth = Math.floor(width * parseInt(column.width, 10) / 100);
		if (column.width.endsWith('px'))
			actualWidth = parseInt(column.width, 10);
		if (column.width == '*')
			total_auto_columns++;
		if (actualWidth > 0) {
			var element = document.getElementById(column.id);
			element.style.width = actualWidth + 'px';
			element.style.height = height + 'px';
		}
		total_width += actualWidth;
	}

	if (total_auto_columns > 0) {
		var auto_width = Math.floor((width - total_width) / total_auto_columns);
		if (auto_width < 0) return;
		for (var i = 0; i < columns.length; i++) {
			var column = columns[i];
			if (column.width == '*') {
				var element = document.getElementById(column.id);
				element.style.width = auto_width + 'px';
				element.style.height = height + 'px';
			}
		}
	}
},

resize: function() {

	if (typeof layout_margin != 'undefined')
		document.body.style.margin = layout_margin.bodymargin + 'px';

	if (typeof layout_info != 'undefined') {
 		for (var i = 0; i < layout_info.length; i++)
		 	layout._resize(layout_info[i]);
 	}
}

};

jQuery(window).resize(function() { layout.resize(); });
jQuery(document).ready(function() { layout.resize(); });

