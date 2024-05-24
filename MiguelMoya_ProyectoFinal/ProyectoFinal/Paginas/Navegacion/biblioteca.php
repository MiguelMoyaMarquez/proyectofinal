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
        $dinero=$_SESSION['usuario']['dinero'];
    }


    ?>

<header>
        <div class='logo'>
            <img src='../../Imagenes/logo.png' alt='Logotipo'>
        </div>
        <button class='menu-toggle'></button>
        <nav class='menu'>
            <ul>
                <li><a href='home.php' >Inicio</a></li>
                <li><a href='biblioteca.php' style='background-color: #555;'>Biblioteca</a></li>
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

    <h1>MIS JUEGOS:</h1>
    <?php
    if($_SESSION!= null) {
    $id_usuario_query = "SELECT id FROM usuarios WHERE correo='$correo'";
    $resultado_id_usuario = $conexion->query($id_usuario_query);
    $fila_id_usuario = $resultado_id_usuario->fetch_assoc();
    $id_usuario = $fila_id_usuario["id"];

    $consulta_numJuegos = "SELECT count(*) AS cantidad from usuarios_adquisiciones where id_usuario=$id_usuario";
    $resultado_numJuegos = $conexion->query($consulta_numJuegos);
    $fila_numJuegos = $resultado_numJuegos->fetch_assoc();
    $numJuegos = $fila_numJuegos["cantidad"];

    echo "<div class='productosGenero'";
    for ($i = 0; $i < $numJuegos; $i++) {
        $idJuego_consulta = "SELECT id_juego FROM usuarios_adquisiciones WHERE id_usuario = $id_usuario LIMIT 1 OFFSET $i";
        $idJuego_resultado = $conexion->query($idJuego_consulta);
        $idJuego_fila = $idJuego_resultado->fetch_assoc();
        $idJuego = $idJuego_fila["id_juego"];

        $portada_consulta = "SELECT portada FROM juegos WHERE id = $idJuego";
        $portada_resultado = $conexion->query($portada_consulta);
        $portada_fila = $portada_resultado->fetch_assoc();
        $portada = $portada_fila["portada"];

        $nombre_consulta = "SELECT nombre FROM juegos WHERE id = $idJuego";
        $nombre_resultado = $conexion->query($nombre_consulta);
        $nombre_fila = $nombre_resultado->fetch_assoc();
        $nombre = $nombre_fila["nombre"];

        $precio_consulta = "SELECT precio FROM juegos WHERE id = $idJuego";
        $precio_resultado = $conexion->query($precio_consulta);
        $precio_fila = $precio_resultado->fetch_assoc();
        $precio = $precio_fila["precio"];

        echo "<a href='../juego.php?id_juego=$idJuego'>
                <div class='producto'>
                    <img src='$portada' alt='Portada'>
                    <p>$nombre</p>
                    <p style='background-color: green;'>Adquerido</p>
                </div>
              </a>";
    }
    echo "</div>";
}
    ?>
</div>

<div class="pie">
        <p>Correo: proyectomiguel@gmail.com</p>
        <p>Direccion de la empresa: Pl. Virgen Milagrosa, 11, Málaga-Este, 29017 Málaga, España.</p>
        <a  href="../folCV.html">Aviso legal</a>
        <a href="../folCV.html">Politica de privacidad</a>
    </div>
</body>

</html>