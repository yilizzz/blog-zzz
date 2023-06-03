<?php
include 'utils/checkSessionExpiration.php';
checkSessionExpiration();


include('db/dbConfig.php');
$db = new LabDB();
// Check the account
if (!isset($_SESSION['userID'])) {
    die("ACCESS DENIED");
} else {
    $userID = $_SESSION['userID'];
}


if (isset($_POST['content']) && isset($_POST['post-title']) && isset($_POST['cgID'])) {

    $title = $_POST['post-title'];
    $body = $_POST['content'];
    $cg = $_POST['cgID'];

    $result = LabDB::insert($db, 'tb_post', array('body' => $body, 'user' => $userID, 'title' => $title, 'category' => $cg));
    if ($result) {
        $_SESSION['message'] = "Le blog est enregistré";
        echo "<meta http-equiv='Refresh' content='0;URL=blogAdmin.php'>";
        return;
    }
}



?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php require_once "css/require.php"; ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" 
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous">
    </script>
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    

    <script>
        $(document).ready(function() {
            $("#post-title").click(function() {
                $("#post-title").val("");
            });
        });
        // Data validation
        window.onload = function() {
            document.getElementById("content-form").onsubmit = function() {
                var title = document.getElementById("post-title").value.trim();
                var category = document.getElementById("cgID").value;
                if (title == '' || category == 0) {

                    document.getElementById('alert').textContent = 'Le titre et la catégorie doivent être indiqués';
                    var form = document.getElementById('content-form');
                    console.log(form);
                    $("#form").attr("target", "iframe");
                    return false;
                }
                document.getElementById('alert').textContent = '';
                return true;
            };
        };
    </script>
    

</head>

<body class="blog-theme">

    <?php 
    include'utils/generateHeaderAndNav.php';
    echo generateHeaderAndNav('LabZZZ Blog', 'Mon Blog', 'blogAdmin.php', 'Logout', 'logOut.php');
    ?>

    <main class="content">
        <form id="content-form" action="blogAdd.php" method="post">
            <input type="text" name="post-title" id="post-title" value="Mon Titre">
            <br>
            <textarea id="summernote" name="editordata"></textarea><br>
            <input name="content" id="content" type="hidden">

            <?php
            /*********************************************************
             *a dropdown select for choose a categoy
             *********************************************************/
            //query for the first class categories
            $result = LabDB::select($db, 'tb_category', ['id', 'cgname'], 'user_id = "' . $userID . '" AND parent IS NULL');

            echo '<select name = "cgID" id="cgID">';
            echo '<option value="0" label="Choissir une catégorie" selected="selected"></option>';
            if ($result != false) {

                foreach ($result as $row) {
                    echo '<option value="' . $row['id'] . '">' . $row['cgname'] . '</option>';
                    //query for the sub categories
                    $sub_result = LabDB::select($db, 'tb_category', ['id', 'cgname'], 'user_id = "' . $userID . '" AND parent = "' . $row['id'] . '"');
                    foreach ($sub_result as $sub_row) {
                        echo '<option value="' . $sub_row['id'] . '">----' . $sub_row['cgname'] . '</option>';
                    }
                }
            }
            echo '</select>';
            ?>

            <span id="alert" class="error"></span>
            <button type="submit" id="btnSave">Enregistrer</button>
            <a href="blogAdmin.php">Cancel</a>
        </form>
        <iframe id="iframe" name="iframe" style="display:none;"></iframe>
        <script>
            /**
             * summernote setup
             * */
            $(document).ready(function() {

                $('#summernote').summernote({
                    height: '500',
                    width: '80%',
                    focus: true,
                    tabsize: 2,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['height', ['height']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],

                    callbacks: {
                        onImageUpload: function(files) {
                            editor = $(this);
                            sendFile(files[0], editor);
                        }
                    }
                });

                //upload a picture
                function sendFile(file, editor) {
                    var formData = new FormData();
                    formData.append("file", file);
                    $.ajax({
                        url: "uploadImg.php",
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        type: 'POST',
                        success: function(url) {
                            var image = $('<img width="400">').attr('src', url);
                            $(editor).summernote("insertNode", image[0]);

                        },
                        error: function() {
                            alert("Erreur lors du tranfert de l'image");
                        }
                    });
                }

            });


            $('#btnSave').click(function() {
                //assign the form to a hidden input
                var content = $('#summernote').summernote('code');

                $("#content").attr("value", content);
                //$('#content-form').submit();

            });
        </script>
    </main>

</body>

</html>