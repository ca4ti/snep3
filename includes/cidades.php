<?php

$setup = parse_ini_file("setup.conf");
            
$idestado = $_GET['estado'];

mysql_connect($setup["db.host"],$setup["db.username"],$setup["db.password"]);
mysql_selectdb($setup["db.dbname"]);

$result = mysql_query("SELECT id,name FROM core_city WHERE state_id = ".$idestado);

while($row = mysql_fetch_array($result) ){
   	echo "<option value='".$row['id']."'>".$row['name']."</option>";
}

?>