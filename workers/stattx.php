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

	public $stats = '';
	public $latestTrades = NULL;

	/**
	 */
	public function __construct() {
		parent::__construct();
		$this->latestTrades = new \SplQueue(100);
	}

	/**
	 * Message Process
	 * TODO: sum tx by currency
	 * TODO: send tx and stats to WS frontend
	 */
	public function work($jobid, $param='') {

		$stats = $param;
		if (isset($stats->trades)) {
			foreach ($stats->trades as $_trade) {
				$this->latestTrades->enqueue($_trade);
			}
		}

		return TRUE;
	}

	public function setWsApp($app) {
		//send last 100 trades on open
		$app->on('open', function($conn) {
			$conn->send( json_encode($this->latestTrades));
		});
	}

/*
	public function onMessage($message) {
var_dump($message);
		if ($message == 'HEARTBEAT') {
			$this->heartbeat();
			return;
		}
	}
*/

	public function updateChatApp($chat) {
		$chat->broadcast($this->stats);
	}

	public function onMessages($message) {
		if ($message[0] == 'HEARTBEAT') {
			$this->heartbeat();
			return;
		}

var_dump('on messages...');
var_dump($message);
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
$w->setWsApp($chat);

//connect up ZMWS with React notices
$dealer->on('connect', function($socket) use($w){
	$w->ready();
});
//$dealer->on('message', array($w, 'onMessage'));

$dealer->on('messages', function($messages) use ($w, $chat) {

var_dump($messages);
	$w->onMessages($messages);
	$w->updateChatApp($chat);
});
$server->run();
