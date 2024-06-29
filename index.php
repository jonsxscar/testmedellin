<?php
// Incluye el autoload de Composer para cargar las dependencias de GuzzleHttp
require 'vendor/autoload.php';

// Importa la clase Client de GuzzleHttp para hacer peticiones HTTP
use GuzzleHttp\Client;

// Inicia la sesión para almacenar el requestId
session_start();

// Define las credenciales y la URL base de Placetopay
$login = '2d9eaf1e662518756a3d78806543af5b';
$secretKey = '3YC5brb5eAR4xBGQ';
$baseURL = 'https://checkout-test.placetopay.com/';

// Crea una instancia del cliente GuzzleHttp con la URL base y un timeout de 2 segundos
$client = new Client([
    'base_uri' => $baseURL,
    'timeout'  => 2.0,
]);

// Genera el seed (semilla) y el nonce (número único) para la autenticación
$seed = date('c');
$rawNonce = rand();
$tranKey = base64_encode(hash('sha256', $rawNonce . $seed . $secretKey, true));
$nonce = base64_encode($rawNonce);

// Define los datos para la solicitud de pago a Placetopay
$data = [
    'auth' => [
        'login' => $login,
        'tranKey' => $tranKey,
        'nonce' => $nonce,
        'seed' => $seed,
    ],
    'payment' => [
        'reference' => "PAY_ABC_1287", // Referencia única del pago
        'description' => 'Pago por Placetopay',
        'amount' => [
            'currency' => 'USD', //monto y tipo de moneda proporcionada
            'total' => 1000,
        ],
    ],
    'locale' => "es_CO", // Configuración regional en español para Colombia
    'buyer' => [  //datos de comprador
        'name' => 'Jonnathan',
        'surname' => 'Scarpetta',
        'email' => 'jonsxscar@gmail.com',
        'documentType' => 'CC',
        'document' => '1127610884',
        'mobile' => '3148308656',
    ],
    'expiration' => date('c', strtotime('+1 day')), // Fecha de expiración de la sesión de pago
    'returnUrl' => 'http://localhost:8000/return.php', // Redireccionamiento después del pago
    'ipAddress' => '127.0.0.1', // Dirección IP del comprador
    'userAgent' => 'PHP-Client', // Agente de usuario utilizado para la solicitud
];

try {
    // Realiza la solicitud POST para crear la sesión de pago
    $response = $client->post('api/session', [
        'json' => $data,
    ]);

    // Obtiene el cuerpo de la respuesta y lo decodifica
    $body = $response->getBody();
    $responseData = json_decode($body, true);

    // Verifica si se recibió el 'processUrl' en la respuesta
    if (isset($responseData['processUrl'])) {
        // Almacena el requestId en la sesión para usarlo en return.php
        $_SESSION['requestId'] = $responseData['requestId'];
        // Redirige al usuario al processUrl para completar la transacción en Placetopay
        header('Location: ' . $responseData['processUrl']);
        exit;
    } else {
        echo 'Error: No processUrl found in the response.';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
