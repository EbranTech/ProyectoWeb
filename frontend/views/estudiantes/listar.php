<?php
$estudiantes = $estudiantes ?? [];
$estudiante = $estudiante ?? null;
$formData = $formData ?? [];
$isEditing = is_array($estudiante);
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$old = static function (string $key, mixed $default = '') use ($formData, $estudiante): string {
    if (array_key_exists($key, $formData)) {
        return (string) $formData[$key];
    }

    if (is_array($estudiante) && array_key_exists($key, $estudiante)) {
        return (string) $estudiante[$key];
    }

    return (string) $default;
};
?>

<section class="panel">
    <div class="panel-header">
        <h3><?php echo $isEditing ? 'Editar estudiante' : 'Nuevo estudiante'; ?></h3>
        <?php if ($isEditing): ?>
            <a href="index.php?action=estudiantes" class="btn subtle">Cancelar edición</a>
        <?php endif; ?>
    </div>
    <form action="index.php?action=<?php echo $isEditing ? 'estudiantes_update&id=' . urlencode((string) $estudiante['id_estudiante']) : 'estudiantes_new'; ?>" method="POST" class="form-grid">
        <label>Carnet
            <input type="text" name="carnet" value="<?php echo $escape($old('carnet')); ?>" required>
        </label>
        <label>Nombres
            <input type="text" name="nombres" value="<?php echo $escape($old('nombres')); ?>" required>
        </label>
        <label>Apellidos
            <input type="text" name="apellidos" value="<?php echo $escape($old('apellidos')); ?>" required>
        </label>
        <label>Carrera
            <input type="text" name="carrera" value="<?php echo $escape($old('carrera')); ?>" required>
        </label>
        <label>Correo
            <input type="email" name="correo" value="<?php echo $escape($old('correo')); ?>">
        </label>
        <label>Telefono
            <input type="text" name="telefono" value="<?php echo $escape($old('telefono')); ?>">
        </label>
        <label>Estado
            <select name="estado">
                <?php $estadoActual = $old('estado', 'ACTIVO'); ?>
                <option value="ACTIVO" <?php echo $estadoActual === 'ACTIVO' ? 'selected' : ''; ?>>Activo</option>
                <option value="INACTIVO" <?php echo $estadoActual === 'INACTIVO' ? 'selected' : ''; ?>>Inactivo</option>
            </select>
        </label>
        <div class="button-row span-3">
            <button type="submit" class="btn success"><?php echo $isEditing ? 'Actualizar estudiante' : 'Guardar estudiante'; ?></button>
        </div>
    </form>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="status-line bad"><?php echo $escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="panel-header">
        <h3>Directorio de estudiantes</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Carnet</th>
                    <th>Estudiante</th>
                    <th>Carrera</th>
                    <th>Correo</th>
                    <th>Telefono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($estudiantes === []): ?>
                    <tr>
                        <td colspan="8" class="empty">No hay estudiantes registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($estudiantes as $row): ?>
                        <?php $estado = (string) ($row['estado'] ?? 'ACTIVO'); ?>
                        <tr>
                            <td><?php echo $escape($row['id_estudiante'] ?? ''); ?></td>
                            <td><?php echo $escape($row['carnet'] ?? ''); ?></td>
                            <td><?php echo $escape(trim(($row['nombres'] ?? '') . ' ' . ($row['apellidos'] ?? ''))); ?></td>
                            <td><?php echo $escape($row['carrera'] ?? ''); ?></td>
                            <td><?php echo $escape($row['correo'] ?? ''); ?></td>
                            <td><?php echo $escape($row['telefono'] ?? ''); ?></td>
                            <td><span class="badge <?php echo $estado === 'ACTIVO' ? 'ok' : 'bad'; ?>"><?php echo $escape($estado); ?></span></td>
                            <td>
                                <div class="actions">
                                    <a href="index.php?action=estudiantes_edit&id=<?php echo urlencode((string) ($row['id_estudiante'] ?? '')); ?>" class="btn warning">Editar</a>
                                    <a href="index.php?action=estudiantes_delete&id=<?php echo urlencode((string) ($row['id_estudiante'] ?? '')); ?>" class="btn danger" onclick="return confirm('¿Eliminar este estudiante?');">Eliminar</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
