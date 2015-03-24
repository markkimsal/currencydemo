<?php

chdir( dirname(dirname(__FILE__)) );
define('ZMWS_DIR', 'local/zmws/');
require './local/autoload.php';
include('chat.php');

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;

use React\ZMQ\Context;


include_once (ZMWS_DIR.'src/worker_base.php');

/**
 * Collect stats and display them to connected
 * web sockets.
 */
class Currency_Worker_Stattx extends Zmws_Worker_Base {

	public $stats        = '';
	public $latestTrades = NULL;
	public $wsApp        = NULL;

	/**
	 */

	public function __construct($backendPort='', $context=NULL, $backendSocket=NULL) {
		parent::__construct($backendPort, $context, $backendSocket);
		$this->latestTrades = new \SplQueue(100);
	}

	/**
	 * Message Process
	 * TODO: sum tx by currency
	 * TODO: send tx and stats to WS frontend
	 */
	public function work($jobid, $param='') {

		if (isset($param->trades)) {
			foreach ($param->trades as $_trade) {
				$this->latestTrades->enqueue($_trade);
			}
		}

		$stats = (object)array();
		if (isset($param->totalTrades)) {
			$stats->totalTrades = $param->totalTrades;
		}

		$this->broadcastStats($stats);

		return TRUE;
	}

	public function setWsApp($app) {
		$this->wsApp = $app;

		//send last 100 trades on open
		$app->on('open', function($conn) {
			$trades = array();
			foreach ($this->latestTrades as $_trade) {
				$trades[] = $_trade;
			}
			$conn->send( json_encode($trades));
		});
	}

	public function onMessage($message) {
		if ($message == 'HEARTBEAT') {
			$this->heartbeat();
			return;
		}
	}

	public function broadcastStats($stats) {
		$this->wsApp->broadcast( json_encode($stats));
	}

	public function onMessages($message) {
		if ($message[0] == 'HEARTBEAT') {
			$this->heartbeat();
			return;
		}

		//TODO: parse jobid
		$jobfield             = array_pop($message);
		$jobid                = substr($jobfield, 5);

		$param                = (object)array();
		$paramfield           = array_pop($message);
		var_dump(strpos($paramfield, 'PARAM-JSON: '));
		if (strpos($paramfield, 'PARAM-JSON: ') === 0) {
			$param            = json_decode( substr($paramfield, 12));
		}

		var_dump(
		$param
		);

		$retstatus            = $this->work($jobid, $param);

		$this->_jobidCurrent  = $jobid;
		$this->_socketCurrent = $this->backend;
		$answer               = new Zmws_Worker_Answer();
		$answer->status       = $retstatus;
		$this->sendAnswer($answer);
	}

}

/**
 * Extend to get access to ZMQ socket with same Loop instance
 */
class ZioServer extends IoServer {

	public function getLoop() {
		return $this->loop;
	}

}
$chat = new Chat();
$server = ZioServer::factory(
	new HttpServer(
		new WsServer(
			$chat
		)
	),
	8080
);

$context = new \React\ZMQ\Context($server->getLoop());
$dealer  = $context->getSocket(ZMQ::SOCKET_DEALER);
//ZMWS worker
$w = Currency_Worker_Stattx::factory('', $context, $dealer);
$w->log_level = 'D';
$w->setWsApp($chat);

//connect up ZMWS with React notices
$dealer->on('connect', function($socket) use($w){
	$w->ready();
});

$dealer->on('message', array($w, 'onMessage'));

$dealer->on('messages', function($messages) use ($w, $chat) {
	$w->onMessages($messages);
});

$server->run();
