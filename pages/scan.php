<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Controleer of er een foto is geüpload
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(array('success' => false, 'error' => 'No photo received or upload error'));
    exit;
}

// MAXIMAAL BESTANDSGROOTTE (5MB)
$max_file_size = 5 * 1024 * 1024;
if ($_FILES['photo']['size'] > $max_file_size) {
    echo json_encode(array('success' => false, 'error' => 'File is too large (max 5MB)'));
    exit;
}

// CONTROLEER OF HET EEN AFBEELDING IS
$allowed_types = array('image/jpeg', 'image/png', 'image/jpg');
if (!in_array($_FILES['photo']['type'], $allowed_types)) {
    echo json_encode(array('success' => false, 'error' => 'Only JPEG and PNG images are allowed'));
    exit;
}

// VERVANG DIT MET JE EIGEN PLANT.ID API KEY!
$api_key = 'Tzjm3d6QtmenotzI7SZjpPyrZUXm41gZF1xuc1ixBKEc6qk1gK'; // ← GET FROM https://web.plant.id/

$image_path = $_FILES['photo']['tmp_name'];
$image_data = base64_encode(file_get_contents($image_path));

// Maak de API request data VOLGENS OFFICIËLE DOCUMENTATIE
$request_data = array(
    'images' => array($image_data),
    'similar_images' => true,
    'health' => 'all', // 'all' voor volledige gezondheidsanalyse
    'language' => 'nl'  // Nederlandse resultaten
);

// Initialiseer cURL request naar Plant.id API V3
$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => 'https://plant.id/api/v3/identification',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Api-Key: ' . $api_key
    ),
    CURLOPT_POSTFIELDS => json_encode($request_data),
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_USERAGENT => 'PlantScanner/1.0'
));

// Verstuur request en ontvang response
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Debug mode (uncomment voor troubleshooting)
/*
error_log("=== PLANT.ID API REQUEST ===");
error_log("API Key: " . $api_key);
error_log("HTTP Code: " . $http_code);
error_log("Response: " . substr($response, 0, 1000) . "...");
error_log("=============================");
*/

// Verwerk de response VOLGENS OFFICIËLE DOCUMENTATIE
if ($http_code === 200 || $http_code === 201) {
    $data = json_decode($response, true);

    // Check of de response de juiste structuur heeft volgens documentatie
    if (isset($data['result']) && isset($data['result']['classification']['suggestions'])) {

        $suggestions = $data['result']['classification']['suggestions'];

        if (count($suggestions) > 0) {
            $suggestion = $suggestions[0]; // Top suggestion

            // Extract data volgens documentatie structuur
            $plant_name = isset($suggestion['name']) ? $suggestion['name'] : 'Unknown';
            $probability = isset($suggestion['probability']) ? $suggestion['probability'] : 0;

            // Common names
            $common_names = 'Unknown';
            if (isset($suggestion['details']['common_names']) &&
                is_array($suggestion['details']['common_names']) &&
                count($suggestion['details']['common_names']) > 0) {
                $common_names = implode(', ', $suggestion['details']['common_names']);
            }

            // Description
            $wiki_description = '';
            if (isset($suggestion['details']['description']['value'])) {
                $wiki_description = $suggestion['details']['description']['value'];
            }

            // Similar images
            $similar_images = array();
            if (isset($suggestion['similar_images']) && is_array($suggestion['similar_images'])) {
                foreach ($suggestion['similar_images'] as $img) {
                    if (isset($img['url'])) {
                        $similar_images[] = array(
                            'url' => $img['url'],
                            'citation' => isset($img['citation']) ? $img['citation'] : 'Similar image'
                        );
                    }
                }
            }

            // Health assessment (ziekte detectie)
            $health_assessment = '';
            if (isset($data['result']['disease']['suggestions']) &&
                count($data['result']['disease']['suggestions']) > 0) {
                $disease = $data['result']['disease']['suggestions'][0];
                $health_assessment = isset($disease['name']) ? $disease['name'] : '';
                if ($health_assessment && isset($disease['probability'])) {
                    $health_assessment .= ' (' . round($disease['probability'] * 100) . '% confidence)';
                }
            }

            // Is plant probability
            $is_plant_probability = 0;
            if (isset($data['result']['is_plant']['probability'])) {
                $is_plant_probability = $data['result']['is_plant']['probability'];
            }

            echo json_encode(array(
                'success' => true,
                'plant_name' => $plant_name,
                'probability' => $probability,
                'common_names' => $common_names,
                'wiki_description' => $wiki_description,
                'similar_images' => $similar_images,
                'health_assessment' => $health_assessment,
                'is_plant_probability' => $is_plant_probability
            ));

        } else {
            echo json_encode(array(
                'success' => false,
                'error' => 'No plant identification suggestions found'
            ));
        }

    } else {
        echo json_encode(array(
            'success' => false,
            'error' => 'Invalid API response structure',
            'debug' => 'Response missing result or classification data'
        ));
    }

} else {
    // Gedetailleerde error handling volgens API documentatie
    $error_message = 'Unknown error occurred';
    $error_details = '';

    // Probeer error details uit response te halen
    if ($response) {
        $error_data = json_decode($response, true);
        if (isset($error_data['error']['message'])) {
            $error_message = $error_data['error']['message'];
        }
        if (isset($error_data['error']['details'])) {
            $error_details = $error_data['error']['details'];
        }
    }

    // HTTP code specific errors
    switch ($http_code) {
        case 400:
            $error_message = $error_message ?: 'Bad request. Please check your input data.';
            break;
        case 401:
            $error_message = $error_message ?: 'Invalid API key. Please check your Plant.id API key.';
            break;
        case 403:
            $error_message = $error_message ?: 'Access denied. Invalid API key or no credits available.';
            break;
        case 404:
            $error_message = $error_message ?: 'API endpoint not found.';
            break;
        case 429:
            $error_message = $error_message ?: 'Too many requests. Please wait and try again.';
            break;
        case 500:
        case 502:
        case 503:
            $error_message = $error_message ?: 'Plant.id server error. Please try again later.';
            break;
        case 0:
            $error_message = 'No connection. Please check your internet connection.';
            break;
        default:
            $error_message = $error_message ?: 'API error: HTTP ' . $http_code;
            break;
    }

    // Voeg details toe als beschikbaar
    if ($error_details) {
        $error_message .= ' - ' . $error_details;
    }

    // Voeg curl error toe indien beschikbaar
    if ($curl_error) {
        $error_message .= ' (CURL: ' . $curl_error . ')';
    }

    echo json_encode(array(
        'success' => false,
        'error' => $error_message,
        'http_code' => $http_code
    ));
}
?>