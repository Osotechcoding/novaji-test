<?php

$targetedUrl = "https://cbn.gov.ng/Documents/circulars.html";
$ch = curl_init($targetedUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$result = curl_exec($ch);
curl_close($ch);

if ($result === false || empty($result)) {
    die("Error: Failed to fetch the content of the URL.");
}

$dom = new DOMDocument();
@$dom->loadHTML($result);
$xpath = new DOMXPath($dom);

$pdfLinks = $xpath->query("//a[contains(@href, '.pdf')]");

$pdfDirectory = __DIR__."/cbn_pdfs";
if (!is_dir($pdfDirectory)) {
#create a new cbn_pdfs folder
    mkdir($pdfDirectory, 0777, true);
}

$data = [];

if ($pdfLinks->length === 0) {
    die("No PDF links were found.");
}

foreach ($pdfLinks as $link) {
    $href = $link->getAttribute('href');
    $text = trim($link->textContent);

    if (!str_starts_with($href, 'http')) {
        $href = 'https://www.cbn.gov.ng' . $href;
    }

    $fileName = basename(str_replace(' ', '_', parse_url($href, PHP_URL_PATH)));
    $filePath = $pdfDirectory . '/' . $fileName;

    $pdfContent = file_get_contents($href);
    if ($pdfContent !== false) {
        file_put_contents($filePath, $pdfContent);
    } else {
        echo "Failed to download: $href\n";
        continue;
    }

    $data[] = [
        'title' => $text,
        'link' => $href,
        'local_path' => $filePath,
    ];
}

file_put_contents('cbn.circular.json', json_encode($data, JSON_PRETTY_PRINT));

echo "Extraction completed. Data saved to cbn.circular.json and PDFs downloaded to $pdfDirectory.";
