<?php

chdir( dirname(dirname(__FILE__)) );
define('ZMWS_DIR', 'local/zmws/');

include_once (ZMWS_DIR.'src/worker_base.php');

class Currency_Worker_Submittx extends Zmws_Worker_Base {

	/**
	 * We forward messages to the processor, so connect
	 * to the work server as a client
	 */
	public function __construct() {
		parent::__construct();
		$this->frontendSocket();
	}

	/**
	 * Message Consumption
	 * TODO: rate limit by userID, use SplQueue
	 */
	public function work($jobid, $param='') {

		$this->forwardToProc($param);
		return TRUE;
	}

	/**
	 * Push the param to the 'proctx' job
	 */
	public function forwardToProc($param) {
		$zforward = new Zmsg($this->frontend);
		$zforward->body_set('proctx');
		$zforward->wrap('PARAM-JSON: '.json_encode($param));
		$zforward->wrap(0x01);
		$zforward->wrap("MDPC02");
		$zforward->send();
		$zforward->recv();
	}
}

$w = new Currency_Worker_Submittx();
while($w->loop()) {}
