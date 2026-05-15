<?php
include 'sql_conn.php';
include 'auth_control.php';

if(isset($_POST['route']))
{

    switch ($_POST['route'])
    {
        case 'new_entry':

            //todo check if user is logged in



            $id = uniqid();
            $ort = $_POST['tankstelle'];
            $datum = $_POST['datum'];
            $zeit = $_POST['zeit'];
            $km_stand = $_POST['kmStand'];
            $liter = $_POST['Liter'];
            $preis = $_POST['preis'];
            $user_id = $_POST['user_id'];



            try {
                    $pdo = connect_pdo();

                    $sql = "SELECT MAX(KM_STAND) FROM " . TB_TANK;
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $max_km_stand = $stmt->fetchColumn();

                    if ($max_km_stand < $km_stand)
                    {
                        $sql = "INSERT INTO " . TB_TANK . " 
                                    (ID, DATUM, ZEIT, ORT, LITER, PREIS, KM_STAND) 
                                    VALUES 
                                    (:id, :datum, :zeit, :ort, :liter, :preis, :km_stand)";
                        $stmt = $pdo->prepare($sql);

                        $stmt->bindParam(':id', $id);
                        $stmt->bindParam(':datum', $datum);
                        $stmt->bindParam(':zeit', $zeit);
                        $stmt->bindParam(':ort', $ort);
                        $stmt->bindParam(':liter', $liter);
                        $stmt->bindParam(':preis', $preis);
                        $stmt->bindParam(':km_stand', $km_stand);

                        $stmt->execute();

                        if($stmt->rowCount() > 0)
                        {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => true,
                                'message' => 'Eintrag gespeichert als '. $id

                            ]);
                        }
                        else
                        {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => false,
                                'message' => 'Eintrag konnte nicht gespeichert werden'
                            ]);
                        }

                    }
                    else
                    {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'message' => 'Eintrag konnte nicht gespeichert werden,Ungültiger Kilometerstand'
                        ]);
                    }
            }
            catch (Exception $e)
            {
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }

            break;

            break;
        case "update_entry":
            //Output all POSTS

            $requiredFields = ['id', 'ort', 'datum', 'zeit', 'kmStand', 'menge', 'preis', 'user_id'];

            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field])) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => "Fehlendes Feld: $field"
                    ]);
                    exit;
                }
            }
            //TODO check if user is logged in
            try {
                $id = $_POST['id'];
                $ort = $_POST['ort'];
                $datum = $_POST['datum'];
                $zeit = $_POST['zeit'];
                $kmStand = $_POST['kmStand'];
                $liter = $_POST['menge'];
                $preis = $_POST['preis'];
                $userId = $_POST['user_id'];


                // ID,DATUM,ZEIT,ORT,LITER,PREIS,KM_STAND)
                $pdo_con = connect_pdo();

                $stmt = $pdo_con->prepare("UPDATE ".TB_TANK." SET ORT=:ort,DATUM = :datum,ZEIT = :zeit,KM_STAND = :kmStand,LITER = :liter,PREIS = :preis WHERE ID =:id");
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':ort', $ort);
                $stmt->bindParam(':datum', $datum);
                $stmt->bindParam(':zeit', $zeit);
                $stmt->bindParam(':kmStand', $kmStand);
                $stmt->bindParam(':liter', $liter);
                $stmt->bindParam(':preis', $preis);
                $stmt->execute();

                if(!$stmt)
                {
                    header('Content-Type: application/json');

                    echo json_encode([
                        'success' => false,
                        'message' => 'Eintrag konnte nicht gespeichert werden. PDO Error: ' .$stmt->errorInfo()[2]
                    ]);
                    exit;
                }

                //if success
                if ($stmt->rowCount() > 0)
                {
                    header('Content-Type: application/json');

                    echo json_encode([
                        'success' => true,
                        'message' => 'Eintrag gespeichert',
                        'new_set' => [
                            'ort' => $ort,
                            'datum' => $datum,
                            'zeit' => $zeit,
                            'kmStand' => $kmStand,
                            'liter' => $liter,
                            'preis' => $preis
                        ]
                    ]);

                    return;
                }
                else
                {
                    header('Content-Type: application/json');

                    echo json_encode([
                        'success' => false,
                        'message' => 'Eintrag konnte nicht gespeichert werden'
                    ]);

                    return;
                }


            }
            catch (Exception $e)
            {
                header('Content-Type: application/json');

                echo json_encode([
                    'success' => false,
                    'message' => $e
                ]);

            }



            break;

        case "delet_entry":

            $requiredFields = ['id', 'user_id'];

            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field])) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => "Fehlendes Feld:". $field
                    ]);
                    exit;
                }
            }

            $id = $_POST['id'];
            $user_id = $_POST['user_id'];

            $pdo_con= connect_pdo();

            $sql = "DELETE FROM ". TB_TANK. " WHERE ID = :id";
            $stmt = $pdo_con->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            if(!$stmt)
            {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => "Eintrag mit ID $id konnte nicht gelöscht werden. Error: ".$stmt->errorInfo()[2]
                ]);
                exit;
            }
            if($stmt->rowCount() > 0)
            {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => " 1 Eintrag mit ID $id konnte  gelöscht werden"
                ]);
                exit;
            }
            else
            {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => "Eintrag mit ID $id konnte nicht gelöscht werden. Error: Kein Eintrag gefunden"
                ]);
                exit;
            }

        default:
            echo("B");
            break;
    }

}







