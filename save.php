<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","ahmad_db");

if($conn->connect_error){
    echo json_encode(["success"=>false,"msg"=>"DB connection failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$ref = $conn->real_escape_string($data['ref']);
$date = $conn->real_escape_string($data['date']);
$address = $conn->real_escape_string($data['address']);
$phone = $conn->real_escape_string($data['phone']);
$email = $conn->real_escape_string($data['email']);
$subject = $conn->real_escape_string($data['subject']);

$item = $data['items'][0];

$name = $conn->real_escape_string($item['name']);
$width = intval($item['width']);
$height = intval($item['height']);
$qty = intval($item['qty']);
$price = floatval($item['price']);
$amount = floatval($item['amount']);

$sql = "INSERT INTO quotations
(ref, quot_date, address, phone, email, subject,
item_name, item_width, item_height, qty, price, total_amount)
VALUES
('$ref','$date','$address','$phone','$email','$subject',
'$name','$width','$height','$qty','$price','$amount')";

if($conn->query($sql)){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false,"msg"=>$conn->error]);
}