<?php

//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require_once 'dbconnect.php';
require 'vendor/autoload.php';

$comment = $_POST['comment'];
$numbermesser = $_POST['numbermesser'];
$id = $_POST['id'];

$sql2 = "SELECT [ProgramName]
FROM SNDBASE_PROD.dbo.Program
WHERE [ArchivePacketID]=$id;";
$datas1 = sqlsrv_query($conn, $sql2);
$program = null;
// Sprawdź, czy zapytanie się powiodło
if ($datas1 === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Iteruj przez wyniki zapytania i dodaj każdy adres e-mail jako odbiorcę
while ($row = sqlsrv_fetch_array($datas1, SQLSRV_FETCH_ASSOC)) {
    $program=$row['ProgramName'];
}

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);


try {
    
//  $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output

    $mail->isSMTP();    

//SMTPOptions for Office 365
    $mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ));                                                             //Send using SMTP

// Office 365 SMTP HOST
   $mail->Host       = 'smtp.office365.com';
   $mail->SMTPAuth   = true;                                      //Enable SMTP authentication
   $mail->Username   = 'tarkonprograms@outlook.com';                      //SMTP username
   $mail->Password   = 'gR@ndix@2000';                         //SMTP password
   $mail->Port       = 587;                                        //TCP port to connect to; use 587 if 

//Set From Email ID and NAME
    $mail->setFrom('tarkonprograms@outlook.com', 'Tarkon Programs');
    
//Recipients
$sql = "SELECT distinct [user] FROM PartCheck.dbo.Persons WHERE role_messer=1 AND [user] LIKE '%@%';";
$datas = sqlsrv_query($conn, $sql);

// Sprawdź, czy zapytanie się powiodło
if ($datas === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Iteruj przez wyniki zapytania i dodaj każdy adres e-mail jako odbiorcę
while ($row = sqlsrv_fetch_array($datas, SQLSRV_FETCH_ASSOC)) {
    $email = $row['user'];
    $mail->addAddress($email);
}

// Następnie ustaw SMTPSecure na 'tls'
$mail->SMTPSecure = 'tls';                                    //SMTPS uses TLS cryptographic protocols for improved security

    //Attachments
//    $mail->addAttachment('/var/tmp/file.tar.gz');                 //Add attachments
//    $mail->addAttachment('gmail.png', 'new.jpg');                 //Add attachment to replace attachment name

    //Content
    $mail->isHTML(true); // Ustawienie formatu e-maila na HTML
$mail->Subject = "Zdarzenie: Nie wykonanie programu $program"; // Temat e-maila
$mail->CharSet = 'UTF-8';
// Treść e-maila w HTML
$mail->Body = '<html>
                  <body>
                    <div style="background-color: #f2f2f2; padding: 20px; font-family: Arial, sans-serif;">
                      <h1 style="color: #333;">Hello!</h1>
                      <p style="color: #666;">Program '.$program.' nie mógł zostać wykonany!!! </p>
                      <p style="color: #666;">Osoba zgłaszająca: '.$numbermesser.' </p>
                      <p style="color: #666;">Powód: '.$comment.'</p>
                      <p style="color: #666;">Thank you for using our services.</p>
                      <p style="color: #999;">Best Regards,<br>Tarkon Programs</p>
                    </div>
                  </body>
                </html>';
if ($mail->send()) {                                       
   echo 'Message has been Success';
}else{
   echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo} <br> Mailer Debug:".$mail->SMTPDebug = SMTP::DEBUG_SERVER;
}

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}