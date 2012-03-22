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


function makeGetterAndSetter($in){
	echo "\n";
	echo "\tpublic function get".upperCaseFirstLetter($in). "()\n";
	echo "\t{\n";
	echo "\t\t".'return $this->';
	echo $in;
	echo ";";
	echo "\n\t}\n";
	echo "\n\n";
	echo "\tpublic function set".upperCaseFirstLetter($in). "( $".$in.' )';
	echo "\n\t{\n\t\t";
	echo '$this->';
	echo $in;
	echo " = ";
	echo "$".$in;
	echo ";\n\t}\n";
	echo "\n";
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

	for ($i=0;$i<=$varCount;$i++){
		makeGetterAndSetter($argv[$i+3]);
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
