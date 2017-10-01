<?php

require_once 'Request.php';
require_once 'Order.php';
require_once 'ErrorHandler.php';

//setlocale( LC_ALL, 'el_GR' );
//set_error_handler('ErrorHandler::handleError');
try {
	$request = new Request();
} catch ( Exception $e ) {
	http_response_code( 500 );
	echo json_encode( [ 'error' => $e->getMessage() ] );

	return false;
}

try {
	$receipt = new Order( $request->getPayload() );
} catch ( Exception $e ) {
	http_response_code( 500 );
	echo json_encode( [ 'error' => $e->getMessage() ] );

	return false;
}

try {
	$printReceiptResult = ( Config::ORDERS_PRINT ) ? $receipt->printIt() : [];
} catch ( Exception $e ) {
	http_response_code( 500 );
	echo json_encode( [ 'error' => $e->getMessage() ] );

	return false;
}

try {
	$saveReceiptResult = ( Config::ORDERS_FILE_SAVE ) ? $receipt->save() : [];
} catch ( Exception $e ) {
	http_response_code( 500 );
	echo json_encode( [ 'error' => $e->getMessage() ] );

	return false;
}

echo json_encode( array_merge( $printReceiptResult, $saveReceiptResult ) );