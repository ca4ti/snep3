<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Get  MySQL data conection
$settings = parse_ini_file('/var/www/html/snep/includes/setup.conf');
$host = $settings['db.host'] ;
$user = $settings['db.username'];
$pwd  = $settings['db.password'];
$db   = $settings['db.dbname'];
try {
   $conn = new PDO('mysql:host='.$host.';dbname='.$db, $user, $pwd) ;
} catch (PDOException $e) {
    echo $e->getMessage();
    exit ;
}

// GEt groups list
$sql = "select id,name,`group` from peers where peer_type = 'R'";
$stmt = $conn->query($sql) ;
while($row = $stmt->fetch()) {
    $grupo = check_group($row['group']) ;
    $sql = 'select * from core_peer_groups where peer_id='.$row['id'].' AND group_id='.$grupo ;
    $scc = $conn->query($sql) ;
    $res = $scc->fetchAll() ;
    if (  count($res) === 0 ) {
       $sql = 'insert into core_peer_groups (`peer_id`,`group_id`) values ('.$row['id'].','.$grupo.')' ;
        $sci = $conn->prepare($sql) ;
       $sci->execute() ;
    }
}
$sql = "alter table peers drop column `group` ; drop table `groups`;";
$sci = $conn->prepare($sql) ;
$sci->execute() ;


function check_group($name) {
   global $conn ;
   if ($name === "admin" || $name === "all" || $name === "users" || $name === "NULL")
      return 1 ;
   $stmt = $conn->query('Select id from core_groups where `name` = "'.$name.'"') ;
   $res = $stmt->fetchAll() ;
   if (count($res) > 0) {
      return $res[0][0];
   } else {
      $stmt = $conn->prepare('insert into  core_groups (`name`) values ("'.$name.'")') ;
      $stmt->execute() ;
      $id = $conn->lastInsertId();
      return $id ;
   }
}
?>
