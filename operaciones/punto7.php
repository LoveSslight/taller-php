<?php
class Calculadora {
    private float $num1;
    private float $num2;

    public function __construct(float $num1, float $num2) {
        $this->num1 = $num1;
        $this->num2 = $num2;
    }

    public function operar(string $operacion): float|string {
        switch ($operacion) {
            case 'suma':
                return $this->num1 + $this->num2;
            case 'resta':
                return $this->num1 - $this->num2;
            case 'multiplicacion':
                return $this->num1 * $this->num2;
            case 'division':
                if ($this->num2 == 0.0) {
                    return "Error (Div 0)";
                }
                return $this->num1 / $this->num2;
            case 'porcentaje':
                return ($this->num1 * $this->num2) / 100;
            default:
                return "Inválido";
        }
    }
}

class GestorSesion {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['historial'])) {
            $_SESSION['historial'] = [];
        }
    }

    public function guardar(string $clave, mixed $valor): void {
        $_SESSION[$clave] = $valor;
    }

    public function obtener(string $clave, mixed $defecto = null): mixed {
        return $_SESSION[$clave] ?? $defecto;
    }

    public function limpiarHistorial(): void {
        $_SESSION['historial'] = [];
    }
}

class CalculadoraController {
    private GestorSesion $sesion;
    private array $simbolos = [
        'suma' => '+', 
        'resta' => '-', 
        'multiplicacion' => '×', 
        'division' => '÷', 
        'porcentaje' => '%'
    ];

    public function __construct() {
        $this->sesion = new GestorSesion();
    }

    public function procesarPeticion(): void {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST['borrar_historial'])) {
                $this->sesion->limpiarHistorial();
                header("Location: punto7.php");
                exit();
            }

            if (isset($_POST['calcular'])) {
                $n1 = isset($_POST['num1']) ? (float)$_POST['num1'] : 0.0;
                $n2 = isset($_POST['num2']) ? (float)$_POST['num2'] : 0.0;
                $op = $_POST['operacion'] ?? 'suma';

                $calc = new Calculadora($n1, $n2);
                $res = $calc->operar($op);

                $simbolo = $this->simbolos[$op] ?? '?';
                $registro = ($op === 'porcentaje') ? "{$n2}% de {$n1} = {$res}" : "{$n1} {$simbolo} {$n2} = {$res}";

                $historial = $this->sesion->obtener('historial', []);
                $historial[] = $registro;
                
                $this->sesion->guardar('historial', $historial);
                $this->sesion->guardar('res_p7', $res);
                $this->sesion->guardar('n1_p7', $_POST['num1']);
                $this->sesion->guardar('n2_p7', $_POST['num2']);
                $this->sesion->guardar('op_p7', $op);

                header("Location: punto7.php");
                exit();
            }
        }
    }

    public function obtenerDatosVista(): array {
        return [
            'resultado'      => $this->sesion->obtener('res_p7', ''),
            'num1Anterior'   => $this->sesion->obtener('n1_p7', ''),
            'num2Anterior'   => $this->sesion->obtener('n2_p7', ''),
            'opAnterior'     => $this->sesion->obtener('op_p7', 'suma'),
            'historial'      => $this->sesion->obtener('historial', [])
        ];
    }
}

$controlador = new CalculadoraController();
$controlador->procesarPeticion();

$datosVista = $controlador->obtenerDatosVista();
$resultado    = $datosVista['resultado'];
$num1Anterior = $datosVista['num1Anterior'];
$num2Anterior = $datosVista['num2Anterior'];
$opAnterior   = $datosVista['opAnterior'];
$historial    = $datosVista['historial'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto 7 - Calculadora Estándar</title>
    <link rel="stylesheet" href="../css/punto7.css">
</head>
<body>

<div class="container-app">

    <!-- Columna Izquierda: El Teléfono / Calculadora -->
    <div class="col-telefono-calc">
        <h2 class="calc-header-title">✨CALCULADORA ✨</h2>
        
        <form method="POST" action="punto7.php">
            <div class="screen-container">
                <div class="screen-top-decor">
                    <span>DALE CLICK AL BOTÓN</span>
                    <span>100% 🩷</span>
                </div>
                
                <div class="profile-fields-list">
                    <div class="profile-field-row">
                        <span class="field-tag">NUM 1 :</span>
                        <input type="number" step="any" name="num1" required placeholder="0" value="<?php echo htmlspecialchars($num1Anterior); ?>">
                    </div>

                    <!-- Teclado de Selección Directa de Iconos -->
                    <div class="teclado-operaciones">
                        <label class="op-radio-label">
                            <input type="radio" name="operacion" value="suma" <?php echo $opAnterior === 'suma' ? 'checked' : ''; ?>>
                            <span class="op-box-icon">+</span>
                        </label>
                        <label class="op-radio-label">
                            <input type="radio" name="operacion" value="resta" <?php echo $opAnterior === 'resta' ? 'checked' : ''; ?>>
                            <span class="op-box-icon">-</span>
                        </label>
                        <label class="op-radio-label">
                            <input type="radio" name="operacion" value="multiplicacion" <?php echo $opAnterior === 'multiplicacion' ? 'checked' : ''; ?>>
                            <span class="op-box-icon">×</span>
                        </label>
                        <label class="op-radio-label">
                            <input type="radio" name="operacion" value="division" <?php echo $opAnterior === 'division' ? 'checked' : ''; ?>>
                            <span class="op-box-icon">÷</span>
                        </label>
                        <label class="op-radio-label">
                            <input type="radio" name="operacion" value="porcentaje" <?php echo $opAnterior === 'porcentaje' ? 'checked' : ''; ?>>
                            <span class="op-box-icon">%</span>
                        </label>
                    </div>

                    <div class="profile-field-row">
                        <span class="field-tag">NUM 2 :</span>
                        <input type="number" step="any" name="num2" required placeholder="0" value="<?php echo htmlspecialchars($num2Anterior); ?>">
                    </div>
                </div>
            </div>

            <button type="submit" name="calcular" class="btn-primary">[ Buscar resultado ]</button>
        </form>
    </div>

    <!-- Columna Derecha: Bloques de Hojas Asimétricos -->
    <div class="col-historial-hojas">
        
        <div class="panel-resultado-actual">
            <div class="result-box-title">Resultado actual:</div>
            <div class="alert-resultado">
                <?php echo $resultado !== '' ? htmlspecialchars($resultado) : '0.00'; ?>
            </div>
        </div>

        <div class="panel-historial-cuadricula">
            <h3>HISTORIAL 🩷</h3>
            
            <?php if (empty($historial)): ?>
                <p style="text-align: center; font-family: 'VT323', monospace; font-size: 1.3em; opacity: 0.6;">Sin registros previos.</p>
            <?php else: ?>
                <ul class="lista-historial">
                    <?php foreach (array_reverse($historial) as $item): ?>
                        <li><span>&gt;&gt;</span> <span><?php echo htmlspecialchars($item); ?></span></li>
                    <?php endforeach; ?>
                </ul>
                <form method="POST" action="punto7.php">
                    <button type="submit" name="borrar_historial" class="btn-secondary">LIMPIAR LOG</button>
                </form>
            <?php endif; ?>
        </div>
        
    </div>

    <div class="sticker-decor sticker-left"></div>
    <div class="sticker-decor sticker-right"></div>
    <div class="sticker-decor sticker-up"></div>
    <div class="sticker-decor sticker-down"></div>

    <div class="back-menu-box">
        <a href="../index.php" class="back-menu-link">← volver al menú</a>
    </div>

</div>

</body>
</html>
