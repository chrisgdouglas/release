A quick and dirty digital release form that sends the data to the client and the photographer via email.

Reads in associated photographers from a CSV file (default: users.csv) stored outside of the webroot directory.

Format:
```
foob,foobar@example.com
barf,barfoo@example.com
```

Additionally, the code will expect a "creds.php" file to exist outside of the webroot directory. This PHP file will contain the SMTP information and your customizations for use by the form.

Format:
```
<?php

$smtp_server = "smtp.your.server";
$smtp_username = "your_smtp_username";
$smtp_password = "you_smtp_password";
$sender_name = "Name that should be on outgoing emails";
$subject = "Your organization";
```

Basic error checking is performed, with messaging to the user.

Your hosted server will require PHP Mailer. You will need to configure your SMTP connectivity.
