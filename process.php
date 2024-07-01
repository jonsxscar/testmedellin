<?php
// Incluye el autoload de Composer para cargar las dependencias de GuzzleHttp
require 'vendor/autoload.php';
require_once 'config.php';

// Importa la clase Client de GuzzleHttp para hacer peticiones HTTP
use GuzzleHttp\Client;

// Inicia la sesión para almacenar el requestId
session_start();

// Crea una instancia del cliente GuzzleHttp con la URL base y un timeout de 2 segundos
$client = new Client([
    'base_uri' => PlacetopayConfig::$BASE_URL,
    'timeout'  => 2.0,
]);

// Genera el seed (semilla) y el nonce (número único) para la autenticación
$seed = date('c');
$rawNonce = rand();
$tranKey = base64_encode(hash('sha256', $rawNonce . $seed . PlacetopayConfig::$SECRET_KEY, true));
$nonce = base64_encode($rawNonce);

// Recupera los datos del formulario enviados a través de POST
$name = $_POST['name'];
$surname = $_POST['surname'];
$email = $_POST['email'];
$id = $_POST['id'];
$phone = $_POST['phone'];

// Verifica que todos los campos estén completos
if (empty($name) || empty($surname) || empty($email) || empty($id) || empty($phone)) {
    // Si algún campo está vacío, guarda los datos en session y redirige al form
    $_SESSION['error_fields'] = [
        'name' => $name,
        'surname' => $surname,
        'email' => $email,
        'id' => $id,
        'phone' => $phone,
    ];
    header('Location: index.php?error=missing_fields');
    exit;
}

// Si todos los campos están completos, borra los error_fields
$_SESSION['error_fields'] = null;

// Define los datos para la solicitud de pago a Placetopay
$data = [
    'auth' => [
        'login' => PlacetopayConfig::$LOGIN,
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
        'name' => $name,
        'surname' => $surname,
        'email' => $email,
        'documentType' => 'CC',
        'document' => $id,
        'mobile' => $phone,
    ],
    'expiration' => date('c', strtotime('+1 day')), // Fecha de expiración de la sesión de pago
    'returnUrl' => 'http://localhost:8000/return.php', // Redireccionamiento después del pago
    'ipAddress' => '127.0.0.1', // Dirección IP del comprador
    'userAgent' => $_SERVER['HTTP_USER_AGENT'], // Agente de usuario utilizado para la solicitud
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
        //Si no encuentra 'processUrl' muestra un mensaje de error
        echo 'Error: No processUrl found in the response.';
    }
} catch (Exception $e) {
    //Captura y muestra cualquier error
    echo 'Error: ' . $e->getMessage();
}
?>
