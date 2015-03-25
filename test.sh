#!/bin/bash
for x in {1..100}
do
	curl -XPOST demo.igotaprinter.com:80/SYNC-submittx \
	 -d '{"userId": "134256", "currencyFrom": "GBP", "currencyTo": "USD", "amountSell": 1000, "amountBuy": 747.10, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "FR"}' &

	curl -XPOST demo.igotaprinter.com:80/SYNC-submittx \
	 -d '{"userId": "134257", "currencyFrom": "GBP", "currencyTo": "USD", "amountSell": 100, "amountBuy": 74.710, "rate": 0.7471, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "SP"}' &

	curl -XPOST demo.igotaprinter.com:80/SYNC-submittx \
	 -d '{"userId": "134258", "currencyFrom": "USD", "currencyTo": "GBP", "amountSell": 747.20, "amountBuy": 1000, "rate": 0.7472, "timePlaced" : "14-JAN-15 10:27:44", "originatingCountry" : "DE"}' &

	curl -XPOST demo.igotaprinter.com:80/SYNC-submittx \
	 -d '{"userId": "124254", "currencyFrom": "USD", "currencyTo": "EUR", "amountSell": 1140.50, "amountBuy": 1000, "rate": 1.1405, "timePlaced" : "14-JAN-15 10:27:45", "originatingCountry" : "US"}' &
done
