# Mail sender with PHPMailer

A simple example to send e-mail with PHPMailer

# Get started

Clone Repo:
```
git clone https://github.com/yunusaydinonline/php-mailer.git
```

Go to the folder
```
cd php-mailer
```

Install libraries
```
composer install
```

Create config.php file
```
cat > config.php
```

Config file should be like:
```php
<?php

return array(
    'recaptchaSecret' => <YOUR SECRET KEY FOR GOOGLE CAPTCHA>,
    'language' => 'tr', // tr, en, ru, fr etc. 
    'mailHost' => <MAIL HOST>, 
    'smtpAuth' => <true or false>, // is authentication necessary 
    'smtpSecure' => <SECURITY>, // ssl or tls
    'smtpPort' => <INTEGER>, // SMTP port
    'senderUsername' => <SENDER USERNAME>, // example@yunusaydin.net
    'senderPassword' => <SENDER PASSWORD>,
    'sentFrom' => <SENDER VISIBLE NAME>, // Ex: Yunus Aydın Personal Website
    'receivers' => array( // Receivers
        'example1@yunusaydin.net' => 'Example User 1',
        'example2@yunusaydin.net' => 'Example User 2'
        ...
    ),
    'subject' => <MAIL SUBJECT>
);
```

Then upload files to the host.
