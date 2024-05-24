<?php
$db_host = "localhost:3308";
$db_name = "proyecto";
$db_user = "root";
$db_pass = "";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);


if ($conn->connect_error) {
    die("Fallo al conectar con la base de datos: " . $conn->connect_error);
}

$operacion = $_GET["operacion"];
echo $operacion;

if ($operacion == "iniciar") {
    session_start();

    $correo = $_POST["correo"];
    $contraseña = $_POST["pass"];

    // Consulta para obtener el hash de la contraseña del usuario
    $consulta = "SELECT * FROM usuarios WHERE correo = ?"; 
    $stmt = mysqli_prepare($conn, $consulta);
    mysqli_stmt_bind_param($stmt, "s", $correo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($resultado && mysqli_num_rows($resultado) == 1) {
        $fila_usuario = mysqli_fetch_assoc($resultado);
        $passIntr = $fila_usuario['contraseña']; // Obtener el hash de la contraseña (ahora llamado passIntr)

        // Verificar la contraseña utilizando password_verify
        if (password_verify($contraseña, $passIntr)) {
            // ¡Contraseña correcta!

            // Guardar datos del usuario en la sesión
            $_SESSION['usuario']['nombre'] = $fila_usuario['nombre'];
            $_SESSION['usuario']['correo'] = $fila_usuario['correo'];
            $_SESSION['usuario']['dinero'] = $fila_usuario['dinero'];
            $_SESSION['usuario']['portada'] = $fila_usuario['portada'];
            $_SESSION['usuario']['id'] = $fila_usuario['id']; 
            $_SESSION['usuario']['contraseña'] = $fila_usuario['contraseña']; 

            header("Location: Navegacion/home.php");
            exit();
        } else {
            // Contraseña incorrecta
            header("Location: iniciarSesionError.html");
            exit();
        }
    } else {
        // Usuario no encontrado o error en la consulta
        header("Location: iniciarSesionError.html");
        exit();
    }
}
 elseif ($operacion == "crear") {


    $nombre = $_POST["nombre"];
    $correo = $_POST["correo"];
    $fechaNac = $_POST["fechaNac"];
    $pass = $_POST["pass"];
    $perfil = $_POST["perfil"];
    $dinero = 0;
    $passEncriptada = password_hash($pass, PASSWORD_BCRYPT);

    $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE correo = '$correo'";
    $resultado = $conn->query($sql);
    $fila = $resultado->fetch_assoc();

    if ($fila['total'] > 0) {
        echo "Ya existe una cuenta con el correo: $correo";
    } else {
        $sql_insert = "INSERT INTO usuarios (nombre, correo, fechaNac, contraseña, dinero, portada) VALUES ('$nombre', '$correo', '$fechaNac', '$passEncriptada', $dinero, '$perfil')";

        if ($conn->query($sql_insert) === TRUE) {
            echo "El usuario ha sido registrado correctamente";
            header("Location: iniciarSesion.html");
        } else {
            echo "Error al registrar el usuario: " . $conn->error;
        }
    }


    $conn->close();


} elseif ($operacion == "accesoP") {


    session_start();
    if ($_SESSION != null) {
        header("Location: Navegacion/perfil.php");
    } else {
        header("Location: iniciarSesion.html");
    }
    echo "Te has quedado atascado, este mensaje no deberias verlo salte de esta pagina";

    exit();


}elseif ($operacion == "modificar") {
    session_start();

    if ($_SESSION != null) {
        $nombre = $_SESSION['usuario']['nombre'];
        $correo = $_POST["correo"];
        $pass = $_POST["pass"];
        $dinero = $_SESSION['usuario']['dinero'];
        $oldPass = $_POST["oldPass"];
        $portada = $_POST["perfil"];

        $oldCorreo = $_SESSION['usuario']['correo'];

        if (password_verify($oldPass, $_SESSION["usuario"]["contraseña"])) {
            $pass = password_hash($pass, PASSWORD_BCRYPT); 

            $sql = "UPDATE usuarios SET nombre='$nombre', correo='$correo', contraseña='$pass', dinero='$dinero', portada='$portada' WHERE correo='$oldCorreo'";
            
            if ($conn->query($sql) === TRUE) {
                echo "<p>Perfil modificado con éxito</p>";
                
                // Actualizar la contraseña en la sesión
                $_SESSION['usuario']['contraseña'] = $pass;
                
                header("Location: iniciarSesion.html"); // o a donde quieras redirigir
            } else {
                echo "Error al modificar el perfil: " . $conn->error;
            }
        } else {
            echo "<p>Contraseña  incorrecta</p>";
        }
    } else {
        header("Location: iniciarSesion.html");
    }

    // Actualizar los demás datos en la sesión
    $_SESSION['usuario']['nombre'] = $nombre;
    $_SESSION['usuario']['correo'] = $correo;
    $_SESSION['usuario']['dinero'] = $dinero;
    $_SESSION['usuario']['portada'] = $portada;
    $_SESSION['usuario']['contraseña'] = $pass;
}elseif ($operacion == "agregar") {
    session_start();
    if ($_SESSION != null) {
        $oldDinero = $_SESSION['usuario']['dinero'];
        $dinero = $_POST["saldoA"] + $oldDinero;
        $correo = $_SESSION['usuario']['correo'];
        $sql = "UPDATE usuarios SET dinero='$dinero' WHERE correo='$correo'";
        if ($conn->query($sql) === TRUE) {
            echo "<p>saldo agregegado con exito</p>";
            header("Location: iniciarSesion.html");
            $_SESSION['usuario']['dinero'] = $dinero;
        } else {
            echo "Error al agregar saldo: " . $conn->error;
        }
        header("Location: Navegacion/home.php");
    }
} elseif ($operacion == "comprar") {
    session_start();

    if (isset($_SESSION['usuario'])) {
        $dinero = $_SESSION['usuario']['dinero'];
        $correo = $_SESSION['usuario']['correo'];

        if ($operacion == "comprar") {
            $idJuego = $_GET['juego'];


            $stmt_precio = $conn->prepare("SELECT precio FROM juegos WHERE id = ?");
            $stmt_precio->bind_param("i", $idJuego);
            $stmt_precio->execute();
            $resultado_precio = $stmt_precio->get_result();
            $precio_fila = $resultado_precio->fetch_assoc();
            $precio = $precio_fila["precio"];

            $fechaActual = date("Y-m-d");

            $stmt_usuario = $conn->prepare("SELECT id FROM usuarios WHERE correo = ?");
            $stmt_usuario->bind_param("s", $correo);
            $stmt_usuario->execute();
            $resultado_usuario = $stmt_usuario->get_result();
            $idUsuario_fila = $resultado_usuario->fetch_assoc();
            $idUsuario = $idUsuario_fila["id"];

            if ($dinero >= $precio) {
                $stmt_compra = $conn->prepare("INSERT INTO usuarios_adquisiciones (id_usuario, id_juego, fecha) VALUES (?, ?, ?)");
                $stmt_compra->bind_param("iis", $idUsuario, $idJuego, $fechaActual);

                if ($stmt_compra->execute()) {
                    $dinero -= $precio;

                    $stmt_saldo = $conn->prepare("UPDATE usuarios SET dinero = ? WHERE correo = ?");
                    $stmt_saldo->bind_param("is", $dinero, $correo);

                    if ($stmt_saldo->execute()) {
                        $_SESSION['usuario']['dinero'] = $dinero;
                        echo "<p>Pago con éxito. Juego agregado a la cuenta.</p>";
                    } else {
                        echo "Error al actualizar el saldo: " . $stmt_saldo->error;
                    }
                } else {
                    echo "Error al comprar el juego: " . $stmt_compra->error;
                }
            } else {
                echo "<p>Saldo insufiente</p>";
                header("Location: Navegacion/perfil.php");
                exit();
            }

            header("Location: juego.php?id_juego=$idJuego");
            exit();
        }
    } else {
        header("Location: iniciarSesion.html");
        exit();
    }

} elseif ($operacion = "cerrarSesion") {
    session_start();
    unset($_SESSION['usuario']);

    header("Location: Navegacion/home.php");
} else {
    header("Location: Navegacion/home.php");
}
