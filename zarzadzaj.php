<!DOCTYPE html>
<html lang="en">
    <?php
        
        require_once('dbconnect.php');
    ?>
<head>
   <?php require_once("globalhead.php"); ?>
</head>

<body class="p-3 mb-2 bg-light bg-gradient text-dark" id="error-container">
<?php require_once("globalnav.php"); ?>
            <div class="container">
            <div class="table-responsive">
            <a href="dodaj.php" class="btn btn-success float-end">Dodaj</a>
            
            <table class="table table-sm">
  <thead>
    <tr>
      <th scope="col">Identyfikator</th>
      <th scope="col">Imię i nazwisko</th>
      <th scope="col">Login</th>
      <th scope="col">Messer</th>
      <th scope="col">Cutlogic</th>
      <th scope="col">Parts</th>
      <th scope="col">Parts Kierownik</th>
      <th scope="col">Admin</th>
      <th scope="col">Zarządzaj</th>
      
    </tr>
  </thead>
  <tbody>
    <?php 
    
        $sql = "Select * from dbo.Persons";
        $stmt = sqlsrv_query($conn, $sql);
        while ($data = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {           
    ?>
    <tr>
        <td><?php echo $data['identyfikator'] ?></td>
        <td><?php echo $data['imie_nazwisko'] ?></td>
        <td><?php echo $data['user'] ?></td>
        <?php if($data['user']!=""){ ?>
        <td>
            <form method="post" action="zmien_status.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <input type="hidden" name="role" value="role_messer">
                <?php if( $data['role_messer']==1){ ?>
                <button type="submit" name="change_status" class="btn btn-success"></button><?php } else { ?>
                    <button type="submit" name="change_status" class="btn btn-danger"></button> <?php } ?>
            </form>
        </td>
        <td>
            <form method="post" action="zmien_status.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <input type="hidden" name="role" value="role_cutlogic">
                <?php if( $data['role_cutlogic']==1){ ?>
                <button type="submit" name="change_status" class="btn btn-success"></button><?php } else { ?>
                    <button type="submit" name="change_status" class="btn btn-danger"></button> <?php } ?>
            </form>
        </td>
        <td>
            <form method="post" action="zmien_status.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <input type="hidden" name="role" value="role_parts">
                <?php 
                if($data['role_parts_kier']==1){
                    if( $data['role_parts']==1){ ?>
                        <button type="submit" name="change_status" class="btn btn-success" disabled></button><?php } else { ?>
                            <button type="submit" name="change_status" class="btn btn-danger" disabled></button> <?php } ?>
                
                <?php } else {
                if( $data['role_parts']==1){ ?>
                <button type="submit" name="change_status" class="btn btn-success"></button><?php } else { ?>
                    <button type="submit" name="change_status" class="btn btn-danger"></button> <?php }} ?>
            </form>
        </td>
        <td>
        <form method="post" action="zmien_status.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <input type="hidden" name="role" value="role_parts_kier">
                
                <?php if( $data['role_parts_kier']==1){ ?>
                <button type="submit" name="change_status" class="btn btn-success"></button><?php } else { ?>
                    <button type="submit" name="change_status" class="btn btn-danger"></button> <?php } ?>
            </form>
        </td>
        <td>
            <form method="post" action="zmien_status.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <input type="hidden" name="role" value="role_admin">
                
                <?php if( $data['role_admin']==1){ ?>
                <button type="submit" name="change_status" class="btn btn-success"></button><?php } else { ?>
                    <button type="submit" name="change_status" class="btn btn-danger"></button> <?php } ?>
            </form>
        </td>
        
        <?php }  ?>
        <td>
            <?php if($data['user']!=""){ ?>
            <form method="post" action="usun_haslo.php" style="float: left;">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <button type="submit" name="usun_haslo" class="btn btn-warning">Usuń hasło</button>
            </form>
            <form method="post" action="usun_konto.php" style="float: left; margin-left:2%;">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <button type="submit" name="usun_konto" class="btn btn-warning">Usuń konto</button>
            </form>
            <div style="clear:both;"></div>
            <?php } else{ ?>
                <form method="post" action="zmien_status.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <input type="hidden" name="role" value="prac_messer">
                
                <?php if( $data['prac_messer']==1){ ?>
                <button type="submit" name="change_status" class="btn btn-success"></button><?php } else { ?>
                    <button type="submit" name="change_status" class="btn btn-danger"></button> <?php } ?>
            </form>
                <td>
                
                </td>
                <td><form method="post" action="zmien_status.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <input type="hidden" name="role" value="prac_parts">
                
                <?php if( $data['prac_parts']==1){ ?>
                <button type="submit" name="change_status" class="btn btn-success"></button><?php } else { ?>
                    <button type="submit" name="change_status" class="btn btn-danger"></button> <?php } ?>
            </form></td>
                <td></td>
                <td></td>
                <td>
                <form method="post" action="usun_konto.php">
                <input type="hidden" name="person_id" value="<?php echo $data['Id'] ?>">
                <button type="submit" name="usun_konto" class="btn btn-warning">Usuń konto</button>
            </form>
                </td>
                <?php } ?>
            
        </td>
    </tr>
    
    <?php } ?>
  </tbody>
</table>
</div>
        </div>
</div>
</div>
</div>
</body>
</html>