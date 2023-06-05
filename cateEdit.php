<?php
require_once './utils/checkSessionExpiration.php';
checkSessionExpiration();

require_once './db/dbconfig.php';
$db = new LabDB();

// Guardian: Make sure that user_id is present
if (!isset($_SESSION['userID'])) {
    die("ACCESS DENIED");
} else {
    $userID = $_SESSION['userID'];
}


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require_once "./css/require.php"; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" 
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous">
    </script>
    <script>
        /*------------------------------------------------------
        - Delete category event 
        --------------------------------------------------------*/
        $(document).ready(function() {
            $(document).on("click", ".delete", function() {

                var flag = confirm("Supprimer tous les articles de cette catégorie et de sous-catégories ?");

                if (flag) {
                    $.post("deleteCG.php", {
                        cgID: $(this).val()

                    });
                }
                window.location.reload();

            });
            /*------------------------------------------------------
            - Edit category event 
            --------------------------------------------------------*/
            $(document).on("click", ".edit", function() {
                var newCate = window.prompt("Le nom de la catégorie", "");
                if (newCate) {
                    $.post("updateCG.php", {
                        cgName: newCate,
                        cgID: $(this).val()

                    });

                }
                window.location.reload();
            });
        });
    </script>
</head>

<body class="blog-theme">
    <?php 
        require_once './utils/generateHeaderAndNav.php';
        echo generateHeaderAndNav('Mes Catégories', 
                                    'Ajoutez une nouvelle category', 
                                    'cateAdd.php',
                                    'Mon Blog', 
                                    'blogAdmin.php');
        ?>

    <main class="blog-content">
        <div class="blog-list">
            <form method="post">

                <?php
                /*********************************************************
                 *a list of user's categoy
                 *********************************************************/
                //query for the first class categories
                $result = LabDB::select($db, 'tb_category', ['id', 'cgname'], 'user_id = "' . $userID . '" AND parent IS NULL');

                if ($result != false) {
                    echo '<form id="all-blog" action="cateEdit.php" method="post">';
                    echo '<table class="list-table">';
                    echo '<tr class="list-tr">';
                    echo '<th class="list-th" width="100%" colspan="3">Liste Category</th>';
                    echo '</tr>';
                    foreach ($result as $row) {
                        echo '<tr class="list-tr">';
                        echo '<td style="text-align:left" value = "' . $row['id'] . '" >&nbsp;&nbsp;' . html_entity_decode(htmlspecialchars($row['cgname'])) . '</td>';
                        echo '<td><button type="submit" class="edit" name="editBtn" value = "' . $row['id'] . '">Editer</button>
                                          <button type="submit" class="delete" name="dlteBtn" value = "' . $row['id'] . '">Supprimer</button></td>';
                        echo '</tr>';
                        //query for the sub categories
                        $sub_result = LabDB::select($db, 'tb_category', ['id', 'cgname'], 'user_id = "' . $userID . '" AND parent = "' . $row['id'] . '"');
                        if ($sub_result != false) {
                            foreach ($sub_result as $sub_row) {
                                echo '<tr class="list-tr">';
                                echo '<td style="text-align:left" value = "' . $sub_row['id'] . '" >&nbsp;&nbsp;' . '----' . html_entity_decode(htmlspecialchars($sub_row['cgname'])) . '</td>';
                                echo '<td><button type="submit" class="edit" name="editBtn" value = "' . $sub_row['id'] . '">Editer</button>
                                                    <button type="submit" class="delete" name="dlteBtn" value = "' . $sub_row['id'] . '">Supprimer</button>
                                                    </td>';
                                echo '</tr>';
                            }
                        }
                    }
                    echo '</table>';
                }
                ?>
            </form>
        </div>
    </main>
</body>

</html>