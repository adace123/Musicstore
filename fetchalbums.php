<?php
            
        try{
         $db = new PDO("mysql:host=localhost;dbname=albumdatabase", '', '');
         $albums = $db->query("select * from album");
         $artists = $db->query("select * from artist");
         $results = array("albums" => $albums->fetchAll(PDO::FETCH_ASSOC), "artists" => $artists->fetchAll(PDO::FETCH_ASSOC));
         echo json_encode($results);
         
        } catch(Exception $e){
           
            echo $e->getMessage();
        }
      
?>