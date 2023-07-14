<?php
require_once './utils/checkSessionExpiration.php';
checkSessionExpiration();
require_once './db/dbconfig.php';

$db = new LabDB();

//check if it's loged in  
if (!isset($_SESSION['userID'])) {
    $_SESSION['message'] = "Veuillez vous connecter";
    // header("Location:blogLogin.php");  
    header("Location:index.php");
    exit();
}
$userID = $_SESSION['userID'];

?>
<!--------------------------------
- Show user's blogs, search by category, add a blog, manage categories 
---------------------------------->
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require_once "./css/require.php"; ?>
</head>

<body class="blog-theme">
    <?php 
    require_once './utils/generateHeaderAndNav.php';
    echo generateHeaderAndNav('Bonjour, '.html_entity_decode(htmlspecialchars($_SESSION['userName'])), 
                                'Lire Plus', 
                                'readBlog.php', 
                                'Logout', 
                                'logOut.php');
    ?>
    <main class="content">
        <div class="blog-search">

        <?php
        /*********************************************************
         *a dropdown select for choose a categoy
        *********************************************************/
        //query for the first class categories

        $result = LabDB::select($db, 'tb_category', ['id', 'cgname'], 'user_id = "' . $userID . '" AND parent IS NULL');

        if ($result != false) {
            echo '<form action="blogAdmin.php" method="post">';
            echo '<select class="cgID" name = "cgID">';
            echo '<option value="-1" label="Editer mon blog par mes catégories" selected="selected"></option>';
            echo '<option value="0" style="font-weight :bold;" label="Toutes mes catégories"></option>';
            foreach ($result as $row) {
                echo '<option value="' . $row['id'] . '">' . html_entity_decode(htmlspecialchars($row['cgname'])) . '</option>';
                //query for the sub categories
                $sub_result = LabDB::select($db, 'tb_category', ['id', 'cgname'], 'user_id = "' . $userID . '" AND parent = "' . $row['id'] . '"');
                if ($sub_result != false) {
                    foreach ($sub_result as $sub_row) {
                        echo '<option value="' . $sub_row['id'] . '">----' . html_entity_decode(htmlspecialchars($sub_row['cgname'])) . '</option>';
                    }
                }
            }

            echo '</select>';
            echo '<br>';
            echo '<input type="submit" name="submit" value = "Confirmer">';

            echo '</form>';
        }

        ?>
        <br>
        <h3 style="color:var(--main-blue);">

            <a href="blogAdd.php" style="color:var(--main-blue);">✍🏻Ecrire</a><br>
            <a href="cateEdit.php" style="color:var(--main-blue);">🔗Gérer mes catégories</a>
        </h3>
        </div>
        <div class="blog-list">
            <?php
            /*************************
             *user's blog total list
             **************************/

            $result = LabDB::select_allblog($db, $userID);

            if ($result != false) {
                echo '<form id="all-blog" action="blogAdmin.php" method="post">';
                echo '<table class="list-table">';

                echo '<tr class="list-tr">';
                echo '<th class="list-th" width="100%" colspan="4">Liste de blog</th>';
                echo '</tr>';
                foreach ($result as $row) {
                    echo '<tr class="list-tr">';
                    echo '<td style="text-align:left" value = "' . $row['id'] . '" width="35%">&nbsp;&nbsp;' . html_entity_decode(htmlspecialchars($row['title'])) . '</td>';
                    echo '<td width="18%" style="font-size:0.8rem">' . $row['time_update'] . '</td>';
                    echo '<td width="10%">[' . html_entity_decode(htmlspecialchars($row['cgname'])) . ']</td>';
                    echo '<td width="20%"><button type="submit" name="editBtn" value = "' . $row['id'] . '">Editer</button>
                                          <button type="submit" name="dlteBtn" value = "' . $row['id'] . '">Supprimer</button></td>';
                    echo '</tr>';
                }

                echo '</table>';
                echo '<br>';
                echo '</form>';
            }


            /*************************
             *button editer event
             **************************/
            if (isset($_POST['editBtn'])) {

                $_SESSION['blogID'] =  $_POST['editBtn'];
                echo "<meta http-equiv='Refresh' content='0;URL=blogEdit.php'>";
            }
            /****************************
             *button supprimer event 
             ****************************/
            if (isset($_POST['dlteBtn'])) {
                $blogID = $_POST['dlteBtn'];
                $_SESSION['blogID'] = $blogID;
                echo "<meta http-equiv='Refresh' content='0;URL=blogDelete.php'>";
            }

            /********************************************************
             *user's blog list searched by category, showed after a category is chosed
             ********************************************************/
            if (isset($_POST['cgID'])) {
                $category = $_POST['cgID'];
                if ($category <= 0) {
                    echo '<script>document.getElementById("all-blog").style.display="";</script>';
                } else {
                    echo '<script>document.getElementById("all-blog").style.display="none";</script>';
                    $result = LabDB::select($db, 'tb_post', ['id', 'title', 'time_update'], 'category = "' . $category . '"');

                    if ($result != false) {
                        echo '<form id="cg-blog" action="blogAdmin.php" method="post">';
                        echo '<table class="list-table">';
                        echo '<tr class="list-tr">';
                        echo '<th class="list-th" width="100%" colspan="3">Liste de blog</th>';
                        echo '</tr>';
                        foreach ($result as $row) {
                            echo '<tr class="list-tr">';
                            echo '<td name = "title"  style="text-align:left" value = "' . $row['id'] . '" width="40%">&nbsp;&nbsp;' . html_entity_decode(htmlspecialchars($row['title'])) . '</td>';
                            echo '<td name = "time" width="20%" style="font-size:0.8rem">' . $row['time_update'] . '</td>';
                            echo '<td><button type="submit" name="editBtn" value = "' . $row['id'] . '">Editer</button>
                                                <button type="submit" name="dlteBtn" value = "' . $row['id'] . '">Supprimer</button>
                                                </td>';
                            echo '</tr>';
                        }

                        echo '</table>';
                        echo '<br>';
                        echo '</form>';
                    }
                }
            }
            ?>
        </div>

        
    </main>
</body>

</html>