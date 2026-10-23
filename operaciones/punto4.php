<?php
// 1. INICIALIZAR SESIÓN PARA EVITAR REENVÍOS AL RECARGAR CON F5
session_start();

// CLASE POO: OPERACIONES DE CONJUNTOS MATEMÁTICOS
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

// 2. CONTROLADOR DE FORMULARIO CON PATRÓN PRG
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['conjuntoA'], $_POST['conjuntoB'])) {
    $conjuntos = new OperacionesConjuntos($_POST['conjuntoA'], $_POST['conjuntoB']);
    
    $_SESSION['res_p4'] = array(
        'union'        => $conjuntos->obtenerUnion(),
        'interseccion' => $conjuntos->obtenerInterseccion(),
        'diffAB'       => $conjuntos->obtenerDiferenciaAB(),
        'diffBA'       => $conjuntos->obtenerDiferenciaBA()
    );
    
    $_SESSION['strA_p4'] = $_POST['conjuntoA'];
    $_SESSION['strB_p4'] = $_POST['conjuntoB'];

    header("Location: punto4.php");
    exit();
}

// 3. CAPTURA Y LIMPIEZA DE MEMORIA TEMPORAL
$resultados = isset($_SESSION['res_p4']) ? $_SESSION['res_p4'] : null;
$strAAnterior = isset($_SESSION['strA_p4']) ? $_SESSION['strA_p4'] : "";
$strBAnterior = isset($_SESSION['strB_p4']) ? $_SESSION['strB_p4'] : "";

unset($_SESSION['res_p4'], $_SESSION['strA_p4'], $_SESSION['strB_p4']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto 4 - Operaciones de Conjuntos</title>
    <!-- Enlazado directo a tu nuevo archivo verde pastel -->
    <link rel="stylesheet" href="../css/punto4.css">
</head>
<body>

<div class="container-app">
    
    <div class="welcome-header">
        <h2>🌸 operaciones algebraicas 🌸</h2>
        <span>unión, intersección y diferencias</span>
    </div>

    <!-- Ilustración expansiva central -->
    <div class="gif-container">
        <img src="../resources/conejo.png" alt="Decoración Álgebra" class="welcome-gif">
    </div>

    <!-- Formulario estructurado -->
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

        <p class="helper-text">* escribe los números enteros separados únicamente por comas.</p>

        <button type="submit" class="btn-primary">procesar conjuntos 🔮</button>
    </form>

    <!-- Ficha con Rejilla de Cuadernos de Matemáticas para el reporte -->
    <?php if ($resultados !== null): ?>
        <div class="alert">
            <div class="result-title">📋 conjuntos calculados</div>
            <div class="result-field"><span class="result-label">Unión (A ∪ B) =</span> { <?php echo implode(', ', $resultados['union']); ?> }</div>
            <div class="result-field"><span class="result-label">Intersección (A ∩ B) =</span> { <?php echo implode(', ', $resultados['interseccion']); ?> }</div>
            <div class="result-field"><span class="result-label">Diferencia (A - B) =</span> { <?php echo implode(', ', $resultados['diffAB']); ?> }</div>
            <div class="result-field"><span class="result-label">Diferencia (B - A) =</span> { <?php echo implode(', ', $resultados['diffBA']); ?> }</div>
        </div>
    <?php endif; ?>

    <!-- Pegatinas desofasadas asimétricamente flotando en vertical -->
    <div class="sticker-decor sticker-left"></div>
    <div class="sticker-decor sticker-right"></div>

    <div class="back-menu-box">
        <a href="../index.php" class="back-menu-link">← volver al panel principal</a>
    </div>

</div>

</body>
</html>
