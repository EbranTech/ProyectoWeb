<section class="panel">
    <h3>Nuevo usuario</h3>
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
        </div>
    </form>
    <?php if (isset($_SESSION['error'])): ?>
        <p class="status-line bad"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>
</section>

<section class="panel">
    <div class="panel-header">
        <h3>Usuarios con acceso</h3>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Estado</th><th>Acceso</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $user): ?>
                    <tr>
                        <td><?php echo $user['id_usuario']; ?></td>
                        <td><?php echo htmlspecialchars($user['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['rol']); ?></td>
                        <td><span class="badge <?php echo $user['activo'] ? 'ok' : 'bad'; ?>"><?php echo $user['activo'] ? 'Activo' : 'Inactivo'; ?></span></td>
                        <td><?php echo $user['activo'] ? 'Puede ingresar' : 'Sin acceso'; ?></td>
                        <td>
                            <div class="actions">
                                <a href="index.php?action=usuarios_edit&id=<?php echo $user['id_usuario']; ?>" class="btn warning">Editar</a>
                                <a href="index.php?action=usuarios_delete&id=<?php echo $user['id_usuario']; ?>" class="btn danger" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
