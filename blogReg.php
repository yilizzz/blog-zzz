<?php

/************************************
*   Proceed user's register datas 
*************************************/
require_once './db/dbconfig.php';
if(isset($_POST['regName']) && isset($_POST['regPW1']) && isset($_POST['regEmail'])){
    $regName = htmlspecialchars($_POST['regName']);
    $regPW = htmlspecialchars($_POST['regPW1']);
    $regEmail = htmlspecialchars($_POST['regEmail']);
    // Concatenate the salt plus password together
    $salt = 'labzzz2333*_';
    $store_PW = hash('md5', $salt.$regPW);
    //insert register data and generate a default blog category
    $db = new LabDB();
    if(LabDB::insert($db, 'tb_user', array('username'=> $regName, 'userpw'=> $store_PW, 'mailaddr'=> $regEmail))){
        session_start();
        // get this new user's id
        $userID = $db->lastInsertID();		
        $_SESSION['userID'] = $userID;
        $_SESSION['userName'] = $regName;
        if(LabDB::insert($db, 'tb_category', array('user_id'=> $userID, 'cgname'=> 'défaut'))){
            echo "<meta http-equiv='Refresh' content='0;URL=blogAdmin.php'>"; 
        }
    } else{
        echo "<meta http-equiv='Refresh' content='0;URL=index.php'>"; 
    }
}

?>
