# Integración con Placetopay

Este proyecto demuestra cómo integrar el proceso de pago utilizando la API de Placetopay.
Es importante aclarar que al ser un entorno de prueba tecnica deje las credenciales, de no ser asi estarian ocultas. 

## Instrucciones para Iniciar el Servidor

Para iniciar el servidor y probar el proceso de pago, sigue estos pasos:

1. **Instalar guzzlehttp/guzzle con composer:**
    ```sh
    composer require guzzlehttp/guzzle
    ```

2. **Iniciar el servidor con PHP:**
    ```sh
    php -S localhost:8000
    ```
3. **Abrir el navegador:**
    - Navega a `http://localhost:8000/index.html`.

## Proceso de Pago

1. Abre tu navegador y navega a `http://localhost:8000/index.html`.
2. Haz clic en el botón **"Iniciar Pago Básico"**.
3. Serás redirigido a Placetopay para completar el pago.

### Tarjetas de Prueba

Utiliza las siguientes tarjetas de prueba para simular diferentes resultados:

#### Aprobado
- **Número de Tarjeta:** `4110760000000081`
- **Mes y Año:** Mes y año actuales
- **CVV:** `123`

#### Pendiente
- **Número de Tarjeta:** `4509564638437551`
- **Mes y Año:** Mes y año actuales
- **CVV:** `123`

#### Rechazado
- **Número de Tarjeta:** `4110760000000016`
- **Mes y Año:** Mes y año actuales
- **CVV:** `123`
