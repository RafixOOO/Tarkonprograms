<?php 
require_once("dbconnectsap.php");

$query = "
SELECT 
    T0.\"ItemCode\", 
    T5.\"ItemName\", 
    T1.\"BinCode\",
    SUM(T3.\"OnHandQty\") as \"Batch Quantity\",
    T5.\"InvntryUom\",
    T4.\"DistNumber\",
    T4.\"ExpDate\",
    T4.\"MnfSerial\" as \"Projekt\",
    T4.\"LotNumber\" as \"Rezerwacja\",
    T4.\"InDate\",
    T4.\"U_NRWYTOPU\",
    T5.\"LastPurPrc\"
FROM 
    \"TARKON_PROD\".OITW T0
    INNER JOIN \"TARKON_PROD\".OBIN T1 ON T0.\"WhsCode\" = T1.\"WhsCode\"
    INNER JOIN \"TARKON_PROD\".OIBQ T2 ON T2.\"WhsCode\" = T0.\"WhsCode\" AND T1.\"AbsEntry\" = T2.\"BinAbs\" AND T0.\"ItemCode\" = T2.\"ItemCode\"
    INNER JOIN \"TARKON_PROD\".OBBQ T3 ON T3.\"ItemCode\" = T0.\"ItemCode\" AND T3.\"BinAbs\" = T1.\"AbsEntry\" AND T3.\"WhsCode\" = T2.\"WhsCode\"
    INNER JOIN \"TARKON_PROD\".OBTN T4 ON T4.\"AbsEntry\" = T3.\"SnBMDAbs\"
    INNER JOIN \"TARKON_PROD\".OITM T5 ON T5.\"ItemCode\" = T0.\"ItemCode\"
WHERE 
    T2.\"OnHandQty\" > 0 AND
    T3.\"OnHandQty\" > 0 AND
    T2.\"OnHandQty\" > 0  AND
    T0.\"ItemCode\" LIKE '%STA%'
GROUP BY 
    T0.\"ItemCode\", T5.\"ItemName\", T0.\"WhsCode\", 
    T0.\"WhsCode\", T1.\"BinCode\", T4.\"DistNumber\",
    T4.\"ExpDate\",T4.\"MnfSerial\" ,T4.\"InDate\",T4.\"LotNumber\",T4.\"U_NRWYTOPU\",T5.\"InvntryUom\" ,T5.\"LastPurPrc\"
ORDER BY T0.\"ItemCode\", T0.\"WhsCode\", T1.\"BinCode\"
";
$result = odbc_exec($conn, $query);
if (!$result) {
    die("Błąd zapytania: " . odbc_errormsg($conn));
}

?>