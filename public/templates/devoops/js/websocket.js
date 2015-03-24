
$(document).ready(function() {

	var host = window.location.hostname;
	var conn = new WebSocket('ws://'+host+':8080');
	conn.onopen = function(e) {
		console.log("Connection established!");
	};
	conn.onmessage = function(e) {
		var stats = JSON.parse(e.data);
		if (stats.totalTrades) {
			$('#total_trades').html(stats.totalTrades);
		}
		console.log(e.data);
	};
});
