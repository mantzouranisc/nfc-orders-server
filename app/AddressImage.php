<?php
/**
 * Created by PhpStorm.
 * User: mantz
 * Date: 30-Sep-17
 * Time: 22:42
 */
require '../vendor/autoload.php';
use GuzzleHttp\Client;

class AddressImage {

	private $_center;
	private $_size;
	private $_zoom;
	private $_apiKey;
	private $_mapType;

	public function __construct( $billingInfo ) {
		if ( empty( $billingInfo ) ) {
			throw new Exception( 'No order Item' );
		}
		error_log( var_export( $billingInfo, true ) );
		$this->_center  = $billingInfo->address_1 . ' ' . $billingInfo->address_2 . ' ' . $billingInfo->city . ' ' . $billingInfo->country;
		$this->_size    = Config::MAP_SIZE;
		$this->_zoom    = Config::MAP_ZOOM;
		$this->_mapType = Config::MAP_TYPE;
		$this->_apiKey  = Config::MAP_API_KEY;
	}

	public function fetchAddressImage() {


		$client = new Client();
		$res    = $client->request( 'GET',
			"https://maps.googleapis.com/maps/api/staticmap?zoom={$this->_zoom}&size={$this->_size}&maptype={$this->_mapType}&center={$this->_center}&key=$this->_apiKey" );

		if ( '200' != $res->getStatusCode() ) {
			error_log( 'Failed to get GMaps static image: ' . $res->getStatusCode() );

			return false;
		}

		return $res->getBody();

	}
}