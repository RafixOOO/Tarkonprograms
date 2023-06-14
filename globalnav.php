         <?php
            require_once('dbconnect.php');

            $sq5 = "SELECT
                Count([ArchivePacketID]) as suma
                FROM [SNDBASE_PROD].[dbo].[Program]
                 where [Comment]='SYLWESTER WOZNIAK' or [Comment]='MARCIN MICHAS' or [Comment]='DARIUSZ MALEK' or [Comment]='LUKASZ PASEK' or [Comment]='ARTUR BEDNARZ'";
            $datas2 = sqlsrv_query($conn, $sq5);
            $max2 = "";
            while ($row3 = sqlsrv_fetch_array($datas2, SQLSRV_FETCH_ASSOC)) {
                $max2 = $row3["suma"];
            }

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

         <div>
             <ul style="float:left;" class="nav nav-tabs" id="myTab" role="tablist">
                 <li class="nav-item">
                     <a class="nav-link active" id="home-tab" data-toggle="tab" href="index.php" role="tab" aria-selected="true">Aktualne</a>
                 </li>
                 <li class="nav-item ">
                     <a class="nav-link active" id="profile-tab" data-toggle="tab" href="wykonane.php" role="tab" aria-controls="profile" aria-selected="true">Wykonane
                     <?php if($max2 != 0){ echo "<span class='badge rounded-pill badge-notification bg-danger'> $max2</span>"; } ?>
                     </a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link active" id="contact-tab" data-toggle="tab" href="niewykonane.php" role="tab" aria-controls="Nie wykonane" aria-selected="true">Nie wykonane
                     <?php if($max3 != 0){ echo "<span class='badge rounded-pill badge-notification bg-danger'>$max3</span>"; } ?>
                     </a>
                 </li>
             </ul>

             <form style="float:right;" class="form-inline my-2 my-lg-0" method="POST" action="logout.php">
                 <input class="btn btn-outline-success my-2 my-sm-0 btn-sm" type="submit" value="Wyloguj">
             </form>
         </div>
         <div style="clear:both;"></div>