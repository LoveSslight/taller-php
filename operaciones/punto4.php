<?php
class OperacionesConjuntos {
    private array $conjuntoA;
    private array $conjuntoB;

    public function __construct(string $strA, string $strB) {
        $this->conjuntoA = $this->procesarCadena($strA);
        $this->conjuntoB = $this->procesarCadena($strB);
    }

    private function procesarCadena(string $cadena): array {
        if (trim($cadena) === "") return array();
        $elementos = explode(',', $cadena);
        $limpios = array_map('intval', array_map('trim', $elementos));
        return array_values(array_unique($limpios));
    }

    public function obtenerUnion(): array {
        $union = array_merge($this->conjuntoA, $this->conjuntoB);
        return array_values(array_unique($union));
    }

    public function obtenerInterseccion(): array {
        return array_values(array_intersect($this->conjuntoA, $this->conjuntoB));
    }

    public function obtenerDiferenciaAB(): array {
        return array_values(array_diff($this->conjuntoA, $this->conjuntoB));
    }

    public function obtenerDiferenciaBA(): array {
        return array_values(array_diff($this->conjuntoB, $this->conjuntoA));
    }
}

//Abstracción de la sesión global de PHP
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

/**
 * Clase Controlador: Maneja el flujo de la petición web.
 */
class ConjuntosController {
    private GestorSesion $sesion;

    public function __construct() {
        $this->sesion = new GestorSesion();
    }

    public function procesarPeticion(): void {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['conjuntoA'], $_POST['conjuntoB'])) {
            $conjuntos = new OperacionesConjuntos($_POST['conjuntoA'], $_POST['conjuntoB']);
            
            $resultados = [
                'union'        => $conjuntos->obtenerUnion(),
                'interseccion' => $conjuntos->obtenerInterseccion(),
                'diffAB'       => $conjuntos->obtenerDiferenciaAB(),
                'diffBA'       => $conjuntos->obtenerDiferenciaBA()
            ];
            
            $this->sesion->guardar('res_p4', $resultados);
            $this->sesion->guardar('strA_p4', $_POST['conjuntoA']);
            $this->sesion->guardar('strB_p4', $_POST['conjuntoB']);

            header("Location: punto4.php");
            exit();
        }
    }

    public function obtenerDatosVista(): array {
        $datos = [
            'resultados'   => $this->sesion->obtener('res_p4'),
            'strAAnterior' => $this->sesion->obtener('strA_p4', ''),
            'strBAnterior' => $this->sesion->obtener('strB_p4', '')
        ];

        $this->sesion->eliminar('res_p4');
        $this->sesion->eliminar('strA_p4');
        $this->sesion->eliminar('strB_p4');

        return $datos;
    }
}

// Ejecución del controlador POO y mapeo seguro de variables para la vista
$controlador = new ConjuntosController();
$controlador->procesarPeticion();

$datosVista = $controlador->obtenerDatosVista();
$resultados   = $datosVista['resultados'];
$strAAnterior = $datosVista['strAAnterior'];
$strBAnterior = $datosVista['strBAnterior'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto 4 - Operaciones de Conjuntos</title>
    <link rel="stylesheet" href="../css/punto4.css">
</head>
<body>

<div class="container-app">
    
    <div class="welcome-header">
        <h2>🌸 Operaciones algebraicas 🌸</h2>
        <span>unión, intersección y diferencias</span>
    </div>

    <div class="gif-container">
        <img src="../resources/baldi.gif" alt="Decoración Álgebra" class="welcome-gif">
    </div>

    <form method="POST" action="punto4.php">
        
        <div class="profile-fields-list">
            <div class="profile-field-row">
                <span class="field-tag">conjunto a :</span>
                <input type="text" name="conjuntoA" required placeholder="ej: 1, 2, 3, 4" value="<?php echo htmlspecialchars($strAAnterior); ?>">
            </div>
            
            <div class="profile-field-row">
                <span class="field-tag">conjunto b :</span>
                <input type="text" name="conjuntoB" required placeholder="ej: 3, 4, 5, 6" value="<?php echo htmlspecialchars($strBAnterior); ?>">
            </div>
        </div>

        <p class="helper-text">--Escribe los números enteros separados únicamente por comas.</p>

        <button type="submit" class="btn-primary">procesar conjuntos</button>
    </form>

    <?php if ($resultados !== null): ?>
        <div class="alert">
            <div class="result-title">Conjuntos calculados</div>
            <div class="result-field"><span class="result-label">Unión (A ∪ B) =</span> { <?php echo implode(', ', $resultados['union']); ?> }</div>
            <div class="result-field"><span class="result-label">Intersección (A ∩ B) =</span> { <?php echo implode(', ', $resultados['interseccion']); ?> }</div>
            <div class="result-field"><span class="result-label">Diferencia (A - B) =</span> { <?php echo implode(', ', $resultados['diffAB']); ?> }</div>
            <div class="result-field"><span class="result-label">Diferencia (B - A) =</span> { <?php echo implode(', ', $resultados['diffBA']); ?> }</div>
        </div>
    <?php endif; ?>

    <div class="sticker-decor sticker-left"></div>
    <div class="sticker-decor sticker-right"></div>

    <div class="back-menu-box">
        <a href="../index.php" class="back-menu-link">
            ← volver al menú
        </a>
    </div>

</div>

</body>
</html>

