<?php
require __DIR__ . '/../../vendor/autoload.php';
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
try {
    $dsn = "pgsql:host=10.100.100.42;port=5432;dbname=hrappka;";
    $username = "hrappka";
    $password = "1UjJ7DIHXO3YpePh";

    // Utworzenie instancji PDO
    $pdo = new PDO($dsn, $username, $password);

    // Ustawienie opcji PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
}

$now = new DateTime();

// Zaokrąglenie do pełnej godziny w dół
$now->setTime($now->format('H'), 0, 0);

// Cofnięcie o jedną godzinę
$now->modify('-1 hour');

// Wyświetlenie wyniku w formacie 'Y-m-d H:i:s'
echo $now->format('Y-m-d H:i:s');
$date = $now->format('Y-m-d H:i:s');
$sqlcreate = "SELECT
    cci_id,
    cci_number AS numer_zewnetrzny,
    cci_issue_date AS data_wystawienia,
    cci_deadline AS termin_płatności,
    cci_seller_name AS nazwa_dostawcy,
    cci_seller_nip AS NIP_dostawcy,
    cta_amount AS koszt_w_proj,
    cci_net AS wartosc_netto_faktury,
    cci_gross AS wartosc_brutto_faktury,
    cci_vat AS VATzfktury,
    cci_currency AS waluta,
    cci_exchange_rate,
    cta_creation_time,
    cta_last_update_time,
    -- Nowa kolumna przeliczająca wartość cta_amount na PLN, jeśli waluta jest inna niż PLN
    CASE 
        WHEN cci_currency != 'PLN' THEN cta_amount * cci_exchange_rate
        ELSE cta_amount
    END AS netto_w_PLN,
    cci_system_state AS status,
    cci_registry_number AS numer_z_rejestru,
    cr_number AS Projekt,
    cr_name AS nazwa_proj,
    proj.cr_operator_fkey, -- Dodanie klucza cr_operator_fkey
    CONCAT(REPLACE(cast(f_path AS text), '/var/www/hrappka/public', 'http://hrappka.budhrd.eu'), f_name) AS Link_url,
    u2.usr_email as email_operator,
    u2.usr_name AS Operator_name
FROM public.cost_allocation
INNER JOIN public.company_contractor_invoices fak ON cci_id = cta_invoice_fkey
INNER JOIN public.company_contractor_requests proj ON cr_id = cta_project_fkey
inner join public.company_accountants_map cam on cam.cam_entity_fkey = proj.cr_id and cam.cam_entity_type ='contractors-requests'
LEFT JOIN public.users u2 ON u2.usr_id = cam.cam_accountant_entity_fkey and cam.cam_accountant_entity_type = 'user' -- Połączenie dla Imie_nazwisko_opiekuna_proj
LEFT JOIN public.files files ON f_entity_fkey = cta_invoice_fkey
WHERE files.f_deleted IS FALSE 
  AND f_entity_type = 'invoice-printout' 
  AND cta_deleted IS FALSE 
  AND (cta_creation_time > :date or cta_last_update_time > :date) and cam.cam_deleted = false
ORDER BY cci_id DESC;";

$stmt = $pdo->prepare($sqlcreate);

// Przekazanie wartości do parametru :date
$stmt->bindParam(':date', $date);

// Wykonanie zapytania
$stmt->execute();

// Pobranie wyników jako tablica asocjacyjna
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

foreach ($results as $row) {
try {
    
//  $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output

    $mail->isSMTP();    


// Office 365 SMTP HOST
//SMTPOptions for Office 365
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ));                                                             //Send using SMTP

// Office 365 SMTP HOST
$mail->Host = '';
$mail->SMTPAuth = true;                                      //Enable SMTP authentication
$mail->Username = '';                      //SMTP username
$mail->Password = '';                         //SMTP password
$mail->Port = 587;                                      //TCP port to connect to; use 587 if 

//Set From Email ID and NAME
    $mail->setFrom('', 'Notifications Noreply');
    

//$email = $row['usr_email'];
$mail->clearAddresses();
$mail->clearCCs();
$mail->clearBCCs();
$email = $row['email_operator'];
$mail->addAddress($email);

    //Attachments
//    $mail->addAttachment('/var/tmp/file.tar.gz');                 //Add attachments
//    $mail->addAttachment('gmail.png', 'new.jpg');                 //Add attachment to replace attachment name

    //Content
    $mail->isHTML(true); // Ustawienie formatu e-maila na HTML
$mail->Subject = "Nowa Faktura na Projekcie $row[projekt]"; // Temat e-maila
$mail->CharSet = 'UTF-8';
// Treść e-maila w HTML
if($row['cta_last_update_time']==''){
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
                Cześć '.$row['operator_name'].',
                Do twojego projektu o nr '.$row['projekt'].' została dodana nowa faktura o nr <a href="'.$row['link_url'].'">'.$row['numer_zewnetrzny'].'</a>.<br>
                Kwota dodana do projektu, to '.$row['koszt_w_proj'].' '.$row['waluta'].' <br>
                Nazwa dostawcy: '.$row['nazwa_dostawcy'].'<br>
                Data wystawienia: '.$row['data_wystawienia'].'<br><br>
                Faktura jest dostępna w linku na dole.
            </td>
        </tr>
        <!-- Przycisk CTA -->
        <tr>
            <td class="button-container">
                <a href="'.$row['link_url'].'" class="button">Faktura</a>
            </td>
        </tr>
    </table>
</body>
</html>';
}else{
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
                Cześć '.$row['operator_name'].',
                Do twojego projektu o nr '.$row['projekt'].' została zaktualizowana faktura o nr <a href="'.$row['link_url'].'">'.$row['numer_zewnetrzny'].'</a>.<br>
                Kwota dodana do projektu, to '.$row['koszt_w_proj'].' '.$row['waluta'].' <br>
                Nazwa dostawcy: '.$row['nazwa_dostawcy'].'<br>
                Data wystawienia: '.$row['data_wystawienia'].'<br><br>
                Faktura jest dostępna w linku na dole.
            </td>
        </tr>
        <!-- Przycisk CTA -->
        <tr>
            <td class="button-container">
                <a href="'.$row['link_url'].'" class="button">Faktura</a>
            </td>
        </tr>
    </table>
</body>
</html>';
}

if ($mail->send()) {                                       
   echo 'Message has been Success';
}else{
   echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo} <br> Mailer Debug:".$mail->SMTPDebug = SMTP::DEBUG_SERVER;
}

} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}}