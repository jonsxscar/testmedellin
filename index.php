<?php
//llamo a config.php donde estan las credenciales
require 'config.php';
//inicia sesion donde se almacenan datos
session_start();

$defaultValues = $_SESSION['error_fields'] ?? [
    'name' => '',
    'surname' => '',
    'email' => '',
    'phone' => '',
    'id' => '',
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Transacción</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Iniciar Transacción</h1>
        <!-- Formulario que envía datos a 'process.php' usando el método POST -->
        <form action="process.php" method="post">
            <?php
            // Si hay un error en la URL muestra un mensaje de error
                if (isset($_GET['error'])) {
                    echo '<p class="message error">Por favor, complete el formulario e intente nuevamente.</p>';
                }
            ?>

            <input type="text" name="name" placeholder="Nombre" required value="<?= $defaultValues['name'] ?>" />
            <input type="text" name="surname" placeholder="Apellido" required value="<?= $defaultValues['surname'] ?>" />
            <input type="email" name="email" placeholder="Correo" required value="<?= $defaultValues['email'] ?>" />
            <input type="tel" name="phone" placeholder="Teléfono" required value="<?= $defaultValues['phone'] ?>" />
            <input type="number" name="id" placeholder="Cédula de ciudadanía" required value="<?= $defaultValues['id'] ?>" />
            <button type="submit">Iniciar Pago basico</button>
        </form>
    </div>
</body>
</html>
