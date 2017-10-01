<?php
/**
 * Created by PhpStorm.
 * User: mantz
 * Date: 30-Sep-17
 * Time: 23:06
 */

class ErrorHandler {

	public static function handleError($errno, $errstr, $errfile, $errline)
	{
		if (!(error_reporting() & $errno)) {
			// This error code is not included in error_reporting, so let it fall
			// through to the standard PHP error handler
			return false;
		}

		switch ($errno) {
			case E_USER_ERROR:
				http_response_code( 500 );
				echo json_encode( [ 'error' => $errstr ] );
				return false;
				break;

			case E_USER_WARNING:
			case E_USER_NOTICE:
				error_log("$errfile:$errline --- [$errno] $errstr");

				break;
			default:
				error_log( "Unknown error type: [$errno] $errstr");
				break;
		}

		/* Don't execute PHP internal error handler */
		return true;
	}

}