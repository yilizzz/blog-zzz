<?php 
    function generateHeaderAndNav($h1Text, $aLink1Text, $aLink1Href, $aLink2Text = null, $aLink2Href = null) {
        $header = '<header class="editor-info"><h1>' . $h1Text . '</h1>&nbsp;&nbsp;&nbsp;';
        if (isset($_SESSION['message'])) {
            $header .= '<p style="color:var(--main-red);">' . htmlentities($_SESSION['message']) . "</p>\n";
            unset($_SESSION["message"]);
        }
        // $header .= '</header>';

        $nav = '<nav class="navbar"><a href="' . $aLink1Href . '" class="draw">' . $aLink1Text . '</a>&nbsp;&nbsp;&nbsp;';
        if ($aLink2Text && $aLink2Href) {
            $nav .= '<a href="' . $aLink2Href . '" class="draw">' . $aLink2Text . '</a>';
        }
        $nav .= '</nav></header>';

        return $header . $nav;
    }
?>