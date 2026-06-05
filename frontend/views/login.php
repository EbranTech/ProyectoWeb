<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioSys - Ingreso</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <main class="login-box">
        <div class="brand">
            <span class="brand-mark">B</span>
            <div>
                <h1>BiblioSys</h1>
                <p>Sistema de gestion bibliotecaria</p>
            </div>
        </div>

        <form action="index.php?action=login" method="POST" class="login-form">
            <label>
                Usuario
                <input type="text" name="username" autocomplete="username" required>
            </label>

            <label>
                Contraseña
                <input type="password" name="password" autocomplete="current-password" required>
            </label>

            <button type="submit" class="btn primary">Ingresar</button>
            <?php if (isset($_SESSION['error'])): ?>
                <p class="form-message error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
            <?php endif; ?>
        </form>
    </main>
</body>
</html>
