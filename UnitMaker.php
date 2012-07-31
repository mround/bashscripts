#!/usr/bin/php
<?php


class Func
{

	public $parameters;
	public $name;


	static function constructFromLine($line)
	{
		$instance = new self();
		$first = explode("function", $line);
		$bits = explode("(", $first[1]);	
		$vars = explode(",", $bits[1]);
		$instance->name = trim(trim($bits[0]), "\n\t\r");
		foreach ($vars as $var) {
			$trimmerVar = trim($var, ")\n\t\r ");
			if ($trimmerVar!="") {
				$instance->parameters[]=$trimmerVar;
			}
			return $instance;
		}
	}


	public function __toString()
	{
		$parmLine;
		if (count($this->parameters)>=1) {
			$parmLine ="// Params = "; 
			foreach($this->parameters as $param) {
				$parmLine .= $param;
			}
		}
		$ret ="\n\tpublic function test";
		$ret .= $this->name;
		$ret .= "()\n\t{\n\t\t";
		if (isset($parmLine)) {
			$ret.=$parmLine."\n\t\t";
		}
		$ret .="//TODO implement me...\n";
		$ret.="\t\t".'$success = false;'."\n";
		$ret.="\t\t".'$this->assertTrue($success, true);'."\n\t}\n\n";
		return $ret;
	}


}//EOC






class MakeUnitTests
{


	public $functions;
	public $classname;


	public function getLinesFromFile($file)
	{
		$lines = array();
		$fp = fopen($file, 'r');
		while (!feof($fp)) {
			$lines[] = fgets($fp);
		}
		fclose($fp);
		return $lines;
	}

	public function __construct($filename)
	{
		$lines = $this->getLinesFromFile($filename);
		$this->processLines($lines);
		$this->printClassName();
		$this->printFunctions();
		$this->printClosing();
	}

	public function printClosing()
	{
		print "\n\n}\n\n?>\n\n";
	}

	public function printClassName()
	{
		$classname = $this->classname;
		$cn = explode(" ", $classname);
		print "<?php\n\nclass ".trim($cn[1], "\n\r\t ")."Test extends PHPUnit_Framework_TestCase\n{\n\n";
	}

	public function printFunctions()
	{
		foreach ($this->functions as $function) {
			print $function;
		}
	}

	public function processLines($lineArray)
	{
		foreach ($lineArray as $line)
		{
			$lineIsFunction =preg_match("/function/", $line);
			if ($lineIsFunction) {
				$function =  Func::constructFromLine($line);
				$this->functions[] = $function;
			}

			$lineIsClassname = preg_match("/^class/", $line);
			if ($lineIsClassname) {
				$this->classname = $line;
			}
			//todo add line in const

		}
	}


}



$file=$argv[1];
$tt = new MakeUnitTests($file);


?>
