<?php
// Incluye el autoload de Composer para cargar las dependencias de GuzzleHttp
require 'vendor/autoload.php';
require_once 'config.php';

// Importa la clase Client de GuzzleHttp para hacer peticiones HTTP
use GuzzleHttp\Client;

// Inicia la sesión para recuperar el requestId
session_start();

// Recupera el requestId de la sesión
$requestId = $_SESSION['requestId'] ?? '';

// Inicializa una variable para el mensaje de estado
$messageBanner = '';

// Verifica si se proporcionó un requestId válido
if (empty($requestId)) {
    $messageBanner = '<div class="message error">Error: No se proporcionó un requestId.</div>';
    exit;
}

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

// Define los datos de autenticación para la solicitud
$auth = [
    'login' => PlacetopayConfig::$LOGIN,
    'tranKey' => $tranKey,
    'nonce' => $nonce,
    'seed' => $seed,
];

try {
    // Realiza la solicitud POST para consultar la sesión utilizando el requestId
    $response = $client->post("api/session/{$requestId}", [
        'json' => [
            'auth' => $auth,
        ],
    ]);

    // Obtiene el cuerpo de la respuesta y lo decodifica
    $body = $response->getBody();
    $responseData = json_decode($body, true);

    // Verifica el estado de la transacción
    if (isset($responseData['status']) && isset($responseData['status']['status'])) {
        $transactionStatus = $responseData['status']['status'];

        // Muestra el estado de la transacción con un mensaje y estilo adecuado
        switch ($transactionStatus) {
            case 'APPROVED':
                $messageBanner = '<div class="status success">La transacción ha sido aprobada.</div>';
                break;
            case 'PENDING':
                $messageBanner = '<div class="status pending">La transacción está pendiente.</div>';
                break;
            case 'REJECTED':
                $messageBanner = '<div class="status failed">La transacción ha sido rechazada.</div>';
                break;
            default:
                $messageBanner = '<div class="status error">Estado desconocido de la transacción.</div>';
                break;
        }
    } else {
        $messageBanner = '<div class="message error">Error: No se encontró información de estado en la respuesta.</div>';
    }
} catch (Exception $e) {
    //Captura y muestra cualquier error
    $messageBanner = '<div class="message error">Error: ' . $e->getMessage() . '</div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Transacción</title>
    <link rel="stylesheet" href="styleReturn.css">
</head>
<body>
    <div class="container">
        <h1>Estado de Transacción</h1>
        <?= $messageBanner ?>
        <form action="index.php" method="get">
            <button type="submit">volver a inicio</button>
        </form>
    </div>
</body>
</html>
