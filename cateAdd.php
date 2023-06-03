<?php
include 'utils/checkSessionExpiration.php';
checkSessionExpiration();
include('db/dbConfig.php');
$db = new LabDB(); 
// Check the account
if ( !isset($_SESSION['userID']) ) {
    die("ACCESS DENIED");
}
else{
    $userID = $_SESSION['userID']; 
}

/********************************************************
*insert a category for this user
********************************************************/

if(isset($_POST['cgName']) && isset($_POST['cgID'])){

    $cgName = trim($_POST['cgName']);
    if(strlen($cgName)<1){
        $_SESSION['message'] = "Manque d'information";
        header("Location: cateAdd.php");
        return;
    }
    
    //add a parent category
    if($_POST['cgID'] == 0){
        // insert into database
        LabDB::insert($db, 'tb_category', array('user_id'=> $userID, 'cgname'=> $cgName));
        
    }else{
    //add a child category
        LabDB::insert($db, 'tb_category', array('user_id'=> $userID, 'cgname'=> $cgName, 'parent'=>$_POST['cgID']));
        
    }
    $_SESSION['message'] = "Catégorie ajouté";
    header("Location: cateEdit.php");
    return;
}

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php require_once "css/require.php"; ?>       
    </head> 

    <body class = "blog-theme">
        <?php 
        include'utils/generateHeaderAndNav.php';
        echo generateHeaderAndNav('LabZZZ Blog', 
                                    'Mon Blog', 
                                    'blogAdmin.php', 
                                    'Logout', 
                                    'logOut.php');
        ?>

        <main class = "content">
            <form action="cateAdd.php" method="post">
            <label>Catégorie:</label>
            <input class="form-control" style="width: 20rem;" type="text" name="cgName"></p>
            <label> Parent Catégorie:</label>
            <select name = "cgID">
            <?php
           
            /*********************************************************
            *a dropdown select for choose a categoy
            *********************************************************/
            //query for the first class categories

            $result = LabDB::select($db, 'tb_category', ['id', 'cgname'], 'user_id = "'.$userID.'" AND parent IS NULL'); 

            if($result != false){
                
                foreach($result as $row){
                    echo '<option value="'.$row['id'].'">'.htmlentities($row['cgname']).'</option>';
                    }
                
            }
            // there is no category yet, or this is for a first class category
            echo '<option value="0" style="font-weight :bold;" label="Ajoutez une parent catégorie"></option>';
            
            ?>
            
            </select>
            <input type="submit" value="Ajouter">
            </form>             
        </main>   
</body>