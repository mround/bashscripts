#!/usr/bin/php
<?php
/**
 *	@author		Matthew Round
 *	@date		2012-03-23 (12:33)
 *	@filename	/home/roundyz/bin/bashscripts/ClassMaker.php
 *	@copyright	Copyright (c) 2012, Matthew Round
 */

//creates a basic class and test class, args like
//Usage Classmaker.php classname <var1:type> <var2:type> ...
//Primative types are supported long, string, int, float, array, bool, int

//fill in these
$tags = array();
$tags["category"] = "FIXME";
$tags["package"] = "com.ftms.FIXME";
$tags["author"] = "Matthew Round";
$tags["email"] = "Matthew.Round@friendmts.co.uk";
$tags["license"] = "http://example.com example";
$tags["link"] = "fmts.co.uk";
$tags["copyright"] = "Fmts";
$tags["version"] = "1.0";

function upperCaseFirstLetter($name)
{
	$tempArray = str_split($name);
	$high = "";
	$high .= strtoupper($tempArray[0]);
	$high .= join("",array_slice($tempArray,1)) ;
	return $high;
}


function nameisSplit($name)
{
	$ret = false;
	$nameIsSplit = preg_match("/:/", $name) == 1;
	if ($nameIsSplit)
		$ret = true;
	return $ret;
}


function makeGetter($a)
{
	$in = $a;
	$type;
	$nameIsSplit = nameIsSplit($a);
	if ($nameIsSplit) {
		$exploded = explode(":", $a);
		$in = $exploded[1];
		$type = $exploded[0];
	}
	$return ="";
	$return.= "\n";
	$return .="\t/**\n";
	$return .="\t * get".upperCaseFirstLetter($in)."\n";
	$return .="\t *\n";
	$return .="\t * Attempts to return the member ".$in.".\n";
	$return .="\t *\n";
	$return .="\t * @access public\n";
	$return .="\t * @return mixed bool on failure, else ". $type."\n";
	$return .="\t */";
	$return.= "\n";
	$return.= "\tpublic function get".upperCaseFirstLetter($in). "()\n";
	$return.= "\t{\n";
	$return.= sprintf("\t\t\$return = false;\n\t\t\$%sIsSet = isset(\$this->_%s);\n\t\tif ($%sIsSet) {\n\t\t\t",$in,$in,$in);
	$return.= "".'$return = $this->_';
	$return.= $in;
	$return.= ";";
	$return.= "\n\t\t}\n";
	$return.= "\t\treturn \$return;";
	$return.= "\n\t}\n";
	$return.= "\n";
	return $return;
}


function makeSetter($a)
{

	$in = $a;
	$type;
	$nameIsSplit = nameIsSplit($a);
	if ($nameIsSplit) {
		$exploded = explode(":", $a);
		$in = $exploded[1];
		$type = $exploded[0];
	}
	$extraCheck = matchTypeCheck($type, $in);


	$TYPE_LONG="/[lL]ong/";
	$TYPE_STRING="/[Ss]tring/";
	$TYPE_FLOAT="/[Ff]loat/";
	$TYPE_ARRAY="/[Aa]rray/";
	$TYPE_BOOL="/[Bb]ool/";
	$TYPE_INTEGER="/[Ii]nt/";
	$ret ="";
	$typeIsLong = preg_match($TYPE_LONG, $type) == 1;
	$typeIsString = preg_match($TYPE_STRING, $type) == 1;
	$typeIsFloat = preg_match($TYPE_FLOAT, $type) == 1;
	$typeIsArray = preg_match($TYPE_ARRAY, $type) == 1;
	$typeIsBool = preg_match($TYPE_BOOL, $type) == 1;
	$typeIsInteger = preg_match($TYPE_INTEGER, $type) == 1;

	$typeIsPrimative = $typeIsLong || $typeIsString || $typeIsFloat || $typeIsArray || $typeIsBool || $typeIsInteger;

	if ($typeIsPrimative) {
		$type ="";
	}

	$return = "";
	$return.= "\n";
	$return.= "\t/**\n";
	$return.= "\t * set".upperCaseFirstLetter($in)."\n";
	$return.= "\t *\n";
	$return.= "\t * Attempts to set the member ".$in."\n";
	$return.= "\t *\n";
	$return.= "\t * @param ".$type." \$".$in." to set.\n";
	$return.= "\t *\n";
	$return.= "\t * @access public\n";
	$return.= "\t * @return bool on success\n";
	$return.= "\t */\n";
	$return.= "\tpublic function set".upperCaseFirstLetter($in). "(".$type." $".$in.')';
	$return.= "\n\t{\n\t\t";
	$return.= sprintf("$%sIsOK = isset($%s) && $%s != null %s;",$in,$in,$in,$extraCheck);
	$return.= "\n";
	$return.= "\t\t\$return = false;\n";
	$return.= sprintf("\t\tif ($%sIsOK) {",$in);
	$return.= "\n\t\t\t";
	$return.= '$this->_';
	$return.= $in;
	$return.= " = ";
	$return.= "$".$in;
	$return.= ";\n\t\t\t\$return = true;\n\t\t}\n";
	$return.= "\t\treturn \$return;\n\t}\n";
	$return.= "\n";
	return $return;
}


function getCheckTypeForTest($type)
{

	$TYPE_LONG="/[lL]ong/";
	$TYPE_STRING="/[Ss]tring/";
	$TYPE_FLOAT="/[Ff]loat/";
	$TYPE_ARRAY="/[Aa]rray/";
	$TYPE_BOOL="/[Bb]ool/";
	$TYPE_INTEGER="/[Ii]nt/";
	$ret ="";
	$typeIsLong = preg_match($TYPE_LONG, $type) == 1;
	$typeIsString = preg_match($TYPE_STRING, $type) == 1;
	$typeIsFloat = preg_match($TYPE_FLOAT, $type) == 1;
	$typeIsArray = preg_match($TYPE_ARRAY, $type) == 1;
	$typeIsBool = preg_match($TYPE_BOOL, $type) == 1;
	$typeIsInteger = preg_match($TYPE_INTEGER, $type) == 1;

	if ($typeIsInteger) 
		$ret = "100"; 
	if ($typeIsBool) 
		$ret = "false"; 
	if ($typeIsArray) 
		$ret = "new array()"; 
	if ($typeIsFloat) 
		$ret = "0.00001"; 
	if ($typeIsString) 
		$ret = "\"TESTSTRING\""; 
	if ($typeIsLong) 
		$ret = "1111111.000000"; 
	if ($ret == "")
		$ret = "new ".$type."()";
	return $ret;
		
}

function makeTestGetterAndSetter($a, $classname)
{
	$in = $a;
	$type;
	$nameIsSplit = nameIsSplit($a);
	if ($nameIsSplit) {
		$exploded = explode(":", $a);
		$type = $exploded[0];
		$in = $exploded[1];
	}

	$extraCheck = matchTypeCheck($type, $in);
	$varToSet = getCheckTypeForTest($type);
	$format = "

	/**
	 * testGetAndSet%s
	 *
	 * Tests the methods get%s() and set%s()
	 *
	 * @access public
	 * @return void
	 */
	public function testGetAndSet%s()
	{
		\$%s = new %s(); 
		\$varToSet = %s;
		\$setResult = \$%s->set%s(\$varToSet);
		\$getResult = \$%s->get%s();
		\$setOK = \$setResult == true;
		\$getOK = \$getResult == \$varToSet;
		\$typeOK = \$getResult === \$varToSet;
		\$success = \$getOK && \$setOK && \$typeOK;
		\$this->assertTrue(\$success, true);
	}
	";
	$return = sprintf($format,
	upperCaseFirstLetter($in), 
	upperCaseFirstLetter($in), 
	upperCaseFirstLetter($in), 
	upperCaseFirstLetter($in), 

	strtolower($classname), 
	upperCaseFirstLetter($classname), 
	$varToSet,
	strtolower($classname), 
	upperCaseFirstLetter($in), 
	strtolower($classname), 
	upperCaseFirstLetter($in)
	);
	return $return;
}



function generateToString($varCount, $argv)
{
	$ret;
	$ret.="\t/**\n";
	$ret.="\t * toString\n";
	$ret.="\t *\n";
	$ret.="\t * Attempts to represent this as a string.\n";
	$ret.="\t *\n";
	$ret.="\t * @return String This as a string\n";
	$ret.="\t */\n";
	$ret.= "\tpublic function __toString()
	{\n\t\t\$out='';\n";
	for ($i=2;$i<=$varCount+1;$i++){
		$a = $argv[$i];
		$in = $a;
		$nameIsSplit = nameIsSplit($a);
		if ($nameIsSplit) {
			$exploded = explode(":", $a);
			$in = $exploded[1];
		}
		$ret.= "\t\t\$out.=\$this->_" .($in).";\n";
	}
	$ret.= "\t\treturn \$out;\n";
	$ret.= "\t}\n\n";
	return $ret;
}



function matchTypeCheck($type, $name)
{
	$TYPE_LONG="/[lL]ong/";
	$TYPE_STRING="/[Ss]tring/";
	$TYPE_FLOAT="/[Ff]loat/";
	$TYPE_ARRAY="/[Aa]rray/";
	$TYPE_BOOL="/[Bb]ool/";
	$TYPE_INTEGER="/[Ii]nt/";
	$ret ="";
	$typeIsLong = preg_match($TYPE_LONG, $type) == 1;
	$typeIsString = preg_match($TYPE_STRING, $type) == 1;
	$typeIsFloat = preg_match($TYPE_FLOAT, $type) == 1;
	$typeIsArray = preg_match($TYPE_ARRAY, $type) == 1;
	$typeIsBool = preg_match($TYPE_BOOL, $type) == 1;
	$typeIsInteger = preg_match($TYPE_INTEGER, $type) == 1;
	if ($typeIsInteger) 
		$ret = "&& is_int(\$".$name.")"; 
	if ($typeIsBool) 
		$ret = "&& is_bool(\$".$name.")"; 
	if ($typeIsArray) 
		$ret = "&& is_array(\$".$name.")"; 
	if ($typeIsFloat) 
		$ret = "&& is_float(\$".$name.")"; 
	if ($typeIsString) 
		$ret = "&& is_string(\$".$name.")"; 
	if ($typeIsLong) 
		$ret = "&& is_long(\$".$name.")"; 
	return $ret;

}


function makeInstanceVar($a)
{
	$return = "";
	$in = $a;
	$type;
	$nameIsSplit = nameIsSplit($a);
	if ($nameIsSplit) {
		$exploded = explode(":", $a);
		$in = $exploded[1];
		$type = $exploded[0];
	}
	$return .=  "\n\n\t/**\n";
	$return .=  "\t * ".$in."\n";
	$return .=  "\t *\n";
	$return .=  "\t * @var    ".$type."\n";
	$return .=  "\t * @access private\n";
	$return .=  "\t */\n";
	$return .=  "\tprivate $"; 
	$return .=  "_";
	$return .=  $in;
	$return .=  ";";
	$return .=  "\n";
	return $return;
}


function makeConstructor($in)
{
	$ret = "\n";
	$ret .="\t/**\n";
	$ret.="\t * __construct\n";
	$ret.="\t *\n";
	$ret.="\t * Default constructor.\n";
	$ret.="\t */\n";
	$ret.= "\tpublic function __construct()
	{
	}";
	$ret.= "\n";
	return $ret;
}


function makeStartClass($className, $tags)
{
	$return =  "<?php";
	$return .=  "\n";
	$return .=  "/**";
	$return .=  "\n";
	$return .=  " * Holds the Class ". $className;
	$return .=  "\n";
	$return .=  " *";
	$return .=  "\n";
	$return .=  " * PHP version 5";
	$return .=  "\n";
	$return .=  " *";
	$return .=  "\n";
	$return .=  " *  @category ".$tags["category"];
	$return .=  "\n";
	$return .=  " *  @package  ".$tags["package"];
	$return .=  "\n";
	$return .=  " *  @author   ".$tags["author"]." <".$tags["email"].">";
	$return .=  "\n";
	$return .=  " *  @license  ".$tags["license"];
	$return .=  "\n";
	$return .=  " *  @link     ".$tags["link"] ;
	$return .=  "\n";
	$return .=  " *";
	$return .=  "\n";
	$return .=  " */";
	$return .=  "\n";
	$return .=  "\n";
	$return .=  "\n";
	$return .=  "\n";
	$return .=  "/**";
	$return .=  "\n";
	$return .=  " * " .$className;
	$return .=  "\n";
	$return .=  " *";
	$return .=  "\n";
	$return .=  " * TODO Change me Description of the Class";
	$return .=  "\n";
	$return .=  " *";
	$return .=  "\n";
	$return .=  " *  @category  ".$tags["category"];
	$return .=  "\n";
	$return .=  " *  @package   ".$tags["package"];
	$return .=  "\n";
	$return .=  " *  @author    ".$tags["author"]." <".$tags["email"].">";
	$return .=  "\n";
	$return .=  " *  @copyright ".strftime("%Y",time())." ".$tags["copyright"];
	$return .=  "\n";
	$return .=  " *  @license   ".$tags["license"];
	$return .=  "\n";
	$return .=  " *  @version   Release:".$tags["version"];
	$return .=  "\n";
	$return .=  " *  @link      ".$tags["link"] ;
	$return .=  "\n";
	$return .=  " */";
	$return .=  "\n";
	$return .=  "class ";
	$return .=  upperCaseFirstLetter($className);
	$return .=  "\n{\n";
	return $return;
}


function makeStartTestClass($className, $tags)
{
	$return =  "<?php";
	$return .=  "\n";
	$return .=  "/**";
	$return .=  "\n";
	$return .=  " * Holds the Test Class ". $className . "Test";
	$return .=  "\n";
	$return .=  " *";
	$return .=  "\n";
	$return .=  " * PHP version 5";
	$return .=  "\n";
	$return .=  " *";
	$return .=  "\n";
	$return .=  " *  @category ".$tags["category"];
	$return .=  "\n";
	$return .=  " *  @package  ".$tags["package"];
	$return .=  "\n";
	$return .=  " *  @author   ".$tags["author"]." <".$tags["email"].">";
	$return .=  "\n";
	$return .=  " *  @license  ".$tags["license"];
	$return .=  "\n";
	$return .=  " *  @link     ".$tags["link"] ;
	$return .=  "\n";
	$return .=  " *";
	$return .=  "\n";
	$return .=  " */";
	$return .=  "\n";
	$return .=  "\n";
	$return .=  "\n";
	$return .=  "require_once \"".$className.".php\";\n";
//	$return .=  "require_once \"PHPUnit/Framework.php\";\n";
	$return .=  "\n";
	$return .=  "\n";
	$return .=  "/**";
	$return .=  "\n";
	$return .=  " * " .$className."Test";
	$return .=  "\n";
	$return .=  " *";
	$return .=  "\n";
	$return .=  " * Tests the class ".$className;
	$return .=  "\n";
	$return .=  " *";
	$return .=  "\n";
	$return .=  " *  @category  ".$tags["category"];
	$return .=  "\n";
	$return .=  " *  @package   ".$tags["package"];
	$return .=  "\n";
	$return .=  " *  @author    ".$tags["author"]." <".$tags["email"].">";
	$return .=  "\n";
	$return .=  " *  @copyright ".strftime("%Y",time())." ".$tags["copyright"];
	$return .=  "\n";
	$return .=  " *  @license   ".$tags["license"];
	$return .=  "\n";
	$return .=  " *  @version   Release:".$tags["version"];
	$return .=  "\n";
	$return .=  " *  @link      ".$tags["link"] ;
	$return .=  "\n";
	$return .=  " *  @use       PHPUnit_Framework_TestCase";
	$return .=  "\n";
	$return .=  " */";
	$return .=  "\n";
	$return .=  "class ";
	$return .=  upperCaseFirstLetter($className);
	$return .=  "Test extends PHPUnit_Framework_TestCase\n{\n";
	return $return;
}


function buildClass($varCount, $className, $argv, $tags)
{
	$class = makeStartClass($className, $tags);
	for ($i=2;$i<=$varCount+1;$i++){
		$class .= makeInstanceVar($argv[$i]);
	}
	$getters = array();
	$setters = array();
	for ($i=2;$i<=$varCount+1;$i++){
		$getters[] = makeGetter($argv[$i]);
		$setters[] = makeSetter($argv[$i]);
	}
	foreach($getters as $getter)
	{
		$class.= $getter;
	}
	foreach($setters as $setter)
	{
		$class.= $setter;
	}
	$class .= "\n";
	$class .=  generateToString($varCount, $argv);
	$class .= generateToArray($varCount, $argv);
	$class .= makeConstructor(upperCaseFirstLetter($className));
	$class .= "\n\n}";
	$class .= "\n";
	$class .= "?>";
	return $class;
}


function buildTestClass($varCount, $className, $argv, $tags)
{
	$class = makeStartTestClass($className, $tags);
	$testGetters = array();
	for ($i=2;$i<=$varCount+1;$i++){
		$testGetters[] = makeTestGetterAndSetter($argv[$i],$className);
	}
	foreach($testGetters as $setter)
	{
		$class.= $setter;
	}
	$class .= "\n\n}";
	$class .= "\n";
	$class .= "?>";
	return $class;
}


function generateToArray($varCount, $argv)
{
	$ret = "\n";
	$ret.="\t/**\n";
	$ret.="\t * toArray\n";
	$ret.="\t *\n";
	$ret.="\t * Attempts to represent this as a array.\n";
	$ret.="\t *\n";
	$ret.="\t * @return Array This as a array\n";
	$ret.="\t */\n";
	$ret.= "\tpublic function toArray()
	{\n\t\t\$out= array();\n";
	for ($i=2;$i<=$varCount+1;$i++){
		$a = $argv[$i];
		$in = $a;
		$nameIsSplit = nameIsSplit($a);
		if ($nameIsSplit) {
			$exploded = explode(":", $a);
			$in = $exploded[1];
		}
		$ret.= "\t\t\$out['".$in."']=\$this->_" .($in).";\n";
	}
	$ret.= "\t\treturn \$out;\n";
	$ret.= "\t}\n\n";
	return $ret;
}



//run from here
$className = $argv[1];
$varCount = count($argv)-2;
$class = buildClass($varCount, $className, $argv, $tags);
$testClass = buildTestClass($varCount, $className, $argv, $tags);
file_put_contents($className.".php", $class);
file_put_contents($className."Test.php", $testClass);
?>
