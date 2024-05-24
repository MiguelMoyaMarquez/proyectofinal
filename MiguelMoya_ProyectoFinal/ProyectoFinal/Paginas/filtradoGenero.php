<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Título de tu página</title>
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

    $nombre_usuario = $_SESSION['usuario']['nombre'];


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

    <?php
    if ($_GET["genero"] != null) {
        $genero = $_GET["genero"];
        echo "<h1>$genero</h1>";
        //Obtenemos el numero de uegos con ese genero que hya en total
        $total_juegos_consulta = "SELECT COUNT(*) AS total_juegos FROM juegos WHERE genero = '$genero'";
        $resultado_total_juegos = $conexion->query($total_juegos_consulta);
        $fila_total_juegos = $resultado_total_juegos->fetch_assoc();
        $total_juegos = $fila_total_juegos["total_juegos"];

        echo "<div class='productosGenero'>";

        for ($i = 0; $i < $total_juegos; $i++) {

            $portada_consulta = "SELECT portada FROM juegos WHERE genero = '$genero' LIMIT 1 OFFSET $i";
            $portada_resultado = $conexion->query($portada_consulta);
            $portada_fila = $portada_resultado->fetch_assoc();
            $portada = $portada_fila["portada"];

            $nombre_consulta = "SELECT nombre FROM juegos WHERE genero = '$genero' LIMIT 1 OFFSET $i";
            $nombre_resultado = $conexion->query($nombre_consulta);
            $nombre_fila = $nombre_resultado->fetch_assoc();
            $nombre = $nombre_fila["nombre"];

            $precio_consulta = "SELECT precio FROM juegos WHERE genero = '$genero' LIMIT 1 OFFSET $i";
            $precio_resultado = $conexion->query($precio_consulta);
            $precio_fila = $precio_resultado->fetch_assoc();
            $precio = $precio_fila["precio"];

            $id_juego_query = "SELECT id FROM juegos WHERE genero = '$genero' LIMIT 1 OFFSET $i";
            $resultado_id_juego = $conexion->query($id_juego_query);
            $fila_id_juego = $resultado_id_juego->fetch_assoc();
            $id_juego = $fila_id_juego["id"];

            echo "<a href='juego.php?id_juego=$id_juego'>
        <div class='producto'>
                <img src='$portada' alt='Portada'>
                <p>$nombre</p>
                <p>$precio € </p>
            </div>
            </a>";
        }
        echo "</div>";

    } else {
        echo "<h1>GENERO NO ESPECIFICADO</h1>";
    }

    ?>

</html>