<?php
session_start();
include('../db.php');
if (!isset($_SESSION['user_id'])) header("Location: ../auth/login.php");
$uid = $_SESSION['user_id'];
$pid = $_POST['product_id'];

$stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii", $uid, $pid);
$stmt->execute();
$rs = $stmt->get_result();
if (!$row = $rs->fetch_assoc()) { header("Location:view.php"); exit(); }
$q = $row['quantity'];
$stmt->close();

$conn->begin_transaction();

if ($q > 1) {
    $stmt2 = $conn->prepare("UPDATE cart SET quantity=quantity-1 WHERE user_id=? AND product_id=?");
    $stmt2->bind_param("ii", $uid, $pid);
    $stmt2->execute(); $stmt2->close();
} else {
    $stmt2 = $conn->prepare("DELETE FROM cart WHERE user_id=? AND product_id=?");
    $stmt2->bind_param("ii", $uid, $pid);
    $stmt2->execute(); $stmt2->close();
}

$stmt3 = $conn->prepare("UPDATE products SET stock = stock + 1 WHERE id=?");
$stmt3->bind_param("i", $pid);
$stmt3->execute(); $stmt3->close();

$conn->commit();
$conn->close();

header("Location:view.php");
exit();
