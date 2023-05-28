# budgetenergy-homeassistant-emailparser
Email parser to send the monthly budget energy prices into home assistant

edit the php files with the right values.

set the crontab 

0 0   1  *   *    /opt/budgetenergie/prices_set.php > /dev/null 2>&1
0 *   *  *   *    /opt/budgetenergie/parse_emails.php > /dev/null 2>&1


currently not using all values because i dont need them
