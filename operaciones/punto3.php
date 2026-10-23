<?php
session_start();
class Estadistica {
    private array $numeros;

    public function __construct(array $numeros) {
        //un array ya guardado en objeto
        $valoresLimpios = array_map('floatval', $numeros);
        sort($valoresLimpios); 
        $this->numeros = $valoresLimpios; 
    }

    public function calcularPromedio(): float {
        if (count($this->numeros) === 0) return 0;
        return array_sum($this->numeros) / count($this->numeros);
    }

    public function calcularMediana(): float {
        $total = count($this->numeros);
        if ($total === 0) return 0;

        $mitad = (int)floor($total / 2);
        
        // Si la cantidad de elementos es impar
        if ($total % 2 !== 0) {
            return $this->numeros[$mitad];
        }
        return ($this->numeros[$mitad - 1] + $this->numeros[$mitad]) / 2;
    }

    public function calcularModa(): string {
        if (count($this->numeros) === 0) return "ninguno";
        
        $frecuencias = array_count_values(array_map('strval', $this->numeros));
        $maxFrecuencia = max($frecuencias);

        if ($maxFrecuencia === 1) {
            return "no hay valores repetidos";
        }

        $modas = array_keys($frecuencias, $maxFrecuencia);
        return implode(", ", $modas);
    }
}

$cantidad = isset($_SESSION['cant_p3']) ? (int)$_SESSION['cant_p3'] : 0;
$valores = isset($_SESSION['valores_p3']) ? $_SESSION['valores_p3'] : array();
$resultados = isset($_SESSION['res_p3']) ? $_SESSION['res_p3'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['establecer_cantidad'])) {
        $cant = (int)$_POST['cantidad'];
        if ($cant > 0 && $cant <= 12) {
            $_SESSION['cant_p3'] = $cant;
            $_SESSION['valores_p3'] = array_fill(0, $cant, "");
        }
        $_SESSION['res_p3'] = null;
        header("Location: punto3.php");
        exit();
    }
    
    if (isset($_POST['calcular_estadisticas'])) {
        $valoresIngresados = $_POST['valores'] ?? array();
        $_SESSION['valores_p3'] = $valoresIngresados;
        
        $analizador = new Estadistica($valoresIngresados);
        $_SESSION['res_p3'] = array(
            'promedio' => round($analizador->calcularPromedio(), 4),
            'mediana'  => $analizador->calcularMediana(),
            'moda'     => $analizador->calcularModa()
        );
        header("Location: punto3.php");
        exit();
    }
    
    if (isset($_POST['reiniciar'])) {
        unset($_SESSION['cant_p3'], $_SESSION['valores_p3'], $_SESSION['res_p3']);
        header("Location: punto3.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto 3 - Ficha Estadística Pastel</title>
    <link rel="stylesheet" href="../css/punto3.css">
</head>
<body>

<div class="container-app">
    
    <div class="welcome-header">
        <h2>🌸 ficha estadística 🌸</h2>
        <span>rellena los campos para calcular</span>
    </div>

    <div class="step-box">
        <span class="step-badge">paso 1</span>
        <form method="POST" action="punto3.php" class="inline-form">
            <label for="cantidad">¿cuántas casillas quieress llenar? (máx 12):</label>
            <div class="action-row">
                <input type="number" id="cantidad" name="cantidad" min="1" max="12" required placeholder="ej: 4" value="<?php echo $cantidad > 0 ? $cantidad : ''; ?>">
                <button type="submit" name="establecer_cantidad" class="btn-small">crear 📂</button>
            </div>
        </form>
    </div>

    <?php if ($cantidad > 0): ?>
        <div class="step-box">
            <span class="step-badge">paso 2</span>
            <form method="POST" action="punto3.php">
                <label>digita un número en cada espacio en blanco:</label>
                
                <div class="profile-fields-list">
                    <?php for ($i = 0; $i < $cantidad; $i++): ?>
                        <div class="profile-field-row">
                            <span class="field-tag">dato #<?php echo $i + 1; ?> :</span>
                            <input type="number" step="any" name="valores[]" required 
                                   value="<?php echo isset($valores[$i]) ? htmlspecialchars($valores[$i]) : ''; ?>" 
                                   placeholder="escribe aquí...">
                        </div>
                    <?php endfor; ?>
                </div>
                
                <button type="submit" name="calcular_estadisticas" class="btn-primary">calcular resultados ✨</button>
                <button type="submit" name="reiniciar" class="btn-secondary">limpiar todo</button>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($resultados !== null): ?>
        <div class="alert shadow-result">
            <div class="result-title">¡Reporte calculado!</div>
            <div class="result-field"><span class="result-label">media / promedio:</span> <?php echo $resultados['promedio']; ?></div>
            <div class="result-field"><span class="result-label">mediana (centro):</span> <?php echo $resultados['mediana']; ?></div>
            <div class="result-field"><span class="result-label">moda (repetido):</span> <?php echo htmlspecialchars($resultados['moda']); ?></div>
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

