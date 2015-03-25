<?php

$serverList = array(
	array(
		'file'=>'local/zmws/src/server_run.php',
		'name'=>'server',
		'flags'=> array(
			'log-level'=>'D',
			'client-port'=>'5555',
			'worker-port'=>'5556',
			'news-port'=>'5557'
		)
	),
	array(
		'file'=>'local/zmws/src/http_gateway.php',
		'name'=>'gateway',
		'flags'=> array(
			'log-level'=>'I',
			'client-port'=>'5555',
			'http-port'=>'5580'
		)
	),
	array(
		'file'=>'local/zmws/src/http_gateway.php',
		'name'=>'gateway',
		'flags'=> array(
			'log-level'=>'I',
			'client-port'=>'5555',
			'http-port'=>'5590'
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
			'frontend-port'=>'5555',
			'backend-port'=>'5556',
			'service-name' => 'submittx'
		)
	),
	array(
		'file'=>'workers/submittx.php',
		//name manages the log name and pid name
		'name'=>'txworker_a',
		'flags'=> array(
			'log-level'=>'E',
			'frontend-port'=>'5555',
			'backend-port'=>'5556',
			'service-name' => 'submittx'
		)
	),
	array(
		'file'=>'workers/proctx.php',
		//name manages the log name and pid name
		'name'=>'proc_tx_b',
		'flags'=> array(
			'log-level'=>'E',
			'frontend-port'=>'5555',
			'backend-port'=>'5556',
			'service-name' => 'proctx'
		)
	),
	array(
		'file'=>'workers/proctx.php',
		//name manages the log name and pid name
		'name'=>'proc_tx_a',
		'flags'=> array(
			'log-level'=>'E',
			'frontend-port'=>'5555',
			'backend-port'=>'5556',
			'service-name' => 'proctx'
		)
	),
	array(
		'file'=>'workers/stattx.php',
		//name manages the log name and pid name
		'name'=>'stat_tx_a',
		'flags'=> array(
			'log-level'=>'E',
			'frontend-port'=>'5555',
			'backend-port'=>'5556',
			'service-name' => 'stattx'
		)
	)
);
