<?php

$serverList = array(
	array(
		'file'=>'local/zmws/src/server_run.php',
		'name'=>'server',
		'flags'=> array(
			'log-level'=>'D',
			'client-port'=>'6655',
			'worker-port'=>'6656',
			'news-port'=>'6657'
		)
	),
	array(
		'file'=>'local/zmws/src/http_gateway.php',
		'name'=>'gateway',
		'flags'=> array(
			'log-level'=>'I',
			'client-port'=>'6655',
			'http-port'=>'6680'
		)
	),
	array(
		'file'=>'local/zmws/src/http_gateway.php',
		'name'=>'gateway_b',
		'flags'=> array(
			'log-level'=>'I',
			'client-port'=>'6655',
			'http-port'=>'6690'
		)
	)
);

$workerList = array(
	array(
		'file'=>'workers/submittx.php',
		//name manages the log name and pid name
		'name'=>'txworker_b',
		'flags'=> array(
			'log-level'=>'E',
			'frontend-port'=>'6655',
			'backend-port'=>'6656',
			'service-name' => 'submittx'
		)
	),
	array(
		'file'=>'workers/submittx.php',
		//name manages the log name and pid name
		'name'=>'txworker_a',
		'flags'=> array(
			'log-level'=>'E',
			'frontend-port'=>'6655',
			'backend-port'=>'6656',
			'service-name' => 'submittx'
		)
	),
	array(
		'file'=>'workers/proctx.php',
		//name manages the log name and pid name
		'name'=>'proc_tx_b',
		'flags'=> array(
			'log-level'=>'E',
			'frontend-port'=>'6655',
			'backend-port'=>'6656',
			'service-name' => 'proctx'
		)
	),
	array(
		'file'=>'workers/proctx.php',
		//name manages the log name and pid name
		'name'=>'proc_tx_a',
		'flags'=> array(
			'log-level'=>'E',
			'frontend-port'=>'6655',
			'backend-port'=>'6656',
			'service-name' => 'proctx'
		)
	),
	array(
		'file'=>'workers/stattx.php',
		//name manages the log name and pid name
		'name'=>'stat_tx_a',
		'flags'=> array(
			'log-level'=>'E',
			'frontend-port'=>'6655',
			'backend-port'=>'6656',
			'service-name' => 'stattx'
		)
	)
);
