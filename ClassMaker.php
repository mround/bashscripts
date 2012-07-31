#!/usr/bin/php
<?php
/**
 *	@author		Matthew Round
 *	@date		2012-03-23 (12:33)
 *	@filename	/home/roundyz/bin/bashscripts/ClassMaker.php
 *	@copyright	Copyright (c) 2012, Matthew Round
 */

//TODO ADD types to setters too
//creates a basic class, args like
// <classname> <varname1> <varname2> etc..
//TODO change to run like Classmaker.php classname <var1:type> <var2:type> ...


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

	$in = $a;
	$type;
	$nameIsSplit = nameIsSplit($a);
	if ($nameIsSplit) {
		$exploded = explode(":", $a);
		$in = $exploded[1];
		$type = $exploded[0];
	}

	echo "\n\n\t/**\n";
	echo "\t * ".$in."\n";
	echo "\t *\n";
	echo "\t * @var    ".$type."\n";
	echo "\t * @access private\n";
	echo "\t */\n";
	echo "\tprivate $"; 
	echo "_";
	echo $in;
	echo ";";
	echo "\n";
}


function makeConstructor($in){
	echo "\n";
	$ret="\t/**\n";
	$ret.="\t * __construct\n";
	$ret.="\t *\n";
	$ret.="\t * Default constructor.\n";
	$ret.="\t */\n";
	print $ret;
	echo "\tpublic function __construct()
	{
	}";
//TODO set default values to types...
	echo "\n";

}




	$className = $argv[1];
	//$varCount = $argv[2]-1;
	$varCount = count($argv)-2;
	//var_dump($varCount);


	echo "<?php";
	echo "\n";
	echo "/**";
	echo "\n";
	echo " * Holds the Class ". $className;
	echo "\n";
	echo " *";
	echo "\n";
	echo " * PHP version 5";
	echo "\n";
	echo " *";
	echo "\n";
	echo " *  @category FIXME";
	echo "\n";
	echo " *  @package  FIXME";
	echo "\n";
	echo " *  @author   FIXME <FIXME@example.com>";
	echo "\n";
	echo " *  @license  http://example.com example";
	echo "\n";
	echo " *  @link     example.co.uk" ;
	echo "\n";
	echo " *";
	echo "\n";
	echo " */";
	echo "\n";
	echo "\n";
	echo "\n";

	echo "\n";
	echo "\n";
	echo "/**";
	echo "\n";
	echo " * " .$className;
	echo "\n";
	echo " *";
	echo "\n";
	echo " * TODO Change me Description of the Class";
	echo "\n";
	echo " *";
	echo "\n";
	echo " *  @category  FIXME";
	echo "\n";
	echo " *  @package   FIXME";
	echo "\n";
	echo " *  @author    FIXME <FIXME@example.com>";
	echo "\n";
	echo " *  @copyright 2012 companyname";
	echo "\n";
	echo " *  @license   http://example.com example";
	echo "\n";
	echo " *  @version   Release:1.0";
	echo "\n";
	echo " *  @link      example.co.uk" ;
	echo "\n";
	echo " */";
	echo "\n";
	echo "class ";
	echo upperCaseFirstLetter($className);
	echo "\n{\n";


	for ($i=2;$i<=$varCount+1;$i++){
		makeInstanceVar($argv[$i]);
	}

	$getters = array();
	$setters = array();
	for ($i=2;$i<=$varCount+1;$i++){
		$getters[] = makeGetter($argv[$i]);
		$setters[] = makeSetter($argv[$i]);
	}
	

	foreach($getters as $getter)
	{
		echo $getter;
	}

	foreach($setters as $setter)
	{
		echo $setter;
	}

	
	echo "\n";
	
	$toString =  generateToString($varCount, $argv);
	print $toString;

	$toArray = generateToArray($varCount, $argv);
	print $toArray;

	makeConstructor(upperCaseFirstLetter($className));
	echo "\n}";
	echo "\n\n";
	echo "?>";
	

?>
