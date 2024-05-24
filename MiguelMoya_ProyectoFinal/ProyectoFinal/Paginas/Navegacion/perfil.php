<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Título de tu página</title>
    <link rel="stylesheet" href="../../CSS/estilos.css">
</head>


    <?php
    session_start();

    $db_host = "localhost:3308";
    $db_name = "proyecto";
    $db_user = "root";
    $db_pass = "";
    $conexion = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

    if ($_SESSION != null) {
        $nombre_usuario = $_SESSION['usuario']['nombre'];
        $correo = $_SESSION['usuario']['correo'];
        $dinero = $_SESSION['usuario']['dinero'];
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
        <button class='menu-toggle'></button>
        <nav class='menu'>
            <ul>
                <li><a href='home.php'>Inicio</a></li>
                <li><a href='biblioteca.php'>Biblioteca</a></li>
                <li><a href='documentacion.php'>Documentacion</a></li>
                <li><a href='estadisticas.php'>Estadisticas</a></li>
            </ul>
        </nav>
        <a href="../proceso_perfil.php?operacion=accesoP">
            <div class='perfil' style='background-color: #555;'>
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

    <div class="perfilPagina">
    <form action="../proceso_perfil.php?operacion=modificar" method="post">
        <div class="datosUsuario">
            <?php
            echo " 
        <h2>Nombre:</h2>
        <p><input type='text' placeholder=$nombre_usuario default=$nombre_usuario name='nombre' minlength='3' maxlength='15' required></p>
        <hr>
        <h2>correo:</h2>
        <p><input type='email' placeholder=$correo default=$correo name='correo'><p>
        <hr>
        <p>contraseña actual:</p>
        <p><input type='password' name='oldPass'></p>
        <hr>
        <h2>Nueva contraseña</h2>
        <p><input type='password' name='pass'  minlength='3' maxlength='15' required></p> 
        <hr>
        <img src='../../Imagenes/logo.png' alt='' >
        <input type='radio' id='_1' name='perfil' value='http://localhost/Proyecto/ProyectoFinal/Imagenes/Perfil/Perfil_01.jpg' checked>
        <img src='../../Imagenes/logo.png' alt='' >
        <input type='radio' id='_2' name='perfil' value='http://localhost/Proyecto/ProyectoFinal/Imagenes/Perfil/Perfil_02.jpg
        '>
        <img src='../../Imagenes/logo.png' alt='' >
        <input type='radio' id='_3' name='perfil' value='http://localhost/Proyecto/ProyectoFinal/Imagenes/Perfil/Perfil_03.jpg
        '>
        <hr>
        <p><button>Actulizar datos</button></p>
        ";

            ?>
    </form>
    </div>
    <form action="../proceso_perfil.php?operacion=agregar" method="post">

        <div class="infoUsuario">
            <h2>Agregar saldo:</h2>
            <?php
            echo "<p>$dinero</p>";
            ?>
            <p><input type="number" min="0" max="999" name="saldoA" required></p>
            <button>Agregar</button>
            <a href="../iniciarSesion.html">Cambiar cuenta</a>
            <a href="../proceso_perfil.php?operacion=cerrarSesion">Cerrar sesion</a>
           
        </div>
        </form>
        


 
</body>

</html>