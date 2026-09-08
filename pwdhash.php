<?php
$hashResult = '';
$inputPassword = '';
$message = '';
$messageType = '';

// Spracovanie generovania hash-u
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate') {
    if (!empty($_POST['password'])) {
        $inputPassword = $_POST['password'];
        $hashResult = password_hash($inputPassword, PASSWORD_DEFAULT);
    }
}

// Spracovanie uloženia do settings.json a následného premenovania skriptu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {
    $hashToSave = $_POST['hash_to_save'] ?? '';

    if (!empty($hashToSave)) {
        $jsonFile = __DIR__ . '/settings.json';
        $settings = [];

        if (file_exists($jsonFile)) {
            $existingContent = file_get_contents($jsonFile);
            $decoded = json_decode($existingContent, true);
            if (is_array($decoded)) {
                $settings = $decoded;
            }
        }

        $settings['pwd_hash'] = $hashToSave;

        if (file_put_contents($jsonFile, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false) {

            // Generovanie 16-znakového náhodného reťazca
            $randomString = bin2hex(random_bytes(8)); // 8 bajtov = 16 hex znakov
            $currentFile = __FILE__;
            $newFileName = "pwdhash_{$randomString}_RENAME_IF_NECESSARY.php.bak";
            $newFilePath = __DIR__ . '/' . $newFileName;

            // Premenovanie aktuálneho súboru
            if (rename($currentFile, $newFilePath)) {
                $message = "Hash bol úspešne uložený do settings.json.<br><strong>Súbor bol z bezpečnostných dôvodov premenovaný na:</strong><br><code>{$newFileName}</code>";
                $messageType = 'success';
            } else {
                $message = "Hash bol uložený do settings.json, ale <strong>nepodarilo sa premenovať súbor skriptu</strong>. Skontrolujte oprávnenia súborového systému.";
                $messageType = 'warning';
            }
        } else {
            $message = 'Chyba: Nepodarilo sa zapísať do súboru settings.json (skontrolujte oprávnenia).';
            $messageType = 'error';
        }
        $hashResult = $hashToSave;
    }
}
?>
<!DOCTYPE html>
<html lang="sk">

<head>
    <meta charset="UTF-8">
    <title>Generátor password_hash()</title>
    <style>
        body {
            font-family: sans-serif;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
        }

        .result {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 4px;
            word-break: break-all;
            font-family: monospace;
            margin-bottom: 15px;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            box-sizing: border-box;
        }

        button {
            padding: 8px 16px;
            cursor: pointer;
        }

        .btn-save {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .msg {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .msg-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .msg-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .msg-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>

    <h2>Generátor PHP password_hash()</h2>

    <?php if (!empty($message)): ?>
        <div class="msg <?php echo 'msg-' . $messageType ?>">
            <?php echo $message ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="action" value="generate">
        <label for="password">Zadajte heslo:</label>
        <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($inputPassword, ENT_QUOTES, 'UTF-8') ?>" required autofocus>
        <button type="submit">Vygenerovať hash</button>
    </form>

    <?php if (!empty($hashResult)): ?>
        <h3>Výsledok:</h3>
        <div class="result">
            <strong>Hash:</strong><br>
            <span id="hashText"><?php echo htmlspecialchars($hashResult, ENT_QUOTES, 'UTF-8') ?></span>
        </div>

        <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('hashText').innerText)" style="margin-bottom: 15px;">
            Skopírovať hash
        </button>

        <form method="POST" action="">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="hash_to_save" value="<?php echo htmlspecialchars($hashResult, ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn-save">Uložiť do settings.json a znefunkčniť skript</button>
        </form>
    <?php endif; ?>

</body>

</html>