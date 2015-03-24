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
use React\Socket\Server as ReactServer;
use React\EventLoop\Factory;


include_once (ZMWS_DIR.'src/worker_base.php');

/**
 * Collect stats and display them to connected
 * web sockets.
 */
class Currency_Worker_Stattx extends Zmws_Worker_Base {

	public $stats        = '';
	public $wsApp        = NULL;
	public $latestTrades = NULL;
	public $runningStats = NULL;

	/**
	 */

	public function __construct($backendPort='', $context=NULL, $backendSocket=NULL) {
		parent::__construct($backendPort, $context, $backendSocket);
		$this->latestTrades = new \SplQueue(100);
		$this->runningStats = (object)array();
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

		if (isset($param->totalTrades)) {
			$this->runningStats->totalTrades = $param->totalTrades;
		}

		$this->broadcastStats($this->runningStats);

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
		if (strpos($paramfield, 'PARAM-JSON: ') === 0) {
			$param            = json_decode( substr($paramfield, 12));
		}

		$retstatus            = $this->work($jobid, $param);

		$this->_jobidCurrent  = $jobid;
		$this->_socketCurrent = $this->backend;
		$answer               = new Zmws_Worker_Answer();
		$answer->status       = $retstatus;
		$this->sendAnswer($answer);
	}

}

$loop   = React\EventLoop\Factory::create();
$socket = new ReactServer($loop);
$socket->listen('8080', '0.0.0.0');

$chat = new Chat();
$server = new IoServer(
	new HttpServer(
		new WsServer(
			$chat
		)
	),
	$socket,
	$loop
);

$context = new \React\ZMQ\Context($loop);
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
