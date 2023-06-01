# budgetenergy-homeassistant-emailparser
Email parser to send the monthly budget energy prices into home assistant

edit the common.php file with the right values and point it to the right location. 

add the yaml to the home assistant configuration.yaml

set the crontab 
```
0 0   1  *   *    /opt/budgetenergie/prices_set.php > /dev/null 2>&1
0 *   *  *   *    /opt/budgetenergie/parse_emails.php > /dev/null 2>&1
```

currently not using all values because i dont need them

note i personally forward the email from gmail to my ziggo account. converting the html email to text.
I dont test the html email content.
