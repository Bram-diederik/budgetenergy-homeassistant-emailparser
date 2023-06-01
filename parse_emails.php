<?php
// Verbindingsinformatie
include("/opt/budgetenergie/common.php");
//end config

if ($tvg) {
 $nTvg = '4';
} else {
 $nTvg = '2';
}

// Verbinding maken met de IMAP-server
$mailbox = imap_open($server, $username, $password);

$bNorm = false;
$bDal = false;
$bGas = false;

if ($mailbox) {
    // Verbinding succesvol
    // Voer hier je verwerkingslogica uit
#	$criteria = 'UNSEEN SUBJECT "Uw variabele tarieven"';
	$criteria = 'UNSEEN';
    $emails = imap_search($mailbox, $criteria);

    if ($emails) {
		foreach ($emails as $email) {
			// E-mailgegevens verwerken
			$header = imap_headerinfo($mailbox, $email);
			$subject = $header->subject;
			$fromAddress = $header->fromaddress;
			if (strpos($fromAddress,$from_email)) {

				$body = imap_fetchbody($mailbox, $email, 1);
				$body = html_entity_decode($body);
				$body = strip_tags($body);


#$body = file_get_contents("mail.txt");
				$patternStartDatum = '#tarieven voor\s+(.*?\s\d{4})#';
				$patternNormaaltarief = '#Totaal normaaltarief\s+(.?|=E2=82=AC) (\d+,\d+)\s+(.?|=E2=82=AC) (\d+,\d+)#';
				$patternDaltarief = '#Totaal daltarief\s+(.?|=E2=82=AC) (\d+,\d+)\s+(.?|=E2=82=AC) (\d+,\d+)#';
				$patternGastarief = '#Totaal\s+(.?|=E2=82=AC) (\d+,\d+)\s+(.?|=E2=82=AC) (\d+,\d+)#';

				// Match the prices using regular expressions
				if (preg_match($patternStartDatum, $body, $matchesStartDatum)) {
						$StartDatum =   str_replace(' ', '',$matchesStartDatum[1]);
						echo "Start Datum: " . $StartDatum . "\n";
				}

				if (preg_match($patternNormaaltarief, $body, $matchesNormaaltarief)) {
						$bNorm = true;
						$normaaltariefPrice =   str_replace(',', '.',$matchesNormaaltarief[$nTvg]);
						echo "Stroom normaaltarief: " . $normaaltariefPrice . "\n";
				}

				if (preg_match($patternDaltarief, $body, $matchesDaltarief)) {
						$bDal = true;
						$daltariefPrice =  str_replace(',', '.',$matchesDaltarief[$nTvg]);
						echo "Stroom daltarief: " . $daltariefPrice . "\n";
				}

				if (preg_match($patternGastarief, $body, $matchesGastarief)) {
						$bGas = true;
						$gastariefPrice =   str_replace(',', '.',$matchesGastarief[$nTvg]);
						echo "Gas tarief: " . $gastariefPrice ." \n";

				}
			}
			if ($bNorm && $bDal && $bGas) {
				file_put_contents($value_folder ."$StartDatum-gastarief", $gastariefPrice);
				file_put_contents($value_folder ."$StartDatum-Stroom_dalltarief", $daltariefPrice);
				file_put_contents($value_folder ."$StartDatum-Stroom_normaaltarief", $normaaltariefPrice);
				// Markeren als gelezen
				imap_setflag_full($mailbox, $email, "\\Seen");

			}
		}
	}

    // Verbinding sluiten
    imap_close($mailbox);



	} else {
    // Verbinding mislukt
    echo "Kon geen verbinding maken met de IMAP-server.";
}
?>
