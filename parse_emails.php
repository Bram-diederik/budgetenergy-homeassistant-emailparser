<?php
// Verbindingsinformatie
$server = "{imap.ziggo.nl:993/imap/ssl}INBOX";
$username = "yourhomeassistant@ziggo.nl";
$password = 'secretpassword';
$value_folder = "/opt/budgetenergie/values/";

$from_email = "noreply@budgetenergie.nl";

//TVG ja of nee
$tvg = true;

//end config

if ($tvg) {
 $nTvg = '2';
} else {
 $nTvg = '1';
}

// Verbinding maken met de IMAP-server
$mailbox = imap_open($server, $username, $password);


$bNorm = false;
$bDal = false;
$bGas = false;

if ($mailbox) {
    // Verbinding succesvol
    // Voer hier je verwerkingslogica uit
    $emails = imap_search($mailbox, 'UNSEEN');

    if ($emails) {
      foreach ($emails as $email) {
        // E-mailgegevens verwerken
        $header = imap_headerinfo($mailbox, $email);
        $subject = $header->subject;
        $fromAddress = $header->fromaddress;
        if (strpos($fromAddress,$from_email)) {

        $body = imap_body($mailbox, $email);
        $patternNormaaltarief = '/Totaal normaaltarief =E2=82=AC ([0-9,]+) =E2=82=AC ([0-9,]+)/';
        $patternDaltarief = '/Totaal daltarief =E2=82=AC ([0-9,]+) =E2=82=AC ([0-9,]+)/';
        $patternGastarief = '/Totaal =E2=82=AC ([0-9,]+) =E2=82=AC ([0-9,]+)/';
        // Match the prices using regular expressions
        if (preg_match($patternNormaaltarief, $body, $matchesNormaaltarief)) {
                $bNorm = true;
                $normaaltariefPrice =   str_replace(',', '.',$matchesNormaaltarief[$nTvg]);
                echo "Stroom normaaltarief:" . $normaaltariefPrice . "\n";
        }

        if (preg_match($patternDaltarief, $body, $matchesDaltarief)) {
                $bDal = true;
                $daltariefPrice =  str_replace(',', '.',$matchesDaltarief[$nTvg]);
                echo "Stroom daltarief:" . $daltariefPrice . "\n";
        }

        if (preg_match($patternGastarief, $body, $matchesGastarief)) {
                $bGas = true;
                $gastariefPrice =   str_replace(',', '.',$matchesGastarief[$nTvg]);
                echo "Gas tarief:" . $gastariefPrice ." \n";

       }

      }
    }
   }
   if ($bNorm && $bDal && $bGas) {
        file_put_contents($value_folder ."gastarief", $gastariefPrice);
        file_put_contents($value_folder ."Stroom_dalltarief", $daltariefPrice);
        file_put_contents($value_folder ."Stroom_normaaltarief", $normaaltariefPrice);
        // Markeren als gelezen
        imap_setflag_full($mailbox, $email, "\\Seen");
 
    }
    // Verbinding sluiten
    imap_close($mailbox);



} else {
    // Verbinding mislukt
    echo "Kon geen verbinding maken met de IMAP-server.";
}
?>
