<!------------------------------------------------------
- Show all user's blogs.
-------------------------------------------------------->
<?php
session_start();

require_once './db/dbconfig.php';
$db = new LabDB();

/***************************************************
 *Button Read event
 ****************************************************/
if (isset($_POST['readBtn'])) {
    $_SESSION['blogID'] = $_POST['readBtn'];
    echo "<meta http-equiv='Refresh' content='0;URL=oneBlog.php'>";
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require_once "./css/require.php"; ?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>

<body class="blog-theme">
    <?php 
        require_once './utils/generateHeaderAndNav.php';
        echo generateHeaderAndNav('Bonjour, voici<br>quelques grands moments', 
                                    'Mon Blog',
                                    'blogAdmin.php');
        ?>
    <main class="content">

        <div class="blog-search">
            <a href="readBlog.php" class="draw">Tous les blogs</a>
            <br>
            <form action="readBlog.php" method="post">
                <input placeholder="Rechercher du titre..." id="search" name="search" type="text">
                <input type="submit" value="Confirmer">
            </form>
            <br>
        </div>

        <div class="blog-list">
            <br>
            <?php
            //query for all blogs
            $result = LabDB::select_everyblog($db);

            if ($result != false) {

                echo '<form action="readBlog.php" method="post" id="all-blog">';
                echo '<table class="list-table" >';

                echo '<tr class="list-tr">';
                echo '<th class="list-th" width="100%" colspan="4">Liste de blog</th>';
                echo '</tr>';
                foreach ($result as $row) {
                    echo '<tr class="list-tr">';
                    echo '<td style="text-align:left" width="40%">&nbsp;&nbsp;' . html_entity_decode(htmlspecialchars($row['title'])) . '</td>';
                    echo '<td width="18%" style="font-size:0.8rem">' . $row['mailaddr'] . '</td>';
                    echo '<td width="18%" style="font-size:0.8rem">' . $row['time_update'] . '</td>';
                    echo '<td width="15%">[' . html_entity_decode(htmlspecialchars($row['cgname'])) . ']</td>';
                    echo '<td><button type="submit" name="readBtn" value = "' . $row['id'] . '">Lire</button></td>';
                    echo '</tr>';
                }

                echo '</table>';
                echo '<br>';
                echo '</form>';
            }

            /********************************************************
             *blog list searched by keyword in title
             ********************************************************/
            if (isset($_POST['search'])) {
                $keyword = $_POST['search'];
                //keyword not valid
                if (strlen($keyword) < 1) {
                    echo '<script>document.getElementById("all-blog").style.display="";</script>';
                } else {
                    echo '<script>document.getElementById("all-blog").style.display="none";</script>';
                    $result = LabDB::select_bytitle($db, $keyword);

                    if ($result != false) {
                        // $num = count($result);
                        // $_SESSION['message'] = $num . " blog trouvé";
                        echo '<form action="readBlog.php" method="post">';
                        echo '<table class="list-table">';
                        echo '<tr class="list-tr">';
                        echo '<th class="list-th" width="100%" colspan="3">Liste de blog</th>';
                        echo '</tr>';
                        foreach ($result as $row) {
                            echo '<tr class="list-tr">';
                            echo '<td style="text-align:left" width="40%">&nbsp;&nbsp;' . html_entity_decode(htmlspecialchars($row['title'])) . '</td>';
                            //echo '<td width="18%" style="font-size:0.8rem">'.$row['mailaddr'].'</td>';
                            echo '<td width="40%"style="font-size:0.8rem">' . $row['time_update'] . '</td>';
                            //echo '<td width="15%">['.$row['cgname'].']</td>';
                            echo '<td><button type="submit" name="readBtn" value = "' . $row['id'] . '">Lire</button></td>';
                            echo '</tr>';
                        }

                        echo '</table>';
                        echo '<br>';
                        echo '</form>';
                    } else {
                        // $_SESSION['message'] = "Aucun blog trouvé";
                        echo '<script>document.getElementById("all-blog").style.display="";</script>';
                        echo '<script>swal({
                            text: "Aucun blog trouvé",
                            className: "swal-modal",
                            buttons: {
                                confirm: {
                                    className: "swal-button--confirm"
                                }
                            }
                        });</script>';
                    }
                }
            }
            ?>
        </div>

    </main>

</body>

</html>