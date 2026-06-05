<?php
$prestamos = $prestamos ?? [];
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$prestamosActivos = array_values(array_filter(
    $prestamos,
    static fn (array $row): bool => ($row['estado'] ?? '') === 'ACTIVO'
));
$today = date('Y-m-d');
?>

<section class="panel">
    <div class="panel-header">
        <h3>Registrar devoluciones</h3>
    </div>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="status-line bad"><?php echo $escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
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
                    <th>Registrar devolucion</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($prestamosActivos === []): ?>
                    <tr>
                        <td colspan="7" class="empty">No hay prestamos pendientes de devolucion.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($prestamosActivos as $row): ?>
                        <tr>
                            <td><?php echo $escape($row['id_prestamo'] ?? ''); ?></td>
                            <td><?php echo $escape($row['carnet'] ?? ''); ?></td>
                            <td><?php echo $escape($row['estudiante'] ?? ''); ?></td>
                            <td><?php echo $escape($row['libro'] ?? ''); ?></td>
                            <td><?php echo $escape($row['fecha_prestamo'] ?? ''); ?></td>
                            <td><?php echo $escape($row['fecha_devolucion_esperada'] ?? ''); ?></td>
                            <td>
                                <form action="index.php?action=devoluciones_update" method="POST" class="actions">
                                    <input type="hidden" name="redirect_action" value="devoluciones">
                                    <input type="hidden" name="id_prestamo" value="<?php echo $escape($row['id_prestamo'] ?? ''); ?>">
                                    <input type="date" name="fecha_devolucion" value="<?php echo $escape($today); ?>" required>
                                    <button type="submit" class="btn primary">Registrar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
