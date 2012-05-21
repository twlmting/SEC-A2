<?php 
require("functions.php");
session_start();
$username = $_SESSION['username'];
createKeyPair($username);

echo "<a href='main.php'><input type='button' value='Back' name='back'></input></a>";
?>