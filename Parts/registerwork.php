<script>
    (function() {
        const rfid = localStorage.getItem('number1');

        // Jeśli w adresie URL nie ma ?rfid=, to przekieruj z nim
        if (rfid && !window.location.search.includes('rfid=')) {
            const url = new URL(window.location.href);
            url.searchParams.set('rfid', rfid);
            window.location.href = url.toString();
        }
    })();
</script>

<?php
$cuce_task = isset($_GET['cuce_task']) && $_GET['cuce_task'] !== '' ? $_GET['cuce_task'] : null;
$cuce_request = isset($_GET['cuce_request']) && $_GET['cuce_request'] !== '' ? $_GET['cuce_request'] : null;
$cuce_category_detail_additional = isset($_GET['cuce_category_detail_additional']) && $_GET['cuce_category_detail_additional'] !== '' ? $_GET['cuce_category_detail_additional'] : null;
$cr_contractor_fkey = isset($_GET['cr_contractor_fkey']) && $_GET['cr_contractor_fkey'] !== '' ? $_GET['cr_contractor_fkey'] : null;
$cuce_position = isset($_GET['cuce_position']) && $_GET['cuce_position'] !== '' ? $_GET['cuce_position'] : null;
$rfid = isset($_GET['rfid']) && $_GET['rfid'] !== '' ? $_GET['rfid'] : null;
$project = isset($_GET['project']) && $_GET['project'] !== '' ? $_GET['project'] : null;


if (
    is_null($cuce_task) ||
    is_null($cuce_request) ||
    is_null($cuce_category_detail_additional) ||
    is_null($cr_contractor_fkey) ||
    is_null($cuce_position) ||
    is_null($rfid) ||
    is_null($project)
) {
    die("Brakuje jednego z wymaganych parametrów.");
}

try {
    $dsn = "pgsql:host=10.100.100.42;port=5432;dbname=hrappka;";
    $username = "hrappka";
    $password = "1UjJ7DIHXO3YpePh";

    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}

$sql = "SELECT
    usr_id AS cuce_user,
    cuc_id AS cuce_contract_fkey_and_cuce_entity_fkey,
    cuc_company_fkey AS cuc_company_fkey
FROM user_ids
INNER JOIN users ON uid_user_fkey = usr_id AND uid_name = 'RFID' AND uid_value = :rfid
LEFT JOIN company_user_contracts 
    ON usr_id = cuc_user_fkey 
    AND cuc_deleted = false 
    AND (
        (cuc_end_date < CURRENT_DATE AND cuc_cancel_date < CURRENT_DATE) 
        OR 
        (cuc_end_date IS NULL AND cuc_cancel_date IS NULL)
        OR 
        (cuc_end_date IS NULL AND cuc_cancel_date < CURRENT_DATE)
    )";

$stmt = $pdo->prepare($sql);
$stmt->execute(['rfid' => $rfid]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);
$cuce_user = null;
$cuce_contract_fkey_and_cuce_entity_fkey = null;
$cuc_company_fkey = null;
if ($row) {
    $cuce_user = $row['cuce_user'];
    $cuce_contract_fkey_and_cuce_entity_fkey = $row['cuce_contract_fkey_and_cuce_entity_fkey'];
    $cuc_company_fkey = $row['cuc_company_fkey'];
    
    // Możesz teraz używać tych zmiennych
} else {
    // Brak danych dla podanego RFID
    die("Nie znaleziono danych dla podanego RFID.");
}

require_once("../dbconnect.php");
$params = array($rfid);
$sql1 = "SELECT PersonsID FROM dbo.PersonsID WHERE identyfikator = ?";
$stmt2 = sqlsrv_query($conn, $sql1, $params);

if ($stmt2 === false) {
    die(print_r(sqlsrv_errors(), true));
}

$row2 = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC);

if ($row2) {
    $personID = $row2['PersonsID'];
    echo "PersonsID: " . $personID;
} else {
    echo "Nie znaleziono rekordu dla podanego identyfikatora.";
}

$sql = "INSERT INTO PartCheck.dbo.HrapWorkTime
(cuce_user, cuce_request, cuce_category_detail_additional, cuce_position, cuce_task, cuce_contractor_fkey, cuce_contract_fkey, cuce_entity_fkey, cuce_company_fkey, PersonID, Project)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$params = array(
    $cuce_user,
    $cuce_request,
    $cuce_category_detail_additional,
    $cuce_position,
    $cuce_task,
    $cr_contractor_fkey,
    $cuce_contract_fkey_and_cuce_entity_fkey,
    $cuce_contract_fkey_and_cuce_entity_fkey,
    $cuc_company_fkey,
    $personID,
    $project
);

$stmtInsert = sqlsrv_prepare($conn, $sql, $params);

if (!$stmtInsert || !sqlsrv_execute($stmtInsert)) {
    die(print_r(sqlsrv_errors(), true));
}

header("Location: worktime.php?identyfikator=$rfid");
exit();