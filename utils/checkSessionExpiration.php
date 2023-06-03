<?php
    $lifetime = 24*60*60;
    function checkSessionExpiration() {
        global $lifetime;
        session_set_cookie_params($lifetime);
        session_start();

        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $lifetime)) {
            // last request was more than one day ago
            session_unset();     // unset $_SESSION variable for the run-time 
            session_destroy();   // destroy session data in storage
            session_start();     // start a new session
            $_SESSION['message'] = 'Votre session a expiré. Veuillez vous reconnecter.'; // set message
            header('Location: index.php'); // redirect to login page
            exit;
        }
        $_SESSION['last_activity'] = time(); // update last activity time stamp
}
?>