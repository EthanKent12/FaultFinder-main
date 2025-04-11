<?php
header('Content-Type: application/json');

// Fake bot data
$bots = [
    [ "id" => 1, "name" => "Pirate Bot", "tests" => 30, "status" => "Success" ],
    [ "id" => 2, "name" => "Tim Bot", "tests" => 10, "status" => "Fail" ],
    [ "id" => 3, "name" => "Phillip Bot", "tests" => 12, "status" => "Success" ],
    [ "id" => 4, "name" => "BluePeak IT AI", "tests" => 22, "status" => "Success" ],
    [ "id" => 5, "name" => "AI Assistant", "tests" => 25, "status" => "Fail" ],
    [ "id" => 6, "name" => "Nate Bot", "tests" => 5, "status" => "Fail" ]
];

// Convert to JSON and send response
echo json_encode($bots);
?>
