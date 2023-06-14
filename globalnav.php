         <div>
            <ul style="float:left;" class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="index.php" role="tab"
                         aria-selected="true">Aktualne</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link active" id="profile-tab" data-toggle="tab" href="wykonane.php" role="tab"
                        aria-controls="profile" aria-selected="true">Wykonane</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" id="contact-tab" data-toggle="tab" href="niewykonane.php" role="tab"
                        aria-controls="Nie wykonane" aria-selected="true">Nie wykonane</a>
                </li>
            </ul>

            <form style="float:right;" class="form-inline my-2 my-lg-0" method="POST" action="logout.php">
                <input class="btn btn-outline-success my-2 my-sm-0 btn-sm" type="submit" value="Wyloguj">
            </form>
        </div>
        <div style="clear:both;"></div>
