<?php
session_start();
require_once 'error_debug.php';
require_once 'auth_control.php';
require_once 'sql_conn.php';
require_once 'log.php';

if(isset($_POST['passcode']) && isset($_POST['password_neu']))
{

    $password_neu = $_POST['password_neu'];

    if(empty($password_neu))
    {
        $_SESSION['error'] = 'Bitte alle Felder ausfüllen';
        header('Location: pw_change.php');
        exit;
    }else
    {
        $auth= file_get_contents("https://www.authenticatorapi.com/Validate.aspx?Pin=".$_POST['passcode']."&SecretCode=0654");
        if($auth == "True")
        {
            $pdo_con = connect_pdo();
             $hashed_password = hash('sha256', $password_neu);

            $pdo_con->exec("UPDATE ".TB_USER." SET PASSWORD = '".$hashed_password."' WHERE ID = '".$_SESSION['user_id']."'");
            $_SESSION['error'] = 'Authentifizierung erfolgreich, Passwort Geändert';
            write_log("Password change by user ","INFO");

            //Auto logout after 5 seconds
            header('Location: logout.php');

            exit;
        }
        else
        {
            $_SESSION['error'] = 'Authentifizierung fehlgeschlagen';
            header('Location: pw_change.php');
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passwort ändern - Tankprotokoll</title>
    <style>
        :root {
            --bg: #f4f7fb;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --border: #dbe3ee;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            --radius: 1px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(180deg, #eaf2ff 0%, var(--bg) 40%, #eef4f8 100%);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 32px;
            border: 1px solid rgba(219, 227, 238, 0.8);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 1.8rem;
        }

        .subtitle {
            margin: 0 0 24px;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .field {
            margin-bottom: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            font-size: 0.95rem;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 1rem;
            background: #fff;
            color: var(--text);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .actions {
            margin-top: 24px;
        }

        button {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            background: var(--primary);
            color: #fff;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        button:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
        }

        .hint {
            margin-top: 16px;
            color: var(--muted);
            font-size: 0.9rem;
            text-align: center;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 22px;
                border-radius: 14px;
            }

            h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
<main class="login-card">
    <h1>Passwort ändern</h1>
    <p class="subtitle">Bitte gib dein aktuelles Passwort ein, um es zu ändern.</p>

    <form action="pw_change.php" method="POST">
        <div class="field">
            <label for="password_neu">Passwort Neu</label>
            <input type="password" id="password_neu" name="password_neu" placeholder="Passwort neu" required>
        </div>

        <div class="field">
            <label for="passcode">Passcode</label>
            <input type="password"  id="passcode" name="passcode" placeholder="Passcode" required>
        </div>

        <div class="actions">
            <button type="submit">Ändern</button>
        </div>

        <div class="actions">
            <button type="button" onclick="window.location.href='index.php'">Abbrechen</button>
        </div
    </form>

    <p class="hint">Tipp: Mit Enter kannst du das Formular absenden.</p>
    <?php
    if(isset($_SESSION['error']))
    {
        echo '<p class="error" style="color:red">' . $_SESSION['error'] . '</p>';
        unset($_SESSION['error']);
    }
    ?>



</main>
</body>
</html>
