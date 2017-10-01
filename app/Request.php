<?php
/**
 * Created by PhpStorm.
 * User: mantz
 * Date: 23-Sep-17
 * Time: 20:19
 */
include_once 'Order.php';

class Request {

	private $_payload;

	/**
	 * @return mixed
	 */
	public function getPayload() {
		return $this->_payload;
	}

	public function __construct() {
		$this->_setResponseHeaders();
		if(!$this->_checkRequest()){
			throw new Exception('Invalid request');
		}
		$this->_fillPayload();

	}

	private function _fillPayload() {
		$this->_payload = json_decode(file_get_contents('php://input'));
		if (empty($this->_payload)){
			throw new Exception('Payload is empty');
		}
	}

	private function _checkRequest() {
		if ('POST' != $_SERVER['REQUEST_METHOD']){
			return false;
		}
		if ('application/json' != $_SERVER['CONTENT_TYPE']){
			return false;
		}
		return true;
	}

	private function _setResponseHeaders() {
		header('Content-Type: application/json');
	}
}