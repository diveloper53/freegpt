<?php

/*
Example usage: askGPT("Hello World!");

See github repos on:
https://github.com/diveloper53/freegpt
*/

// Small config:
define('REQUEST_URL', 'https://chat.chatgptdemo.net/chat_api_stream');
define('CHAT_ID', '651828e2be877feed46b2b96');
// Small config.

function makeRequest($question) {
    $data = json_encode([
        'chat_id' => CHAT_ID,
        'question' => $question,
        'timestamp' => time()
    ]);

    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n",
            'method' => REQUEST_METHOD,
            'content' => $data
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents(REQUEST_URL, false, $context);
    return $result;
}

function getResponse($question) {
    $response = makeRequest($question);
    $completeData = [];
    $reader = fopen('php://temp', 'r+');
    fwrite($reader, $response);
    rewind($reader);

    while (!feof($reader)) {
        $completeData[] = fread($reader, 1024);
    }

    fclose($reader);
    return $completeData;
}

function processResponse($response) {
    $combinedResponse = implode("", $response);
    $pattern = '/\{\"content\"\:\"([^\"]+)\"\}/';
    preg_match_all($pattern, $combinedResponse, $extractedContent);
    $formattedResponse = implode("", $extractedContent[1]);
    return $formattedResponse;
}

function askGPT($question) {
    $response = getResponse($question);
    $formattedResponse = processResponse($response);
    return $formattedResponse;
}
