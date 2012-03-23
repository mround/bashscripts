#!/usr/bin/php

<?php
//ADD types to setters too
//creates a basic class, args like
// <classname> <numberOfVars> <varname1> <varname2> etc..
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
	$return.= "\t\t".'return $this->';
	$return.= $in;
	$return.= ";";
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
	$return.= sprintf("$%sIsOK = isset($%s) && $%s != NULL;",$in,$in,$in);
	$return .= "\n";
	$return.= sprintf("\t\tif ( $%sIsOK )",$in);
	$return.= "\n\t\t{\n\t\t\t";
	$return.= '$this->';
	$return.= $in;
	$return.= " = ";
	$return.= "$".$in;
	$return.= ";\n\t\t}\n";
	$return.= "\n\t}\n";
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
	$varCount = $argv[2]-1;

	echo "<?php";
	echo "\n";
	echo "class ";
	echo upperCaseFirstLetter($className);
	echo "\n{\n";
	echo "\n";
	echo "\n";


	for ($i=0;$i<=$varCount;$i++){
		makeInstanceVar($argv[$i+3]);
	}

	$getters = array();
	$setters = array();
	for ($i=0;$i<=$varCount;$i++){
		$getters[] = makeGetter($argv[$i+3]);
		$setters[] = makeSetter($argv[$i+3]);
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
	for ($i=0;$i<=$varCount;$i++){
		echo "\t\t\$out.=\$" .($argv[$i+3]).";\n";
	}
	echo "\t\treturn \$out;\n";
	echo "\t}\n\n";


	makeConstructor(upperCaseFirstLetter($className));
	echo "\n}//EOC";
	echo "\n\n";
	echo "?>";
	

?>
