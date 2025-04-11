<?php
require 'db.php';

$stmt = $pdo->query("SELECT failure_log.*, testing_log.Details AS TestDetails
                    FROM failure_log
                    JOIN testing_log ON failure_log.TestLogID = testing_log.TestLogID");

$failures = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($failures);
?>