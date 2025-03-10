<?php
/**
 * Made by: Saul Gonzalez 
 * Copyright (c) 2019.
 */

require_once __DIR__.'/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function fechaLarga($fecha){
    $arrMesLetra = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
    $arrDiaLetra = array('Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sábado');
    return $arrDiaLetra[date("w",strtotime($fecha))] . " " . date("j",strtotime($fecha)) . " de " . $arrMesLetra[date("n",strtotime($fecha))-1] . " del " . date("Y",strtotime($fecha));
}

function enviaCorreo($cuerpo,$destino,$nombre,$titulo,$inbound,$username){
    try {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;  // Enable verbose debug output
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host = 'mail.server.smtp';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                                   // Enable SMTP authentication
        $mail->Username = $username. '@email.com';                     // SMTP username
        $mail->Password = '';                     // SMTP password
        $mail->SMTPSecure = 'tls';                              // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to
        if ($inbound){
            $mail->setFrom($destino, $nombre);
            $mail->addAddress('your@email.com', "SmartDoor", false);
        } else {
            $mail->setFrom('no-reply@email.com', "SmartDoor", false);
            $mail->addAddress($destino, $nombre);     // Add a recipient
        }
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $titulo;
        $mail->Body = $cuerpo;
        $mail->send();
        return 1;
    } catch (Exception $e) {
        return 'Mensaje no pudo ser enviado, por favor reporte este error: {' . $e . '}';
    }
}

function correoInvita($nombre_evento,$nombre_persona,$fecha,$hora,$codigo) {
        // correo de invitacion a un evento
        return "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\"><html lang=\"es\"><body>
    <div style=\"width:100%!important;min-width:100%;box-sizing:border-box;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;text-align:left;line-height:17px;font-size:13px;margin:0;padding:0\">
        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;height:100%;width:100%;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" bgcolor=\"#ebf3fa\">
            <tbody>
            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                <td align=\"center\" valign=\"top\" style=\"word-wrap:break-word;border-collapse:collapse!important;float:none;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0\">
                    <center style=\"width:100%;min-width:580px\">
                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                            <tbody>
                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                <td height=\"10px\" style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:10px;font-size:10px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:center;width:580px;float:none;margin:0 auto;padding:0\" bgcolor=\"#ffffff\">
                            <tbody>
                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                    <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                        <tbody>
                                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                            <td height=\"60px\" style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:60px;font-size:60px;margin:0;padding:0\" align=\"left\" valign=\"top\">

                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;display:table;padding:0\">
                                        <tbody>
                                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                            <th style=\"width:520px;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0 60px 16px\" align=\"left\">
                                                <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <th style=\"color:#f60444;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\">
                                                            <img src=\"https://smartdoor.mx/common_files/img/logo_small.png\" width=\"250\">
                                                        </th>
                                                        <th style=\"width:0;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\"></th>
                                                    </tr>
                                                </table>
                                            </th>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;display:table;padding:0\">
                                        <tbody>
                                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                            <th style=\"width:520px;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0 60px 16px\" align=\"left\">
                                                <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <th style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\">
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                <tbody>
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <td height=\"30px\" style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:30px;font-size:30px;margin:0;padding:0\" align=\"left\" valign=\"top\">

                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                            <h4 style=\"color:inherit;font-family:Arial,Helvetica,sans-serif;font-weight:bold;line-height:1.3;word-wrap:normal;font-size:24px;margin:0 0 16px;padding:0\" align=\"left\">Invitación de ".$nombre_persona." para ".$nombre_evento."</h4>
                                                            <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:1.6;font-size:16.25px;margin:0 0 30px;padding:0\" align=\"left\">
        Hola !, <label style=\"font-weight:bold;\">".$nombre_persona."</label> te a enviado una invitación para acudir a \"<label style=\"font-weight:bold;\">".$nombre_evento."</label>\" el dia " . fechaLarga($fecha) . " a partir de las " . date("H:i",strtotime($hora)) . ". Para poder entrar a las instalaciones ve el código dando clic en:
                                                            </p>
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%!important;margin:0 0 16px;padding:0\">
                                                                <tbody>
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                            <tbody>
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;border-radius:5px;margin:0;padding:0;border:2px none #0071bb\" align=\"center\" bgcolor=\"#0071bb\" valign=\"top\">
                                                                                    <a rel=\"noopener noreferrer\" href=\"https://smartdoor.mx/?v=".$codigo."\" style=\"color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-weight:bold;text-align:center;line-height:1.3;text-decoration:none;font-size:20px;display:inline-block;border-radius:5px;width:100%;margin:0;padding:20px 0;border:0 solid #0071bb\" target=\"_blank\">Ver en SmartDoor</a>
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </td>
                                                                    <td style=\"word-wrap:break-word;border-collapse:collapse!important;width:0;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\"></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                            <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 0 10px;padding:0\" align=\"left\">
                                                                <a rel=\"noopener noreferrer\" href=\"https://smartdoor.mx/?v=".$codigo."\" style=\"color:#0071bb;font-family:Arial,Helvetica,sans-serif;font-weight:normal;text-align:left;line-height:1.3;text-decoration:none;margin:0;padding:0\" target=\"_blank\">https://smartdoor.mx/?v=".$codigo."</a>
                                                            <p style=\"color:#D40E0EFF;\">Importante: Este código solo es valido durante el evento, no funciona antes o después del mismo.</p>


                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                <tbody>
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <td height=\"30px\" style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:30px;font-size:30px;margin:0;padding:0\" align=\"left\" valign=\"top\">

                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                </table>
                                            </th>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table align=\"center\" style=\"width:100%;border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;padding:0\">
                                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                            <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                                <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;display:table;padding:0\">
                                                    <tbody>
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <th style=\"width:520px;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0 60px 16px\" align=\"left\">
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <th style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\">
                                                                        <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 0 10px;padding:0\" align=\"left\">
        Por favor no responda a este mensaje. Si necesita ayuda, por favor visita <a href=\"https://smartdoor.mx\" style=\"color:#0071bb;font-family:Arial,Helvetica,sans-serif;font-weight:normal;text-align:left;line-height:1.3;text-decoration:none;margin:0;padding:0\" target=\"_blank\">www.smartdoor.mx</a>
                                                                        </p>
                                                                    </th>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                    <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;padding:0\">
                                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                            <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                                <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;display:table;padding:0\">
                                                    <tbody>
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <th style=\"width:520px;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0 60px 16px\" align=\"left\">
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;margin:10px auto;padding:0\">
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <th style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;height:0;max-width:580px;border-bottom-color:#ebf3fa;border-bottom-style:solid;clear:both;margin:10px auto;padding:0;border-width:0 0 2px\" align=\"left\">

                                                                    </th>
                                                                </tr>
                                                            </table>
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <th style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\">
                                                                        <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 0 10px;padding:0\" align=\"left\">
        Gracias !
                                                                        </p>
                                                                    </th>
                                                                    <th style=\"width:0;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\"></th>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </center>
                </td>
            </tr>
            </tbody>
            <td><div style=\"display:none;white-space:nowrap;font:15px courier\">
                </div>
            </td></table>
    </div>
</body>
</html>";
}

function correoNuevoUusario($codigoemail,$email,$password){
    return "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"https://www.w3.org/TR/html4/strict.dtd\">
            <html lang=\"es\">
            <body>
                <div style=\"width:100%!important;min-width:100%;box-sizing:border-box;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;text-align:left;line-height:17px;font-size:13px;margin:0;padding:0\">
                    <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;height:100%;width:100%;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" bgcolor=\"#ebf3fa\">
                        <tbody>
                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                            <td align=\"center\" valign=\"top\" style=\"word-wrap:break-word;border-collapse:collapse!important;float:none;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0\">
                                <center style=\"width:100%;min-width:580px\">
                                    <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                        <tbody>
                                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                            <td height=\"10px\" style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:10px;font-size:10px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:center;width:580px;float:none;margin:0 auto;padding:0\" bgcolor=\"#ffffff\">
                                        <tbody>
                                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                            <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                                <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                    <tbody>
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <td height=\"60px\" style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:60px;font-size:60px;margin:0;padding:0\" align=\"left\" valign=\"top\">
            
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;display:table;padding:0\">
                                                    <tbody>
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <th style=\"width:520px;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0 60px 16px\" align=\"left\">
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <th style=\"color:#f60444;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\">
                                                                        <img src=\"https://smartdoor.mx/common_files/img/logo_small.png\" width=\"250\">
                                                                    </th>
                                                                    <th style=\"width:0;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\"></th>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;display:table;padding:0\">
                                                    <tbody>
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <th style=\"width:520px;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0 60px 16px\" align=\"left\">
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <th style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\">
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                            <tbody>
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <td height=\"30px\" style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:30px;font-size:30px;margin:0;padding:0\" align=\"left\" valign=\"top\">
            
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        <h4 style=\"color:inherit;font-family:Arial,Helvetica,sans-serif;font-weight:bold;line-height:1.3;word-wrap:normal;font-size:24px;margin:0 0 16px;padding:0\" align=\"left\">Confirma tu correo</h4>
                                                                        <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:1.6;font-size:16.25px;margin:0 0 30px;padding:0\" align=\"left\">
                                                                            Gracias por utilizar SmartDoor, por favor activa tu cuenta confirmando tu correo en la siguiente liga:
                                                                        </p>
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%!important;margin:0 0 16px;padding:0\">
                                                                            <tbody>
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                                                                    <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                                        <tbody>
                                                                                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                            <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;border-radius:5px;margin:0;padding:0;border:2px none #0071bb\" align=\"center\" bgcolor=\"#0071bb\" valign=\"top\">
                                                                                                <a rel=\"noopener noreferrer\" href=\"https://smartdoor.mx/?c=".$codigoemail."\" style=\"color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-weight:bold;text-align:center;line-height:1.3;text-decoration:none;font-size:20px;display:inline-block;border-radius:5px;width:100%;margin:0;padding:20px 0;border:0 solid #0071bb\" target=\"_blank\">Confirmar correo</a>
                                                                                            </td>
                                                                                        </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </td>
                                                                                <td style=\"word-wrap:break-word;border-collapse:collapse!important;width:0;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\"></td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 0 10px;padding:0\" align=\"left\">
                                                                            <a rel=\"noopener noreferrer\" href=\"https://smartdoor.mx/?c=".$codigoemail."\" style=\"color:#0071bb;font-family:Arial,Helvetica,sans-serif;font-weight:normal;text-align:left;line-height:1.3;text-decoration:none;margin:0;padding:0\" target=\"_blank\">https://smartdoor.mx/?c=".$codigoemail."</a>
                                                                        </p>
                                                                        <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:1.6;font-size:16.25px;margin:0 0 30px;padding:0\" align=\"left\">
                                                                            Usuario: <label style=\"color:#d03333;\">".$email."</label><br>
                                                                            Contraseña: <label style=\"color:#d03333;\">".$password."</label>                                                                        
                                                                        </p>
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                            <tbody>
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <td height=\"30px\" style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:30px;font-size:30px;margin:0;padding:0\" align=\"left\" valign=\"top\">
            
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </th>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table align=\"center\" style=\"width:100%;border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;padding:0\">
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;display:table;padding:0\">
                                                                <tbody>
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <th style=\"width:520px;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0 60px 16px\" align=\"left\">
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <th style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\">
                                                                                    <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 0 10px;padding:0\" align=\"left\">
                                                                                        Por favor no responda a este mensaje. Si necesita ayuda, por favor visita <a href=\"https://smartdoor.mx\" style=\"color:#0071bb;font-family:Arial,Helvetica,sans-serif;font-weight:normal;text-align:left;line-height:1.3;text-decoration:none;margin:0;padding:0\" target=\"_blank\">www.smartdoor.mx</a>
                                                                                    </p>
                                                                                </th>
                                                                            </tr>
                                                                        </table>
                                                                    </th>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;padding:0\">
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;display:table;padding:0\">
                                                                <tbody>
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <th style=\"width:520px;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0 60px 16px\" align=\"left\">
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;margin:10px auto;padding:0\">
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <th style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;height:0;max-width:580px;border-bottom-color:#ebf3fa;border-bottom-style:solid;clear:both;margin:10px auto;padding:0;border-width:0 0 2px\" align=\"left\">
            
                                                                                </th>
                                                                            </tr>
                                                                        </table>
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <th style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\">
                                                                                    <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 0 10px;padding:0\" align=\"left\">
                                                                                        Gracias !
                                                                                    </p>
                                                                                </th>
                                                                                <th style=\"width:0;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\"></th>
                                                                            </tr>
                                                                        </table>
                                                                    </th>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </center>
                            </td>
                        </tr>
                        </tbody>
                        <td><div style=\"display:none;white-space:nowrap;font:15px courier\"></div></td></table></div></body></html>
            ";
}

function correoNuevoPass($email){
    return "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"https://www.w3.org/TR/html4/strict.dtd\">
            <html lang=\"es\">
            <body>
                <div style=\"width:100%!important;min-width:100%;box-sizing:border-box;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;text-align:left;line-height:17px;font-size:13px;margin:0;padding:0\">
                    <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;height:100%;width:100%;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" bgcolor=\"#ebf3fa\">
                        <tbody>
                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                            <td align=\"center\" valign=\"top\" style=\"word-wrap:break-word;border-collapse:collapse!important;float:none;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0\">
                                <center style=\"width:100%;min-width:580px\">
                                    <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                        <tbody>
                                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                            <td height=\"10px\" style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:10px;font-size:10px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:center;width:580px;float:none;margin:0 auto;padding:0\" bgcolor=\"#ffffff\">
                                        <tbody>
                                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                            <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                                <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                    <tbody>
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <td height=\"60px\" style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:60px;font-size:60px;margin:0;padding:0\" align=\"left\" valign=\"top\">
            
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;display:table;padding:0\">
                                                    <tbody>
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <th style=\"width:520px;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0 60px 16px\" align=\"left\">
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <th style=\"color:#f60444;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\">
                                                                        <img src=\"https://smartdoor.mx/common_files/img/logo_small.png\" width=\"250\">
                                                                    </th>
                                                                    <th style=\"width:0;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\"></th>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;display:table;padding:0\">
                                                    <tbody>
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <th style=\"width:520px;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0 60px 16px\" align=\"left\">
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <th style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\">
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                            <tbody>
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <td height=\"30px\" style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:30px;font-size:30px;margin:0;padding:0\" align=\"left\" valign=\"top\">
            
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        <h4 style=\"color:inherit;font-family:Arial,Helvetica,sans-serif;font-weight:bold;line-height:1.3;word-wrap:normal;font-size:24px;margin:0 0 16px;padding:0\" align=\"left\">Reestrablecer contraseña</h4>
                                                                        <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:1.6;font-size:16.25px;margin:0 0 30px;padding:0\" align=\"left\">
                                                                            Gracias por utilizar SmartDoor, reestablecer contraseña, da clic en la siguiente liga para crear una contraseña nueva:
                                                                        </p>
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%!important;margin:0 0 16px;padding:0\">
                                                                            <tbody>
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                                                                    <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                                        <tbody>
                                                                                        <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                            <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;border-radius:5px;margin:0;padding:0;border:2px none #0071bb\" align=\"center\" bgcolor=\"#0071bb\" valign=\"top\">
                                                                                                <a rel=\"noopener noreferrer\" href=\"https://smartdoor.mx/?r=".str_replace("=","",base64_encode($email))."\" style=\"color:#ffffff;font-family:Arial,Helvetica,sans-serif;font-weight:bold;text-align:center;line-height:1.3;text-decoration:none;font-size:20px;display:inline-block;border-radius:5px;width:100%;margin:0;padding:20px 0;border:0 solid #0071bb\" target=\"_blank\">Cambiar mi contraseña</a>
                                                                                            </td>
                                                                                        </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </td>
                                                                                <td style=\"word-wrap:break-word;border-collapse:collapse!important;width:0;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\"></td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 0 10px;padding:0\" align=\"left\">
                                                                            <a rel=\"noopener noreferrer\" href=\"https://smartdoor.mx/?r=".str_replace("=","",base64_encode($email))."\" style=\"color:#0071bb;font-family:Arial,Helvetica,sans-serif;font-weight:normal;text-align:left;line-height:1.3;text-decoration:none;margin:0;padding:0\" target=\"_blank\">https://smartdoor.mx/?r=".str_replace("=","",base64_encode($email))."</a>
                                                                        </p>
                                                                        
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                            <tbody>
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <td height=\"30px\" style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:30px;font-size:30px;margin:0;padding:0\" align=\"left\" valign=\"top\">
            
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </th>
                                                                </tr>
                                                            </table>
                                                        </th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <table align=\"center\" style=\"width:100%;border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;padding:0\">
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;display:table;padding:0\">
                                                                <tbody>
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <th style=\"width:520px;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0 60px 16px\" align=\"left\">
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <th style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\">
                                                                                    <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 0 10px;padding:0\" align=\"left\">
                                                                                        Por favor no responda a este mensaje. Si necesita ayuda, por favor visita <a href=\"https://smartdoor.mx\" style=\"color:#0071bb;font-family:Arial,Helvetica,sans-serif;font-weight:normal;text-align:left;line-height:1.3;text-decoration:none;margin:0;padding:0\" target=\"_blank\">www.smartdoor.mx</a>
                                                                                    </p>
                                                                                </th>
                                                                            </tr>
                                                                        </table>
                                                                    </th>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;padding:0\">
                                                    <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                        <td style=\"word-wrap:break-word;border-collapse:collapse!important;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\" valign=\"top\">
                                                            <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;display:table;padding:0\">
                                                                <tbody>
                                                                <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                    <th style=\"width:520px;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 auto;padding:0 60px 16px\" align=\"left\">
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;margin:10px auto;padding:0\">
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <th style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;height:0;max-width:580px;border-bottom-color:#ebf3fa;border-bottom-style:solid;clear:both;margin:10px auto;padding:0;border-width:0 0 2px\" align=\"left\">
            
                                                                                </th>
                                                                            </tr>
                                                                        </table>
                                                                        <table style=\"border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;padding:0\">
                                                                            <tr style=\"vertical-align:top;padding:0\" align=\"left\">
                                                                                <th style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\">
                                                                                    <p style=\"color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0 0 10px;padding:0\" align=\"left\">
                                                                                        Gracias !
                                                                                    </p>
                                                                                </th>
                                                                                <th style=\"width:0;color:#5b616a;font-family:Arial,Helvetica,sans-serif;font-weight:normal;line-height:17px;font-size:13px;margin:0;padding:0\" align=\"left\"></th>
                                                                            </tr>
                                                                        </table>
                                                                    </th>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </center>
                            </td>
                        </tr>
                        </tbody>
                        <td><div style=\"display:none;white-space:nowrap;font:15px courier\"></div></td></table></div></body></html>
            ";
}