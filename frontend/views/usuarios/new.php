<?php
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<section class="panel">
    <div class="panel-header">
        <h3>Nuevo usuario</h3>
    </div>
    <form action="index.php?action=usuarios_new" method="POST" class="form-grid">
        <label>Nombre completo <input type="text" name="nombre" required></label>
        <label>Usuario <input type="text" name="username" required></label>
        <label>Contraseña <input type="password" name="password" required></label>
        <label>Rol
            <select name="id_rol">
                <option value="1">Administrador</option>
                <option value="2">Bibliotecario</option>
            </select>
        </label>
        <label>Estado
            <select name="activo">
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </label>
        <div class="button-row span-3">
            <button type="submit" class="btn success">Guardar usuario</button>
            <a href="index.php?action=usuarios" class="btn subtle">Cancelar</a>
        </div>
    </form>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="status-line bad"><?php echo $escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
</section>
