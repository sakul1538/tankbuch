<?php
require_once 'auth_control.php';
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tankprotokoll</title>
    <style>
        :root {
            --bg: #f4f7fb;
            --card: #ffffff;
            --text: #1f2937;
            --muted: #6b7280;
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --danger: #ef4444;
            --border: #dbe3ee;
            --shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            --radius: 16px;
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
            padding: 32px 16px;
        }

        .app {
            max-width: 760px;
            margin: 0 auto;
        }

        .card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 28px;
            border: 1px solid rgba(219, 227, 238, 0.8);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 2rem;
            line-height: 1.2;
        }

        .subtitle {
            margin: 0 0 24px;
            color: var(--muted);
            font-size: 0.98rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
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
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        button {
            border: none;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
        }

        button:hover {
            transform: translateY(-1px);
        }

        button:active {
            transform: translateY(0);
        }

        #speichern {
            background: var(--primary);
            color: white;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
        }

        #speichern:hover {
            background: var(--primary-hover);
        }

        #reset {
            background: #eef2f7;
            color: #334155;
        }

        #reset:hover {
            background: #e2e8f0;
        }

        .hint {
            margin-top: 16px;
            color: var(--muted);
            font-size: 0.9rem;
        }

        @media (max-width: 640px) {
            body {
                padding: 18px 12px;
            }

            .card {
                padding: 20px;
                border-radius: 14px;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 1.6rem;
            }
        }
    </style>
    <script>
        function saveEntry()
        {
            //Collect data from the form
            const tankstelle = document.getElementById('tankstelle').value;
            const datum = document.getElementById('datum').value;
            const zeit = document.getElementById('zeit').value;
            const km_stand = document.getElementById('km_stand').value;
            const Liter = document.getElementById('Liter').value;
            const Preis = document.getElementById('Preis').value;
            const route = 'new_entry';
            const user_id = <?php echo $_SESSION['user_id']; ?>;


            //Validate input
            if (!tankstelle || !datum || !zeit || !km_stand || !Liter || !Preis) {
                alert('Bitte füllen Sie alle Felder aus.');
                return;
            }

            //Send data to server
            fetch('sql.php',
                {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `route=${route}&user_id=${user_id}&tankstelle=${tankstelle}&datum=${datum}&zeit=${zeit}&kmStand=${km_stand}&Liter=${Liter}&preis=${Preis}`
            })

            .then(response => response.json())
            .then(data => {
                    if (data.success)
                    {
                        console.log(data.message);
                        alert(data.message);
                        // Got ot entrys
                        window.location.href = "dataset.php?action=show_entries";
                    }
                    else
                    {
                        console.error("Fehler beim Speichern:", data.error);
                        alert("Fehler beim Speichern: " + data.error);
                    }
                }
            )
                .catch(error => console.error('Fehler beim Senden der Daten:', error));
        }


    </script>
</head>
<body>
</body>
<main class="app">
    <section class="card">
        <h1>Tankprotokoll</h1>
        <p class="subtitle">Erfasse hier deine Tankdaten übersichtlich und schnell.</p>

        <div class="grid">
            <div class="field full">
                <label for="tankstelle">Tankstelle</label>
                <input type="text" id="tankstelle" placeholder="" list="tankstellen_liste">
            </div>

            <div class="field">
                <label for="datum">Datum</label>
                <input type="date" id="datum" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="field">
                <label for="zeit">Zeit</label>
                <input type="time" id="zeit" value="<?php echo date('H:i'); ?>">
            </div>

            <div class="field">
                <label for="Liter">Liter</label>
                <input type="number" id="Liter" min="0" step="0.01" placeholder="0,00">
            </div>

            <div class="field">
                <label for="Preis">Preis</label>
                <input type="number" id="Preis" min="0" step="0.01" placeholder="0,00 CHF">
            </div>

            <div class="field full">
                <label for="km_stand">Km Stand</label>
                <input type="number" id="km_stand" min="0" step="1" placeholder="z. B. 125000">
            </div>
        </div>

        <div class="actions">

            <button id="speichern" type="button" onclick="saveEntry()">Speichern</button>
            <button id="reset" type="button" onClick="window.location.reload()">Reset</button>
            <button id="abbrechen" type="button"  onClick="window.location.href='home.php'">Abbrechen</button>
        </div>

        <p class="hint">Tipp: Mit Tab kannst du schnell durch alle Felder springen.</p>
    </section>
</main>

<datalist id="tankstellen_liste">
    <option value="Coop Pronto Ostermundigen">
    <option value="Coop Pronto Heimberg">
    <option value="Spar Heimberg">
</datalist>
</html>

