<?php
session_start();
class CalculadoraSeries {
    private int $numero;

    public function __construct(int $numero) {
        $this->numero = $numero;
    }

    // Calcula el factorial
    public function calcularFactorial(): string {
        if ($this->numero < 0) return "No definido para números negativos";
        if ($this->numero > 170) return "Infinito (Número demasiado grande)";
        
        $factorial = 1;
        for ($i = 1; $i <= $this->numero; $i++) {
            $factorial *= $i;
        }
        return (string)$factorial;
    }

    // Genera la secuencia matemática de Fibonacci
    public function generarFibonacci(): array {
        if ($this->numero <= 0) return [];
        if ($this->numero === 1) return array(0);
        
        // Inicializar
        $serie = array(0, 1);
        
        while (count($serie) < $this->numero) {
            $serie[] = $serie[count($serie) - 1] + $serie[count($serie) - 2];
        }
        return $serie;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['numero']) && !empty($_POST['operacion'])) {
    $num = (int)$_POST['numero'];
    $operacion = $_POST['operacion'];
    
    $calculadora = new CalculadoraSeries($num);
    
    if ($operacion === "factorial") {
        $_SESSION['resultado_series'] = "El Factorial de $num es: " . $calculadora->calcularFactorial();
    } elseif ($operacion === "fibonacci") {
        $secuencia = $calculadora->generarFibonacci();
        $_SESSION['resultado_series'] = "Serie de Fibonacci ($num términos): " . implode(", ", $secuencia);
    }
    
    $_SESSION['num_ingresado'] = $num;
    $_SESSION['op_seleccionada'] = $operacion;

    header("Location: punto2.php");
    exit();
}

$salida = isset($_SESSION['resultado_series']) ? $_SESSION['resultado_series'] : "";
$numAnterior = isset($_SESSION['num_ingresado']) ? $_SESSION['num_ingresado'] : "";
$opAnterior = isset($_SESSION['op_seleccionada']) ? $_SESSION['op_seleccionada'] : "";

unset($_SESSION['resultado_series']);
unset($_SESSION['num_ingresado']);
unset($_SESSION['op_seleccionada']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto 2 - Fibonacci y Factorial</title>
    <link rel="stylesheet" href="../css/punto2.css">
</head>
<body>

<div class="container-app">
    
    <div class="welcome-header">
        <h2>punto 2</h2>
        <span>sucesión de fibonacci o factorial</span>
    </div>

    <div class="gif-container">
        <img src="../resources/zayne.gif" alt="Decoración Pastel" class="welcome-gif">
    </div>

    <!-- Estructura limpia de Formulario HTML5 -->
    <form method="POST" action="punto2.php">
        <label for="numero">Ingresa un número entero positivo:</label>
        <input type="number" id="numero" name="numero" min="0" required placeholder="Ej: 6" value="<?php echo htmlspecialchars($numAnterior); ?>">

        <label for="operacion">Seleccione la serie o cálculo:</label>
        <select id="operacion" name="operacion" required>
            <option value="">-- Elige --</option>
            <option value="fibonacci" <?php echo ($opAnterior === 'fibonacci') ? 'selected' : ''; ?>>Sucesión de Fibonacci</option>
            <option value="factorial" <?php echo ($opAnterior === 'factorial') ? 'selected' : ''; ?>>Cálculo de Factorial</option>
        </select>

        <button type="submit">Calcular Serie ✨</button>
    </form>

    <!-- Caja de alertas/resultados estética -->
    <?php if ($salida !== ""): ?>
        <div class="alert">
            <strong>Cálculo Terminado:</strong>
            <div class="result-output"><?php echo htmlspecialchars($salida); ?></div>
        </div>
    <?php endif; ?>

    <!-- Pegatinas laterales animadas por CSS -->
    <div class="sticker-decor sticker-left"></div>
    <div class="sticker-decor sticker-right"></div>

    <!-- Enlace inferior hacia el panel central -->
    <div class="back-menu-box">
        <a href="../index.php" class="back-menu-link">
            ← Volver al menú
        </a>
    </div>

</div>

</body>
</html>
