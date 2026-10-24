<?php
class ConversorBinario
{
    private int $numero;

    public function __construct(int $numero)
    {
        $this->numero = $numero;
    }

    public function obtenerBinario(): string
    {
        $num = $this->numero;

        if ($num === 0) return "0";

        $esNegativo = false;
        if ($num < 0) {
            $esNegativo = true;
            $num = abs($num);
        }

        $binario = "";
        while ($num > 0) {
            $residuo = $num % 2;
            $binario = $residuo . $binario;
            $num = intdiv($num, 2);
        }

        return $esNegativo ? "-" . $binario : $binario;
    }
}

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

class ConversorController
{
    private GestorSesion $sesion;

    public function __construct()
    {
        $this->sesion = new GestorSesion();
    }

    public function procesarPeticion(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['numero'])) {
            $conversor = new ConversorBinario((int)$_POST['numero']);

            $this->sesion->guardar('res_p5', $conversor->obtenerBinario());
            $this->sesion->guardar('num_p5', $_POST['numero']);

            header("Location: punto5.php");
            exit();
        }
    }

    public function obtenerDatosVista(): array
    {
        $datos = [
            'resultadoBinario' => $this->sesion->obtener('res_p5', ''),
            'numeroAnterior'   => $this->sesion->obtener('num_p5', '')
        ];

        $this->sesion->eliminar('res_p5');
        $this->sesion->eliminar('num_p5');

        return $datos;
    }
}

$controlador = new ConversorController();
$controlador->procesarPeticion();

$datosVista = $controlador->obtenerDatosVista();
$resultadoBinario = $datosVista['resultadoBinario'];
$numeroAnterior   = $datosVista['numeroAnterior'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto 5 - Convertidor Binario</title>
    <link rel="stylesheet" href="../css/punto5.css">
</head>

<body>

    <div class="container-app">

        <div class="welcome-header">
            <h2>🌸 Conversión 🌸</h2>
            <span>convertidor de entero a binario</span>
        </div>

        <div class="gif-container">
            <img src="../resources/galleta.gif" alt="Decoración Álgebra" class="welcome-gif">
        </div>

        <form method="POST" action="punto5.php">
            <div class="profile-fields-list">
                <div class="profile-field-row">
                    <span class="field-tag">número :</span>
                    <input type="number" name="numero" required placeholder="ej: 25" value="<?php echo htmlspecialchars($numeroAnterior); ?>">
                </div>
            </div>

            <p class="helper-text">--Escribe un número entero (Base 10) para calcularlo.</p>

            <button type="submit" class="btn-primary">convertir a binario</button>
        </form>

        <?php if ($resultadoBinario !== ""): ?>
            <div class="alert">
                <div class="result-title">Conversión calculada</div>
                <div class="result-field">
                    <span class="result-label">Binario Resultante =</span>
                    <span>{ <?php echo htmlspecialchars($resultadoBinario); ?> }</span>
                </div>
            </div>
        <?php endif; ?>

        <div class="sticker-decor sticker-left"></div>
        <div class="sticker-decor sticker-right"></div>
        <div class="sticker-decor sticker-up"></div>
        <div class="sticker-decor sticker-down"></div>

        <div class="back-menu-box">
            <a href="../index.php" class="back-menu-link">
                ← volver al menú
            </a>
        </div>

    </div>

</body>

</html>