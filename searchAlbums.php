<?php
if(count($_POST) > 0){
    $_POST['search'] = htmlspecialchars(stripslashes(trim($_POST['search'])));

    try{
            $search = $_POST['search'];
            $db = new PDO('mysql:host=localhost;dbname=albumdatabase;charset=utf8mb4', '', '');
            if(count($_POST) < 2)
            $album = $db->prepare("select * from album join artist on album.artist_id = artist.id where 
            title like :search or name like :search or genre like :search");
            else $album = $db->prepare("select * from album join artist on album.artist_id = artist.id
            where ".$_POST['filter']." like :search");
            $album->bindValue(":search", "%$search%");
            $album->execute();
             echo json_encode(array("albums" => $album->fetchAll(PDO::FETCH_ASSOC)));
            
            } catch(Exception $e){
            
                echo $e->getMessage();
            }
    

}
?>