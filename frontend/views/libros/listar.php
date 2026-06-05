<?php
$libros = $libros ?? [];
$autores = $autores ?? [];
$libro = $libro ?? null;
$formData = $formData ?? [];
$isEditing = is_array($libro);
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$old = static function (string $key, mixed $default = '') use ($formData, $libro): string {
    if (array_key_exists($key, $formData)) {
        return (string) $formData[$key];
    }

    if (is_array($libro) && array_key_exists($key, $libro)) {
        return (string) $libro[$key];
    }

    return (string) $default;
};
$selectedAutor = $old('id_autor');
?>

<section class="panel">
    <div class="panel-header">
        <h3><?php echo $isEditing ? 'Editar libro' : 'Nuevo libro'; ?></h3>
        <?php if ($isEditing): ?>
            <a href="index.php?action=libros" class="btn subtle">Cancelar edición</a>
        <?php endif; ?>
    </div>
    <form action="index.php?action=<?php echo $isEditing ? 'libros_update&id=' . urlencode((string) $libro['id_libro']) : 'libros_new'; ?>" method="POST" class="form-grid">
        <label>Codigo interno
            <input type="text" name="codigo" value="<?php echo $escape($old('codigo', $libro['codigo_libro'] ?? '')); ?>" required>
        </label>
        <label>ISBN
            <input type="text" name="isbn" value="<?php echo $escape($old('isbn')); ?>" required>
        </label>
        <label>Titulo
            <input type="text" name="titulo" value="<?php echo $escape($old('titulo')); ?>" required>
        </label>
        <label>Autor
            <select name="id_autor" required>
                <option value="">Seleccione un autor</option>
                <?php foreach ($autores as $row): ?>
                    <?php $autorId = (string) ($row['id_autor'] ?? ''); ?>
                    <option value="<?php echo $escape($autorId); ?>" <?php echo $selectedAutor === $autorId ? 'selected' : ''; ?>>
                        <?php echo $escape(trim(($row['nombres'] ?? '') . ' ' . ($row['apellidos'] ?? ''))); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Categoria
            <input type="text" name="categoria" value="<?php echo $escape($old('categoria')); ?>">
        </label>
        <label>Editorial
            <input type="text" name="editorial" value="<?php echo $escape($old('editorial')); ?>">
        </label>
        <label>Año de publicacion
            <input type="number" name="anio" min="0" value="<?php echo $escape($old('anio', $libro['anio_publicacion'] ?? '')); ?>">
        </label>
        <label>Cantidad total
            <input type="number" name="total" min="1" value="<?php echo $escape($old('total', $libro['cantidad_total'] ?? '1')); ?>" required>
        </label>
        <label>Ubicacion
            <input type="text" name="ubicacion" value="<?php echo $escape($old('ubicacion')); ?>" required>
        </label>
        <label>Estado
            <?php $estadoActual = $old('estado', 'DISPONIBLE'); ?>
            <select name="estado">
                <option value="DISPONIBLE" <?php echo $estadoActual === 'DISPONIBLE' ? 'selected' : ''; ?>>Disponible</option>
                <option value="PRESTADO" <?php echo $estadoActual === 'PRESTADO' ? 'selected' : ''; ?>>Prestado</option>
                <option value="MANTENIMIENTO" <?php echo $estadoActual === 'MANTENIMIENTO' ? 'selected' : ''; ?>>Mantenimiento</option>
            </select>
        </label>
        <div class="button-row span-3">
            <button type="submit" class="btn success"><?php echo $isEditing ? 'Actualizar libro' : 'Guardar libro'; ?></button>
        </div>
    </form>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="status-line bad"><?php echo $escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="panel-header">
        <h3>Inventario bibliografico</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Codigo</th>
                    <th>ISBN</th>
                    <th>Titulo</th>
                    <th>Autor</th>
                    <th>Categoria</th>
                    <th>Total</th>
                    <th>Disponibles</th>
                    <th>Ubicacion</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($libros === []): ?>
                    <tr>
                        <td colspan="11" class="empty">No hay libros registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($libros as $row): ?>
                        <?php $estado = (string) ($row['estado'] ?? 'DISPONIBLE'); ?>
                        <?php
                        $badgeClass = 'info';
                        if ($estado === 'DISPONIBLE') {
                            $badgeClass = 'ok';
                        } elseif ($estado === 'PRESTADO') {
                            $badgeClass = 'warn';
                        } elseif ($estado === 'MANTENIMIENTO') {
                            $badgeClass = 'bad';
                        }
                        ?>
                        <tr>
                            <td><?php echo $escape($row['id_libro'] ?? ''); ?></td>
                            <td><?php echo $escape($row['codigo_libro'] ?? ''); ?></td>
                            <td><?php echo $escape($row['isbn'] ?? ''); ?></td>
                            <td><?php echo $escape($row['titulo'] ?? ''); ?></td>
                            <td><?php echo $escape($row['autor'] ?? ''); ?></td>
                            <td><?php echo $escape($row['categoria'] ?? ''); ?></td>
                            <td><?php echo $escape($row['cantidad_total'] ?? ''); ?></td>
                            <td><?php echo $escape($row['cantidad_disponible'] ?? ''); ?></td>
                            <td><?php echo $escape($row['ubicacion'] ?? ''); ?></td>
                            <td><span class="badge <?php echo $badgeClass; ?>"><?php echo $escape($estado); ?></span></td>
                            <td>
                                <div class="actions">
                                    <a href="index.php?action=libros_edit&id=<?php echo urlencode((string) ($row['id_libro'] ?? '')); ?>" class="btn warning">Editar</a>
                                    <a href="index.php?action=libros_delete&id=<?php echo urlencode((string) ($row['id_libro'] ?? '')); ?>" class="btn danger" onclick="return confirm('¿Eliminar este libro?');">Eliminar</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
