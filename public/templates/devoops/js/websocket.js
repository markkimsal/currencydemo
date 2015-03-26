


function EnableTestButton() {
	var host = window.location.hostname;
	$('#submittest').on('click', function(e) {
		$.ajax({
			url: 'http://' + host + '/SYNC-submittx',
			type: 'POST',
			data: '{"userId": "134256", "currencyFrom": "GBP", "currencyTo": "USD", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}',
			dataType: "text/plain"
		});
	});
}

function EnableWebSocket() {
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

		if (stats.trades) {
			var tbody = $('#ticker-table tbody');
			var template = tbody.find('tr').first().clone();

			for(var i=0; i < stats.trades.length; i++) {
				var trow = template.clone();
				var trade = stats.trades[i];
				populateTr(trade, trow);

				tbody.prepend( trow );
			}
			//keep DOM form inflating forever
			tbody.find("tr:gt(100)").remove();
		}

		if (stats.initialTrades) {
			var tbody = $('#ticker-table tbody');
			var template = tbody.find('tr').first().clone();
			//don't blow away template row if no trades are present
			if (stats.initialTrades.length) {
				tbody.empty();
			}
			for(var i=0; i < stats.initialTrades.length; i++) {
				var trow = template.clone();
				var trade = stats.initialTrades[i];
				populateTr(trade, trow);

				tbody.append( trow );
			}
		}
	};

	function populateTr(trade, tr) {
		tr.find('td').each(function(idx) {
			switch(idx) {
				case 0:
					$(this).html(trade.currencyFrom+'/'+trade.currencyTo );
					break;

				case 1:
					$(this).html(trade.rate);
					break;

				case 2:
					$(this).html(trade.originatingCountry);
					break;

				case 3:
					$(this).html(trade.amountBuy);
					break;

			}
		});

	}
};
