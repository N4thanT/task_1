<?php
$city = "Kuressaare";  
$apiKey = "#";  // Replace with your actual API key
$cacheFile = "weather_cache.json";  
$cacheTime = 900;  

header('Content-Type: application/json');

// Check if cache file exists and is still valid
if (file_exists($cacheFile) && time() - filemtime($cacheFile) < $cacheTime) {
    // Use cached data
    echo file_get_contents($cacheFile);
} else {
    // Fetch data from OpenWeatherMap API
    $url = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=$apiKey&units=metric";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {  // Check for cURL errors
        $error_msg = curl_error($ch);
        echo json_encode(['error' => "Request failed: $error_msg"]);
    } else {
        $decodedResponse = json_decode($response, true);
        if (json_last_error() == JSON_ERROR_NONE && isset($decodedResponse['weather'])) {  // Check if response is valid JSON and has expected data
            // Save to cache
            file_put_contents($cacheFile, $response);
            // Output the fresh data
            echo $response;
        } else {
            echo json_encode(['error' => 'Invalid API response']);
        }
    }
    
    curl_close($ch);
}
?>
