<?php

/**
 * Clase Modelo: Lógica de cálculo matemático puro.
 */
class CalculadoraSeries
{
    private int $numero;

    public function __construct(int $numero)
    {
        $this->numero = $numero;
    }

    public function calcularFactorial(): string
    {
        if ($this->numero < 0) return "No definido para números negativos";
        if ($this->numero > 170) return "Infinito (Número demasiado grande)";

        $factorial = 1;
        for ($i = 1; $i <= $this->numero; $i++) {
            $factorial *= $i;
        }
        return (string)$factorial;
    }

    public function generarFibonacci(): array
    {
        if ($this->numero <= 0) return [];
        if ($this->numero === 1) return array(0);

        $serie = array(0, 1);
        while (count($serie) < $this->numero) {
            $serie[] = $serie[count($serie) - 1] + $serie[count($serie) - 2];
        }
        return $serie;
    }
}

/**
 * Clase de Servicio: Encapsula la superglobal $_SESSION.
 */
class GestorSesion
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function guardar(string $clave, mixed $valor): void
    {
        $_SESSION[$clave] = $valor;
    }

    public function obtener(string $clave, mixed $defecto = null): mixed
    {
        return $_SESSION[$clave] ?? $defecto;
    }

    public function eliminar(string $clave): void
    {
        unset($_SESSION[$clave]);
    }
}

/**
 * Clase Controlador: Coordina el ciclo de vida de la petición HTTP.
 */
class SeriesController
{
    private GestorSesion $sesion;

    public function __construct()
    {
        $this->sesion = new GestorSesion();
    }

    /**
     * Captura los datos del formulario, procesa la serie solicitada 
     * e implementa la redirección PRG para evitar duplicidad de envíos.
     */
    public function procesarPeticion(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['numero']) && !empty($_POST['operacion'])) {
            $num = (int)$_POST['numero'];
            $operacion = $_POST['operacion'];

            $calculadora = new CalculadoraSeries($num);
            $resultado = "";

            if ($operacion === "factorial") {
                $resultado = "El Factorial de $num es: " . $calculadora->calcularFactorial();
            } elseif ($operacion === "fibonacci") {
                $secuencia = $calculadora->generarFibonacci();
                $resultado = "Serie de Fibonacci ($num términos): " . implode(", ", $secuencia);
            }

            $this->sesion->guardar('resultado_series', $resultado);
            $this->sesion->guardar('num_ingresado', $num);
            $this->sesion->guardar('op_seleccionada', $operacion);

            header("Location: punto2.php");
            exit();
        }
    }

    /**
     * Extrae las respuestas para inyectar en la vista y destruye las 
     * variables de sesión de forma controlada inmediatamente.
     */
    public function obtenerDatosVista(): array
    {
        $datos = [
            'salida'      => $this->sesion->obtener('resultado_series', ''),
            'numAnterior' => $this->sesion->obtener('num_ingresado', ''),
            'opAnterior'  => $this->sesion->obtener('op_seleccionada', '')
        ];

        $this->sesion->eliminar('resultado_series');
        $this->sesion->eliminar('num_ingresado');
        $this->sesion->eliminar('op_seleccionada');

        return $datos;
    }
}

// Inicialización del ecosistema orientado a objetos
$controlador = new SeriesController();
$controlador->procesarPeticion();

$datosVista = $controlador->obtenerDatosVista();
$salida      = $datosVista['salida'];
$numAnterior = $datosVista['numAnterior'];
$opAnterior  = $datosVista['opAnterior'];
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

        <?php if ($salida !== ""): ?>
            <div class="alert">
                <strong>Cálculo Terminado:</strong>
                <div class="result-output"><?php echo htmlspecialchars($salida); ?></div>
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