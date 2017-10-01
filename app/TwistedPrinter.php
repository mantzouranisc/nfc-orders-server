<?php
/**
 * Created by PhpStorm.
 * User: mantz
 * Date: 30-Sep-17
 * Time: 23:40
 */

require '../autoload.php';
use Mike42\Escpos\Printer;
class TwistedPrinter extends \Mike42\Escpos\Printer {

	/**
	 * Add text to the buffer.
	 *
	 * Text should either be followed by a line-break, or feed() should be called
	 * after this to clear the print buffer.
	 *
	 * @param string $str Text to print
	 */
	public function text($str = "")
	{
		self::validateString($str, __FUNCTION__);
		$this -> buffer -> writeText(mb_convert_encoding((string)$str,'ISO-8859-7')."\n");
	}

	public function printEmptyLine() {
		$this -> buffer -> writeText("\n");
	}

//	public function initialize() {
//		parent::initialize();
//		$this->printEmptyLine();
//	}

	public function section( $sectionTitle ) {

		if (empty($sectionTitle)){
			return false;
		}
		$this->printEmptyLine();
		$this->setUnderline();
		$this->text( $sectionTitle );
		$this->setUnderline(PRINTER::UNDERLINE_NONE);
		$this->printEmptyLine();
	}
}