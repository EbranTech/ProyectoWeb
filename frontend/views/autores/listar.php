<?php
$autores = $autores ?? [];
$autor = $autor ?? null;
$formData = $formData ?? [];
$isEditing = is_array($autor);
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$old = static function (string $key, mixed $default = '') use ($formData, $autor): string {
    if (array_key_exists($key, $formData)) {
        return (string) $formData[$key];
    }

    if (is_array($autor) && array_key_exists($key, $autor)) {
        return (string) $autor[$key];
    }

    return (string) $default;
};
?>

<section class="panel">
    <div class="panel-header">
        <h3><?php echo $isEditing ? 'Editar autor' : 'Nuevo autor'; ?></h3>
        <?php if ($isEditing): ?>
            <a href="index.php?action=autores" class="btn subtle">Cancelar edición</a>
        <?php endif; ?>
    </div>
    <form action="index.php?action=<?php echo $isEditing ? 'autores_update&id=' . urlencode((string) $autor['id_autor']) : 'autores_new'; ?>" method="POST" class="form-grid">
        <label>Nombres
            <input type="text" name="nombres" value="<?php echo $escape($old('nombres')); ?>" required>
        </label>
        <label>Apellidos
            <input type="text" name="apellidos" value="<?php echo $escape($old('apellidos')); ?>" required>
        </label>
        <label>Nacionalidad
            <input type="text" name="nacionalidad" value="<?php echo $escape($old('nacionalidad')); ?>">
        </label>
        <div class="button-row span-3">
            <button type="submit" class="btn success"><?php echo $isEditing ? 'Actualizar autor' : 'Guardar autor'; ?></button>
        </div>
    </form>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="status-line bad"><?php echo $escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="panel-header">
        <h3>Catalogo de autores</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Nacionalidad</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($autores === []): ?>
                    <tr>
                        <td colspan="6" class="empty">No hay autores registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($autores as $row): ?>
                        <?php $activo = (bool) ($row['activo'] ?? true); ?>
                        <tr>
                            <td><?php echo $escape($row['id_autor'] ?? ''); ?></td>
                            <td><?php echo $escape($row['nombres'] ?? ''); ?></td>
                            <td><?php echo $escape($row['apellidos'] ?? ''); ?></td>
                            <td><?php echo $escape($row['nacionalidad'] ?? ''); ?></td>
                            <td><span class="badge <?php echo $activo ? 'ok' : 'bad'; ?>"><?php echo $activo ? 'Activo' : 'Inactivo'; ?></span></td>
                            <td>
                                <div class="actions">
                                    <a href="index.php?action=autores_edit&id=<?php echo urlencode((string) ($row['id_autor'] ?? '')); ?>" class="btn warning">Editar</a>
                                    <a href="index.php?action=autores_delete&id=<?php echo urlencode((string) ($row['id_autor'] ?? '')); ?>" class="btn danger" onclick="return confirm('¿Eliminar este autor?');">Eliminar</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
