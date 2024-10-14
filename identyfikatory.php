<?php
        
        require_once('dbconnect.php');
        require_once('auth.php');
        if(!isUserAdmin()){
            header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
        }
    ?>
<head>
<?php require_once 'auth.php'; ?>
   <?php require_once("globalhead.php"); ?>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container" style="width:40%; margin-left: auto;margin-right:auto;">
    <!-- 2024 Created by: Rafał Pezda-->
<!-- link: https://github.com/RafixOOO -->
    <?php
    if (isset($_GET['id'])) {
        $userID = $_GET['id'];
    }
        $sql = "SELECT pid.id , pid.identyfikator , p.imie_nazwisko 
from PartCheck.dbo.PersonsID pid
inner join PartCheck.dbo.Persons p on p.Id = pid.PersonsID 
where pid.PersonsID = $userID";
                    $stmt = sqlsrv_query($conn, $sql);
                    $name=1;
                    while ($data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                        if($name===1){
                            echo "<p>".$data['imie_nazwisko']."</p>";
                            $name=0;
                        }
                    ?>
                    <hr>
                    <form method="post" action="usun_identyfikator.php">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                <label><?php echo $data['identyfikator']; ?></label>
                    <button type="submit" name="change_status" class="btn btn-danger">Usuń</button>
                    </div>
            </form>
            
                    <?php } ?>
                    <hr>
                    <form method="post" action="dodaj_identyfikator.php">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <input type="hidden" name="personid" value="<?php echo $userID; ?>">
                    <input type="number" class="form-control" id="id" name="id"
                    placeholder="Identyfikator" style="width:30%;" required>
                    <button type="submit" name="change_status" class="btn btn-success">Dodaj</button>
                    </div>
                    <div style="float:right;">
                      <a href="zarzadzaj.php"> <button type="button" class="btn btn-secondary">wróć</button></a>
                      </div>
</body>
</html>