<?php
class Nodo
{
    //puede o contener de forma valida un objeto se nulo
    public string $valor;
    public ?Nodo $izq = null;
    public ?Nodo $der = null;

    public function __construct(string $valor)
    {
        $this->valor = $valor;
    }
}

class ConstructorArbol
{
    private array $preorden;
    private array $inorden;
    private int $preIndice = 0;

    public function __construct(array $preorden, array $inorden)
    {
        $this->preorden = $preorden;
        $this->inorden = $inorden;
    }

    public function construir(): ?Nodo
    {
        //inicia un contador en 0 que con cada letra avanza
        $this->preIndice = 0;
        return $this->construirMapeo(0, count($this->inorden) - 1);
    }

        private function construirMapeo(int $inInicio, int $inFin): ?Nodo
    {
        if ($inInicio > $inFin) {
            return null;
        }

        // se verifica que el indice preorden exista
        if (!isset($this->preorden[$this->preIndice])) {
            return null;
        }

        $valorNodo = $this->preorden[$this->preIndice++];
        $nodo = new Nodo($valorNodo);

        if ($inInicio === $inFin) {
            return $nodo;
        }

        // Se busca la raiz SOLO EN EL RANGO ACTUAL
        $inIndice = -1;
        for ($i = $inInicio; $i <= $inFin; $i++) {
            if ($this->inorden[$i] === $valorNodo) {
                $inIndice = $i;
                break;
            }
        }

        // Si por un recorrido inválido no se encuentra el nodo en el rango, SO SE RROMPE EL SISTEMA
        if ($inIndice === -1) {
            return $nodo; 
        }

        // Todo antes de la raíz está en la izquierda y viceversa
        $nodo->izq = $this->construirMapeo($inInicio, $inIndice - 1);
        $nodo->der = $this->construirMapeo($inIndice + 1, $inFin);

        return $nodo;
    }

    //convertir en texto los resultados
    public function obtenerVistaEstructura(?Nodo $raiz): string
    {
        if ($raiz === null) return "Árbol vacío";

        $salida = "";
        $cola = [[$raiz, 0]];
        $niveles = [];

        while (count($cola) > 0) {
            list($nodoActual, $nivel) = array_shift($cola);

            if (!isset($niveles[$nivel])) {
                $niveles[$nivel] = [];
            }

            $niveles[$nivel][] = $nodoActual->valor;

            if ($nodoActual->izq !== null) $cola[] = [$nodoActual->izq, $nivel + 1];
            if ($nodoActual->der !== null) $cola[] = [$nodoActual->der, $nivel + 1];
        }

        foreach ($niveles as $n => $valores) {
            $salida .= "Nivel $n: " . implode("   ", $valores) . "\n";
        }

        return $salida;
    }

    //recorre el objeto para estructurar los nodos en html
    public function renderizarArbolGrafico(?Nodo $nodo): string
    {
        if ($nodo === null) return "";

        $html = '<li>';
        $html .= '<span class="arbol-nodo">' . htmlspecialchars($nodo->valor) . '</span>';

        if ($nodo->izq !== null || $nodo->der !== null) {
            $html .= '<ul>';
            $html .= $nodo->izq !== null ? $this->renderizarArbolGrafico($nodo->izq) : '<li><span class="arbol-nodo" style="opacity:0.2; border-style:dashed;">-</span></li>';
            $html .= $nodo->der !== null ? $this->renderizarArbolGrafico($nodo->der) : '<li><span class="arbol-nodo" style="opacity:0.2; border-style:dashed;">-</span></li>';
            $html .= '</ul>';
        }

        $html .= '</li>';
        return $html;
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

//procesa lo ingresado y lo almacena para que con flexbox se grafique
class ArbolController
{
    private GestorSesion $sesion;

    public function __construct()
    {
        $this->sesion = new GestorSesion();
    }

    public function procesarPeticion(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['preorden']) && !empty($_POST['inorden'])) {
            $preArr = array_map('trim', explode('-', $_POST['preorden']));
            $inArr = array_map('trim', explode('-', $_POST['inorden']));

            if (count($preArr) === count($inArr) && count($preArr) > 0) {
                $constructor = new ConstructorArbol($preArr, $inArr);
                $raiz = $constructor->construir();
                $estructura = $constructor->obtenerVistaEstructura($raiz);
                $grafico = '<ul>' . $constructor->renderizarArbolGrafico($raiz) . '</ul>';
            } else {
                $estructura = "Error: Los recorridos deben tener el mismo número de elementos.";
                $grafico = "";
            }

            $this->sesion->guardar('res_p6', $estructura);
            $this->sesion->guardar('graf_p6', $grafico);
            $this->sesion->guardar('pre_p6', $_POST['preorden']);
            $this->sesion->guardar('in_p6', $_POST['inorden']);
            $this->sesion->guardar('post_p6', $_POST['postorden'] ?? '');

            header("Location: punto6.php");
            exit();
        }
    }

    public function obtenerDatosVista(): array
    {
        $datos = [
            'estructuraArbol'   => $this->sesion->obtener('res_p6', ''),
            'graficoArbol'      => $this->sesion->obtener('graf_p6', ''),
            'preordenAnterior'  => $this->sesion->obtener('pre_p6', 'A-B-D-E-C'),
            'inordenAnterior'   => $this->sesion->obtener('in_p6', 'D-B-E-A-C'),
            'postordenAnterior' => $this->sesion->obtener('post_p6', '')
        ];

        $this->sesion->eliminar('res_p6');
        $this->sesion->eliminar('graf_p6');
        $this->sesion->eliminar('pre_p6');
        $this->sesion->eliminar('in_p6');
        $this->sesion->eliminar('post_p6');

        return $datos;
    }
}

$controlador = new ArbolController();
$controlador->procesarPeticion();

$datosVista = $controlador->obtenerDatosVista();
$estructuraArbol   = $datosVista['estructuraArbol'];
$graficoArbol      = $datosVista['graficoArbol'];
$preordenAnterior  = $datosVista['preordenAnterior'];
$inordenAnterior   = $datosVista['inordenAnterior'];
$postordenAnterior = $datosVista['postordenAnterior'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto 6 - Árbol Binario</title>
    <link rel="stylesheet" href="../css/punto6.css">
</head>

<body>

    <div class="container-app">

        <div class="welcome-header">
            <h2>🌸 árbol binario 🌸</h2>
            <span>reconstrucción por niveles</span>
        </div>

        <div class="intro-box">
            <p><strong>En caso de que no sepas que es</strong></p>
            <p>Un arbol binario una estructura de datos jerárquica donde cada "burbuja" (nodo) puede tener como máximo dos hijos: uno a la izquierda y otro a la derecha.</p>
            <p><strong>¿Cómo usar esta aplicación? ✨</strong></p>
            <p>Ingresa las secuencias de nodos separadas por guiones en los campos de abajo y presiona el botón. El sistema leerá el orden en que se visitan las burbujas para volver a tejer las ramas gráficamente.</p>
        </div>

        <div class="gif-container">
            <img src="../resources/raf.gif" alt="Decoración Álgebra" class="welcome-gif">
        </div>

        <form method="POST" action="punto6.php">
            <div class="profile-fields-list">
                <div class="profile-field-row">
                    <span class="field-tag">recorrido preorden :</span>
                    <input type="text" name="preorden" required placeholder="Ej: A-B-D-E-C" value="<?php echo htmlspecialchars($preordenAnterior); ?>">
                </div>

                <div class="profile-field-row">
                    <span class="field-tag">recorrido inorden :</span>
                    <input type="text" name="inorden" required placeholder="Ej: D-B-E-A-C" value="<?php echo htmlspecialchars($inordenAnterior); ?>">
                </div>

                <div class="profile-field-row">
                    <span class="field-tag">recorrido postorden (opcional) :</span>
                    <input type="text" name="postorden" placeholder="Ej: D-E-B-C-A" value="<?php echo htmlspecialchars($postordenAnterior); ?>">
                </div>
            </div>

            <p class="helper-text">--Escribe los elementos separados por guiones (-).</p>

            <button type="submit" class="btn-primary">construir árbol</button>
        </form>

        <?php if ($estructuraArbol !== ""): ?>
            <div class="flex-resultados">

                <?php if (!empty($graficoArbol) && strpos($estructuraArbol, 'Error') === false): ?>
                    <div class="col-grafico">
                        <div class="result-title">mapa gráfico</div>
                        <div class="arbol-grafico">
                            <?php echo $graficoArbol; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="col-texto">
                    <div class="result-title" style="background: transparent;">salida en texto</div>
                    <div class="result-structure"><?php echo htmlspecialchars($estructuraArbol); ?></div>
                </div>

            </div>
        <?php endif; ?>

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