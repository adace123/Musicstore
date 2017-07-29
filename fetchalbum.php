<?php
    
            try{
                $db = new PDO("mysql:host=localhost;dbname=albumdatabase", '', '');
                $albums = $db->prepare("select * from album where title = ?");
                $albums->execute(array($_POST['title']));
                $artists = $db->prepare("select * from artist where id = ?");
                $artists->execute(array($_POST['id']));
                $songs = $db->prepare("select * from song where artist = ? and album_name = ? order by track_num");
                $artists = $artists->fetchAll(PDO::FETCH_ASSOC);
                $songs->execute(array( $artists[0]['name'], $_POST['title']));
                $results = array("album" => $albums->fetchAll(PDO::FETCH_ASSOC), "artist" => $artists,
                "songs" => $songs->fetchAll(PDO::FETCH_ASSOC));
                echo json_encode($results);
            
            } catch(Exception $e){
                echo $e->getMessage();
            }
              
?>