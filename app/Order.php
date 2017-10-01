<?php
/**
 * Created by PhpStorm.
 * User: mantz
 * Date: 23-Sep-17
 * Time: 20:35
 */

require '../autoload.php';
include_once 'Config.php';
include_once 'TwistedPrinter.php';
include_once 'AddressImage.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\EscposImage;

class Order {

	private $_errors;
	private $billing;
	private $total;
	private $line_items;

	public function __construct( $data ) {

		foreach ( $data as $key => $value ) {
			$this->{$key} = $value;
		}
		if ( empty( $this->line_items ) || ! is_array( $this->line_items ) ) {
			throw new Exception( 'no line_items defined' );
		}
		$this->_errors = [];
	}

	public function __get( $key ) {
		if ( ! isset( $key ) ) {
			return null;
		}
	}

	public function printIt() {

		$start     = microtime( true );
		$connector = new FilePrintConnector( Config::PRINTER_ADDRESS );
		$profile   = CapabilityProfile::load( 'simple' );
		$printer   = new TwistedPrinter( $connector, $profile );
		$printer->initialize();

		//Title
		$printer->setJustification( PRINTER::JUSTIFY_CENTER );
		$printer->setTextSize( 2, 2 );
		$printer->section( Config::TITLE );
		$printer->setTextSize( 1, 1 );
		$printer->setJustification( PRINTER::JUSTIFY_LEFT );

		//General Info
		//$date = strftime( "%a %d %b %Y %X" );
		$date = date( 'd/m/Y H:i:s' );
		$printer->text( "Παραγγελία #{$this->id} " . $date );

		//Contact info
		$printer->section( "Στοιχεία Πελάτη:" );

		$printer->text( "Όνομα: {$this->billing->first_name}" );
		$printer->text( "Επίθετο: {$this->billing->last_name}" );
		$printer->text( "Διεύθυνση: {$this->billing->address_1} {$this->billing->address_2} {$this->billing->city} {$this->billing->state} {$this->billing->postcode}" );
		$printer->text( "Τηλ.: {$this->billing->phone}" );
		$printer->text( "Σχόλια: {$this->billing->comments}" );

		//Order info

		$printer->section( "Παραγγελία:" );

		$i = 1;
		foreach ( $this->line_items as $lineItem ) {
			$quantityText = ( $lineItem->quantity > 1 ) ? "({$lineItem->quantity}) " : '';
			$printer->text( "- $i. {$lineItem->name} $quantityText: {$lineItem->price}e" );
			$i ++;
		}
		unset( $i );

		$printer->printEmptyLine();
		$printer->setEmphasis( true );
		$printer->setTextSize( 2, 2 );
		$printer->text( "Σύνολο: {$this->total}e" );
		$printer->setTextSize( 1, 1 );
		$printer->setEmphasis( false );

		if ( Config::MAP_PRINT ) {
			try {
				$addressImage = new AddressImage( $this->billing );
				$image = $addressImage->fetchAddressImage();
				file_put_contents( 'tempImageFile', $image );
				$map = EscposImage::load( "tempImageFile", false );
				$printer->bitImage( $map );
			} catch ( Exception $e ) {
				error_log( "Unable to print map: {$e->getMessage()}" );
			}
		}

		$printer->cut();
		$printer->close();

		$processTimeMs = ( microtime( true ) - $start );

		return [
			'orderId'     => $this->id,
			'printTimeMs' => $processTimeMs,
			'errors'      => $this->_errors
		];
	}

	public function save() {
		$processTimeMs = 1000;

		return [
			'saveTimeMs' => $processTimeMs
		];
	}
}