<?php
$prestamos = $prestamos ?? [];
$estudiantes = $estudiantes ?? [];
$libros = $libros ?? [];
$formData = $formData ?? [];
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$old = static fn (string $key, mixed $default = ''): string => array_key_exists($key, $formData) ? (string) $formData[$key] : (string) $default;
$prestamosActivos = array_values(array_filter(
    $prestamos,
    static fn (array $row): bool => ($row['estado'] ?? '') === 'ACTIVO'
));
$today = date('Y-m-d');
$defaultEntrega = date('Y-m-d', strtotime('+7 days'));
?>

<section class="panel">
    <div class="panel-header">
        <h3>Registrar prestamo</h3>
    </div>
    <form action="index.php?action=prestamos_new" method="POST" class="form-grid">
        <label>Carnet del estudiante
            <input type="text" name="carnet" list="estudiantes-disponibles" value="<?php echo $escape($old('carnet')); ?>" required>
            <datalist id="estudiantes-disponibles">
                <?php foreach ($estudiantes as $row): ?>
                    <option value="<?php echo $escape($row['carnet'] ?? ''); ?>">
                        <?php echo $escape(trim(($row['nombres'] ?? '') . ' ' . ($row['apellidos'] ?? '') . ' - ' . ($row['carrera'] ?? ''))); ?>
                    </option>
                <?php endforeach; ?>
            </datalist>
        </label>
        <label>ISBN del libro
            <input type="text" name="isbn" list="libros-disponibles" value="<?php echo $escape($old('isbn')); ?>" required>
            <datalist id="libros-disponibles">
                <?php foreach ($libros as $row): ?>
                    <?php if ((int) ($row['cantidad_disponible'] ?? 0) > 0 && ($row['estado'] ?? '') !== 'MANTENIMIENTO'): ?>
                        <option value="<?php echo $escape($row['isbn'] ?? ''); ?>">
                            <?php echo $escape(($row['titulo'] ?? '') . ' - ' . ($row['autor'] ?? '')); ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </datalist>
        </label>
        <label>Fecha de prestamo
            <input type="date" name="fecha_prestamo" value="<?php echo $escape($old('fecha_prestamo', $today)); ?>" required>
        </label>
        <label>Fecha esperada de devolucion
            <input type="date" name="fecha_esperada" value="<?php echo $escape($old('fecha_esperada', $defaultEntrega)); ?>" required>
        </label>
        <label class="span-2">Observaciones
            <textarea name="observaciones"><?php echo $escape($old('observaciones')); ?></textarea>
        </label>
        <div class="button-row span-3">
            <button type="submit" class="btn success">Registrar prestamo</button>
        </div>
    </form>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="status-line bad"><?php echo $escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="panel-header">
        <h3>Prestamos activos y devoluciones</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Carnet</th>
                    <th>Estudiante</th>
                    <th>Libro</th>
                    <th>Autor</th>
                    <th>Prestamo</th>
                    <th>Devolucion esperada</th>
                    <th>Ubicacion</th>
                    <th>Estado</th>
                    <th>Registrar devolucion</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($prestamosActivos === []): ?>
                    <tr>
                        <td colspan="10" class="empty">No hay prestamos activos.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($prestamosActivos as $row): ?>
                        <tr>
                            <td><?php echo $escape($row['id_prestamo'] ?? ''); ?></td>
                            <td><?php echo $escape($row['carnet'] ?? ''); ?></td>
                            <td><?php echo $escape($row['estudiante'] ?? ''); ?></td>
                            <td><?php echo $escape($row['libro'] ?? ''); ?></td>
                            <td><?php echo $escape($row['autor'] ?? ''); ?></td>
                            <td><?php echo $escape($row['fecha_prestamo'] ?? ''); ?></td>
                            <td><?php echo $escape($row['fecha_devolucion_esperada'] ?? ''); ?></td>
                            <td><?php echo $escape($row['ubicacion'] ?? ''); ?></td>
                            <td><span class="badge warn"><?php echo $escape($row['estado'] ?? ''); ?></span></td>
                            <td>
                                <form action="index.php?action=devoluciones_update" method="POST" class="actions">
                                    <input type="hidden" name="redirect_action" value="prestamos">
                                    <input type="hidden" name="id_prestamo" value="<?php echo $escape($row['id_prestamo'] ?? ''); ?>">
                                    <input type="date" name="fecha_devolucion" value="<?php echo $escape($today); ?>" required>
                                    <button type="submit" class="btn primary">Devolver</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="panel-header">
        <h3>Historial reciente de prestamos</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Carnet</th>
                    <th>Estudiante</th>
                    <th>Libro</th>
                    <th>Prestamo</th>
                    <th>Esperada</th>
                    <th>Devuelto</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($prestamos === []): ?>
                    <tr>
                        <td colspan="9" class="empty">No hay movimientos registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($prestamos as $row): ?>
                        <?php $estado = (string) ($row['estado'] ?? ''); ?>
                        <tr>
                            <td><?php echo $escape($row['id_prestamo'] ?? ''); ?></td>
                            <td><?php echo $escape($row['carnet'] ?? ''); ?></td>
                            <td><?php echo $escape($row['estudiante'] ?? ''); ?></td>
                            <td><?php echo $escape($row['libro'] ?? ''); ?></td>
                            <td><?php echo $escape($row['fecha_prestamo'] ?? ''); ?></td>
                            <td><?php echo $escape($row['fecha_devolucion_esperada'] ?? ''); ?></td>
                            <td><?php echo $escape($row['fecha_devolucion_real'] ?? ''); ?></td>
                            <td><span class="badge <?php echo $estado === 'DEVUELTO' ? 'ok' : 'warn'; ?>"><?php echo $escape($estado); ?></span></td>
                            <td><?php echo $escape($row['observaciones'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
