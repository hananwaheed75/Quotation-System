<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost","root","","ahmad_db");

$id = intval($_GET['id']);

$result = $conn->query("SELECT * FROM quotations WHERE id=$id");

$row = $result->fetch_assoc();

$row['items'] = [[
    "name"=>$row['item_name'],
    "width"=>$row['item_width'],
    "height"=>$row['item_height'],
    "qty"=>$row['qty'],
    "price"=>$row['price'],
    "amount"=>$row['total_amount']
]];

echo json_encode($row);