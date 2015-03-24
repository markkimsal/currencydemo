
$(document).ready(function() {

	var host = window.location.hostname;
	var conn = new WebSocket('ws://'+host+':8080');
	conn.onopen = function(e) {
		console.log("Connection established!");
	};
	conn.onmessage = function(e) {
		console.log(e.data);
	};
});
