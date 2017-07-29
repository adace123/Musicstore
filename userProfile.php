<?php
if(isset($_POST['username'])){
    $db = new PDO('mysql:host=localhost;dbname=albumdatabase;charset=utf8mb4', '', '');
    getUserData();
}

function getUserData(){
    global $db;
    $user =  $db->prepare("select * from Users where username = ?");
    $user->execute(array($_POST['username']));
    $results = array("userInfo" => $user->fetchAll(PDO::FETCH_ASSOC));
    echo json_encode($results,true);
}

?>