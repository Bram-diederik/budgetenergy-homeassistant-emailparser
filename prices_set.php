#!/usr/bin/php
<?php
include("/opt/budgetenergie/common.php");

function HA_set($sSensorName,$sValue) {

global $sHomeApiUrl;
global $sHomeApiKey;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/".$sSensorName);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey,
    "Content-Type: application/json"
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

$stateObj = json_decode($result, true); // Decode the retrieved state object into an associative array
$stateObj["state"] = $sValue; // Modify the state value in the retrieved object

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $sHomeApiUrl."/api/states/".$sSensorName);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer ".$sHomeApiKey,
    "Content-Type: application/json"
));
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Use HTTP PUT method
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stateObj)); // Send the modified state object
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

echo "$result \nSensor ".$sSensorName." set to ".$sValue.".\n";
}

// Nederlandse maandnamen
$maandnamen = [
    1 => 'januari',
    2 => 'februari',
    3 => 'maart',
    4 => 'april',
    5 => 'mei',
    6 => 'juni',
    7 => 'juli',
    8 => 'augustus',
    9 => 'september',
    10 => 'oktober',
    11 => 'november',
    12 => 'december'
];

// Huidige maand en jaar ophalen
$huidigeMaand = date('n');
$huidigJaar = date('Y');

// Nederlandse maandnaam bepalen
$nederlandseMaand = $maandnamen[$huidigeMaand];

// Maand en jaar aan elkaar schrijven
$datum = $nederlandseMaand . $huidigJaar;


HA_set("input_number.budget_stroom_dal",file_get_contents($value_folder."$datum-Stroom_dalltarief"));
HA_set("input_number.budget_stroom_normaal",file_get_contents($value_folder."$datum-Stroom_normaaltarief"));
HA_set("input_number.budget_gas",file_get_contents($value_folder."$datum-gastarief"));


?>
