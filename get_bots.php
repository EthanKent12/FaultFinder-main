<?php
// this is a mock api created for the sake of testing the faultfinder project

header("Content-Type: application/json");

// mock bot data
$bots = [
    ["id" => 1, "name" => "Pirate Bot"],
    ["id" => 2, "name" => "Tim Bot"],
    ["id" => 3, "name" => "Phillip Bot"],
    ["id" => 4, "name" => "BluePeak IT AI"],
    ["id" => 5, "name" => "AI Assistant"],
    ["id" => 6, "name" => "Nate Bot"]
];

// return JSON response
echo json_encode($bots);
?>
