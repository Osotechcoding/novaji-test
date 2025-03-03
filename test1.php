<?php
# write a php code to consume a google gemini API and print out all the answers to this question
# Who is Donald Trump?
$api_key="AIzaSyD8mjSdgFEO2bvq52nHLN2-uUzDuvx0oGU";
$api_url = "cloudaicompanion.googleapis.com";
$question = "Who is Donald Trump?";
$data = [
    'question' => $question,
    'language' => 'en'
];
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); g
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $api_key"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo "cURL error: " . curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);
$response_data = json_decode($response, true);

if (isset($response_data['answer'])) {
    echo "Answer: " . $response_data['answer'];
} else {
    echo "Couldn't fetch the answer. Response: " . $response;
}
