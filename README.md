# budgetenergy-homeassistant-emailparser
Email parser to send the monthly budget energy prices into home assistant

edit the php files with the right values.

add the yaml to the home assistant configuration.

set the crontab 
```
0 0   1  *   *    /opt/budgetenergie/prices_set.php > /dev/null 2>&1
0 *   *  *   *    /opt/budgetenergie/parse_emails.php > /dev/null 2>&1
```

currently not using all values because i dont need them

note i personally forward the email from gmail to my ziggo account. that is specially made for this interface.

dont open the email if it points to your main directly 
