<?php
       
        if(count($_POST) > 0){
            filterPost();
            try {
                                  
                 if(!duplicateArtist()){
                    addArtist($_POST['artist']);

                    addAlbum($_POST['artist'],$_POST['title']);
                    
                    echoResults();
                 
                 } else {
                    if(!duplicateAlbum()){
                        addAlbum($_POST['artist'],$_POST['title']);
                        echoResults();
                        return;
                    }
                    else echo "Duplicate album";
                           
                 }
                                  
            } catch(Exception $e){
            
                echo $e->getMessage();
            }
        
        } else{
            echo "503 error";           
        }

    function getDB(){
        try{
            $db = new PDO('mysql:host=localhost;dbname=albumdatabase;charset=utf8mb4', '', '');
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $db;
        }catch(Exception $e){
            echo $e->getMessage();
        }
        
    }

    function duplicateArtist(){
         $db = getDB();
         $stmt = $db->prepare("select * from artist where name = ?");
         $stmt->execute(array($_POST['artist']));
         return $stmt->rowCount() != 0;
    }

    function duplicateAlbum(){
        $db = getDB();
        $stmt = $db->prepare("select * from artist join album on artist.id = album.artist_id where name = ? and title = ?");
        $stmt->execute(array($_POST['artist'],$_POST['title']));
        return $stmt->rowCount() != 0;
    }

    function addArtist($name){
        $db = getDB();
        $stmt = $db->prepare("insert into artist (name) values (?)");
        $stmt->execute(array($name));
    }

    function addAlbum($artist, $title){
        $db = getDB();
        $stmt = $db->prepare("select id from artist where name = ?");
        $stmt->execute(array($artist));
        $artistresult = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['id'];
        $search = json_decode(file_get_contents("https://itunes.apple.com/search?term=".urlencode($_POST['artist'])."+".urlencode($_POST['title'])."&entity=album"),true);
        if(! empty($search['results'])){
        $albumid = $search['results'][0]['collectionId'];
        $image = str_replace("100x100bb","600x600bb",$search['results'][0]['artworkUrl100']);

        $stmt = $db->prepare("insert into album values (?,?,?,?,?,?)");
        $stmt->execute(array($title, $search['results'][0]['primaryGenreName'], substr($search['results'][0]['releaseDate'],0,10),$artistresult,$image,$search['results'][0]['collectionPrice']));
        $songPreviews = json_decode(file_get_contents("https://itunes.apple.com/lookup?id=$albumid&entity=song"),true);
        $songPreviewUrls = array();
        foreach($songPreviews as $values){
            foreach($songPreviews['results'] as $song){
                if(array_key_exists("previewUrl", $song)){
                    try{
                        if($song['kind'] == 'song'){
                    $stmt = $db->prepare("insert into song(song_title,artist,album_name,track_num,audio_url) values (?,?,?,?,?)");
                    $stmt->execute(array($song['trackName'],$artist, $title,$song['trackNumber'],$song['previewUrl']));
                        }
                    } catch(Exception $e){
                        continue;
                    }
                }
            }
                           
        }
        } 
        
    }

    function echoResults(){
        $db = getDB();
        $albums = $db->prepare("select * from album where title = ?");
        $albums->execute(array($_POST['title']));
        $artists = $db->prepare("select * from artist where name = ?");
        $artists->execute(array($_POST['artist']));
        $results = array("albums" => $albums->fetchAll(PDO::FETCH_ASSOC), "artists" => $artists->fetchAll(PDO::FETCH_ASSOC));
        echo json_encode($results);
    }

    function filterPost(){
        foreach($_POST as $field => $value){
                    $value = filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
                    $value = filter_input(INPUT_POST, $field, FILTER_SANITIZE_SPECIAL_CHARS);
                    $value = filter_input(INPUT_POST, $field, FILTER_SANITIZE_ENCODED);
        }
    }
        
?> 