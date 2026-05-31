<?php
session_start();
require_once 'error_debug.php';
require_once 'sql_conn.php';
require_once 'log.php';

if(!isset($_SESSION['login']))
{
    write_log("User attempted login without session", "INFO");
    $_SESSION['login'] = false;
    $_SESSION['user_id'] = null;
    header('Location: login.php');

}
else {
if(($_SESSION['login']==true))
header('Location: main.php');
}
{
  //Get user id from session
    if(isset($_POST['username']) AND isset($_POST['password']))
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $pdo_con = connect_pdo();

   $sql = "SELECT PASSWORD,ID FROM ".TB_USER." WHERE USER_NAME = :username";
   $stmt = $pdo_con->prepare($sql);
   $stmt->bindParam(':username', $username);
   $stmt->execute();

   $result = $stmt->fetch(PDO::FETCH_ASSOC);

   //sha
   $hashed_password = hash('sha256', $password);


   if($result['PASSWORD'] == $hashed_password)
   {
            $timestamp  = time();
            $sql = "UPDATE ".TB_USER." SET LOGIN = :timestamp WHERE USER_NAME = :username";
            $stmt = $pdo_con->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':timestamp', $timestamp);
            $stmt->execute();

            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $result['ID'];
            $_SESSION['login_timestamp'] = $timestamp;
            $_SESSION['username']=$username;
            write_log("User successfully logged in", "INFO");
            header('Location: main.php');
   }
   else
   {
       write_log("User login failed", "ERROR");
       header('Location: login.php');
   }
    }

}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tankprotokoll</title>
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
        <h1>Anmelden Tankbuch</h1>
        <p class="subtitle">Bitte melde dich an, um das Tankprotokoll zu öffnen.</p>

        <form action="login.php" method="POST">
            <div class="field">
                <label for="username">Benutzername</label>
                <input type="text" id="username" name="username" placeholder="Benutzername" required>
            </div>

            <div class="field">
                <label for="password">Passwort</label>
                <input type="password" id="password" name="password" placeholder="Passwort" required>
            </div>

            <div class="actions">
                <button type="submit">Login</button>
            </div>
        </form>

        <p class="hint">Tipp: Mit Enter kannst du das Formular absenden.</p>
    </main>
</body>
</html>