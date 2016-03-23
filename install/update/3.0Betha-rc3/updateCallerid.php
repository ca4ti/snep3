<?php 
// update callerid for used module billing
$conecta = mysql_connect("localhost", "snep", "sneppass") or print (mysql_error()); 
mysql_select_db("snep", $conecta) or print(mysql_error()); 

$sql = "SELECT callerid,name FROM peers"; 

$result = mysql_query($sql, $conecta); 
var_dump($result); 
/* Escreve resultados até que não haja mais linhas na tabela */  
while($consulta = mysql_fetch_array($result)) { 
	
	$nameValue = explode("<", $consulta['callerid']);
	if(count($nameValue) <= 1){
        $new_callerid = $consulta['callerid']. " <".$consulta['name'].">"; 
    }else{
    	$new_callerid = $consulta['callerid'];
    };
	
	$name = $consulta['name'];
	var_dump("Atualizando callerid: ".$new_callerid);
	$sql = "UPDATE peers SET callerid = '$new_callerid' WHERE name='$name'";
	       
	mysql_query($sql) or die(mysql_error());
	
}; 

mysql_free_result($result); 
mysql_close($conecta); 
?>
