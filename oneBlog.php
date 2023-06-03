<?php
include('db/dbConfig.php');
session_start();
$db = new LabDB();
// Check the blogID
if ( !isset($_SESSION['blogID']) ) {
    die("Missing blogID");
}
else{
    $blogID = $_SESSION['blogID'];
}
/*--------------------
Deal with the comments
---------------------*/

if(isset($_POST['comment'])){ 
    // Data validation
    if ( strlen(trim($_POST['comment'])) < 1 ) {
        header("Location: oneBlog.php");
        return;
    }
    //show the user's email address in the comment
    if(isset($_SESSION['userID'])){
        $user = LabDB::find($db, 'tb_user', ['mailaddr'], 'id = "'.$_SESSION['userID'].'"');
        if($user){
        $addComment = LabDB::insert($db, 'tb_comment', array('post_id'=>$blogID, 'email'=>$user['mailaddr'], 'comment'=>$_POST['comment']));
            
        if($addComment){
            header("Location: oneBlog.php");
            return;
        }
    }
    // Or show default name "Internaute"
    }else{
        $addComment = LabDB::insert($db, 'tb_comment', array('post_id'=>$blogID, 'comment'=>$_POST['comment']));
        if($addComment){
            header("Location: oneBlog.php");
            return;
        }
    }
}

$result = LabDB::find($db, 'tb_post', ['title', 'body'], 'id = "'.$blogID.'"');
$comment = LabDB::select($db, 'tb_comment', ['time_insert', 'email','comment'], 'post_id = "'.$blogID.'"' );

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once "css/require.php"; ?>
</head>

<body class = "blog-theme">
    
    <?php 
        include'utils/generateHeaderAndNav.php';
        echo generateHeaderAndNav('Blog LabZZZ', 
                                    'Lire Plus', 
                                    'readBlog.php',
                                    'Mon Blog',
                                    'blogAdmin.php');
        ?>
    <main class= "blog-content">
        <?php
        if($result){ ?> 
            <h2>
                <?php echo htmlentities($result['title']); ?>
            </h2>
            <br>
            <div class = "blog-list">
                <?php echo $result['body']; ?>
            </div>  
            
        <?php } 
        ?>
        <br>
        <div id = "blog-comment">
            <?php
            /*--------------------
            Show the comments of a certain blog
            ---------------------*/
            if($comment != false){

                echo '<table class="list-table" style="font-size: 14px; ">';
                    
                    echo'<tr class="list-tr">';
                    echo '<th class="list-th" width="100%">Commentaires</th>';
                    echo '</tr>';
                    foreach($comment as $row){
                        echo '<tr class="list-tr">';
                            echo '<td width="30%" style="text-align:left; color:var(--main-green);">'.$row['email'];
                            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row['time_insert'].'</td>';
                        echo '</tr>';
                        echo '<tr class="list-tr" style="color:var(--main-blue)";>';
                            echo '<td style="text-align:left" width="100%">&nbsp;&nbsp;'.htmlentities($row['comment']).'</td>';
                        echo '</tr>';
                        
                    }
                
                echo '</table>';
                echo '<br>';   
            }
            ?>
            <form action="oneBlog.php" method="post">
                <textarea name="comment" id="comment" value="Laissez quelques mots..." class="auto-input" name="comment"></textarea>
                <button type="submit" id="btnSave">Commenter</button>
            </form>
            
        </div>
        </main>
  
</body>
</html>
