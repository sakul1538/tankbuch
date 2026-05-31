<?php
require_once 'auth_control.php';
require_once 'error_debug.php';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tankprotokoll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        <?php
        if(isset($_SESSION['login']) && ($_SESSION['login']==true) && isset($_SESSION['user_id']) && ($_SESSION['user_id'] != null))
        {
            echo "localStorage.setItem('user_id', '".$_SESSION['user_id']."');";
        }
            ?>
    </script>
    
    <style>
        body {
            background: #f4f7fb;
        }

        .app-header {
            margin-bottom: 1.5rem;
        }

        .tab-pane .card {
            border: 0;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border-radius: 16px;
        }

        .tab-pane .card-body {
            padding: 1.5rem;
        }

        .subtitle {
            color: #6b7280;
        }

        .hint {
            color: #6b7280;
            font-size: 0.95rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">

            <div class="app-header">
                <h1 class="mb-1">Tankprotokoll <?php echo $_SESSION['username']; ?></h1>
            </div>

            <ul class="nav nav-tabs mb-3" id="appTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="newentry-tab" data-bs-toggle="tab"
                            data-bs-target="#newentry" type="button" role="tab" aria-controls="newentry" aria-selected="false">
                        Neuer Eintrag
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="entries-tab" data-bs-toggle="tab"
                            data-bs-target="#entries" type="button" role="tab" aria-controls="entries" aria-selected="true">
                        Einträge anzeigen
                    </button>
                </li>


                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="settings-tab" data-bs-toggle="tab"
                            data-bs-target="#settings" type="button" role="tab" aria-controls="info" aria-selected="false">
                        Einstellungen
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="info-tab" data-bs-toggle="tab"
                            data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="false">
                        Info
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="logout-tab" data-bs-toggle="tab"
                            data-bs-target="#logout" type="button" role="tab" aria-controls="logout" aria-selected="false">
                        Logout
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="appTabsContent">

                <div class="tab-pane fade show active" id="entries" role="tabpanel" aria-labelledby="entries-tab">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Einträge anzeigen</h5>
                            <p class="card-text">Hier kannst du deine vorhandenen Tankeinträge öffnen.</p>
                            <a href="dataset.php?action=show_entries&page=1" class="btn btn-primary">
                                Zu den Einträgen
                            </a>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="newentry" role="tabpanel" aria-labelledby="newentry-tab">
                    <div class="card">
                        <div class="card-body">

                            <h5 class="card-title">Neuer Eintrag</h5>
                            <p class="card-text">Erfasse hier eine neue Tankung.</p>
                            <a href="new_entry_dialog.php" class="btn btn-success">
                                Neuen Eintrag öffnen
                            </a>
                        </div>
                    </div>

                </div>

                <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                    <div class="card">
                        <div class="card-body">

                            <p class="card-text">Hier kannst du deine Einstellungen verändern.</p>
                            <ul>
                                <li><a href="pw_change.php">Passwort ändern</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="info" role="tabpanel" aria-labelledby="info-tab">
                    <div class="card">
                        <div class="card-body">

                            <p class="card-text">Letzte Anmeldung :  <?php echo  date('d.m.Y H:m:s',$_SESSION['login_timestamp']); ?></p>
                            <p class="card-text">Angemeldet als :  <?php echo  $_SESSION['username'] ?? 'N/A' ?></p>
                            <p class="card-text">Auto Logout: <?php echo AUTOLOGOUT_TIME/60 ."min" ?? 'N/A' ?></p>

                            <small><p class="card-text">Letzte Software Aktualisierung  31.05.2026</p></small>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="logout" role="tabpanel" aria-labelledby="logout-tab">
                    <div class="card">
                        <div class="card-body">

                            <p class="card-text">Hier kannst du dich sicher abmelden.</p>
                            <a href="logout.php" class="btn btn-danger">
                                Logout
                            </a>
                        </div>


            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>