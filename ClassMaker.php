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
function upperCaseFirstLetter($name){
	$tempArray = str_split($name);
	$high = "";
	$high .= strtoupper($tempArray[0]);
	$high .= join("",array_slice($tempArray,1)) ;
	return $high;
}


function makeGetter($in)
{
	$return ="";
	$return.= "\n";
	$return.= "\tpublic function get".upperCaseFirstLetter($in). "()\n";
	$return.= "\t{\n";
	$return.= sprintf("\t\t\$return = FALSE;\n\t\t%sIsSet = isset( \$this->%s );\n\t\tif ( $%sIsSet )\n\t\t{\n\t\t\t",$in,$in,$in);
	$return.= "".'$return = $this->';
	$return.= $in;
	$return.= ";";
	$return.= "\n\t\t}\n";
	$return.= "\t\treturn \$return\n";
	$return.= "\n\t}\n";
	$return.= "\n";
	return $return;
}

function makeSetter($in)
{
	$return = "";
	$return.= "\n";
	$return.= "\tpublic function set".upperCaseFirstLetter($in). "( $".$in.' )';
	$return.= "\n\t{\n\t\t";
	$return.= sprintf("$%sIsOK = isset( $%s ) && $%s != NULL;",$in,$in,$in);
	$return .= "\n";
	$return.= sprintf("\t\tif ( $%sIsOK )",$in);
	$return.= "\n\t\t{\n\t\t\t";
	$return.= '$this->';
	$return.= $in;
	$return.= " = ";
	$return.= "$".$in;
	$return.= ";\n\t\t}\n";
	$return.= "\t}\n";
	$return.= "\n";
	return $return;
}


function makeInstanceVar($in){
	echo "\tprivate $"; 
	echo $in;
	echo ";";
	echo "\n";
}


function makeConstructor($in){
	echo "\n";
	echo "\tfunction __construct()
	{
	}";
//TODO set default values to types...
	echo "\n";

}

	$className = $argv[1];
	//$varCount = $argv[2]-1;
	$varCount = count($argv)-2;
	var_dump($varCount);


	echo "<?php";
	echo "\n";
	echo "class ";
	echo upperCaseFirstLetter($className);
	echo "\n{\n";
	echo "\n";
	echo "\n";


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


	echo "\tpublic function __toString()
	{\n\t\t\$out='';\n";
	for ($i=2;$i<=$varCount+1;$i++){
		echo "\t\t\$out.=\$" .($argv[$i]).";\n";
	}
	echo "\t\treturn \$out;\n";
	echo "\t}\n\n";



	echo "\tpublic function toArray()
	{\n\t\t\$out= array();\n";
	for ($i=2;$i<=$varCount+1;$i++){
		echo "\t\t\$out['".$argv[$i]."']=\$this->" .($argv[$i]).";\n";
	}
	echo "\t\treturn \$out;\n";
	echo "\t}\n\n";


	makeConstructor(upperCaseFirstLetter($className));
	echo "\n}";
	echo "\n\n";
	echo "?>";
	

?>
