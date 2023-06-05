<?php
require_once './utils/checkSessionExpiration.php';
checkSessionExpiration();
require_once './db/dbconfig.php';

$cgID = isset($_POST['cgID']) ? ($_POST['cgID']) : '';
$cgName = isset($_POST['cgName']) ? htmlspecialchars($_POST['cgName']) : '';

$db = new LabDB();
$result = LabDB::update($db, 'tb_category',array('cgname'=>$cgName), 'id = "'.$cgID.'"');
//Execution succeed
if($result){ 
    $_SESSION['message'] = 'enregistrement mis à jour';  
} 
//failed
else{
   $_SESSION['message'] = 'Echec de la mise à jour';
}

?>