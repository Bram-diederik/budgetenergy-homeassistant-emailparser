#!/usr/bin/php
<?php

$sHomeApiUrl = "https://homeassistant";
$sHomeApiKey = "apikey";

$value_dir = "/opt/budgetenergie/values/";
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

HA_set("input_number.budget_stroom_dal",file_get_contents($value_dir."Stroom_dalltarief"));
HA_set("input_number.budget_stroom_normaal",file_get_contents($value_dir."Stroom_normaaltarief"));
HA_set("input_number.budget_gas",file_get_contents($value_dir."gastarief"));


?>
