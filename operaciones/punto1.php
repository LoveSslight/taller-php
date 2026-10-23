<?php
// CLASE CON LA LÓGICA EN PROGRAMACIÓN ORIENTADA A OBJETOS (POO)
class Acronimo {
    private string $frase;

    public function __construct(string $frase) {
        $this->frase = $frase;
    }

    public function generar(): string {
        //Quitar signos de puntuación
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

$resultado = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['frase'])) {
    $procesador = new Acronimo($_POST['frase']);
    $resultado = $procesador->generar();
}
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
        <h2>punto 1</h2>
        <span>Convierte tus frases a acrónimos</span>
    </div>

    <!-- El formulario apunta a su propio archivo de manera dinámica -->
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for="frase">Ingresa un nombre largo o una frase:</label>
        <input type="text" id="frase" name="frase" required placeholder="Ej: Así es el destino" value="<?php echo isset($_POST['frase']) ? htmlspecialchars($_POST['frase']) : ''; ?>">
        
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