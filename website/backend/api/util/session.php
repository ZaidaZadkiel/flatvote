<?php
function startSession($time = 3600, $ses = 'MYSES') {
    session_set_cookie_params($time);
    session_name($ses);
    session_start();

    // Reset the expiration time upon page load
    if (isset($_COOKIE[$ses]))
      setcookie($ses, $_COOKIE[$ses], time() + $time, "/");
}

echo "lol"

?>
