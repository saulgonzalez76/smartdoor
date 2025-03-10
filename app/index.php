<?php

/*
 * GET ids ocupadas
 * id = idestacion para setup
 * t = token de usuario para abrir con nfc tag
 *  i = imei
 * v = visitas
 * c = confirmar correo
 *
 *
 * licencias o cuentas de pagos
 * tblEstacionTipoCuenta
 *  1 = fraccionamientos (tienen que pagar todos los usuarios o se bloquea a todos, pagos mensuales), usuarios ilimitados 50 por usuario
 *  2 = negocios (paga por un usuario de negocio, pagos mensuales), hasta 50 usuarios 200 pesos mensuales
 *  3 = casa o particulares (paga anualidad por usuario, solo un usuario por estacion), hasta 10 usuarios
 *  4 = demo un usuario, no puede agregar usuarios, no puede invitar, vigencia indefinida
 *
 *
 * bloqueos
 *  1 = bloqueado por administrador
 *  2 = bloqueado por falta de pago
 *
 *
 * por hacer !!
 * hacer el pago en linea por paypal y guardar sus datos
 * agregar a configuracion de admin una opcion para borrar la configuracion wifi del dispositivo
 *
 * planes de servicio
 * -1 = ilimitado
 * > -1 = limite de uso
 *
 *NFC tags
 * agregar la mac del tag a tblNfcTag con el idregistro de tblClientePuerta
 *
 * IMPORTANTE, AL CREAR NUEVOS AGREGAR A LA TABLA DE PING PARA QUE PUEDAN SER INSTALADOS
 * IMPORTANTE, AL ENTREGAR EQUIPO AL DISTRIBUIDOR ASIGNARLE EL EQUIPO EN LA TABLA
 * */

if(!isset($_SESSION)) {
    ini_set('session.save_handler', 'redis');
    if (getenv('APPLICATION_ENV') === "development")
        ini_set('session.save_path', 'tcp://smartdoor_redis:6379');
    else
        ini_set('session.save_path', 'tcp://localhost:6379');

    session_start();
    $_SESSION['CREATED'] = time();
}

require_once 'common_files/clases/session_config.php';
include "common_files/clases/base_datos.php";
$clsBaseDatos = new Base_Datos();

$t = "";
$nombre = "";
$bloqueado = "";

// si se escaneo el tag de la estacion entra aqui   SETUP DE NUEVO DISPOSITIVO
if (null !== (filter_input(INPUT_GET,'id'))) {
    $id = base64_decode(filter_input(INPUT_GET,'id'));
    $idcliente =$clsBaseDatos->estacion_asignado($id);
    $_SESSION['idestacion'] = $id;
    $_SESSION['idcliente']= $idcliente;
    header('Location: setup/');
    exit;
}

// si se escaneo el tag NFC de la estacion entra aqui
// token = serial number del tag
$token = "";
$usuario = "";
if (null !== (filter_input(INPUT_GET,'t'))) {
    $t = filter_input(INPUT_GET,'t');
    header('Location: index_nfc.php?t=' . $t);
    exit;
}

// confirmacion de correo
if (null !== (filter_input(INPUT_GET,'c'))) {
    $correo = base64_decode(filter_input(INPUT_GET,'c'));
    $clsBaseDatos->usuarios_confirma_email($correo);
    header('Location: /');
    exit;
}

// invitado de whatsapp
if (null !== (filter_input(INPUT_GET,'v'))) {
    header('Location: visitas.php?v=' . filter_input(INPUT_GET,'v'));
    exit;
}

// password reset
if (null !== (filter_input(INPUT_GET,'r'))) {
    $_SESSION['usuario']['idusuario'] = $clsBaseDatos->usuarios_email(base64_decode(filter_input(INPUT_GET,'r')));
    $_SESSION['usuario']['email'] = base64_decode(filter_input(INPUT_GET,'r'));
    header('Location: desktop/cambio_pass.php?r=1');
    exit;
}

$version = time();
$useragent=$_SERVER['HTTP_USER_AGENT'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include "common_files/meta_tags.php" ?>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link rel="stylesheet" href="common_files/css/font-awesome.min.css">
    <link href="plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="plugins/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="plugins/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="plugins/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="plugins/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="common_files/css/style.css" rel="stylesheet">
    <link href="plugins/flipclock/css/flipclock.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <script src="common_files/java/jquery-latest.min.js"></script>
    <script src="common_files/java/jquery.min.js?<?= $version; ?>"></script>
    <script src="common_files/java/sweetalert.js?<?= time(); ?>" type="text/javascript"></script>
    <script src="plugins/flipclock/js/flipclock.min.js"></script>
    <script src="common_files/java/base64.js"></script>
    <script src="common_files/java/index_scripts.min.js?<?= $version; ?>"></script>
    <style>
        .swal2-container {
            zoom: 1.5;
        }
        .swal2-icon {
            width: 5em !important;
            height: 5em !important;
            border-width: .25em !important;
        }
    </style>
    <script>
        var total_registros = <?= $clsBaseDatos->registro_totales_todo(); ?>;
        var total_particulares = <?= $clsBaseDatos->registro_totales_particulares(); ?>;
        var total_negocios = <?= $clsBaseDatos->registro_totales_negocio(); ?>;
        var total_fracc = <?= $clsBaseDatos->registro_totales_fraccionamiento(); ?>;
        let token = '<?= $token; ?>';
        let nombre = '<?= $nombre; ?>';
        let usuario = '<?= $usuario; ?>';
        let bloqueado = Number(<?= $bloqueado; ?>);
    </script>
</head>
<body>

<?php


if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){ ?>
    <header id="header" class="fixed-top header-scrolled">
        <div class="container d-flex align-items-center justify-content-between">

            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto active" href="#hero">Inicio</a></li>
                    <li><a class="nav-link scrollto" href="#about">Acerca</a></li>
                    <li><a class="nav-link scrollto" href="#services">Servicios</a></li>
                    <li><a class="nav-link scrollto " href="#media">Media</a></li>
                    <li><a class="nav-link scrollto" href="#distribuidores">Distribuidores</a></li>
                    <li><a class="nav-link scrollto" href="#pricing">Precios</a></li>
                    <li><a class="nav-link scrollto" href="#contact">Contacto</a></li>
                    <li><a class="nav-link scrollto" href="login.php">Login</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
            <a href="mailto:info@smartdoor" style="color: rgba(255,247,37,0.91)">info@smartdoor</a>
        </div>
    </header><br><br><br>
    <section id="hero" class="d-flex align-items-center justify-content-center" style="background: url('common_files/img/logo_small.png') center center; background-size: contain; background-repeat: no-repeat; left: 0; top: 0; bottom: 0; right: 0; height: auto;">
        <div class="container position-relative">
            <div id="container" ></div>
        </div>
    </section>
<?php } else {
?>
    <header id="header" class="fixed-top header-transparent">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="mailto:info@smartdoor" class="logo">info@smartdoor</a>
            <nav id="navbar" class="navbar">
                <ul>
                    <li><a class="nav-link scrollto active" href="#hero">Inicio</a></li>
                    <li><a class="nav-link scrollto" href="#about">Acerca</a></li>
                    <li><a class="nav-link scrollto" href="#services">Servicios</a></li>
                    <li><a class="nav-link scrollto " href="#media">Media</a></li>
                    <li><a class="nav-link scrollto" href="#distribuidores">Distribuidores</a></li>
                    <li><a class="nav-link scrollto" href="#pricing">Precios</a></li>
                    <li><a class="nav-link scrollto" href="#contact">Contacto</a></li>
                    <li><a class="nav-link scrollto" href="login.php">Login</a></li>
                </ul>
                <i class="bi bi-list mobile-nav-toggle"></i>
            </nav>
        </div>
    </header>
    <section id="hero" class="d-flex align-items-center justify-content-center" style="background: url('common_files/img/logo.png') center center; background-size:cover; left: 0; top: 0; bottom: 0; right: 0;">
        <div class="container position-relative">
            <div id="container" ></div>
        </div>
    </section>
<?php }

//echo base64_encode("e8:68:e7:ea:2e:de") . "<br>";

?>
<main id="main">
    <section id="about" class="about">
        <div class="container">

            <div class="row">
                <div class="col-lg-6 text-center">
                    <video height="350" controls>
                        <source src="common_files/img/promo2.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <div class="col-lg-6 pt-4 pt-lg-0">
                    <h3>SmartDoor</h3>
                    <p>
                        Control de acceso en la palma de tu mano. Opera cualquier porton o cerradura electrica donde te encuentres !
                    </p>
                    <div class="row">
                        <div class="col-md-6">
                            <i class="bx bx-building-house"></i>
                            <h4>Fraccionamientos y Colonias Privadas</h4>
                            <p>Controla entradas de todos, residentes, visitas y empleados. Todo donde quiera que te encuentres.</p>
                        </div>
                        <div class="col-md-6">
                            <i class="bx bx-home"></i>
                            <h4>Particulares y Negocios</h4>
                            <p>Disfruta de la comodidad de tener el control en tus manos, dando acceso a quien tu quieras.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <section id="counts" class="counts section-bg">
        <div class="container">

            <div class="row counters">

                <div class="col-lg-3 col-6 col-12 text-left">
                    <div class="container">
                        <div class="clock" id="totreg" style="zoom: 0.6; -moz-transform: scale(0.6);"></div>
                        <p>&nbsp;&nbsp;&nbsp;&nbsp;Aperturas</p>
                    </div>
                </div>

                <div class="col-lg-3 col-6 col-12 text-left">
                    <div class="container">
                        <div class="clock" id="totpart" style="zoom: 0.6; -moz-transform: scale(0.6);"></div>

                        <p>&nbsp;&nbsp;&nbsp;&nbsp;Particulares</p>
                    </div>
                </div>

                <div class="col-lg-3 col-6 col-12 text-left">
                    <div class="container">
                        <div class="clock" id="totneg" style="zoom: 0.6; -moz-transform: scale(0.6);"></div>

                        <p>&nbsp;&nbsp;&nbsp;&nbsp;Negocios</p>
                    </div>
                </div>

                <div class="col-lg-3 col-6 col-12 text-left">
                    <div class="container">
                        <div class="clock" id="totfracc" style="zoom: 0.6; -moz-transform: scale(0.6);"></div>

                        <p>&nbsp;&nbsp;&nbsp;&nbsp;Fraccionamientos</p>
                    </div>
                </div>

            </div>

        </div>
    </section>
    <section id="services" class="services">
        <div class="container">

            <div class="section-title">
                <h2>Servicios</h2>
                <p>Nuestro objetivo es llevar el control de acceso registrando entradas de empleados, visitas y residentes con la mayor eficiencia posible, contamos con diferentes paquetes que te ayudan a cumplir tus metas y obtener mejores resultados. Estas son algunas caracteristicas de SmartDoor:</p>
            </div>

            <div class="row">

                <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="zoom-in" data-aos-delay="100">
                    <div class="icon-box iconbox-blue">
                        <div class="icon">
                            <i class="bx bxs-user"></i>
                        </div>
                        <h4><a href="">Control de empleados</a></h4>
                        <p>Agrega, elimina o bloquea empleados. Pueden entrar si el dispositivo cuenta con el lector de QR.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-md-0" data-aos="zoom-in" data-aos-delay="200">
                    <div class="icon-box iconbox-orange ">
                        <div class="icon">
                            <i class="bx bx-body"></i>
                        </div>
                        <h4><a href="">Visitas</a></h4>
                        <p>Envia invitaciones a tus visitas con tiempo limitado, podran tener acceso solo mientras el token sea vigente. Poran escanear el QR si el dispositivo cuenta con lector, de lo contrario pueden entrar desde el app.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4 mt-lg-0" data-aos="zoom-in" data-aos-delay="300">
                    <div class="icon-box iconbox-pink">
                        <div class="icon">
                            <i class="bx bx-group"></i>
                        </div>
                        <h4><a href="">Residentes/ Usuarios</a></h4>
                        <p>Dependiendo del paquete que escojas tendras limite de usuarios por acceso.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="icon-box iconbox-yellow">
                        <div class="icon">
                            <i class="bx bx-shape-square"></i>
                        </div>
                        <h4><a href="">Acceso por QR</a></h4>
                        <p>Si el dispositivo cuenta con lector de QR los usuarios, empleados o visitantes podran entrar usando la imagen, este QR se cambia en automatico para mayor seguridad.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="icon-box iconbox-red">
                        <div class="icon">
                            <i class="bx bx-mobile"></i>
                        </div>
                        <h4><a href="">Acceso desde app</a></h4>
                        <p>En caso de no tener lector de QR, puedes tener acceso usando el web app, disponible para todos los dispositivos y computadoras.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 d-flex align-items-stretch mt-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="icon-box iconbox-teal">
                        <div class="icon">
                            <i class="bx bx-image"></i>
                        </div>
                        <h4><a href="">Imagenes de acceso</a></h4>
                        <p>Si se cuenta con camara IP al momento de escanear el codigo QR el sistema toma una instantanea para ligarla con el registro de entrada.</p>
                    </div>
                </div>

            </div>

        </div>
    </section>
    <section id="cta" class="cta">
        <div class="container">

            <div class="text-center">
                <h3>Te interesa ser distribuidor ?</h3>
                <p> Ventas exclisivas para distribuidor, SmartDoor no se vende al publico final. Forma parte de nuestra red de distribuidores !</p>
                <a class="cta-btn" href="#contact">Contactanos !</a>
            </div>

        </div>
    </section>

    <section id="media">
        <div class="container">

            <div class="section-title">
                <h2>Media</h2>
                <p>Aqui algunos videos e imagenes de operacion.</p>
            </div>

            <div class="testimonials-slider swiper-container" data-aos="fade-up" data-aos-delay="100">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="common_files/img/sd1.png" style="height: 400px; width: auto;">
                        <p>Acceso con QR y status del porton</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd2.png" style="height: 400px; width: auto;">
                        <p>Menu</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd3.png" style="height: 400px; width: auto;">
                        <p>Invitados</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd4.png" style="height: 400px; width: auto;">
                        <p>Invitados - formulario</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd5.png" style="height: 400px; width: auto;">
                        <p>Empleados - horario</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd6.png" style="height: 400px; width: auto;">
                        <p>Reporte de registros exportable a PDF y Excel</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd7.png" style="height: 400px; width: auto;">
                        <p>Listado de usuarios y empleados</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd8.png" style="height: 400px; width: auto;">
                        <p>Grafica de uso</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd9.png" style="height: 400px; width: auto;">
                        <p>Resumen de invitados y envio por whatsapp</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd10.png" style="height: 400px; width: auto;">
                        <p>Confirmacion de invitado por email</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd11.png" style="height: 400px; width: auto;">
                        <p>Acceso de invitado por QR y app</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd12.png" style="height: 400px; width: auto;">
                        <p>Acceso de invitado por app</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd13.png" style="height: 400px; width: auto;">
                        <p>Acceso de invitado por app</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd14.png" style="height: 400px; width: auto;">
                        <p>Errores de conexion a la vista</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd15.png" style="height: 400px; width: auto;">
                        <p>Acceso de usuario por QR y app</p>
                    </div>
                    <div class="swiper-slide">
                        <img src="common_files/img/sd16.png" style="height: 400px; width: auto;">
                        <p>Status del dispositivo a la vista</p>
                    </div>
                </div><br>
                <div class="swiper-pagination"></div>
            </div>
        </div><br><br>
        <div class="container">
            <div class="row">
                <div class="col-lg-4"></div>
                <div class="col-lg-4">
                    <iframe width="560" height="315" style="flex-grow: 1;" src="https://www.youtube.com/embed/videoseries?list=PL5Nyk1bjJxJXP_Hy3jEW1XMUVapj0Kju_&rel=0" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                </div>
                <div class="col-lg-4"></div>
            </div>
        </div>
    </section>

    <section id="distribuidores" class="pricing">
        <div class="container">

            <div class="section-title">
                <h2>Distribuidores</h2>
                <p>SmartDoor solo se vende a trevez de alguno de nuestros distribuidores, por favor ponte en contacto con alguno de ellos para brindarte un mejor servicio.</p>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="box">
                        <h3>Tamaulipas</h3>
                        <h4>Nombre del dist</h4>
                        <ul>
                            <li>dir</li>
                            <li>dir</li>
                            <li>ciudad</li>
                            <a href="javascript:window.open('https://maps.app.goo.gl/QBMDZfzY8HpcHCF78');" class="btn btn-warning">Google Maps <i class="bx bx-map-pin"></i> </a>
                        </ul>
                        <div class="btn-wrap">
                            <a href="tel:1234567890" class="btn btn-warning">1234567890 <i class="bx bx-phone"></i> </a><br><br>
                            <a href="tel:1234567890" class="btn btn-warning">1234567890 <i class="bx bxl-whatsapp"></i> </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="pricing" class="pricing">
        <div class="container">

            <div class="section-title">
                <h2>Precios</h2>
                <p>Contamos con diferentes planes de servicio para el tipo de instalacion, los precios los podras ver durante el proceso de setup o hablando directamente con un distribuidor autorizado.</p>
            </div>
        </div>
    </section>

    <section id="contact" class="contact">
        <div class="container">
            <div class="section-title">
                <h2>Contacto</h2>
                <p>Envianos un correo, nos gustaria saber que piensas !</p>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box">
                                <i class="bx bx-map"></i>
                                <h3>Direccion</h3>
                                <p>Ciudad Victoria, Tamaulipas Mexico</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mt-4">
                                <i class="bx bx-envelope"></i>
                                <h3>Email</h3>
                                <a href="mailto:email@smartdoor" class="btn btn-warning">email@smartdoor </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box mt-4">
                                <i class="bx bxl-whatsapp"></i>
                                <h3>Whatsapp</h3>
                                <a href="javascript:window.open('https://wa.me/1234567890');" class="btn btn-warning">1234567890 </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <input type="text" class="form-control" id="txtname" placeholder="Nombre">
                            </div>
                            <div class="col-md-6 form-group mt-3 mt-md-0">
                                <input type="email" class="form-control" id="txtemail" placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <input type="text" class="form-control" id="txttitulo" placeholder="Titulo">
                        </div>
                        <div class="form-group mt-3">
                            <textarea class="form-control" id="txtbody" rows="5" placeholder="Mensaje"></textarea>
                        </div><br>
                        <div class="text-center"><button class="btn btn-primary" type="submit" onclick="contacto();">Enviar Mensaje</button></div>
                </div>

            </div>

        </div>
    </section>
</main>

<footer id="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 footer-contact">
                    <h3>SmartDoor</h3>
                    <p>
                        Ciudad, Estado<br>
                        Pais <br><br>
                    </p>
                </div>
                <div class="col-lg-2 col-md-6 footer-links">
                    <strong>Whatsapp:</strong> 1234567890<br>
                    <strong>Email:</strong> info@smartdoor<br> ayuda@smartdoor<br>
                </div>
                <div class="col-lg-2 col-md-6 footer-links">
                    <h4>Manuales</h4>
                    <ul>
                        <li><i class="bx bx-chevron-right"></i> <a href="#">Instalacion</a></li>
                        <li><i class="bx bx-chevron-right"></i> <a href="#">Instalacion a chapa</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 footer-links">
                    <h4></h4>
                    <ul>
                        <li><i class="bx bx-chevron-right"></i> <a href="#">Setup WiFi</a></li>
                        <li><i class="bx bx-chevron-right"></i> <a href="#">Setup App</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="container d-md-flex py-4">
        <div class="me-md-auto text-center text-md-start">
            <strong>&copy; 2019-<?= date("Y"); ?> <a href="https://github.com/saulgonzalez76/smartdoor"> GitHub <i class="fab fa-github"></i> </a></strong>
            <label id="lblfecha" class="float-right text-danger text-uppercase text-xs"></label><input type="hidden" id="txtHora" name="txtHora" value="">
        </div>

    </div>
</footer>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="plugins/glightbox/js/glightbox.min.js"></script>
<script src="plugins/isotope-layout/isotope.pkgd.min.js"></script>
<script src="plugins/swiper/swiper-bundle.min.js"></script>
<script src="common_files/java/main.min.js"></script>
</body>
</html>