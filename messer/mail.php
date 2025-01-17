<?php

//These must be at the top of your script, not inside a function
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require_once("../dbconnect.php");


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
$mail->Host = '	s190.cyber-folks.pl';
$mail->SMTPAuth = true;                                      //Enable SMTP authentication
$mail->Username = 'tarkon.powiadomienia@crm-link.eu';                      //SMTP username
$mail->Password = 'G@*C!Ij-g-24evVz';                         //SMTP password
$mail->Port = 587;                                        //TCP port to connect to; use 587 if 

//Set From Email ID and NAME
    $mail->setFrom('tarkon.powiadomienia@crm-link.eu', 'Notifications Noreply');
    
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
$mail->Body = '<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szablon maila</title>
    <style>
        /* Ogólne style */
        body {
            margin-left: auto;
            margin-right: auto;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        table {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: #fff;
            font-size: 18px;
            text-align: center;
        }
        td {
            border-bottom: 1px solid #ddd;
        }
        .header {
            padding: 20px;
            text-align: center;
            background-color: #007BFF;
            color: white;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            font-size: 16px;
            line-height: 1.6;
        }
        .button-container {
            text-align: center;
            margin: 20px 0;
        }
        .button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #666;
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <table>
        <!-- Nagłówek -->
        <tr>
            <th class="header">Powiadomienie</th>
        </tr>
        <!-- Treść wiadomości -->
        <tr>
            <td class="content">
                Cześć, <br />
                Program '.$program.' nie mógł zostać wykonany!!!<br />
                Osoba zgłaszająca: '.$numbermesser.' <br>
                Powód: '.$comment.'<br>
            </td>
        </tr>
    </table>
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