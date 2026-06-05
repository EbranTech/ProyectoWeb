<?php
$usuario = $usuario ?? [];
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<section class="panel">
    <div class="panel-header">
        <h3>Editar usuario</h3>
    </div>
    <form action="index.php?action=usuarios_update&id=<?php echo urlencode((string) ($usuario['id_usuario'] ?? '')); ?>" method="POST" class="form-grid">
        <label>Nombre completo <input type="text" name="nombre" value="<?php echo $escape($usuario['nombre'] ?? ''); ?>" required></label>
        <label>Usuario <input type="text" name="username" value="<?php echo $escape($usuario['username'] ?? ''); ?>" required></label>
        <label>Contraseña <input type="password" name="password" placeholder="Dejar vacío para conservar"></label>
        <label>Rol
            <select name="id_rol">
                <option value="1" <?php echo ($usuario['id_rol'] ?? '') == '1' ? 'selected' : ''; ?>>Administrador</option>
                <option value="2" <?php echo ($usuario['id_rol'] ?? '') == '2' ? 'selected' : ''; ?>>Bibliotecario</option>
            </select>
        </label>
        <label>Estado
            <select name="activo">
                <option value="1" <?php echo ($usuario['activo'] ?? '') == '1' ? 'selected' : ''; ?>>Activo</option>
                <option value="0" <?php echo ($usuario['activo'] ?? '') == '0' ? 'selected' : ''; ?>>Inactivo</option>
            </select>
        </label>
        <div class="button-row span-3">
            <button type="submit" class="btn success">Actualizar usuario</button>
            <a href="index.php?action=usuarios" class="btn subtle">Cancelar</a>
        </div>
    </form>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="status-line bad"><?php echo $escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
</section>
