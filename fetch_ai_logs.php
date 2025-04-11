<?php
require 'db.php';

$stmt = $pdo->query("SELECT ai_interaction_log.*, product.Name AS ProductName
                    FROM ai_interaction_log
                    JOIN product ON ai_interaction_log.Productid = product.ProductID");

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($logs);
?>