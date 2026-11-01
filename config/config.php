<?php
// Declarar tipos estrictos para mejorar la seguridad y consistencia del código
declare(strict_types=1);

// Definir constantes de configuración para la base de datos y la aplicación
const DB_HOST = '127.0.0.1';
const DB_PORT = 3306; // Ajustar a 33065 solo si tu XAMPP usa ese puerto personalizado
const DB_NAME = 'restaurantela40';
const DB_USER = 'root'; // Ajustar según la configuración de tu servidor
const DB_PASS = ''; // Ajustar según la configuración de tu servidor
const DB_CHARSET = 'utf8mb4';

// Definir la ruta base de la aplicación para facilitar la gestión de rutas y recursos
const APP_URL = 'http://localhost/restauranteLa40';
const IDLE_TIMEOUT = 1800; // 30 minutos por inactividad
const ABS_TIMEOUT = 28800; // 8 horas límite absoluto
const MAIL_FROM = 'no-reply@la40.local';

$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log('Error de conexión MySQL: ' . $e->getMessage());
    http_response_code(500);
    exit('No se pudo conectar con la base de datos. Verifica el estado de MySQL en XAMPP.');
}