<?php
$estado = isset($_GET['estado']) ? $_GET['estado'] : 'pausa';
$track = isset($_GET['track']) ? (int)$_GET['track'] : 1;
$canciones = [
    1 => 'bell-song.mp3',
    2 => 'boba-tea.mp3',
    3 => 'chiikawa.mp3'
];

$trackAnterior = $track - 1;
if ($trackAnterior < 1) {
    $trackAnterior = count($canciones);
}

$trackSiguiente = $track + 1;
if ($trackSiguiente > count($canciones)) {
    $trackSiguiente = 1;
}

$autoplay = ($estado === 'play') ? 'autoplay' : '';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página principal - Taller PHP</title>
    <link rel="stylesheet" href="css/menu.css">
</head>

<body>

    <div class="wrapper">

        <div class="header-banner">
            <h1>TALLER DE PHP</h1>
            <div class="decor-line">🌸 • X • 🌸 • O </div>
        </div>

        <div class="content-layout">

            <div class="welcome-box">
                <div class="welcome-header">
                    <h2>¡Bienvenido!</h2>
                    <span>Veamos algo de Estadística</span>
                </div>

                <p>🌸 <strong>Taller Práctico:</strong> En esta aplicación interactiva podras hacer unas cuantas operaciones matemáticas.</p>

                <div class="gif-container">
                    <img src="resources/Sy.gif" alt="Chibi" class="welcome-gif">
                </div>

                <p>¡Haz clic directamente sobre el botón dependiendo de lo que quieras hacer!</p>
            </div>

            <div class="image-buttons-section">
                <div class="links-title">🎀 Aplicaciones: </div>

                <div class="list-buttons">

                    <!-- Punto 1 -->
                    <div class="menu-item-row">
                        <a href="operaciones/punto1.php" class="pure-image-link" title="Acrónimos">
                            <img src="resources/conejo.png" alt="Icono Punto 1">
                        </a>
                        <div class="item-description">
                            <h3>Acrónimos</h3>
                            <p>Convierte cadenas de texto extensas en sus respectivos acrónimos.</p>
                        </div>
                    </div>

                    <!-- Punto 2 -->
                    <div class="menu-item-row">
                        <a href="operaciones/punto2.php" class="pure-image-link" title="Series">
                            <img src="resources/conejo.png" alt="Icono Punto 2">
                        </a>
                        <div class="item-description">
                            <h3>Series</h3>
                            <p>Calcula sucesiones dinámicas de Fibonacci o factoriales matemáticos.</p>
                        </div>
                    </div>

                    <!-- Punto 3 -->
                    <div class="menu-item-row">
                        <a href="operaciones/punto3.php" class="pure-image-link" title="Estadística">
                            <img src="resources/conejo.png" alt="Icono Punto 3">
                        </a>
                        <div class="item-description">
                            <h3>Estadística</h3>
                            <p>Mapea inmediatamente el Promedio, la Mediana y la Moda de números.</p>
                        </div>
                    </div>

                    <!-- Punto 4 -->
                    <div class="menu-item-row">
                        <a href="operaciones/punto4.php" class="pure-image-link" title="Conjuntos">
                            <img src="resources/conejo.png" alt="Icono Punto 4">
                        </a>
                        <div class="item-description">
                            <h3>Conjuntos</h3>
                            <p>Calcula la Unión, Intersección y Diferencias de conjuntos enteros.</p>
                        </div>
                    </div>

                    <!-- Punto 5 -->
                    <div class="menu-item-row">
                        <a href="operaciones/punto5.php" class="pure-image-link" title="Binario">
                            <img src="resources/conejo.png" alt="Icono Punto 5">
                        </a>
                        <div class="item-description">
                            <h3>Binario</h3>
                            <p>Transforma números enteros del sistema decimal a binario.</p>
                        </div>
                    </div>

                    <!-- Punto 6 -->
                    <div class="menu-item-row">
                        <a href="operaciones/punto6.php" class="pure-image-link" title="Árboles">
                            <img src="resources/conejo.png" alt="Icono Punto 6">
                        </a>
                        <div class="item-description">
                            <h3>Árboles Binarios</h3>
                            <p>Estructura y grafica un árbol binario usando Preorden e Inorden.</p>
                        </div>
                    </div>

                    <!-- Punto 7 -->
                    <div class="menu-item-row">
                        <a href="operaciones/punto7.php" class="pure-image-link" title="Calculadora">
                            <img src="resources/conejo.png" alt="Icono Punto 7">
                        </a>
                        <div class="item-description">
                            <h3>Calculadora</h3>
                            <p>Calculadora estándar con almacenamiento de historial en sesión PHP.</p>
                        </div>
                    </div>

                </div>

                <div class="music-player-widget">
                    <div class="player-top">
                        <span class="music-note-icon <?php echo ($estado === 'play') ? 'is-playing' : ''; ?>">🎵</span>
                        <div class="song-info">
                            <span class="song-title"><?php echo $canciones[$track]; ?></span>
                            <span class="song-artist">pista <?php echo $track; ?> de 3</span>
                        </div>
                    </div>

                    <div class="native-audio-container">
                        <audio src="resources/musica/<?php echo $canciones[$track]; ?>" controls loop <?php echo $autoplay; ?> style="width: 100%; border-radius: 20px;"></audio>
                    </div>

                    <div class="player-controls">
                        <a href="index.php?estado=<?php echo $estado; ?>&track=<?php echo $trackAnterior; ?>" class="control-btn-link" title="Anterior">Anterior</a>
                        <a href="index.php?estado=<?php echo $estado; ?>&track=<?php echo $trackSiguiente; ?>" class="control-btn-link" title="Siguiente">Siguiente</a>
                    </div>
                </div>
            </div>

        </div>

        <div class="footer-credits">
            🎀 hecho con estética cutecore 🎀
        </div>

    </div>

</body>

</html>