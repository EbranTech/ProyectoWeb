(function(){
    "use strict";

    const adminMenuItem = {id:"usuarios", label:"Usuarios", title:"Usuarios", subtitle:"Gestion de usuarios con acceso al sistema."};

    const baseMenuItems = [
        {id:"prestamos", label:"Prestamos", title:"Prestamos", subtitle:"Registro por carnet e ISBN con datos consultados desde la base simulada."},
        {id:"devoluciones", label:"Devoluciones", title:"Devoluciones", subtitle:"Consulta los prestamos y registra la fecha real de devolucion."},
        {id:"consulta", label:"Libros disponibles", title:"Consulta de libros", subtitle:"Estado, ubicacion y existencias de cada libro registrado."},
        {id:"historial", label:"Historial", title:"Historial de prestamos", subtitle:"Trazabilidad completa de prestamos activos y devueltos."},
        {id:"libros", label:"Libros", title:"Registro de libros", subtitle:"Catalogo conectado con autores y prestamos."},
        {id:"autores", label:"Autores", title:"Registro de autores", subtitle:"Autores disponibles para asociar al catalogo."},
        {id:"estudiantes", label:"Estudiantes", title:"Registro de estudiantes", subtitle:"Estudiantes consultados por carnet al prestar libros."}
    ];

    let currentView = "prestamos";
    let editing = {usuario:null, autor:null, estudiante:null, libro:null};

    const $ = selector => document.querySelector(selector);
    const content = $("#appContent");

    function todayISO(){
        return new Date().toISOString().slice(0,10);
    }

    function addDaysISO(dateISO, days){
        const date = new Date(`${dateISO}T00:00:00`);
        date.setDate(date.getDate() + days);
        return date.toISOString().slice(0,10);
    }

    function escapeHTML(value){
        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function badge(value){
        const estado = String(value || "");
        let type = "info";
        if(["Disponible","Activo"].includes(estado)) type = "ok";
        if(["Prestado","Pendiente","Prestamo activo"].includes(estado)) type = "warn";
        if(["Inactivo","Mantenimiento"].includes(estado)) type = "bad";
        if(["Devuelto","Devolucion"].includes(estado)) type = "info";
        return `<span class="badge ${type}">${escapeHTML(estado)}</span>`;
    }

    function notify(message, type="ok"){
        const target = $("#viewMessage");
        if(!target) return;
        target.textContent = message;
        target.className = `status-line ${type}`;
    }

    function currentRole(){
        return sessionStorage.getItem("rolActual") || "Bibliotecario";
    }

    function currentUsername(){
        return sessionStorage.getItem("usuarioActual") || "";
    }

    function visibleMenuItems(){
        return currentRole() === "Administrador" ? [adminMenuItem, ...baseMenuItems] : baseMenuItems;
    }

    function setHeader(viewId){
        const view = visibleMenuItems().find(item => item.id === viewId);
        $("#viewTitle").textContent = view.title;
        $("#viewSubtitle").textContent = view.subtitle;
        document.querySelectorAll(".menu-button").forEach(button => {
            button.classList.toggle("active", button.dataset.view === viewId);
        });
    }

    function renderMenu(){
        $("#mainMenu").innerHTML = visibleMenuItems().map(item => `
            <button type="button" class="menu-button" data-view="${item.id}">${item.label}</button>
        `).join("");

        document.querySelectorAll(".menu-button").forEach(button => {
            button.addEventListener("click", function(){
                currentView = button.dataset.view;
                render();
            });
        });
    }

    function render(){
        const allowedViews = visibleMenuItems().map(item => item.id);
        if(!allowedViews.includes(currentView)){
            currentView = "prestamos";
        }
        setHeader(currentView);
        const renderers = {
            usuarios:renderUsuarios,
            prestamos:renderPrestamos,
            devoluciones:renderDevoluciones,
            consulta:renderConsulta,
            historial:renderHistorial,
            libros:renderLibros,
            autores:renderAutores,
            estudiantes:renderEstudiantes
        };
        renderers[currentView]();
    }

    function authorOptions(selectedId){
        const db = BiblioDB.load();
        return db.autores.map(author => {
            const selected = Number(author.id) === Number(selectedId) ? "selected" : "";
            return `<option value="${author.id}" ${selected}>${escapeHTML(BiblioDB.authorName(author))}</option>`;
        }).join("");
    }

    function filterRows(tableSelector, filter){
        const text = String(filter || "").toUpperCase();
        document.querySelectorAll(`${tableSelector} tbody tr`).forEach(row => {
            row.style.display = row.textContent.toUpperCase().includes(text) ? "" : "none";
        });
    }

    function renderUsuarios(){
        const db = BiblioDB.load();
        const rows = db.usuarios.map(user => {
            const acceso = user.estado === "Activo" ? "Puede ingresar" : "Sin acceso";
            return `
                <tr>
                    <td>${user.id}</td>
                    <td>${escapeHTML(user.nombre)}</td>
                    <td>${escapeHTML(user.username)}</td>
                    <td>${escapeHTML(user.rol)}</td>
                    <td>${badge(user.estado)}</td>
                    <td>${escapeHTML(acceso)}</td>
                    <td>
                        <div class="actions">
                            <button type="button" class="btn warning" data-edit-user="${user.id}">Editar</button>
                            <button type="button" class="btn danger" data-delete-user="${user.id}">Eliminar</button>
                        </div>
                    </td>
                </tr>
            `;
        }).join("");

        content.innerHTML = `
            <section class="panel">
                <h3>${editing.usuario ? "Editar usuario" : "Nuevo usuario"}</h3>
                <form id="userForm" class="form-grid">
                    <label>Nombre completo<input type="text" id="userName" required></label>
                    <label>Usuario<input type="text" id="userUsername" required></label>
                    <label>Contrasena<input type="password" id="userPassword" ${editing.usuario ? "" : "required"}></label>
                    <label>Rol<select id="userRole"><option>Administrador</option><option>Bibliotecario</option></select></label>
                    <label>Estado<select id="userStatus"><option>Activo</option><option>Inactivo</option></select></label>
                    <div class="button-row">
                        <button type="submit" class="btn success">${editing.usuario ? "Actualizar usuario" : "Guardar usuario"}</button>
                        <button type="button" id="cancelUser" class="btn subtle">Limpiar</button>
                    </div>
                </form>
                <p id="viewMessage" class="status-line"></p>
            </section>
            <section class="panel">
                <div class="panel-header">
                    <h3>Usuarios con acceso</h3>
                    <input type="search" id="userSearch" placeholder="Buscar usuario">
                </div>
                <div class="table-wrap">
                    <table id="usersTable">
                        <thead><tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Rol</th><th>Estado</th><th>Acceso</th><th>Acciones</th></tr></thead>
                        <tbody>${rows || `<tr><td class="empty" colspan="7">No hay usuarios registrados.</td></tr>`}</tbody>
                    </table>
                </div>
            </section>
        `;

        if(editing.usuario){
            const user = db.usuarios.find(item => Number(item.id) === Number(editing.usuario));
            $("#userName").value = user.nombre;
            $("#userUsername").value = user.username;
            $("#userRole").value = user.rol;
            $("#userStatus").value = user.estado;
            $("#userPassword").placeholder = "Dejar vacio para conservar";
        }

        $("#userForm").addEventListener("submit", event => {
            event.preventDefault();
            try{
                const data = {
                    nombre:$("#userName").value,
                    username:$("#userUsername").value,
                    password:$("#userPassword").value,
                    rol:$("#userRole").value,
                    estado:$("#userStatus").value
                };
                let savedUser;
                if(editing.usuario){
                    const previousUsername = currentUsername();
                    savedUser = BiblioDB.updateUser(editing.usuario, data, currentUsername());
                    if(previousUsername === currentUsername() && previousUsername !== savedUser.username){
                        sessionStorage.setItem("usuarioActual", savedUser.username);
                    }
                    if(Number(savedUser.id) === Number(editing.usuario) && sessionStorage.getItem("usuarioActual") === savedUser.username){
                        sessionStorage.setItem("nombreUsuario", savedUser.nombre);
                        sessionStorage.setItem("rolActual", savedUser.rol);
                        $("#nombreUsuario").textContent = savedUser.nombre;
                        $("#rolUsuario").textContent = savedUser.rol;
                    }
                    editing.usuario = null;
                }else{
                    BiblioDB.createUser(data);
                }
                renderUsuarios();
                renderMenu();
                setHeader("usuarios");
                notify("Usuario guardado. Si esta activo, ya puede iniciar sesion.", "ok");
            }catch(error){
                notify(error.message, "bad");
            }
        });

        $("#cancelUser").addEventListener("click", () => {
            editing.usuario = null;
            renderUsuarios();
        });
        $("#userSearch").addEventListener("input", event => filterRows("#usersTable", event.target.value));
        document.querySelectorAll("[data-edit-user]").forEach(button => {
            button.addEventListener("click", () => {
                editing.usuario = Number(button.dataset.editUser);
                renderUsuarios();
            });
        });
        document.querySelectorAll("[data-delete-user]").forEach(button => {
            button.addEventListener("click", () => {
                try{
                    if(confirm("Eliminar este usuario?")){
                        BiblioDB.deleteUser(Number(button.dataset.deleteUser), currentUsername());
                        renderUsuarios();
                    }
                }catch(error){
                    notify(error.message, "bad");
                }
            });
        });
    }

    function renderAutores(){
        const db = BiblioDB.load();
        const rows = db.autores.map(author => {
            const libros = db.libros.filter(book => Number(book.autorId) === Number(author.id)).length;
            return `
                <tr>
                    <td>${author.id}</td>
                    <td>${escapeHTML(BiblioDB.authorName(author))}</td>
                    <td>${escapeHTML(author.nacionalidad)}</td>
                    <td>${libros}</td>
                    <td>
                        <div class="actions">
                            <button type="button" class="btn warning" data-edit-author="${author.id}">Editar</button>
                            <button type="button" class="btn danger" data-delete-author="${author.id}">Eliminar</button>
                        </div>
                    </td>
                </tr>
            `;
        }).join("");

        content.innerHTML = `
            <section class="panel">
                <h3>${editing.autor ? "Editar autor" : "Nuevo autor"}</h3>
                <form id="authorForm" class="form-grid">
                    <label>Nombres<input type="text" id="autorNombres" required></label>
                    <label>Apellidos<input type="text" id="autorApellidos" required></label>
                    <label>Nacionalidad<input type="text" id="autorNacionalidad"></label>
                    <div class="button-row span-3">
                        <button type="submit" class="btn success">${editing.autor ? "Actualizar" : "Registrar"}</button>
                        <button type="button" id="cancelAuthor" class="btn subtle">Limpiar</button>
                    </div>
                </form>
                <p id="viewMessage" class="status-line"></p>
            </section>
            <section class="panel">
                <div class="panel-header">
                    <h3>Autores registrados</h3>
                    <input type="search" id="authorSearch" placeholder="Buscar autor">
                </div>
                <div class="table-wrap">
                    <table id="authorsTable">
                        <thead><tr><th>ID</th><th>Autor</th><th>Nacionalidad</th><th>Libros</th><th>Acciones</th></tr></thead>
                        <tbody>${rows || `<tr><td class="empty" colspan="5">No hay autores registrados.</td></tr>`}</tbody>
                    </table>
                </div>
            </section>
        `;

        if(editing.autor){
            const author = db.autores.find(item => Number(item.id) === Number(editing.autor));
            $("#autorNombres").value = author.nombres;
            $("#autorApellidos").value = author.apellidos;
            $("#autorNacionalidad").value = author.nacionalidad;
        }

        $("#authorForm").addEventListener("submit", event => {
            event.preventDefault();
            try{
                const data = {
                    nombres:$("#autorNombres").value,
                    apellidos:$("#autorApellidos").value,
                    nacionalidad:$("#autorNacionalidad").value
                };
                if(editing.autor){
                    BiblioDB.updateAuthor(editing.autor, data);
                    editing.autor = null;
                }else{
                    BiblioDB.createAuthor(data);
                }
                renderAutores();
                notify("Autor guardado.", "ok");
            }catch(error){
                notify(error.message, "bad");
            }
        });

        $("#cancelAuthor").addEventListener("click", () => {
            editing.autor = null;
            renderAutores();
        });
        $("#authorSearch").addEventListener("input", event => filterRows("#authorsTable", event.target.value));
        document.querySelectorAll("[data-edit-author]").forEach(button => {
            button.addEventListener("click", () => {
                editing.autor = Number(button.dataset.editAuthor);
                renderAutores();
            });
        });
        document.querySelectorAll("[data-delete-author]").forEach(button => {
            button.addEventListener("click", () => {
                try{
                    if(confirm("Eliminar este autor?")){
                        BiblioDB.deleteAuthor(Number(button.dataset.deleteAuthor));
                        renderAutores();
                    }
                }catch(error){
                    notify(error.message, "bad");
                }
            });
        });
    }

    function renderEstudiantes(){
        const db = BiblioDB.load();
        const rows = db.estudiantes.map(student => `
            <tr>
                <td>${escapeHTML(student.carnet)}</td>
                <td>${escapeHTML(BiblioDB.studentName(student))}</td>
                <td>${escapeHTML(student.carrera)}</td>
                <td>${escapeHTML(student.correo)}</td>
                <td>${escapeHTML(student.telefono)}</td>
                <td>${badge(student.estado)}</td>
                <td>
                    <div class="actions">
                        <button type="button" class="btn warning" data-edit-student="${student.id}">Editar</button>
                        <button type="button" class="btn danger" data-delete-student="${student.id}">Eliminar</button>
                    </div>
                </td>
            </tr>
        `).join("");

        content.innerHTML = `
            <section class="panel">
                <h3>${editing.estudiante ? "Editar estudiante" : "Nuevo estudiante"}</h3>
                <form id="studentForm" class="form-grid">
                    <label>Carnet<input type="text" id="estCarnet" required></label>
                    <label>Nombres<input type="text" id="estNombres" required></label>
                    <label>Apellidos<input type="text" id="estApellidos" required></label>
                    <label>Carrera<input type="text" id="estCarrera" required></label>
                    <label>Correo<input type="email" id="estCorreo"></label>
                    <label>Telefono<input type="text" id="estTelefono"></label>
                    <label>Estado<select id="estEstado"><option>Activo</option><option>Inactivo</option></select></label>
                    <div class="button-row span-3">
                        <button type="submit" class="btn success">${editing.estudiante ? "Actualizar" : "Registrar"}</button>
                        <button type="button" id="cancelStudent" class="btn subtle">Limpiar</button>
                    </div>
                </form>
                <p id="viewMessage" class="status-line"></p>
            </section>
            <section class="panel">
                <div class="panel-header">
                    <h3>Estudiantes registrados</h3>
                    <input type="search" id="studentSearch" placeholder="Buscar estudiante">
                </div>
                <div class="table-wrap">
                    <table id="studentsTable">
                        <thead><tr><th>Carnet</th><th>Estudiante</th><th>Carrera</th><th>Correo</th><th>Telefono</th><th>Estado</th><th>Acciones</th></tr></thead>
                        <tbody>${rows || `<tr><td class="empty" colspan="7">No hay estudiantes registrados.</td></tr>`}</tbody>
                    </table>
                </div>
            </section>
        `;

        if(editing.estudiante){
            const student = db.estudiantes.find(item => Number(item.id) === Number(editing.estudiante));
            $("#estCarnet").value = student.carnet;
            $("#estNombres").value = student.nombres;
            $("#estApellidos").value = student.apellidos;
            $("#estCarrera").value = student.carrera;
            $("#estCorreo").value = student.correo;
            $("#estTelefono").value = student.telefono;
            $("#estEstado").value = student.estado;
        }

        $("#studentForm").addEventListener("submit", event => {
            event.preventDefault();
            try{
                const data = {
                    carnet:$("#estCarnet").value,
                    nombres:$("#estNombres").value,
                    apellidos:$("#estApellidos").value,
                    carrera:$("#estCarrera").value,
                    correo:$("#estCorreo").value,
                    telefono:$("#estTelefono").value,
                    estado:$("#estEstado").value
                };
                if(editing.estudiante){
                    BiblioDB.updateStudent(editing.estudiante, data);
                    editing.estudiante = null;
                }else{
                    BiblioDB.createStudent(data);
                }
                renderEstudiantes();
                notify("Estudiante guardado.", "ok");
            }catch(error){
                notify(error.message, "bad");
            }
        });

        $("#cancelStudent").addEventListener("click", () => {
            editing.estudiante = null;
            renderEstudiantes();
        });
        $("#studentSearch").addEventListener("input", event => filterRows("#studentsTable", event.target.value));
        document.querySelectorAll("[data-edit-student]").forEach(button => {
            button.addEventListener("click", () => {
                editing.estudiante = Number(button.dataset.editStudent);
                renderEstudiantes();
            });
        });
        document.querySelectorAll("[data-delete-student]").forEach(button => {
            button.addEventListener("click", () => {
                try{
                    if(confirm("Eliminar este estudiante?")){
                        BiblioDB.deleteStudent(Number(button.dataset.deleteStudent));
                        renderEstudiantes();
                    }
                }catch(error){
                    notify(error.message, "bad");
                }
            });
        });
    }

    function renderLibros(){
        const db = BiblioDB.load();
        const books = BiblioDB.listBooksWithAuthor();
        const rows = books.map(book => `
            <tr>
                <td>${escapeHTML(book.codigo)}</td>
                <td>${escapeHTML(book.isbn)}</td>
                <td>${escapeHTML(book.titulo)}</td>
                <td>${escapeHTML(book.autor)}</td>
                <td>${escapeHTML(book.categoria)}</td>
                <td>${book.disponibles}/${book.total}</td>
                <td>${escapeHTML(book.ubicacion)}</td>
                <td>${badge(book.estado)}</td>
                <td>
                    <div class="actions">
                        <button type="button" class="btn warning" data-edit-book="${book.id}">Editar</button>
                        <button type="button" class="btn danger" data-delete-book="${book.id}">Eliminar</button>
                    </div>
                </td>
            </tr>
        `).join("");

        content.innerHTML = `
            <section class="panel">
                <h3>${editing.libro ? "Editar libro" : "Nuevo libro"}</h3>
                <form id="bookForm" class="form-grid">
                    <label>Codigo libro<input type="text" id="bookCodigo" required></label>
                    <label>ISBN<input type="text" id="bookISBN" required></label>
                    <label>Titulo<input type="text" id="bookTitulo" required></label>
                    <label>Autor<select id="bookAutor" required>${authorOptions()}</select></label>
                    <label>Categoria<input type="text" id="bookCategoria"></label>
                    <label>Editorial<input type="text" id="bookEditorial"></label>
                    <label>Ano<input type="number" id="bookAnio" min="1"></label>
                    <label>Cantidad total<input type="number" id="bookTotal" min="1" required></label>
                    <label>Estado<select id="bookEstado"><option>Disponible</option><option>Prestado</option><option>Mantenimiento</option></select></label>
                    <label class="span-2">Ubicacion<input type="text" id="bookUbicacion" required></label>
                    <div class="button-row span-3">
                        <button type="submit" class="btn success">${editing.libro ? "Actualizar" : "Registrar"}</button>
                        <button type="button" id="cancelBook" class="btn subtle">Limpiar</button>
                    </div>
                </form>
                <p id="viewMessage" class="status-line"></p>
            </section>
            <section class="panel">
                <div class="panel-header">
                    <h3>Catalogo registrado</h3>
                    <input type="search" id="bookSearch" placeholder="Buscar libro">
                </div>
                <div class="table-wrap">
                    <table id="booksTable">
                        <thead><tr><th>Codigo</th><th>ISBN</th><th>Libro</th><th>Autor</th><th>Categoria</th><th>Existencias</th><th>Ubicacion</th><th>Estado</th><th>Acciones</th></tr></thead>
                        <tbody>${rows || `<tr><td class="empty" colspan="9">No hay libros registrados.</td></tr>`}</tbody>
                    </table>
                </div>
            </section>
        `;

        if(editing.libro){
            const book = db.libros.find(item => Number(item.id) === Number(editing.libro));
            $("#bookCodigo").value = book.codigo;
            $("#bookISBN").value = book.isbn;
            $("#bookTitulo").value = book.titulo;
            $("#bookAutor").innerHTML = authorOptions(book.autorId);
            $("#bookCategoria").value = book.categoria;
            $("#bookEditorial").value = book.editorial;
            $("#bookAnio").value = book.anio;
            $("#bookTotal").value = book.total;
            $("#bookEstado").value = book.estado;
            $("#bookUbicacion").value = book.ubicacion;
        }

        $("#bookForm").addEventListener("submit", event => {
            event.preventDefault();
            try{
                const data = {
                    codigo:$("#bookCodigo").value,
                    isbn:$("#bookISBN").value,
                    titulo:$("#bookTitulo").value,
                    autorId:$("#bookAutor").value,
                    categoria:$("#bookCategoria").value,
                    editorial:$("#bookEditorial").value,
                    anio:$("#bookAnio").value,
                    total:$("#bookTotal").value,
                    estado:$("#bookEstado").value,
                    ubicacion:$("#bookUbicacion").value
                };
                if(editing.libro){
                    BiblioDB.updateBook(editing.libro, data);
                    editing.libro = null;
                }else{
                    BiblioDB.createBook(data);
                }
                renderLibros();
                notify("Libro guardado.", "ok");
            }catch(error){
                notify(error.message, "bad");
            }
        });

        $("#cancelBook").addEventListener("click", () => {
            editing.libro = null;
            renderLibros();
        });
        $("#bookSearch").addEventListener("input", event => filterRows("#booksTable", event.target.value));
        document.querySelectorAll("[data-edit-book]").forEach(button => {
            button.addEventListener("click", () => {
                editing.libro = Number(button.dataset.editBook);
                renderLibros();
            });
        });
        document.querySelectorAll("[data-delete-book]").forEach(button => {
            button.addEventListener("click", () => {
                try{
                    if(confirm("Eliminar este libro?")){
                        BiblioDB.deleteBook(Number(button.dataset.deleteBook));
                        renderLibros();
                    }
                }catch(error){
                    notify(error.message, "bad");
                }
            });
        });
    }

    function renderPrestamos(){
        const loanRows = BiblioDB.listLoanRows();
        const rows = loanRows.map(row => `
            <tr>
                <td>${row.id}</td>
                <td>${escapeHTML(row.carnet)}</td>
                <td>${escapeHTML(row.estudiante)}</td>
                <td>${escapeHTML(row.carrera)}</td>
                <td>${escapeHTML(row.codigoLibro)}</td>
                <td>${escapeHTML(row.libro)}</td>
                <td>${escapeHTML(row.fechaPrestamo)}</td>
                <td>${escapeHTML(row.fechaEsperadaDevolucion)}</td>
                <td>${escapeHTML(row.fechaDevolucion)}</td>
                <td>${badge(row.estado)}</td>
            </tr>
        `).join("");

        content.innerHTML = `
            <section class="panel">
                <h3>Nuevo prestamo</h3>
                <form id="loanForm" class="form-grid">
                    <label>Carnet del estudiante<input type="text" id="loanCarnet" required></label>
                    <label>Estudiante<input type="text" id="loanStudentName" readonly></label>
                    <label>Carrera<input type="text" id="loanCareer" readonly></label>
                    <label>ISBN del libro<input type="text" id="loanISBN" required></label>
                    <label>Codigo libro<input type="text" id="loanBookCode" readonly></label>
                    <label>Libro<input type="text" id="loanBookTitle" readonly></label>
                    <label>Fecha de entrega<input type="date" id="loanStart" required></label>
                    <label>Fecha esperada de devolucion<input type="date" id="loanExpectedReturn" required></label>
                    <label>Disponibles<input type="text" id="loanAvailable" readonly></label>
                    <label class="span-3">Observaciones<textarea id="loanNotes"></textarea></label>
                    <div class="button-row span-3">
                        <button type="submit" class="btn success">Registrar prestamo</button>
                        <button type="button" id="clearLoan" class="btn subtle">Limpiar</button>
                    </div>
                </form>
                <p id="viewMessage" class="status-line"></p>
            </section>
            <section class="panel">
                <div class="panel-header">
                    <h3>Prestamos registrados</h3>
                    <input type="search" id="loanSearch" placeholder="Buscar por carnet, estudiante, libro o estado">
                </div>
                <div class="table-wrap">
                    <table id="loansTable">
                        <thead>
                            <tr>
                                <th>ID</th><th>Carnet</th><th>Estudiante</th><th>Carrera</th><th>Codigo libro</th><th>Libro</th><th>Fecha entrega</th><th>Fecha esperada devolucion</th><th>Fecha devolucion</th><th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>${rows || `<tr><td class="empty" colspan="10">No hay prestamos registrados.</td></tr>`}</tbody>
                    </table>
                </div>
            </section>
        `;

        $("#loanStart").value = todayISO();
        $("#loanExpectedReturn").value = addDaysISO(todayISO(), 7);

        const fillStudent = () => {
            const student = BiblioDB.lookupStudent($("#loanCarnet").value);
            $("#loanStudentName").value = student ? BiblioDB.studentName(student) : "";
            $("#loanCareer").value = student ? student.carrera : "";
            if($("#loanCarnet").value && !student) notify("No se encontro un estudiante con ese carnet.", "bad");
        };

        const fillBook = () => {
            const book = BiblioDB.lookupBook($("#loanISBN").value);
            $("#loanBookCode").value = book ? book.codigo : "";
            $("#loanBookTitle").value = book ? book.titulo : "";
            $("#loanAvailable").value = book ? `${book.disponibles} en ${book.ubicacion}` : "";
            if($("#loanISBN").value && !book) notify("No se encontro un libro con ese ISBN.", "bad");
        };

        $("#loanCarnet").addEventListener("input", fillStudent);
        $("#loanISBN").addEventListener("input", fillBook);
        $("#loanSearch").addEventListener("input", event => filterRows("#loansTable", event.target.value));
        $("#clearLoan").addEventListener("click", renderPrestamos);
        $("#loanForm").addEventListener("submit", event => {
            event.preventDefault();
            try{
                BiblioDB.createLoan({
                    carnet:$("#loanCarnet").value,
                    isbn:$("#loanISBN").value,
                    fechaPrestamo:$("#loanStart").value,
                    fechaEsperadaDevolucion:$("#loanExpectedReturn").value,
                    observaciones:$("#loanNotes").value
                });
                renderPrestamos();
                notify("Prestamo registrado. El libro fue descontado de disponibles.", "ok");
            }catch(error){
                notify(error.message, "bad");
            }
        });
    }

    function renderDevoluciones(){
        const loanRows = BiblioDB.listLoanRows();
        const rows = loanRows.map(row => {
            const selectable = row.estado === "Activo" ? "selectable" : "";
            return `
                <tr class="${selectable}" data-loan-id="${row.id}">
                    <td>${row.id}</td>
                    <td>${escapeHTML(row.carnet)}</td>
                    <td>${escapeHTML(row.estudiante)}</td>
                    <td>${escapeHTML(row.carrera)}</td>
                    <td>${escapeHTML(row.codigoLibro)}</td>
                    <td>${escapeHTML(row.libro)}</td>
                    <td>${escapeHTML(row.fechaPrestamo)}</td>
                    <td>${escapeHTML(row.fechaDevolucion)}</td>
                    <td>${badge(row.estado)}</td>
                </tr>
            `;
        }).join("");

        content.innerHTML = `
            <section class="panel">
                <h3>Registrar devolucion</h3>
                <form id="returnForm" class="form-grid">
                    <label>ID prestamo<input type="number" id="returnLoanId" min="1" required></label>
                    <label>Fecha de devolucion<input type="date" id="returnDate" required></label>
                    <div class="button-row">
                        <button type="submit" class="btn success">Registrar devolucion</button>
                    </div>
                </form>
                <p id="viewMessage" class="status-line"></p>
            </section>
            <section class="panel">
                <div class="panel-header">
                    <h3>Query de prestamos</h3>
                    <input type="search" id="returnSearch" placeholder="Buscar por ID, carnet, estudiante o libro">
                </div>
                <div class="table-wrap">
                    <table id="returnsTable">
                        <thead>
                            <tr>
                                <th>ID</th><th>Carnet</th><th>Estudiante</th><th>Carrera</th><th>Codigo libro</th><th>Libro</th><th>Prestamo</th><th>Fecha devolucion</th><th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>${rows || `<tr><td class="empty" colspan="9">No hay prestamos para consultar.</td></tr>`}</tbody>
                    </table>
                </div>
            </section>
        `;

        $("#returnDate").value = todayISO();
        $("#returnForm").addEventListener("submit", event => {
            event.preventDefault();
            try{
                BiblioDB.returnLoan($("#returnLoanId").value, $("#returnDate").value);
                renderDevoluciones();
                notify("Devolucion registrada. El libro vuelve a estar disponible.", "ok");
            }catch(error){
                notify(error.message, "bad");
            }
        });
        $("#returnSearch").addEventListener("input", event => filterRows("#returnsTable", event.target.value));
        document.querySelectorAll("#returnsTable tbody tr[data-loan-id]").forEach(row => {
            row.addEventListener("click", () => {
                if(row.classList.contains("selectable")){
                    $("#returnLoanId").value = row.dataset.loanId;
                    notify(`Prestamo ${row.dataset.loanId} seleccionado.`, "ok");
                }
            });
        });
    }

    function renderConsulta(){
        const books = BiblioDB.listBooksWithAuthor();
        const rows = books.map(book => `
            <tr>
                <td>${escapeHTML(book.codigo)}</td>
                <td>${escapeHTML(book.isbn)}</td>
                <td>${escapeHTML(book.titulo)}</td>
                <td>${escapeHTML(book.autor)}</td>
                <td>${book.disponibles}/${book.total}</td>
                <td>${escapeHTML(book.ubicacion)}</td>
                <td>${badge(book.estado)}</td>
            </tr>
        `).join("");

        content.innerHTML = `
            <section class="panel">
                <div class="toolbar">
                    <label>Buscar<input type="search" id="availableSearch" placeholder="Titulo, autor, codigo o ISBN"></label>
                    <label>Estado<select id="availableState"><option>Disponible</option><option>Prestado</option><option>Mantenimiento</option><option>Todos</option></select></label>
                </div>
            </section>
            <section class="panel">
                <div class="table-wrap">
                    <table id="availableTable">
                        <thead><tr><th>Codigo</th><th>ISBN</th><th>Libro</th><th>Autor</th><th>Existencias</th><th>Ubicacion</th><th>Estado</th></tr></thead>
                        <tbody>${rows || `<tr><td class="empty" colspan="7">No hay libros registrados.</td></tr>`}</tbody>
                    </table>
                </div>
            </section>
        `;

        const applyFilters = () => {
            const text = $("#availableSearch").value.toUpperCase();
            const state = $("#availableState").value;
            document.querySelectorAll("#availableTable tbody tr").forEach(row => {
                const rowState = row.cells[6] ? row.cells[6].textContent.trim() : "";
                const matchesText = row.textContent.toUpperCase().includes(text);
                const matchesState = state === "Todos" || rowState === state;
                row.style.display = matchesText && matchesState ? "" : "none";
            });
        };
        $("#availableSearch").addEventListener("input", applyFilters);
        $("#availableState").addEventListener("change", applyFilters);
        applyFilters();
    }

    function renderHistorial(){
        const historyRows = BiblioDB.listHistoryRows();
        const rows = historyRows.map(row => `
            <tr>
                <td>${row.id}</td>
                <td>${escapeHTML(row.carnet)}</td>
                <td>${escapeHTML(row.estudiante)}</td>
                <td>${escapeHTML(row.carrera)}</td>
                <td>${escapeHTML(row.codigoLibro)}</td>
                <td>${escapeHTML(row.libro)}</td>
                <td>${escapeHTML(row.fechaPrestamo)}</td>
                <td>${escapeHTML(row.fechaEsperadaDevolucion)}</td>
                <td>${escapeHTML(row.fechaDevolucion)}</td>
                <td>${badge(row.movimiento)}</td>
            </tr>
        `).join("");

        content.innerHTML = `
            <section class="panel">
                <div class="toolbar">
                    <label>Buscar<input type="search" id="historySearch" placeholder="Carnet, estudiante, libro o codigo"></label>
                    <label>Movimiento<select id="historyState"><option>Todos</option><option>Prestamo activo</option><option>Devolucion</option></select></label>
                </div>
            </section>
            <section class="panel">
                <div class="table-wrap">
                    <table id="historyTable">
                        <thead>
                            <tr>
                                <th>ID</th><th>Carnet</th><th>Estudiante</th><th>Carrera</th><th>Codigo libro</th><th>Libro</th><th>Fecha entrega</th><th>Fecha esperada devolucion</th><th>Fecha devolucion</th><th>Movimiento</th>
                            </tr>
                        </thead>
                        <tbody>${rows || `<tr><td class="empty" colspan="10">No hay historial de prestamos.</td></tr>`}</tbody>
                    </table>
                </div>
            </section>
        `;

        const applyFilters = () => {
            const text = $("#historySearch").value.toUpperCase();
            const state = $("#historyState").value;
            document.querySelectorAll("#historyTable tbody tr").forEach(row => {
                const rowState = row.cells[9] ? row.cells[9].textContent.trim() : "";
                const matchesText = row.textContent.toUpperCase().includes(text);
                const matchesState = state === "Todos" || rowState === state;
                row.style.display = matchesText && matchesState ? "" : "none";
            });
        };
        $("#historySearch").addEventListener("input", applyFilters);
        $("#historyState").addEventListener("change", applyFilters);
    }

    function init(){
        const usuario = sessionStorage.getItem("usuarioActual");
        if(!usuario){
            window.location.href = "index.html";
            return;
        }

        $("#nombreUsuario").textContent = sessionStorage.getItem("nombreUsuario") || usuario;
        $("#rolUsuario").textContent = sessionStorage.getItem("rolActual") || "Bibliotecario";
        $("#logoutBtn").addEventListener("click", () => {
            sessionStorage.clear();
            window.location.href = "index.html";
        });

        currentView = currentRole() === "Administrador" ? "usuarios" : "prestamos";
        renderMenu();
        render();
    }

    document.addEventListener("DOMContentLoaded", init);
})();
