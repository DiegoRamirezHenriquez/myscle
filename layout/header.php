<?php
include("conexion.php");
    session_start();
    if(isset($_SESSION['email'])){
        $email = $_SESSION['email'];
    }
$isLoggedIn = isset($email);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyScle</title>
    <link rel="shortcut icon" href="images/icons/solo.png">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/edit-profile.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/posts.css">
    <link rel="stylesheet" href="css/rutinas.css">
    <link rel="stylesheet" href="css/explorar.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>    
    <header>
        <img src="images/icons/logo.png">
        <p></p>
        <ul class="categorias">
            <li><a href="profile.php">PERFIL</a></li>
            <li><a href="home.php">HOME</a></li>
            <li><a href="explorar.php">EXPLORAR</a></li>
        </ul>
        <?php if(!$isLoggedIn){ ?>
        <ul class="btns-registro-ingreso">
            <li><a href="login.php">INGRESAR</a></li>
            <li><a href="register.php" id="btn-register"> <span>REGISTRARSE</span>
                </a></li>
        </ul>
        <?php } else { ?>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
                session_destroy();
                header("Location: index.php");
                exit();
            }
            ?>
            <form method="post">
                <button type="submit" name="logout" class="btn-logout Btn">
                <div class="sign"><svg viewBox="0 0 512 512"><path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path></svg></div>
                <div class="text">CERRAR SESIÓN</div>
                </button>
            </form>

        </ul>
        <?php } ?>

    </header>
    <!-- quiero que este header se pase abajo y se modifiquen las palabras por iconos, desaparezca la img al pasar a celular -->
    <div class="header-movil">
        <div class="header-movil-contenido">
            <a href="home.php"><i class='bx bx-home' style="color: white;"></i></a>
            <a href="explorar.php"><i class='bx bx-compass' style="color: white;"></i></a>
            <a href="profile.php"><i class='bx bx-user' style="color: white;"></i></a>
            <?php if(!$isLoggedIn){ ?>
                <a href="login.php"><i class='bx bx-log-in' style="color: white;"></i></a>
                <a href="register.php"><i class='bx bx-user-plus' style="color: white;"></i></a>
            <?php } else { ?>
                <form method="post" class="logout-form">
                    <button type="submit" name="logout" class="logout-btn">
                        <i class='bx bx-log-out' style="color: white;"></i>
                    </button>
                </form>
            <?php } ?>
        </div>
    </div>
    <style>
        .header-movil {
            display: none;
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #164B60;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .header-movil-contenido {
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
        }
        .header-movil-contenido a, .header-movil-contenido .logout-btn {
            color: #333;
            font-size: 24px;
            text-decoration: none;
        }
        .logout-form {
            margin: 0;
        }
        .logout-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #333;
            font-size: 24px;
        }
        @media (max-width: 768px) {
            header {
                display: none;
            }
            .header-movil {
                display: block;
            }
        }
    </style>
