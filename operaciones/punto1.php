<?php
class Acronimo {
    private string $frase;

    public function __construct(string $frase) {
        $this->frase = $frase;
    }

    public function generar(): string {
        $limpio = preg_replace('/[^\w\s\-]/u', '', $this->frase);
        $limpio = str_replace('-', ' ', $limpio);
        $palabras = preg_split('/\s+/', trim($limpio));
        
        $acronimo = "";
        foreach ($palabras as $palabra) {
            if (!empty($palabra)) {
                $acronimo .= mb_strtoupper(mb_substr($palabra, 0, 1));
            }
        }
        
        return $acronimo;
    }
}

class GestorSesion {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function guardar(string $clave, mixed $valor): void {
        $_SESSION[$clave] = $valor;
    }

    public function obtener(string $clave, mixed $defecto = null): mixed {
        return $_SESSION[$clave] ?? $defecto;
    }

    public function eliminar(string $clave): void {
        unset($_SESSION[$clave]);
    }
}

class AcronimoController {
    private GestorSesion $sesion;

    public function __construct() {
        $this->sesion = new GestorSesion();
    }

    /**
     * Captura el envío del formulario, procesa la lógica del modelo
     * y redirige para evitar el reenvío accidental de datos al recargar.
     */
    public function procesarPeticion(): void {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['frase'])) {
            $procesador = new Acronimo($_POST['frase']);
            
            $this->sesion->guardar('res_p1', $procesador->generar());
            $this->sesion->guardar('frase_p1', $_POST['frase']);

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    /**
     * Recupera la información temporal almacenada en la sesión para la vista
     * y limpia las variables inmediatamente para futuros usos.
     */
    public function obtenerDatosVista(): array {
        $datos = [
            'resultado'     => $this->sesion->obtener('res_p1', ''),
            'fraseAnterior' => $this->sesion->obtener('frase_p1', '')
        ];

        $this->sesion->eliminar('res_p1');
        $this->sesion->eliminar('frase_p1');

        return $datos;
    }
}

// Inicialización y ejecución controlada del flujo web
$controlador = new AcronimoController();
$controlador->procesarPeticion();

$datosVista = $controlador->obtenerDatosVista();
$resultado     = $datosVista['resultado'];
$fraseAnterior = $datosVista['fraseAnterior'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acrónimos</title>
    <link rel="stylesheet" href="../css/punto1.css">
</head>
<body>

<div class="container-app">
    
    <div class="welcome-header">
        <h2>Acronimos</h2>
        <span>Convierte tus frases a acrónimos</span>
    </div>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for="frase">Ingresa un nombre largo o una frase:</label>
        <input type="text" id="frase" name="frase" required placeholder="Ej: Así es el destino" value="<?php echo htmlspecialchars($fraseAnterior); ?>">
        
        <button type="submit">Generar Acrónimo</button>
    </form>

    <?php if ($resultado !== ""): ?>
        <div class="alert">
            <strong>Resultado Exitoso:</strong>
            <span class="result-acronym">
                <?php echo htmlspecialchars($resultado); ?>
            </span>
        </div>
    <?php endif; ?>

    <div class="sticker-decor sticker-left"></div>
    <div class="sticker-decor sticker-right"></div>

    <div class="back-menu-box">
        <a href="../index.php" class="back-menu-link">
            ← Volver al menú
        </a>
    </div>

</div>

</body>
</html>
