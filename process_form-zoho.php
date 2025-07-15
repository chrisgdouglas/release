<?php

/*

Note that you'll need to replace your_zoho_mail_access_token and your_zoho_mail_from_email with your actual Zoho Mail access token 
and from email address.

Also, keep in mind that this code uses the Zoho Mail API to send emails to specific email addresses. You may need to modify the 
code to fit your specific use case.

You can obtain the access token by following these steps:

Go to the Zoho Mail API documentation and click on the "Get Started" button.
Click on the "Create an App" button and fill in the required information.
Click on the "Create" button to create the app.
Go to the "API Keys" tab and click on the "Generate API Key" button.
Copy the API key and use it in your code.
You can also use the Zoho Mail API to add a contact to a mailing list, send a campaign, and more. You can find more information
about the Zoho Mail API in the documentation.

*/
// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$date = $_POST['date'];
$photo_number = $_POST['photo_number'];
$disclaimer = $_POST['disclaimer'];

// Get email address from data array
$search_param = $_GET['param'];
$email_address = $data[$search_param];

// Zoho Mail API settings
$access_token = 'your_zoho_mail_access_token';
$from_email = 'your_zoho_mail_from_email';
$to_email = $email_address;

// Send email using Zoho Mail API
$url = 'https://www.zoho.com/mail/api/v1/messages';
$headers = array(
    'Authorization: Zoho-oauthtoken ' . $access_token,
    'Content-Type: application/json'
);
$data = array(
    'from' => $from_email,
    'to' => $to_email,
    'bcc' => $from_email,
    'subject' => 'Form Submission',
    'content' => 'Name: ' . $name . '\nEmail: ' . $email . '\nDate: ' . $date . '\nPhoto Number: ' . $photo_number . '\nDisclaimer: ' . $disclaimer
);
$json = json_encode($data);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

// Send email to form submitter using Zoho Mail API
/*
$to_email = $email;
$url = 'https://www.zoho.com/mail/api/v1/messages';
$headers = array(
    'Authorization: Zoho-oauthtoken ' . $access_token,
    'Content-Type: application/json'
);
$data = array(
    'from' => $from_email,
    'to' => $to_email,
    'subject' => 'Form Submission',
    'content' => 'Name: ' . $name . '\nEmail: ' . $email . '\nDate: ' . $date . '\nPhoto Number: ' . $photo_number . '\nDisclaimer: ' . $disclaimer
);
$json = json_encode($data);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);
*/
/*
curl "https://mail.zoho.com/api/accounts/123456789/messages" \
-X POST \
-H "Accept: application/json" \
-H "Content-Type: application/json" \
-H "Authorization:Zoho-oauthtoken *****" \
-d '{
   "fromAddress": "rebecca@zylker.com",
   "toAddress": "paula@zylker.com",
   "ccAddress": "david@zylker.com",
   "bccAddress": "rebecca11@zylker.com",
   "subject": "Email - Always and Forever",
   "content": "Email can never be dead. The most neutral and effective way, that can be used for one to many and two way communication.",
   "askReceipt" : "yes"
}'
*/
?>
