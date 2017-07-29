<?php 
session_start();
$db = new PDO('mysql:host=localhost;dbname=albumdatabase;charset=utf8mb4', '', '');

if(isset($_SESSION['username']) && isset($_POST['addalbum'])) {
        addAlbumToFavorites();
}

else if(isset($_SESSION['username']) && (isset($_POST['user']) || isset($_POST['fetch']))) {
    fetchFavorites();
}

else {
    deleteAlbumFromFavorites();
}

function addAlbumToFavorites() {
    global $db;
    try{
        $image = $db->prepare("select image_url from Album where title = ?");
        $image->execute(array($_POST['addalbum']));
        $favorite = $db->prepare("insert into Favorites values (?,?,?,?)");
        $favorite->execute(array($_POST['artist'],$_POST['addalbum'],$_SESSION['username']
        ,str_replace("600x600","100x100",$image->fetch(PDO::FETCH_ASSOC)['image_url'])));}
    catch(Exception $e) {}
    fetchFavorites();
}

function deleteAlbumFromFavorites() {
    global $db;
    try {
        $favorite = $db->prepare("delete from Favorites where username = ? and title = ? and artist = ?");
        $favorite->execute(array($_SESSION['username'], $_POST['deletealbum'], $_POST['artist']));
        fetchFavorites();
    } catch(Exception $e) {}
}

function fetchFavorites() {
    global $db;
    $favorite = $db->prepare("select * from Favorites where username = ? order by artist");
    $favorite->execute(array($_SESSION['username']));
    echo json_encode(array("favorites" => $favorite->fetchAll(PDO::FETCH_ASSOC)));
}

?>