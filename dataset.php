<?php
include_once 'auth_control.php';
include_once 'sql_conn.php';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tankprotokoll Einträge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
    <a href="home.php" class="btn btn-primary m-4"><i class="fas fa-home"></i></a>
    <a href="export.php" class="btn btn-success m-4"><i class="fas fa-file-csv"></i></a>
    <a href="#"  onclick="javascirpt:alert('Nicht implementert')" class="btn btn-secondary m-4"><i class="fas fa-search"></i></a>
    <a href="new_entry_dialog.php"  class="btn btn-secondary m-4"><i class="fas fa-plus"></i></a>

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

    <div class="row">
        <div class="col-md-9">
        <table class="table table-striped table-hover width">
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
            $sql_con = connect_db();
            $sql = "SELECT * FROM `tankprotokoll_einträge`";
            $result = mysqli_query($sql_con, $sql);
            $line = 0;
            while($row = mysqli_fetch_assoc($result))
            {
                $id =$line;
                $date=explode("-",$row['DATUM']); // 2026-5-03 Format
                $date_conterted = $date[2] . "." . $date[1] . "." . $date[0];

                echo "<tr>";
                echo "<td id='ID_".$id."'>" . $row['ID'] ." </td>";
                echo "<td id='DATE_".$id."'>". $date_conterted . "</td>";
                echo "<td id='TIME_".$id."'>". $row['ZEIT'] . "</td>";
                echo "<td id='ORT_".$id."'>". $row['ORT'] . "</td>";
                echo "<td id='MENGE_".$id."'>". $row['LITER'] . "</td>";
                echo "<td id='PREIS_".$id."'>". $row['PREIS'] . "</td>";
                echo "<td id='KM_STAND_".$id."'>". $row['KM_STAND'] . "</td>";

                echo "<td><button type='button' class='btn btn-danger' onclick='deleteEntry(" . $id . ")' id='delete_button_" . $id . "'>";
                echo "<i class='fas fa-trash'></i></button></td>";
                echo "<td><button type='button' class='btn btn-warning' onclick='editEntry(" . $id . ")' id='edit_button_" . $id . "'>";
                echo "<i class='fas fa-edit'></i></button></td>";
                echo "</tr>";

                $line++;
            }
            close_db($sql_con);
            ?>

        </table>
    </div>
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Zusammenfassung
                </div>
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
</div>


</body>
</html>
