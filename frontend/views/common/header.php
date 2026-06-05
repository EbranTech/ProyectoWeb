<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'BiblioSys'); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="app-page">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span class="brand-mark">B</span>
            <div>
                <h1>BiblioSys</h1>
                <p>Biblioteca universitaria</p>
            </div>
        </div>
        <nav class="main-menu">
            <a href="index.php?action=prestamos" class="menu-button <?php echo ($_GET['action'] ?? 'prestamos') === 'prestamos' ? 'active' : ''; ?>">Prestamos</a>
            <a href="index.php?action=devoluciones" class="menu-button <?php echo ($_GET['action'] ?? '') === 'devoluciones' ? 'active' : ''; ?>">Devoluciones</a>
            <a href="index.php?action=consulta" class="menu-button <?php echo ($_GET['action'] ?? '') === 'consulta' ? 'active' : ''; ?>">Libros disponibles</a>
            <a href="index.php?action=historial" class="menu-button <?php echo ($_GET['action'] ?? '') === 'historial' ? 'active' : ''; ?>">Historial</a>
            <a href="index.php?action=libros" class="menu-button <?php echo ($_GET['action'] ?? '') === 'libros' ? 'active' : ''; ?>">Libros</a>
            <a href="index.php?action=autores" class="menu-button <?php echo ($_GET['action'] ?? '') === 'autores' ? 'active' : ''; ?>">Autores</a>
            <a href="index.php?action=estudiantes" class="menu-button <?php echo ($_GET['action'] ?? '') === 'estudiantes' ? 'active' : ''; ?>">Estudiantes</a>
            <?php if ($_SESSION['user_rol'] === 'ADMIN'): ?>
                <a href="index.php?action=usuarios" class="menu-button <?php echo ($_GET['action'] ?? '') === 'usuarios' ? 'active' : ''; ?>">Usuarios</a>
            <?php endif; ?>
        </nav>
    </aside>
    <main class="workspace">
        <header class="topbar">
            <div>
                <h2 id="viewTitle"><?php echo htmlspecialchars($title); ?></h2>
                <p id="viewSubtitle"><?php echo htmlspecialchars($subtitle ?? ''); ?></p>
            </div>
            <div class="user-box">
                <strong><?php echo htmlspecialchars($_SESSION['user_nombre']); ?></strong>
                <span><?php echo htmlspecialchars($_SESSION['user_rol']); ?></span>
                <a href="index.php?action=logout" class="btn subtle">Salir</a>
            </div>
        </header>
        <section id="appContent" class="content">
