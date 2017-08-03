<?php
require 'php-mailjet-v3-simple.class.php';

session_start();
ini_set("smtp_port",25);
$db = new PDO('mysql:host=localhost;dbname=albumdatabase;charset=utf8mb4', '', '');

if(isset($_POST['addalbum'])) {
    addAlbumToCart();
}

else if(isset($_POST['deletealbum'])) {
    deleteAlbumFromCart();
} 

else if(isset($_POST['fetch'])) {
    fetchOrders();
} 

else if(isset($_POST['add']) || isset($_POST['minus'])) {
    updateQuantity();
}

else if(isset($_POST['updatePriceQuantity'])) {
    updateQuantity();
}

else if(isset($_POST['ordercomplete'])) {
    addToOrderHistory();
    sendConfirmation();
}

else if(isset($_POST['updateFormat'])) {
    updateFormat();
}

else if(isset($_POST['orderhistory'])) {
    fetchOrderHistory();
}

else {
    deleteAlbumFromCart();
}


function addAlbumToCart() {
    global $db;
    try {
        $image = $db->prepare("select image_url from Album where title = ?");
        $image->execute(array($_POST['addalbum']));
        $albumPrice = $db->prepare("select price from Album join Artist on Album.artist_id = Artist.id where name = ? and title = ?");
        $albumPrice->execute(array($_POST['artist'],$_POST['addalbum']));
        $orders = $db->prepare("insert into Orders values (?,?,?,1,'digital',?,?)");
        $orders->execute(array($_POST['artist'],$_POST['addalbum'],$_SESSION['username'],$albumPrice->fetchColumn(),
        str_replace("600x600","100x100",$image->fetch(PDO::FETCH_ASSOC)['image_url'])));
        fetchOrders();
    } catch(Exception $e) {}
}

function deleteAlbumFromCart() {
    global $db;
    try{
        $order = $db->prepare("delete from Orders where artist = ? and title = ? and username = ?");
        $order->execute(array($_POST['artist'],$_POST['deletealbum'],$_SESSION['username']));
        fetchOrders();
    } catch(Exception $e) {}
}

function fetchOrders() {
    global $db;
    $orders = $db->prepare("select * from Orders where username = ?");
    $orders->execute(array($_SESSION['username']));
    getOrderCount();
    echo json_encode(array("orders" => $orders->fetchAll(PDO::FETCH_ASSOC),"orderCount" => getOrderCount()));
}

function getOrderCount() {
    global $db;
    $count = $db->query("select count(*) from Orders")->fetchColumn();
    return $count;
}

function updateQuantity() {
    global $db;
    $update = $db->prepare("update Orders set price = ? where username = ? and title = ? and artist = ?");
    $update->execute(array($_POST['price'],$_SESSION['username'],$_POST['updatePriceQuantity']['title'],$_POST['updatePriceQuantity']['artist']));
    $update = $db->prepare("update Orders set quantity = ? where username = ? and title = ? and artist = ?");
    $update->execute(array($_POST['quantity'],$_SESSION['username'],$_POST['updatePriceQuantity']['title'],$_POST['updatePriceQuantity']['artist']));
}

function updateFormat() {
    global $db;
    $update = $db->prepare("update Orders set format = ? where username = ? and title = ? and artist = ?");
    $update->execute(array($_POST['updateFormat']['format'],$_SESSION['username'],$_POST['updateFormat']['title'],$_POST['updateFormat']['artist']));
}

function addToOrderHistory() {
    global $db;
    $orders = $db->prepare("select * from Orders where username = ?");
    $orders->execute(array($_SESSION['username']));

}

function sendConfirmation() {
    global $db;
    print_r($_POST['orders']);
    print_r($_POST['total']);
    $email = $db->prepare("select email from Users where username = ?");
    $email->execute(array($_SESSION['username']));
    $email =  $email->fetch(PDO::FETCH_ASSOC)['email'];
    $message = "";
    foreach($_POST['orders'] as $index => $item) {
        foreach($item as $field => $value) {
            if($field != "thumbnail" && $field != "username")
            $message .= ucfirst($field).": ". $value."<br>";
            else if($field == "thumbnail") $message .= "<img src='$value'>";
        }
            $message .= "<br><br>";
    }
    foreach($_POST['total'] as $field => $value) {
        $message .= ucfirst($field).": ".$value."<br>";
    }
    $message.= "<br><br>We appreciate your business!";
    $mailer = new MailJet("#############","################");
    $params = array("method" => "POST","from" => "rexforddrive@gmail.com","to" => $email
    ,"subject" => "Thanks so much for your order! Here are your order details:",
    "html" => $message);
    $result = $mailer->sendEmail($params);

    if ($mailer->_response_code == 200)
       echo "success - email sent";
    else
       echo "error - ".$mailer->_response_code;
    try{
        $orderhistory = $db->prepare("insert into OrderHistory select * from Orders where username = ?");
        $orderhistory->execute(array($_SESSION['username']));
        $clearOrders = $db->prepare("delete from Orders where username = ?");
        $clearOrders->execute(array($_SESSION['username']));
    } catch(Exception $e) {}
}

function fetchOrderHistory() {
    global $db;
    $history = $db->prepare("select * from OrderHistory where username = ?");
    $history->execute(array($_SESSION['username']));
    echo json_encode(array("history" => $history->fetchAll(PDO::FETCH_ASSOC)));
}
?>
