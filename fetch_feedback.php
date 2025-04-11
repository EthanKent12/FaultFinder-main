<?php
require 'db.php';

$stmt = $pdo->query("SELECT user_input.*, ai_interaction_log.InteractionDetails
                    FROM user_input
                    JOIN ai_interaction_log ON user_input.LogID = ai_interaction_log.LogID");

$feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($feedback);
?>