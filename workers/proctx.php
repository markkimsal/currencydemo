<?php

chdir( dirname(dirname(__FILE__)) );
define('ZMWS_DIR', 'local/zmws/');

include_once (ZMWS_DIR.'src/worker_base.php');

class Currency_Worker_Proctx extends Zmws_Worker_Base {

	public $numTrades = 0;

	/**
	 * We forward messages to the processor, so connect
	 * to the work server as a client
	 */
	public function __construct() {
		parent::__construct();
		$this->frontendSocket();
	}

	/**
	 * Message Process
	 * TODO: sum tx by currency
	 * TODO: send tx and stats to WS frontend
	 */
	public function work($jobid, $param='') {

		$this->numTrades++;
		$this->forwardToWs($param);
		return TRUE;
	}

	/**
	 * Push the param to the 'proctx' job
	 */
	public function forwardToWs($param) {

		$stats = (object)array();

		$stats->trades =array($param);
		$stats->totalTrades = $this->numTrades;

		$zforward = new Zmsg($this->frontend);
		$zforward->body_set('stattx');
		$zforward->wrap('PARAM-JSON: '.json_encode($stats));
		$zforward->wrap(0x01);
		$zforward->wrap("MDPC02");
		$zforward->send();
	}
}

$w = new Currency_Worker_Proctx();
while($w->loop()) {}
