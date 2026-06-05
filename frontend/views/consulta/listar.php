<?php
$libros = $libros ?? [];
$escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$disponibles = array_values(array_filter(
    $libros,
    static fn (array $row): bool => (bool) ($row['activo'] ?? true)
        && ($row['estado'] ?? '') === 'DISPONIBLE'
        && (int) ($row['cantidad_disponible'] ?? 0) > 0
));
?>

<section class="panel">
    <div class="panel-header">
        <h3>Libros disponibles para prestamo</h3>
    </div>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="status-line bad"><?php echo $escape($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Codigo</th>
                    <th>ISBN</th>
                    <th>Titulo</th>
                    <th>Autor</th>
                    <th>Categoria</th>
                    <th>Disponibles</th>
                    <th>Ubicacion</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($disponibles === []): ?>
                    <tr>
                        <td colspan="7" class="empty">No hay libros disponibles actualmente.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($disponibles as $row): ?>
                        <tr>
                            <td><?php echo $escape($row['codigo_libro'] ?? ''); ?></td>
                            <td><?php echo $escape($row['isbn'] ?? ''); ?></td>
                            <td><?php echo $escape($row['titulo'] ?? ''); ?></td>
                            <td><?php echo $escape($row['autor'] ?? ''); ?></td>
                            <td><?php echo $escape($row['categoria'] ?? ''); ?></td>
                            <td><span class="badge ok"><?php echo $escape($row['cantidad_disponible'] ?? '0'); ?></span></td>
                            <td><?php echo $escape($row['ubicacion'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
