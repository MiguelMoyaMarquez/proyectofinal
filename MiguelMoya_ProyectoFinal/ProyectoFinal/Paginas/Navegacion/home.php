<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Título de tu página</title>
    <link rel="stylesheet" href="../../CSS/estilos.css">
</head>

<body>
    <?php
    session_start();

    $db_host = "localhost:3308";
    $db_name = "proyecto";
    $db_user = "root";
    $db_pass = "";
    $conexion = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if (!$conexion) {
        echo "Fallo al conectar con la base de datos";
        exit();
    }


    if ($_SESSION != null) {
        $nombre_usuario = $_SESSION['usuario']['nombre'];
        $correo = $_SESSION['usuario']['correo'];
        $dinero = $_SESSION['usuario']['dinero'];

        $id_usuario_query = "SELECT id FROM usuarios WHERE correo='$correo'";
        $resultado_id_usuario = $conexion->query($id_usuario_query);
        $fila_id_usuario = $resultado_id_usuario->fetch_assoc();
        $id_usuario = $fila_id_usuario["id"];
    }


    ?>

    <header>
        <div class='logo'>
            <img src='../../Imagenes/logo.png' alt='Logotipo'>
        </div>
        <button class='menu-toggle'></button>
        <nav class='menu'>
            <ul>
                <li><a href='home.php' style='background-color: #555;'>Inicio</a></li>
                <li><a href='biblioteca.php'>Biblioteca</a></li>
                <li><a href='documentacion.php'>Documentacion</a></li>
                <li><a href='estadisticas.php'>Estadisticas</a></li>
            </ul>
        </nav>
        <a href="../proceso_perfil.php?operacion=accesoP">
            <div class='perfil'>
                <img src='../../Imagenes/logo.png' alt='Imagen_perfil'>
                <?php
                if (isset($_SESSION['usuario']['nombre'])) {
                    echo "<p>$nombre_usuario</p>";
                    echo "<p>==>$dinero €</p>";
                } else {
                    echo "<p>Iniciar Sesion</p>";
                }
                ?>
            </div>
        </a>
    </header>

    <div class='contenido'>
        <a href="#">
            <h2 style="text-align:center">DESTACADOS</h2>
        </a>
        <?php
        echo "<div class='productosDestacados'>";

        $total_juegos_consulta = "SELECT COUNT(DISTINCT id_juego) AS total_juegos FROM usuarios_adquisiciones";
        $resultado_total_juegos = $conexion->query($total_juegos_consulta);
        $fila_total_juegos = $resultado_total_juegos->fetch_assoc();
        $total_juegos = $fila_total_juegos["total_juegos"];

        if ($total_juegos > 4) {
            $total_juegos = 4;
        }

        for ($i = 0; $i < $total_juegos; $i++) {
            $destacado_consulta = "
        SELECT id_juego, COUNT(*) AS cantidad_adquisiciones 
        FROM usuarios_adquisiciones 
        GROUP BY id_juego 
        ORDER BY cantidad_adquisiciones DESC 
        LIMIT 1 OFFSET $i";

            $destacado_resultado = $conexion->query($destacado_consulta);

            if ($destacado_resultado && $destacado_resultado->num_rows > 0) {
                $destacado_fila = $destacado_resultado->fetch_assoc();
                $destacado = $destacado_fila["id_juego"];

                if (isset($_SESSION["usuario"])) {
                    $consulta_adquisicion = "SELECT count(*) AS cantidad FROM usuarios_adquisiciones WHERE id_usuario='$id_usuario' AND id_juego='$destacado'";
                    $resultado_adquisicion = $conexion->query($consulta_adquisicion);
                    $fila_adquisicion = $resultado_adquisicion->fetch_assoc();
                    $cantidad_adquisiciones = $fila_adquisicion["cantidad"];
                } else {
                    $cantidad_adquisiciones = 0;
                }
                $juego_consulta = "SELECT portada, nombre, precio FROM juegos WHERE id = '$destacado'";
                $juego_resultado = $conexion->query($juego_consulta);

                if ($juego_resultado && $juego_resultado->num_rows > 0) {
                    $juego_fila = $juego_resultado->fetch_assoc();
                    $portada = $juego_fila["portada"];
                    $nombre = $juego_fila["nombre"];
                    $precio = $juego_fila["precio"];
                    $checked = ($i == 0) ? 'checked' : '';

                    echo "<a href='../juego.php?id_juego=$destacado'>
                    <div class='productoDestacado' id='imgDestacado_$i'>
                        <img src='$portada'>
                        <p>$nombre</p>";
                    if ($cantidad_adquisiciones > 0) {
                        echo "<p style='background-color: green;'>Adquerido</p>";
                    } else {
                        echo "<p>$precio €</p>";
                    }
                    echo "</div>
                    <input type='radio' name='navegacion' id='radio_$i' class='boton_destacado' $checked>
                  </a>";
                }
            } else {
                echo "No se encontró un juego destacado en el offset $i<br>";
            }
        }
        echo "<div style='padding: 150px 50px;'></div>";
        ?>
    </div>

    <div class="recomendados">
        <h2>RECOMENDADOS</h2>
        <div class='productosR'>
            <?php
            $limite = 4;

            if (isset($_SESSION['usuario'])) {
                // Obtener el género más jugado por el usuario
                $genero_consulta = "SELECT j.genero, COUNT(ua.id_juego) AS cantidad 
                               FROM usuarios_adquisiciones ua 
                               JOIN juegos j ON ua.id_juego = j.id 
                               WHERE ua.id_usuario = '$id_usuario' 
                               GROUP BY j.genero 
                               ORDER BY cantidad DESC 
                               LIMIT 1";

                $genero_resultado = $conexion->query($genero_consulta); // Ejecutamos la consulta una sola vez
            
                if ($genero_resultado && $genero_resultado->num_rows > 0) { // Verificamos si hay resultados
            
                    $genero_fila = $genero_resultado->fetch_assoc();
                    $genero_destacado = $genero_fila['genero'];

                    $juegos_consulta = "SELECT id, portada, nombre, precio 
                                    FROM juegos 
                                    WHERE genero = '$genero_destacado'
                                    LIMIT $limite";

                    $juegos_resultado = $conexion->query($juegos_consulta);

                    $juegos = [];
                    while ($juego_fila = $juegos_resultado->fetch_assoc()) {
                        $juegos[] = $juego_fila;
                    }

                    for ($j = 0; $j < 2; $j++) { // Bucle externo para repetir dos veces
                        foreach ($juegos as $juego_fila) {
                            $id_juego = $juego_fila['id'];
                            $portada = $juego_fila['portada'];
                            $nombre = $juego_fila['nombre'];
                            $precio = $juego_fila['precio'];

                            $consulta_adquisicion = "SELECT count(*) AS cantidad FROM usuarios_adquisiciones WHERE id_usuario='$id_usuario' AND id_juego='$id_juego'";
                            $resultado_adquisicion = $conexion->query($consulta_adquisicion);
                            $fila_adquisicion = $resultado_adquisicion->fetch_assoc();
                            $cantidad_adquisiciones = $fila_adquisicion["cantidad"];

                            echo "<a href='../juego.php?id_juego=$id_juego'>
                                <div class='productoR'>
                                    <img src='$portada' alt='Portada'>
                                    <p>$nombre</p>";
                            if ($cantidad_adquisiciones > 0) {
                                echo "<p style='background-color: green;'>Adquirido</p>";
                            } else {
                                echo "<p>$precio €</p>";
                            }
                            echo "</div>
                              </a>";
                        }
                    }
                } else {
                    echo "<p>Compra un juego para empezar a recibir recomendaciones</p>";
                }
            }
            ?>
        </div>
    </div>



    <?php
    $resultado = $conexion->query("SELECT COUNT(DISTINCT genero) AS total_generos FROM juegos");
    $fila = $resultado->fetch_assoc();
    $total_generos = $fila["total_generos"];

    for ($i = 0; $i < $total_generos; $i++) {
        $consulta = "SELECT genero FROM juegos GROUP BY genero LIMIT 1 OFFSET $i";
        $resultado = $conexion->query($consulta);
        $fila = $resultado->fetch_assoc();
        $genero = $fila["genero"];

        echo "<a href='../filtradoGenero.php?genero=$genero'><h2>$genero ></h2></a>";
        echo "<div class='productos'>";

        $total_juegos_consulta = "SELECT COUNT(*) AS total_juegos FROM juegos WHERE genero = '$genero'";
        $resultado_total_juegos = $conexion->query($total_juegos_consulta);
        $fila_total_juegos = $resultado_total_juegos->fetch_assoc();
        $total_juegos = $fila_total_juegos["total_juegos"];

        if ($total_juegos > 4) {
            $total_juegos = 4;
        }

        for ($j = 0; $j < $total_juegos; $j++) {
            $portada_consulta = "SELECT portada FROM juegos WHERE genero = '$genero' LIMIT 1 OFFSET $j";
            $portada_resultado = $conexion->query($portada_consulta);
            $portada_fila = $portada_resultado->fetch_assoc();
            $portada = $portada_fila["portada"];

            $nombre_consulta = "SELECT nombre FROM juegos WHERE genero = '$genero' LIMIT 1 OFFSET $j";
            $nombre_resultado = $conexion->query($nombre_consulta);
            $nombre_fila = $nombre_resultado->fetch_assoc();
            $nombre = $nombre_fila["nombre"];

            $precio_consulta = "SELECT precio FROM juegos WHERE genero = '$genero' LIMIT 1 OFFSET $j";
            $precio_resultado = $conexion->query($precio_consulta);
            $precio_fila = $precio_resultado->fetch_assoc();
            $precio = $precio_fila["precio"];

            $id_juego_query = "SELECT id FROM juegos WHERE genero = '$genero' LIMIT 1 OFFSET $j";
            $resultado_id_juego = $conexion->query($id_juego_query);
            $fila_id_juego = $resultado_id_juego->fetch_assoc();
            $id_juego = $fila_id_juego["id"];
            if ($_SESSION != null) {


                $consulta_adquisicion = "SELECT count(*) AS cantidad FROM usuarios_adquisiciones WHERE id_usuario='$id_usuario' AND id_juego='$id_juego'";
                $resultado_adquisicion = $conexion->query($consulta_adquisicion);
                $fila_adquisicion = $resultado_adquisicion->fetch_assoc();
                $cantidad_adquisiciones = $fila_adquisicion["cantidad"];
            } else {
                $cantidad_adquisiciones = 0;
            }

            echo "
                <a href='../juego.php?id_juego=$id_juego'> 
                <div class='producto'>
                            <img src='$portada' alt='Portada'>
                            <p>$nombre</p>";
            if ($cantidad_adquisiciones > 0) {
                echo "<p style='background-color: green;'>Adquerido</p>";

            } else {
                echo "<p>$precio € </p>";
            }
            echo "</div>
                        </a>";
        }
        echo "</div>";
    }

    mysqli_close($conexion);
    ?>
    </div>

    <div class="pie">
        <p>Correo: proyectomiguel@gmail.com</p>
        <p>Direccion de la empresa: Pl. Virgen Milagrosa, 11, Málaga-Este, 29017 Málaga, España.</p>
        <a  href="../folCV.html">Aviso legal</a>
        <a href="../folCV.html">Politica de privacidad</a>
    </div>

    <script src="../../Java/ocultar.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radioButtons = document.querySelectorAll('input[name="navegacion"]');
            const productosDestacados = document.querySelectorAll('.productoDestacado');

            function mostrarProducto(radioId) {
                // Encontrar el div correspondiente al radio button seleccionado
                const productoSeleccionado = document.querySelector(`.productoDestacado#${radioId.replace('radio_', 'imgDestacado_')}`);

                // Ocultar todos los productos destacados y sus elementos
                productosDestacados.forEach(producto => {
                    producto.style.opacity = 0;
                    const img = producto.querySelector('img');
                    const paragraphs = producto.querySelectorAll('p');
                    if (img) img.style.opacity = 0;
                    paragraphs.forEach(p => p.style.opacity = 0);
                });

                // Mostrar el producto destacado seleccionado (si se encontró)
                if (productoSeleccionado) {
                    productoSeleccionado.style.opacity = 1;
                    const img = productoSeleccionado.querySelector('img');
                    const paragraphs = productoSeleccionado.querySelectorAll('p');
                    if (img) img.style.opacity = 1;
                    paragraphs.forEach(p => p.style.opacity = 1);
                }
            }

            // Mostrar el primer producto destacado al cargar la página
            mostrarProducto('radio_0');

            // Agregar event listeners a los radio buttons
            radioButtons.forEach(radio => {
                radio.addEventListener('change', () => {
                    if (radio.checked) {
                        mostrarProducto(radio.id);
                    }
                });
            });
        });
    </script>


</body>

</html>