         <?php
            require_once('dbconnect.php');

            $sq6 = "SELECT
                Count([ArchivePacketID]) as suma
                FROM [SNDBASE_PROD].[dbo].[Program]
                 where [Comment]='nie znaleziono arkusza' or [Comment]='zla jakosc otworow' or [Comment]='DARIUSZ MALEK' or [Comment]='zla jakosc faz' or [Comment]='inne' ";
            $datas3 = sqlsrv_query($conn, $sq6);
            $max3 = "";
            while ($row4 = sqlsrv_fetch_array($datas3, SQLSRV_FETCH_ASSOC)) {
                $max3 = $row4["suma"];
            }

            ?>
              <a href="/programs/Tarkonprograms/index.php" class="link-dark link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover"><h1><b>Tarkon <i>programs</i></b></h1></a>
              <br/>
         <div>
             <ul style="float:left;" class="nav nav-tabs" id="myTab" role="tablist">
                 <li class="nav-item">
                     <a class="nav-link active" id="home-tab" data-toggle="tab" href="main.php" role="tab" aria-selected="true">Aktualne</a>
                 </li>
                 <li class="nav-item ">
                     <a class="nav-link active" id="profile-tab" data-toggle="tab" href="wykonane.php" role="tab" aria-controls="profile" aria-selected="true">Wykonane
                     </a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link active" id="contact-tab" data-toggle="tab" href="niewykonane.php" role="tab" aria-controls="Nie wykonane" aria-selected="true">Nie wykonane
                     <?php if($max3!=0){ echo "<sup><span class='badge rounded-pill badge-notification bg-danger'><span class='visually-hidden'>New alerts</span></span></sup>"; } ?>
                     </a>
                 </li>
             </ul>
             <?php if (isUserAdmin()){ ?>
                
                 </form>
                <form style="float:right;" class="form-inline my-2 my-lg-0" method="POST" action="logout.php">
                 <input class="btn btn-outline-success my-2 my-sm-0 btn-sm" type="submit" value="Wyloguj">
                 </form>
                 <?php } else{ ?>
                    <form style="float:right;" class="form-inline my-2 my-lg-0" action="login.php">
                 <input class="btn btn-outline-success my-2 my-sm-0 btn-sm" type="submit" value="Zaloguj">
                 </form>
             <?php } ?>
             
         </div>
         <div style="clear:both;"></div>

         <button
        type="button"
        class="btn btn-danger btn-floating"
        id="btn-back-to-top"
        >
  <i class="bi bi-arrow-up"></i>
</button>