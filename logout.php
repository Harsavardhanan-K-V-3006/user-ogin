<?php

$conn = mysqli_connect('localhost', 'root', '', 'userdb1');

session_start();
session_unset();
session_destroy();

header('location:login_form.html');

?>