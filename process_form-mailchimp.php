<?php
// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$date = $_POST['date'];
$photo_number = $_POST['photo_number'];
$disclaimer = $_POST['disclaimer'];

// Get email address from data array
$search_param = $_GET['param'];
$email_address = $data[$search_param];

// Mailchimp API settings
$api_key = 'your_mailchimp_api_key';
$list_id = 'your_mailchimp_list_id';

// Add subscriber to Mailchimp list
$member_id = md5(strtolower($email));
$data_center = substr($api_key, strpos($api_key, '-') + 1);
$url = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . $member_id;
$json = json_encode([
    'email_address' => $email,
    'status' => 'subscribed',
    'merge_fields' => [
        'NAME' => $name,
        'DATE' => $date,
        'PHOTO_NUMBER' => $photo_number,
        'DISCLAIMER' => $disclaimer
    ]
]);
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);
$response = curl_exec($ch);
curl_close($ch);

// Send email to email address from data array using Mailchimp's API
$to_email = $email_address;
$subject = "Form Submission";
$body = "Name: $name\nEmail: $email\nDate: $date\nPhoto Number: $photo_number\nDisclaimer: $disclaimer";
$api_url = 'https://' . $data_center . '.api.mailchimp.com/3.0/campaigns';
$json = json_encode([
    'type' => 'regular',
    'recipients' => [
        'segment_text' => $to_email
    ],
    'settings' => [
        'subject_line' => $subject,
        'reply_to' => 'your_email@example.com'
    ],
    'content' => [
        'html' => $body
    ]
]);
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
]);
$response = curl_exec($ch);
curl_close($ch);
?>