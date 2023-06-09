<?php
require_once  './utils/checkSessionExpiration.php';
checkSessionExpiration();
require_once './db/dbconfig.php';

$blogID = $_SESSION['blogID']; 
$db = new LabDB();
// Guardian: Make sure that blog_id is present
if ( ! isset($_SESSION['blogID']) ) {
    $_SESSION['message'] = "Missing blog_id";
    header('Location: blogAdmin.php');
    return;
}

if ( isset($_POST['delete']) ) {

    $result = LabDB::delete($db, 'tb_post', 'id = '.$_SESSION['blogID']);
    //delete failed
    if(!$result){    
        $_SESSION['message'] = 'Suppression échouée';
        header( 'Location: blogAdmin.php' ) ;
        return;
    } 
    //delete succeed
    else{
        $_SESSION['message'] = 'Un blog a été supprimé';
        header( 'Location: blogAdmin.php' ) ;
        return;
        
    }
    
}


$result = LabDB::find($db, 'tb_post', 'title', 'id = "'.$blogID.'"');
       
if($result == false){
        $_SESSION['message'] = 'Bad value for blog_id';
        header( 'Location: blogAdmin.php' ) ;
        return;
}else{
    $title = $result['title'];
}


?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <?php require_once "./css/require.php"; ?>
    </head> 

    <body class = "blog-theme">
        <?php 
        require_once './utils/generateHeaderAndNav.php';
        echo generateHeaderAndNav('LabZZZ Blog', 'Lire Plus', 'readBlog.php', 'Mon Blog', 'blogAdmin.php');
        ?>
        <div id="del-info">
            <h3>Confirmez: Supprimer <?= html_entity_decode(htmlspecialchars($title)) ?> ?</h3>
            <br>
            <form method="post">
                <button type="submit" value="Delete" name="delete">Supprimer</button>
            </form>
            <br>
            <a href="blogAdmin.php">Cancel</a>
        </div>
 
  </body>
</html>