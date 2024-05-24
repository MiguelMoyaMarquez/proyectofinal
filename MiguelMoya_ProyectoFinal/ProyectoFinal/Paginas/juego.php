<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../CSS/estilos.css">
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
    if (isset($_SESSION['usuario'])) {
        $nombre_usuario = $_SESSION['usuario']['nombre'];
        $correo = $_SESSION['usuario']['correo'];
        $id_usuario_query = "SELECT id FROM usuarios WHERE correo='$correo'";
        $resultado_id_usuario = $conexion->query($id_usuario_query);
        $fila_id_usuario = $resultado_id_usuario->fetch_assoc();
        $id_usuario = $fila_id_usuario["id"];
    }
    ?>

    <header>
        <div class='logo'>
            <img src='../Imagenes/logo.png' alt='Logotipo'>
        </div>
        <button class='menu-toggle'>></button>
        <nav class='menu'>
            <ul>
                <li><a href='Navegacion/home.php'>Inicio</a></li>
                <li><a href='Navegacion/biblioteca.php'>Biblioteca</a></li>
                <li><a href='Navegacion/documentacion.php'>Documentacion</a></li>
                <li><a href='Navegacion/estadisticas.php'>Estadisticas</a></li>
            </ul>
        </nav>
        <a href="proceso_perfil.php?operacion=accesoP">
            <div class='perfil'>
                <img src='../Imagenes/logo.png' alt='Imagen_perfil'>
                <?php
                if (isset($_SESSION['usuario']['nombre'])) {
                    echo "<p>$nombre_usuario</p>";
                } else {
                    echo "<p>Iniciar Sesion</p>";
                }
                ?>
            </div>
        </a>
    </header>
    <div class="contenidoMostrado">
        <div class="contenidoMostrado-elementos">
            <?php
            $idJuego = $_GET["id_juego"];

            $enlacesVideo_consulta = "SELECT videos FROM juegos WHERE id = $idJuego";
            $enlacesVideo_resultado = $conexion->query($enlacesVideo_consulta);
            $enlacesVideo_fila = $enlacesVideo_resultado->fetch_assoc();
            $enlacesVideo = $enlacesVideo_fila["videos"];
            $enlacesVideo = explode(",", $enlacesVideo);


            $enlacesImagen_consulta = "SELECT imagenes FROM juegos WHERE id = $idJuego";
            $enlacesImagen_resultado = $conexion->query($enlacesImagen_consulta);
            $enlacesImagen_fila = $enlacesImagen_resultado->fetch_assoc();
            $enlacesImagen = $enlacesImagen_fila["imagenes"];


            $portada_consulta = "SELECT portada FROM juegos WHERE id = '$idJuego'";
            $portada_resultado = $conexion->query($portada_consulta);
            $portada_fila = $portada_resultado->fetch_assoc();
            $portada = $portada_fila["portada"];

            $descripcion_consulta = "SELECT descripcion FROM juegos WHERE id = '$idJuego'";
            $descripcion_resultado = $conexion->query($descripcion_consulta);
            $descripcion_fila = $descripcion_resultado->fetch_assoc();
            $descripcion = $descripcion_fila["descripcion"];

            $enlacesImagen = explode(",", $enlacesImagen);
            if (!empty($enlacesVideo)) {
                echo "<video src='$enlacesVideo[0]' alt='contenidoGrande' autoplay controls>";
            } elseif (!empty($enlacesImagen)) {
                echo "<img src='$enlacesImagen[0]' alt='contenidoGrande'>";
            }

            echo " </div>
       <div class='contenidoMostrado-portada'>
       <img src='$portada' alt='portada'>
       <p>$descripcion</p>
       </div>";
            ?>

        </div>

        <div class="galeriaJuego">
            <?php

            echo "<div class='botones_juego'>";
            $count = 0;
            foreach ($enlacesVideo as $enlace) {
                $exixte = get_headers($enlace);
                if ($exixte) {
                    $count++;
                    $videoId = "myVideo" . $enlace;
                    echo "<a href='#'><div class='boton_juego '><div class='video-container'>
            <video src='$enlace' alt='Video_Juego' width='600' height='377' id='$videoId'></video>
            <img src='http://localhost/ProyectoFinal/Imagenes/iconoR_video.png' alt='Imagen superpuesta' class='overlay-image'>
          </div></div></a>";

                } else {
                    echo "<img scr='../Imagenes/ImageNotFound.jpg' alt='Error_Imagen'/>";
                }
            }


            foreach ($enlacesImagen as $enlace) {

                echo "<a href='#'><div class='boton_juego'><img src='$enlace'  alt='Imagen_Juego' width='600' height='377'/></div></a>'";

            }

            echo "<div id='contenido-expandido'></div>";

            echo "</div>";
            ?>

        </div>

        <div class="comprar">
            <?php
            //Obtenemos le nombre del juego
            $titulo_consulta = "SELECT nombre FROM juegos WHERE id = '$idJuego'";
            $titulo_resultado = $conexion->query($titulo_consulta);
            $titulo_fila = $titulo_resultado->fetch_assoc();
            $titulo = $titulo_fila["nombre"];
            //Obtenemos el precio
            $precio_consulta = "SELECT precio FROM juegos WHERE id = '$idJuego'";
            $precio_resultado = $conexion->query($precio_consulta);
            $precio_fila = $precio_resultado->fetch_assoc();
            $precio = $precio_fila["precio"];

            if (!isset($_SESSION['usuario'])) {
                echo "<p>Comprar $titulo</p>
            <div class='comprarPrecio'>
            <p>$precio €</p>
           <a href='#cC'> <div class='comprarPrecioBoton'
            <p>Comprar</p>
            </div></a>
            </div>
            ";
            } else {
                $consulta_adquisicion = "SELECT count(*) AS cantidad FROM usuarios_adquisiciones WHERE id_usuario='$id_usuario' AND id_juego='$idJuego'";
                $resultado_adquisicion = $conexion->query($consulta_adquisicion);
                $fila_adquisicion = $resultado_adquisicion->fetch_assoc();
                $cantidad_adquisiciones = $fila_adquisicion["cantidad"];
                if ($cantidad_adquisiciones > 0) {
                    echo "<p style='background-color: green; text-align: center; transform: translate(0px,35px);'>Adquirido</p>";

                } else {
                    echo "<p>Comprar $titulo</p>
                    <div class='comprarPrecio'>
                    <p>$precio €</p>
                   <a href='#cC'> <div class='comprarPrecioBoton'
                    <p>Comprar</p>
                    </div></a>
                    </div>
                    ";
                }
            }
            ?>
        </div>



        <div class="confirmarCompra" id="cC">
            <h2>Confirmar Compra</h2>
            <p>¿Estás seguro de que quieres comprar este producto para esta cuenta?</p>
            <div class="opcionesConfirmarCompra">
                <a href="#">
                    <div class="botonOpcionConfirmarCompra">
                        <p>Cancelar</p>
                    </div>
                </a>
                <?php
                echo "<a href='proceso_perfil.php?operacion=comprar&juego=$idJuego'>";
                ?>
                <div class="botonOpcionConfirmarCompra">
                    <p>Confrimar</p>
                </div>
                </a>

            </div>
            <div class="fondoNegro" id="cC">
               
            </div>

        </div>

        <div class="pie">
        <p>Correo: proyectomiguel@gmail.com</p>
        <p>Direccion de la empresa: Pl. Virgen Milagrosa, 11, Málaga-Este, 29017 Málaga, España.</p>
        <a  href="folCV.html">Aviso legal</a>
        <a href="folCV.html">Politica de privacidad</a>
    </div>

        <script>
            const contenidoMostradoElementos = document.querySelector(".contenidoMostrado-elementos");
            const galeriaJuego = document.querySelector(".galeriaJuego");

            let currentContent = null;

            galeriaJuego.addEventListener("click", function (event) {
                const target = event.target;

                // Verificar si el clic fue en un elemento válido (imagen o video)
                if (target.tagName === "IMG" || target.tagName === "VIDEO") {

                    // Verificar si el contenido ya está mostrado para evitar replicación innecesaria
                    if (currentContent && currentContent.src === target.src) {
                        return;
                    }

                    contenidoMostradoElementos.innerHTML = ""; // Vaciar solo contenidoMostrado-elementos

                    // Verificar si es una imagen y NO es la imagen de "play"
                    if (target.tagName === "IMG" && !target.classList.contains("overlay-image")) {
                        contenidoMostradoElementos.appendChild(target.cloneNode(true));
                    } else if (target.tagName === "VIDEO" || (target.tagName === "IMG" && target.classList.contains("overlay-image"))) {
                        // Manejar videos y clics en la imagen de "play"
                        let videoTarget = target;
                        if (target.tagName === "IMG") {
                            videoTarget = target.closest(".video-container")?.querySelector("video");
                        }

                        if (videoTarget) {
                            const replica = videoTarget.cloneNode(true);
                            replica.currentTime = 0;
                            replica.autoplay = true;
                            replica.controls = true;
                            contenidoMostradoElementos.appendChild(replica);
                        }
                    }
                }
            });

            // Seleccionamos todos los elementos de video
            const videos = document.querySelectorAll('.galeriaJuego video');

            // Iteramos sobre cada video
            videos.forEach(video => {
                video.addEventListener('loadedmetadata', function () {
                    // Calculamos la mitad del tiempo del video
                    var mitadTiempo = video.duration / 1.5;
                    // Establecemos el tiempo actual a la mitad
                    video.currentTime = mitadTiempo;
                    // Pausamos el video para mostrar el fotograma
                    video.pause();
                });
            });

        </script>

</body>

</html>