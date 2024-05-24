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
    // Iniciar la sesión
    session_start();

        $db_host = "localhost:3308";
        $db_name = "proyecto";
        $db_user = "root";
        $db_pass = "";
        $conexion = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

        if($_SESSION!=null){
        $nombre_usuario= $_SESSION['usuario']['nombre'];
        }

        if (!$conexion) {
            echo "Fallo al conectar con la base de datos";
            exit();
        }
    ?>

    <header>
        <div class='logo'>
            <img src='../../Imagenes/logo.png' alt='Logotipo'>
        </div>
        <button class='menu-toggle'>></button>
        <nav class='menu'>
            <ul>
                <li><a href='home.php'>Inicio</a></li>
                <li><a href='biblioteca.php' >Biblioteca</a></li>
                <li><a href='documentacion.php'>Documentacion</a></li>
                <li><a href='estadisticas.php' style='background-color: #555;'>Estadisticas</a></li>
            </ul>
        </nav>
        <a href="../iniciarSesion.html">
            <div class='perfil'>
                <img src='../../Imagenes/logo.png' alt='Imagen_perfil'>
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

    <div class="pie">
        <p>Correo: proyectomiguel@gmail.com</p>
        <p>Direccion de la empresa: Pl. Virgen Milagrosa, 11, Málaga-Este, 29017 Málaga, España.</p>
        <a  href="../folCV.html">Aviso legal</a>
        <a href="../folCV.html">Politica de privacidad</a>
    </div>
    </body>

    </html>