<?php
// Database connection settings for source database
$sourceHost = '10.100.100.42';
$sourceDb = 'hrappka';
$sourceUser = 'hrappka';
$sourcePass = '1UjJ7DIHXO3YpePh';
// Database connection settings for target database
$targetHost = '10.100.100.48,49827';
$targetDb = 'PartCheck';
$targetUser = 'Sa';
$targetPass = 'Shark1445NE$T';
try {
    // Connect to the source database
    $sourcePdo = new PDO("pgsql:host=$sourceHost;dbname=$sourceDb", $sourceUser, $sourcePass);
    $sourcePdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Connect to the target database
    $targetPdo = new PDO("sqlsrv:Server=$targetHost;Database=$targetDb", $targetUser, $targetPass);
    $targetPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch data from source database
    $sql = "
    SELECT
        SUM(cuce_quantity) AS sum_cuce_quantity,
        CASE WHEN ut_name IS NOT NULL THEN ut_name ELSE cuce_category_detail_additional END AS czynnosc,
        request_event.cr_number
    FROM public.company_user_calendar_events
    LEFT JOIN company_user_contracts ON cuce_entity_type = 'contracts' AND cuce_entity_fkey = cuc_id
    LEFT JOIN company_contractor_requests AS request_event ON request_event.cr_id = cuce_request_fkey
    LEFT JOIN user_tasks ON cuce_task_fkey = ut_id
    WHERE cuce_category IN ('RATE')
      AND cuce_deleted IS FALSE
      AND cuce_entity_type = 'contracts'
      AND cuce_source IN ('INTERNAL_WORKER', 'WIDGET_RCP')
      AND cuc_deleted IS false
    GROUP BY CASE WHEN ut_name IS NOT NULL THEN ut_name ELSE cuce_category_detail_additional END, request_event.cr_number
    ORDER BY request_event.cr_number DESC;
    ";
    $stmt = $sourcePdo->prepare($sql);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Clear the target table
    $clearSql = "TRUNCATE TABLE PartCheck.dbo.hrappka_godziny";
    $targetPdo->exec($clearSql);

    // Insert fetched data into the target table
    $insertSql = "INSERT INTO PartCheck.dbo.hrappka_godziny (sum_cuce_quantity, czynnosc, cr_number) VALUES (?, ?, ?)";
    $insertStmt = $targetPdo->prepare($insertSql);

    foreach ($data as $row) {
        $insertStmt->execute([$row['sum_cuce_quantity'], $row['czynnosc'], $row['cr_number']]);
    }

    echo "Data transferred successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>