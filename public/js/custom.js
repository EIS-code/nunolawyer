document.addEventListener('keypress', function(event) {
	if ($(event.target).attr('type') == 'number') {
		e = event || window.event;
	  	let charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
	  	let charStr = String.fromCharCode(charCode);

	  	if (!charStr.match(/^[0-9]+$/)) {
	    	e.preventDefault();
	    }
	}
});