<?php
include_once 'auth_control.php';
include_once 'sql_conn.php';
require_once 'error_debug.php';
require_once 'log.php';
$pagesize= 25;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tankprotokoll Einträge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteEntry(entryId)
        {
            var id = document.getElementById("ID_"+entryId).textContent.trim();
            var user_id=<?php echo $_SESSION['user_id']; ?>;

            if (confirm("Soll der Eintrag wirklich gelöscht werden?"))
            {
                //Send data to server
                fetch('sql.php',
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `route=delet_entry&user_id=${user_id}&id=${id}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success)
                        {
                            window.location.reload();
                        }
                        else
                        {
                            alert("Eintrag konnte nicht gelöscht werden. Bitte versuchen Sie es später erneut.");
                            window.location.reload();
                        }
                    })
            }
        }
        function editEntry(entryId)
        {

            try {
                var time = document.getElementById("TIME_"+ entryId).textContent;
                var date = document.getElementById("DATE_"+ entryId).textContent;

                conts = date.split(".");
                date = conts[2] + "-" + conts[1] + "-" + conts[0];


                var ort = document.getElementById("ORT_"+ entryId).textContent;
                var menge = document.getElementById("MENGE_"+ entryId).textContent;
                var preis = document.getElementById("PREIS_"+ entryId).textContent;
                var kmStand = document.getElementById("KM_STAND_"+ entryId).textContent;

                console.log(entryId, time, date, ort, menge, preis, kmStand);

                document.getElementById("TIME_" + entryId).innerHTML = "<input type='time'  value='" + time + "'>";
                document.getElementById("DATE_" + entryId).innerHTML = "<input type='date'  value='" + date + "'>";
                document.getElementById("ORT_" + entryId).innerHTML = "<input type='text' value='" + ort + "'>";
                document.getElementById("MENGE_" + entryId).innerHTML = "<input type='number' value='" + menge + "'>";
                document.getElementById("PREIS_" + entryId).innerHTML = "<input type='number'  value='" + preis + "'>";
                document.getElementById("KM_STAND_" + entryId).innerHTML = "<input type='number' value='" + kmStand + "'>";

                document.getElementById("edit_button_" + entryId).setAttribute("onclick", "updateEntry(" + entryId + ")");
                document.getElementById("edit_button_" + entryId).setAttribute("class", "btn btn-success");
                document.getElementById("edit_button_" + entryId).innerHTML = "<i class='fas fa-save'></i>";
                document.getElementById("delete_button_" + entryId).style.display = "none";
            }
            catch (error)
            {
                console.error("Error updating entry:", error);
                alert("Fehler: " + error.message);
            }
        }
        function updateEntry(entryId)
        {
            try {
                var id = document.getElementById("ID_" + entryId).textContent.trim();
                var date = document.getElementById("DATE_" + entryId).getElementsByTagName("input")[0].value;
                var time = document.getElementById("TIME_" + entryId).getElementsByTagName("input")[0].value;
                var ort = document.getElementById("ORT_" + entryId).getElementsByTagName("input")[0].value;
                var menge = document.getElementById("MENGE_" + entryId).getElementsByTagName("input")[0].value;
                var preis = document.getElementById("PREIS_" + entryId).getElementsByTagName("input")[0].value;
                var kmStand = document.getElementById("KM_STAND_" + entryId).getElementsByTagName("input")[0].value;
                var user_id=<?php echo $_SESSION['user_id']; ?>;

                //Validate input
                if (id === "" || date === "" || time === "" || ort === "" || (menge === "" || isNaN(menge)) || (preis === "" || isNaN(preis)) || (kmStand === "" || isNaN(kmStand)))
                {
                    alert('Ungültige Eingaben. Bitte überprüfen Sie alle Felder.');
                    return;
                } else
                {
                    //Send data to server
                    fetch('sql.php',
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `route=update_entry&user_id=${user_id}&id=${id}&ort=${ort}&datum=${date}&zeit=${time}&kmStand=${kmStand}&menge=${menge}&preis=${preis}`
                        })

                        .then(response => response.json())
                        .then(data => {
                                if (data.success)
                                {
                                    alert(data.message);

                                    const datum_raw = data.new_set.datum.split('-');
                                    const datum_readable = `${datum_raw[2]}.${datum_raw[1]}.${datum_raw[0]}`;

                                    document.getElementById("DATE_" + entryId).innerText =datum_readable;
                                    document.getElementById("TIME_" + entryId).innerText = data.new_set.zeit;
                                    document.getElementById("ORT_" + entryId).innerHTML = data.new_set.ort;
                                    document.getElementById("MENGE_" + entryId).innerHTML = data.new_set.liter;
                                    document.getElementById("PREIS_" + entryId).innerHTML = data.new_set.preis;
                                    document.getElementById("KM_STAND_" + entryId).innerHTML = data.new_set.kmStand;

                                    document.getElementById("edit_button_" + entryId).setAttribute("onclick", "editEntry(" + entryId + ")");
                                    document.getElementById("edit_button_" + entryId).setAttribute("class", "btn btn-warning");
                                    document.getElementById("edit_button_" + entryId).innerHTML = "<i class='fas fa-edit'></i>";
                                    document.getElementById("delete_button_" + entryId).style.display = "";
                                }
                                else
                                {
                                    alert("Fehler beim Speichern: " + data.message);
                                    //Page reload
                                    window.location.reload();
                                }
                            }
                        )
                        .catch(error => console.error('Fehler beim Senden der Daten:', error));
                }
            }
            catch
                (error)
                {
                    console.error('Fehler beim Aktualisieren der Eintrag:', error);
                    alert('Fehler beim Aktualisieren der Eintrag. Bitte versuchen Sie es später erneut.');
                }
            }

    </script>
    <style>
       body
       {
           background-color: #f8f9fa;
       }

    </style>
</head>
<body>

<div class="container-fluid" >
    <input type="image" onclick="window.location.href='home.php'" src="icons/home_512dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.svg" width="48" height="48"  class="btn btn-primary m-1">
   <input type="image" src="icons/donut_small_512dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.svg" alt="Summary" width="48" height="48" class="btn btn-secondary m-4" data-bs-toggle="modal" data-bs-target="#summaryModal"></button>
    <input type="image" onclick="window.location.href='dataset.php?action=show_entries&page=1'" src="icons/database_512dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.svg" width="48" height="48"  class="btn btn-secondary m-4">
    <input type="image" onclick="window.location.href='new_entry_dialog.php'" src="icons/add_512dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.svg" height="48" width="48" class="btn btn-secondary m-4">
    <input type="image"  src="icons/search_512dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.svg" height="48" width="48" class="btn btn-secondary m-4" data-bs-toggle="modal" data-bs-target="#searchModal">
    <input type="image" onclick="window.location.href='export.php'" src="icons/download_512dp_E3E3E3_FILL0_wght400_GRAD0_opsz48.svg" height="48" width="48" class="btn btn-secondary m-4">

</div>

<!-- Modal -->
<div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="get" action="dataset.php?action=search">
                <input type="hidden" name="action" value="show_entries">
                <input type="hidden" name="page" value="1">

                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">Suche</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label for="search" class="form-label">Suchbegriff</label>
                            <input type="text"
                                   class="form-control"
                                   id="search"
                                   name="search"
                                   placeholder="Tankstelle oder Ort suchen">
                        </div>

                        <div class="col-md-6">
                            <label for="date_from" class="form-label">Datum von</label>
                            <input type="date" class="form-control" id="date_from" name="date_from">
                        </div>

                        <div class="col-md-6">
                            <label for="date_to" class="form-label">Datum bis</label>
                            <input type="date" class="form-control" id="date_to" name="date_to">
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Schließen
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Suchen
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
<div class="container-fluid">


    <?php

    $pdo_con = connect_pdo();


    $sql = "SELECT SUM(LITER) FROM " . TB_TANK ;
    $stmt = $pdo_con->prepare($sql);
    $stmt->execute();

    $totalLiter = $stmt->fetchColumn();


    $sql = "SELECT SUM(PREIS) FROM " . TB_TANK ;
    $stmt = $pdo_con->prepare($sql);
    $stmt->execute();

    $totalPrice = $stmt->fetchColumn();

    $sql = "SELECT COUNT(ID) FROM " . TB_TANK ;
    $stmt = $pdo_con->prepare($sql);
    $stmt->execute();
    $entryCount = $stmt->fetchColumn();

    $sql = "SELECT MAX(KM_STAND) FROM " . TB_TANK ;
    $stmt = $pdo_con->prepare($sql);
    $stmt->execute();
    $maxKm = $stmt->fetchColumn();


    $sql = "SELECT MIN(KM_STAND) FROM " . TB_TANK ;
    $stmt = $pdo_con->prepare($sql);
    $stmt->execute();
    $minKm = $stmt->fetchColumn();



    $pdo_con = null;

    $averagePricePerLiter = $totalLiter > 0 ? $totalPrice / $totalLiter : 0;
    $drivenKm = ($minKm !== null && $maxKm !== null) ? $maxKm - $minKm : 0;
    $averageLiterPerKm = $drivenKm > 0 ? $totalLiter / $drivenKm : 0;
    ?>


    <div class="row" >

        <div class="col-md-9 ">
            <ul class="pagination justify-content-center">
                <li class="page-item"><a class="page-link" href="dataset.php?action=show_entries&page=1">Start</a></li>
                <li class="page-item"><a class="page-link" href="dataset.php?action=show_entries&page=<?php  echo $_GET['page'] == 1 ?  $_GET['page']: $_GET['page'] - 1; ?>">Zurück</a></li>
                <li class="page-item"><a class="page-link" href="dataset.php?action=show_entries&page=2"><?php echo $_GET['page'] ."/".ceil($entryCount/$pagesize); ?></a></li>
                <li class="page-item"><a class="page-link" href="dataset.php?action=show_entries&page=<?php echo $_GET['page'] == ceil($entryCount/$pagesize) ? ceil($entryCount/$pagesize) : $_GET['page'] + 1; ?>">Vorwärts</a></li>
                <li class="page-item"><a class="page-link" href="dataset.php?action=show_entries&page=<?php echo ceil($entryCount/$pagesize)  ?>">Ende</a></li>
            </ul>
        </div>

        <div class="col-md-9">
        <table class="table table-striped table-hover width " >
            <thead>
                <tr>
                    <th data-sort="id">ID</th>
                    <th data-sort="date">Datum</th>
                    <th data-sort="time">Zeit</th>
                    <th data-sort="ort">Ort</th>
                    <th data-sort="menge">Menge (Liter)</th>
                    <th data-sort="preis">Preis (CHF)</th>
                    <th data-sort="km-stand">km-Stand</th>
                    <th></th>
                    <th </th>
                    <th </th>
                </tr>
            </thead>

            <?php

            //Wurde per KI von mysql nach PDO migriert 15.05.2026
            $pdo_con = connect_pdo();
            write_log("Dataset page loaded from user ","INFO");
            if(isset($_GET['page'])) {
                $page = $_GET['page'];
            } else {
                $page = 1;
            }

            $limit = $page*$pagesize;

            $search="";

            if(isset($_GET['search']) AND $_GET['search']!="")
            {
                $search .= "WHERE ORT LIKE '%".$_GET['search']."%'";
            }

            if((isset($_GET['date_from']) AND $_GET['date_from'] != "") AND (isset($_GET['date_to']) AND $_GET['date_to'] != "")) {
                if (!$search == "") {
                    $search .= " AND DATUM BETWEEN '" . $_GET['date_from'] . "' AND '" . $_GET['date_to'] . "'";
                } else {
                    $search .= "WHERE DATUM BETWEEN '" . $_GET['date_from'] . "' AND '" . $_GET['date_to'] . "'";
                }
            }

            $offset = ($page-1)*$pagesize;
            $sql = "SELECT * FROM ". TB_TANK." $search LIMIT $limit OFFSET $offset";

            $stmt = $pdo_con->prepare($sql);
            $stmt->execute();
            $line = 0;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $id = $line;

                $date = explode("-", $row['DATUM']); // 2026-05-03 Format
                $date_conterted = $date[2] . "." . $date[1] . "." . $date[0];

                echo "<tr>";
                echo "<td id='ID_" . $id . "'>" . $row['ID'] . " </td>";
                echo "<td id='DATE_" . $id . "'>" . $date_conterted . "</td>";
                echo "<td id='TIME_" . $id . "'>" . $row['ZEIT'] . "</td>";
                echo "<td id='ORT_" . $id . "'>" . $row['ORT'] . "</td>";
                echo "<td id='MENGE_" . $id . "'>" . $row['LITER'] . "</td>";
                echo "<td id='PREIS_" . $id . "'>" . $row['PREIS'] . "</td>";
                echo "<td id='KM_STAND_" . $id . "'>" . $row['KM_STAND'] . "</td>";

                echo "<td><button type='button' class='btn btn-danger' onclick='deleteEntry(" . $id . ")' id='delete_button_" . $id . "'>";
                echo "<i class='fas fa-trash'></i></button></td>";

                echo "<td><button type='button' class='btn btn-warning' onclick='editEntry(" . $id . ")' id='edit_button_" . $id . "'>";
                echo "<i class='fas fa-edit'></i></button></td>";

                echo "</tr>";

                $line++;
            }

            $pdo_con = null;
            ?>

        </table>
    </div>


</div>

    <div class="modal fade" id="summaryModal" tabindex="-1" aria-labelledby="sumary_modallabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="sumary_modallabel">Zusammenfassung</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Schließen"></button>
                    </div>

                        <div class="modal-body">
                            <div class="card shadow-sm" >
                                <div class="card-body">
                                    <p><strong>Einträge:</strong><br><?= $entryCount ?>
                                    <p><strong>Gefahrene km:</strong><br><?= number_format($drivenKm, 0, '.', '\'') ?> km</p>
                                    <p><strong>Liter gesamt:</strong><br><?= number_format($totalLiter, 2, '.', '\'') ?> L</p>
                                    <p><strong>Kosten gesamt:</strong><br><?= number_format($totalPrice, 2, '.', '\'') ?> CHF</p>
                                    <p><strong>Ø Preis/Liter:</strong><br><?= number_format($averagePricePerLiter, 2, '.', '\'') ?> CHF</p>

                                    <p><strong>Ø Liter/km:</strong><br><?= number_format($averageLiterPerKm, 2, '.', '\'') ?> L/km</p>
                                </div>
                            </div>
                        </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Schließen
                        </button>

                    </div>
            </div>
        </div>
    </div>




</body>
</html>
