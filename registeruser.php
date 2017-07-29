<?php 
session_start();
if(count($_POST)>0) {
    filterPost();
    $db = new PDO('mysql:host=localhost;dbname=albumdatabase;charset=utf8mb4', '', '');
    insertUser();
}

function newUser(){
    global $db;
    $user = $db->prepare("select * from users where email = ? or username = ?");
    $user->execute(array($_POST['email'],$_POST['username']));
    return count($user->fetchAll(PDO::FETCH_ASSOC)) == 0;
}

function filterPost(){
    foreach($_POST as $field => $value){
        $value = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
        $value = filter_input(INPUT_POST, $field, FILTER_SANITIZE_SPECIAL_CHARS);
        $value = filter_input(INPUT_POST, $field, FILTER_SANITIZE_ENCODED);
    }
}

function insertUser(){
    global $db;
    if(newUser()){
        $stmt = $db->prepare("insert into users (username,email,pass) values (?,?,?)");
        $stmt->execute(array($_POST['username'],$_POST['email'],password_hash($_POST['password'],PASSWORD_DEFAULT)));
        echo "User successfully registered.";
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $_POST['username'];
    } else {
        echo "Error. User already registered.";
        return;
    }
}

?>