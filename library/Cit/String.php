<?php
defined("UTF_8") || define("UTF_8", 1);
defined("ASCII") || define("ASCII", 2);
defined("ISO_8859_1") || define("ISO_8859_1", 3);

class Cit_String
{
    protected $_string = '';
    
    public function __construct($string = '')
    {
        $this->_string = $string;
    }
    
    public static function parseString($string)
    {
        return new Cit_String($string);
    }
    
    public function encode()
	{
		$c		= 0;
		$ascii	= true;

		$i 				= 0;
		$numberCharacters	= strlen($this->_string);
		if ($numberCharacters > 0) {
			do {
				$byte = ord($this->_string[$i]);
				if ($c > 0) {
				   if (($byte>>6) != 0x2) {
						return ISO_8859_1;
				   } else {
						$c--;
				   }
				} elseif ($byte&0x80) {
				  $ascii = false;
				  if (($byte>>5) == 0x6) {
					 $c = 1;
				  } elseif (($byte>>4) == 0xE) {
					 $c = 2;
				  } elseif (($byte>>3) == 0x14) {
					 $c = 3;
				  } else {
					 return ISO_8859_1;
				  }
				}
				++$i;
			} while ($i < $numberCharacters);
		}
		return ($ascii) ? ASCII : UTF_8;
	}
    
    public function toUTF8()
	{
		$string = ($this->encode() == ISO_8859_1) ? iconv("ISO-8859-1", "UTF-8", $this->_string) : $this->_string;
        return new Cit_String($string);
	}
    
    public function toISO()
	{
        $string = ($this->encode() == ISO_8859_1) ? $this->_string : iconv("UTF-8", "ISO-8859-1", $this->_string);
		return new Cit_String($string);
	}
    
    public function  __toString()
    {
		return $this->_string;
	}
    
}