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
              <h1 class="text-center"><b>Messer</b></h1>
              <br/>
         <div>
             <ul style="float:left;" class="nav nav-tabs" id="myTab" role="tablist">
                 <li class="nav-item">
                     <a class="nav-link active" id="home-tab" data-toggle="tab" href="index.php" role="tab" aria-selected="true">Aktualne</a>
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
             <form style="float:right;">
                 <a href="../index.php" class="btn btn-outline-success my-2 my-sm-0 btn-sm" value="Strona główna">Strona główna</a>
        </form>
             
         </div>
         <div style="clear:both;"></div>

         <button
        type="button"
        class="btn btn-danger btn-floating"
        id="btn-back-to-top"
        >
  <i class="bi bi-arrow-up"></i>
</button>