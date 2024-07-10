<?php


try {
    $dsn = "pgsql:host=10.100.100.48;port=5432;dbname=punktualnik_db;";
    $username = "postgres";
    $password = "sa";

    // Utworzenie instancji PDO
    $pdo = new PDO($dsn, $username, $password);

    // Ustawienie opcji PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Błąd połączenia z bazą danych: " . $e->getMessage();
}

$query1 = "
SELECT
    COUNT(DISTINCT CASE WHEN l.in_out = '0' THEN o.idx_osoby END) -
    COUNT(DISTINCT CASE WHEN l.in_out = '1' THEN o.idx_osoby END) AS diff_count
FROM users o
JOIN att_log l ON l.idx_osoby = o.idx_osoby
JOIN dzialy d ON o.idx_dzialu = d.idx_dzialu
WHERE
    l.aktywny = 'true'
    AND l.idx_device IN ('37', '1', '38', '5', '2', '43', '42', '4', '6', '3')
    AND d.nazwa LIKE '%Produkcja%'
    AND CAST(l.data_czas AS DATE) = CURRENT_DATE;

";

$pracownicy = $pdo->query($query1);

require_once("dbconnect.php");


$osoba = "WITH SumowaniePierceQty AS (
    SELECT
        SUM(PierceQty) AS Suma_QtyInProcess,
        CASE WHEN CHARINDEX(',', [Comment]) > 0 THEN
            SUBSTRING([Comment], 1, CHARINDEX(',', [Comment]) - 1)
        ELSE
            [Comment]
        END AS CommentBeforeComma,
        CASE WHEN CHARINDEX(',', [Comment]) > 0 THEN
            CONCAT(
                SUBSTRING([Comment], CHARINDEX(',', [Comment]) + 1, 4),  -- Pobierz rok
                '-',
                SUBSTRING([Comment], CHARINDEX(',', [Comment]) + 6, 2)   -- Pobierz miesiąc
            )
        ELSE
            NULL  -- or any default value you want when there is no comma
        END AS CommentAfterComma
    FROM 
        [SNDBASE_PROD].[dbo].[ProgArchive]
    WHERE 
        [Comment] != ''
        AND ISDATE(
            SUBSTRING([Comment], CHARINDEX(',', [Comment]) + 1, LEN([Comment]))
        ) = 1
        AND SUBSTRING([Comment], CHARINDEX(',', [Comment]) + 1, 7) = CONCAT(YEAR(GETDATE()), '-', RIGHT('0' + CAST(MONTH(GETDATE()) AS VARCHAR), 2))
    GROUP BY 
        CASE WHEN CHARINDEX(',', [Comment]) > 0 THEN
            SUBSTRING([Comment], 1, CHARINDEX(',', [Comment]) - 1)
        ELSE
            [Comment]
        END,
        CASE WHEN CHARINDEX(',', [Comment]) > 0 THEN
            CONCAT(
                SUBSTRING([Comment], CHARINDEX(',', [Comment]) + 1, 4),  -- Pobierz rok
                '-',
                SUBSTRING([Comment], CHARINDEX(',', [Comment]) + 6, 2)   -- Pobierz miesiąc
            )
        ELSE
            NULL
        END
),
SumowanieIloscZrealizowana AS (
    SELECT
        r.Osoba,
        SUM(r.[Ilosc_zrealizowana]) AS ilosc_zrealizowana,
        DATEPART(YEAR, r.[Data]) AS Rok,
        DATEPART(MONTH, r.[Data]) AS Miesiac
    FROM
        [PartCheck].dbo.Product_Recznie r
    WHERE
        DATEPART(YEAR, r.[Data]) = YEAR(GETDATE()) -- Filtruje tylko dane z bieżącego roku
    GROUP BY
        r.Osoba,
        DATEPART(YEAR, r.[Data]),
        DATEPART(MONTH, r.[Data])
)
SELECT
    COALESCE(NULLIF(SP.CommentBeforeComma, ''), SI.Osoba COLLATE Latin1_General_CS_AS) AS Osoba,
    SP.Suma_QtyInProcess as messer,
    SI.ilosc_zrealizowana as recznie,
    COALESCE(SP.Suma_QtyInProcess, 0) + COALESCE(SI.ilosc_zrealizowana, 0) AS suma
FROM
    SumowaniePierceQty SP
full JOIN
    SumowanieIloscZrealizowana SI ON SP.CommentBeforeComma COLLATE Latin1_General_CS_AS = SI.Osoba COLLATE Latin1_General_CS_AS
    ORDER BY
    COALESCE(SP.Suma_QtyInProcess, 0) + COALESCE(SI.ilosc_zrealizowana, 0) DESC;";

$osoby = sqlsrv_query($conn, $osoba);

$V630="SELECT
    SUM(AmountDone) AS Suma_AmountDone,
    DATEFROMPARTS(YEAR(ModificationDate), MONTH(ModificationDate), 1) AS Miesiac
FROM 
    PartCheck.dbo.Product_V630
WHERE
    (ModificationDate >= DATEADD(MONTH, -11, DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE()), 0))  -- Od ostatnich 12 miesięcy
    AND ModificationDate < DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE()) + 1, 0))  -- Do początku bieżącego miesiąca
    OR (YEAR(ModificationDate) = YEAR(GETDATE()) AND MONTH(ModificationDate) = MONTH(GETDATE()))  -- Bieżący miesiąc
GROUP BY
    DATEFROMPARTS(YEAR(ModificationDate), MONTH(ModificationDate), 1)
ORDER BY
    Miesiac;
";

$V630data = sqlsrv_query($conn, $V630);

$messer="SELECT
    SUM(QtyProgram) AS Suma_AmountDone,
    DATEFROMPARTS(YEAR(ArcDateTime), MONTH(ArcDateTime), 1) AS Miesiac
FROM 
    PartCheck.dbo.PartArchive_Messer
WHERE
    (ArcDateTime >= DATEADD(MONTH, -11, DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE()), 0))  -- Od ostatnich 12 miesięcy
    AND ArcDateTime < DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE()) + 1, 0))  -- Do początku bieżącego miesiąca
    OR (YEAR(ArcDateTime) = YEAR(GETDATE()) AND MONTH(ArcDateTime) = MONTH(GETDATE()))  -- Bieżący miesiąc
GROUP BY
    DATEFROMPARTS(YEAR(ArcDateTime), MONTH(ArcDateTime), 1)
ORDER BY
    Miesiac;
";

$messerdata=sqlsrv_query($conn, $messer);

$recznie="SELECT
    SUM(Ilosc_zrealizowana) AS Suma_AmountDone,
    DATEFROMPARTS(YEAR([Data]), MONTH([Data]), 1) AS Miesiac
FROM 
    PartCheck.dbo.Product_Recznie
WHERE
    ([Data] >= DATEADD(MONTH, -11, DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE()), 0))  -- Od ostatnich 12 miesięcy
    AND [Data] < DATEADD(MONTH, DATEDIFF(MONTH, 0, GETDATE()) + 1, 0))  -- Do początku bieżącego miesiąca
    OR (YEAR([Data]) = YEAR(GETDATE()) AND MONTH([Data]) = MONTH(GETDATE()))  -- Bieżący miesiąc
GROUP BY
    DATEFROMPARTS(YEAR([Data]), MONTH([Data]), 1)
ORDER BY
    Miesiac;
";

$reczniedata=sqlsrv_query($conn, $recznie);

$sumadetali="SELECT 
    SUM(Suma_AmountDone) AS Suma_AmountDone
FROM (
    SELECT
        SUM(AmountDone) AS Suma_AmountDone
    FROM 
        PartCheck.dbo.Product_V630
    WHERE
        ModificationDate >= DATEADD(DAY, -29, GETDATE())  -- Od ostatnich 30 dni
    UNION ALL
    SELECT
        SUM(QtyProgram) AS Suma_AmountDone
    FROM 
        PartCheck.dbo.PartArchive_Messer
    WHERE
        ArcDateTime >= DATEADD(DAY, -29, GETDATE())  -- Od ostatnich 30 dni
    UNION ALL
    SELECT
        SUM(Ilosc_zrealizowana) AS Suma_AmountDone
    FROM 
        PartCheck.dbo.Product_Recznie
    WHERE
        [Data] >= DATEADD(DAY, -29, GETDATE())  -- Od ostatnich 30 dni
) AS SumyOstatnich30Dni;";

$sumadetalidata=sqlsrv_query($conn, $sumadetali);

$sumadniadetali="SELECT 
    Data,
    SUM(Suma_AmountDone) AS Suma_AmountDone
FROM (
    SELECT
        SUM(AmountDone) AS Suma_AmountDone,
        CAST(ModificationDate AS DATE) AS Data
    FROM 
        PartCheck.dbo.Product_V630
    WHERE
        ModificationDate >= DATEADD(DAY, -29, GETDATE())  -- Od ostatnich 30 dni
    GROUP BY
        CAST(ModificationDate AS DATE)
    UNION ALL
    SELECT
        SUM(QtyProgram) AS Suma_AmountDone,
        CAST(ArcDateTime AS DATE) AS Data
    FROM 
        PartCheck.dbo.PartArchive_Messer
    WHERE
        ArcDateTime >= DATEADD(DAY, -29, GETDATE())  -- Od ostatnich 30 dni
    GROUP BY
        CAST(ArcDateTime AS DATE) 
    UNION ALL
    SELECT
        SUM(Ilosc_zrealizowana) AS Suma_AmountDone,
        CAST([Data] AS DATE) AS Data
    FROM 
        PartCheck.dbo.Product_Recznie
    WHERE
        [Data] >= DATEADD(DAY, -29, GETDATE())  -- Od ostatnich 30 dni
    GROUP BY
        CAST([Data] AS DATE)
) AS SumyOstatnich30Dni
GROUP BY
    Data
ORDER BY
    Data;";

$sumadniadetalidata=sqlsrv_query($conn, $sumadniadetali);
