<?php
$prestamos = $prestamos ?? [];
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>

<section class="panel">
    <div class="panel-header">
        <h3>Historial general de prestamos</h3>
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
                    <th>Devuelto</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($prestamos === []): ?>
                    <tr>
                        <td colspan="9" class="empty">No hay prestamos en el historial.</td>
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
