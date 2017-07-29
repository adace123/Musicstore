<?php
session_start();
$db = new PDO('mysql:host=localhost;dbname=albumdatabase;charset=utf8mb4', '', '');
if(isset($_POST['username']) && isset($_POST['password'])){
    filterPost();
    if(verifyUser()){
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $_POST['username'];
        echo "success";
        return;
    } else {
        echo "failure";
        return;
    }
}


function verifyUser(){
   global $db;
   $user = $db->prepare("select * from users where username = ?");
   $user->execute(array($_POST['username']));
   if(password_verify($_POST['password'],$user->fetch(PDO::FETCH_ASSOC)['pass'])) {
       return true;
   } 
    return false;
}

function filterPost(){
    foreach($_POST as $field => $value){
        $value = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
        $value = filter_input(INPUT_POST, $field, FILTER_SANITIZE_SPECIAL_CHARS);
        $value = filter_input(INPUT_POST, $field, FILTER_SANITIZE_ENCODED);
    }
}
?>