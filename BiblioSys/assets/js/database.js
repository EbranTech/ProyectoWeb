(function(window){
    "use strict";

    const STORAGE_KEY = "bibliosys_db_v3";

    const seed = {
        usuarios:[
            {id:1, username:"karla.motta", password:"karla123*", nombre:"Karla Motta", rol:"Administrador", estado:"Activo"},
            {id:2, username:"yesenia.garcia", password:"yesenia123", nombre:"Yesenia Garcia", rol:"Bibliotecario", estado:"Activo"}
        ],
        autores:[
            {id:1, nombres:"Robert", apellidos:"Martin", nacionalidad:"Estados Unidos"},
            {id:2, nombres:"Martin", apellidos:"Fowler", nacionalidad:"Estados Unidos"},
            {id:3, nombres:"Abraham", apellidos:"Silberschatz", nacionalidad:"Estados Unidos"},
            {id:4, nombres:"Gabriel", apellidos:"Garcia Marquez", nacionalidad:"Colombia"},
            {id:5, nombres:"Thomas", apellidos:"Cormen", nacionalidad:"Estados Unidos"}
        ],
        estudiantes:[
            {id:1, carnet:"20250001", nombres:"Carlos", apellidos:"Ramirez", carrera:"Ingenieria en Sistemas", correo:"carlos@uvg.edu.gt", telefono:"55510001", estado:"Activo"},
            {id:2, carnet:"20250002", nombres:"Ana", apellidos:"Lopez", carrera:"Ingenieria Industrial", correo:"ana@uvg.edu.gt", telefono:"55510002", estado:"Activo"},
            {id:3, carnet:"20250003", nombres:"Luis", apellidos:"Morales", carrera:"Ingenieria Civil", correo:"luis@uvg.edu.gt", telefono:"55510003", estado:"Activo"},
            {id:4, carnet:"20250004", nombres:"Maria", apellidos:"Castillo", carrera:"Arquitectura", correo:"maria@uvg.edu.gt", telefono:"55510004", estado:"Activo"}
        ],
        libros:[
            {id:1, codigo:"LIB001", isbn:"9780132350884", titulo:"Clean Code", autorId:1, categoria:"Programacion", editorial:"Prentice Hall", anio:2008, total:5, disponibles:4, ubicacion:"EST-001 Programacion", estado:"Disponible"},
            {id:2, codigo:"LIB002", isbn:"9780137081073", titulo:"Clean Coder", autorId:1, categoria:"Programacion", editorial:"Prentice Hall", anio:2011, total:4, disponibles:4, ubicacion:"EST-001 Programacion", estado:"Disponible"},
            {id:3, codigo:"LIB003", isbn:"9780201485677", titulo:"Refactoring", autorId:2, categoria:"Ingenieria de Software", editorial:"Addison Wesley", anio:1999, total:3, disponibles:3, ubicacion:"EST-004 Software", estado:"Disponible"},
            {id:4, codigo:"LIB004", isbn:"9780073523323", titulo:"Database System Concepts", autorId:3, categoria:"Bases de Datos", editorial:"McGraw Hill", anio:2019, total:6, disponibles:6, ubicacion:"EST-002 Bases de Datos", estado:"Disponible"},
            {id:5, codigo:"LIB005", isbn:"9780262033848", titulo:"Introduction to Algorithms", autorId:5, categoria:"Algoritmos", editorial:"MIT Press", anio:2009, total:5, disponibles:4, ubicacion:"EST-005 Matematica", estado:"Disponible"},
            {id:6, codigo:"LIB006", isbn:"9780307474728", titulo:"Cien Anos de Soledad", autorId:4, categoria:"Literatura", editorial:"Sudamericana", anio:1967, total:2, disponibles:0, ubicacion:"EST-009 Literatura", estado:"Prestado"}
        ],
        prestamos:[
            {id:1, estudianteId:1, libroId:1, fechaPrestamo:"2026-06-01", fechaEsperadaDevolucion:"2026-06-10", fechaDevolucionReal:null, estado:"Activo", observaciones:"Prestamo vigente"},
            {id:2, estudianteId:2, libroId:3, fechaPrestamo:"2026-05-20", fechaEsperadaDevolucion:"2026-05-30", fechaDevolucionReal:"2026-05-29", estado:"Devuelto", observaciones:"Devuelto en buen estado"},
            {id:3, estudianteId:3, libroId:5, fechaPrestamo:"2026-06-02", fechaEsperadaDevolucion:"2026-06-12", fechaDevolucionReal:null, estado:"Activo", observaciones:"Prestamo vigente"},
            {id:4, estudianteId:4, libroId:6, fechaPrestamo:"2026-05-25", fechaEsperadaDevolucion:"2026-06-04", fechaDevolucionReal:null, estado:"Activo", observaciones:"Ejemplar unico prestado"}
        ]
    };

    function clone(value){
        return JSON.parse(JSON.stringify(value));
    }

    function load(){
        const raw = localStorage.getItem(STORAGE_KEY);
        if(!raw){
            const fresh = clone(seed);
            save(fresh);
            return fresh;
        }

        try{
            const db = JSON.parse(raw);
            if(!db.autores || !db.estudiantes || !db.libros || !db.prestamos){
                throw new Error("Estructura incompleta");
            }
            let migrated = false;
            if(!db.usuarios){
                db.usuarios = clone(seed.usuarios);
                migrated = true;
            }
            db.usuarios.forEach(user => {
                if(!user.estado){
                    user.estado = "Activo";
                    migrated = true;
                }
            });
            if(migrated) save(db);
            return db;
        }catch(error){
            const fresh = clone(seed);
            save(fresh);
            return fresh;
        }
    }

    function save(db){
        localStorage.setItem(STORAGE_KEY, JSON.stringify(db));
    }

    function reset(){
        const fresh = clone(seed);
        save(fresh);
        return fresh;
    }

    function nextId(collection){
        return collection.length ? Math.max.apply(null, collection.map(item => Number(item.id))) + 1 : 1;
    }

    function clean(value){
        return String(value || "").trim();
    }

    function upper(value){
        return clean(value).toUpperCase();
    }

    function authorName(author){
        return author ? `${author.nombres} ${author.apellidos}`.trim() : "Autor no registrado";
    }

    function studentName(student){
        return student ? `${student.nombres} ${student.apellidos}`.trim() : "Estudiante no registrado";
    }

    function getAuthor(db, id){
        return db.autores.find(item => Number(item.id) === Number(id));
    }

    function getStudent(db, id){
        return db.estudiantes.find(item => Number(item.id) === Number(id));
    }

    function getBook(db, id){
        return db.libros.find(item => Number(item.id) === Number(id));
    }

    function findStudentByCarnet(db, carnet){
        return db.estudiantes.find(item => upper(item.carnet) === upper(carnet));
    }

    function findBookByISBN(db, isbn){
        return db.libros.find(item => upper(item.isbn) === upper(isbn));
    }

    function normalizeBookState(book){
        if(book.estado === "Mantenimiento"){
            return book;
        }
        book.estado = Number(book.disponibles) > 0 ? "Disponible" : "Prestado";
        return book;
    }

    function loanRow(db, loan){
        const student = getStudent(db, loan.estudianteId);
        const book = getBook(db, loan.libroId);
        const author = book ? getAuthor(db, book.autorId) : null;

        return {
            id:loan.id,
            carnet:student ? student.carnet : "",
            estudiante:studentName(student),
            carrera:student ? student.carrera : "",
            codigoLibro:book ? book.codigo : "",
            isbn:book ? book.isbn : "",
            libro:book ? book.titulo : "",
            autor:authorName(author),
            ubicacion:book ? book.ubicacion : "",
            fechaPrestamo:loan.fechaPrestamo,
            fechaEsperadaDevolucion:loan.fechaEsperadaDevolucion,
            fechaDevolucion:loan.fechaDevolucionReal || "Pendiente",
            estado:loan.estado,
            observaciones:loan.observaciones || ""
        };
    }

    function listLoanRows(){
        const db = load();
        return db.prestamos
            .slice()
            .sort((a,b) => Number(b.id) - Number(a.id))
            .map(loan => loanRow(db, loan));
    }

    function listHistoryRows(){
        return listLoanRows().map(row => ({
            ...row,
            movimiento:row.estado === "Devuelto" ? "Devolucion" : "Prestamo activo"
        }));
    }

    function listBooksWithAuthor(){
        const db = load();
        db.libros.forEach(normalizeBookState);
        save(db);

        return db.libros.map(book => ({
            ...book,
            autor:authorName(getAuthor(db, book.autorId))
        }));
    }

    function authenticate(username, password){
        const db = load();
        return db.usuarios.find(user => user.username === username && user.password === password && user.estado === "Activo") || null;
    }

    function normalizeUsername(value){
        return clean(value).toLowerCase();
    }

    function findUser(db, id){
        return db.usuarios.find(item => Number(item.id) === Number(id));
    }

    function hasAnotherActiveAdmin(db, userId){
        return db.usuarios.some(user =>
            Number(user.id) !== Number(userId) &&
            user.rol === "Administrador" &&
            user.estado === "Activo"
        );
    }

    function createUser(data){
        const db = load();
        const nombre = clean(data.nombre);
        const username = normalizeUsername(data.username);
        const password = clean(data.password);
        const rol = data.rol || "Bibliotecario";
        const estado = data.estado || "Activo";

        if(!nombre || !username || !password){
            throw new Error("Complete nombre, usuario y contrasena.");
        }
        if(db.usuarios.some(user => user.username === username)){
            throw new Error("Ya existe un usuario con ese acceso.");
        }

        const id = nextId(db.usuarios);
        db.usuarios.push({id, nombre, username, password, rol, estado});
        save(db);
        return id;
    }

    function updateUser(id, data, currentUsername){
        const db = load();
        const user = findUser(db, id);
        if(!user) throw new Error("Usuario no encontrado.");

        const nombre = clean(data.nombre);
        const username = normalizeUsername(data.username);
        const rol = data.rol || "Bibliotecario";
        const estado = data.estado || "Activo";
        const duplicated = db.usuarios.some(item => Number(item.id) !== Number(id) && item.username === username);

        if(!nombre || !username){
            throw new Error("Complete nombre y usuario.");
        }
        if(duplicated){
            throw new Error("Ya existe un usuario con ese acceso.");
        }
        if(user.username === currentUsername && (rol !== "Administrador" || estado !== "Activo")){
            throw new Error("No puede quitar el acceso administrador del usuario en sesion.");
        }
        if(user.rol === "Administrador" && user.estado === "Activo" && (rol !== "Administrador" || estado !== "Activo") && !hasAnotherActiveAdmin(db, user.id)){
            throw new Error("Debe quedar al menos un administrador activo.");
        }

        user.nombre = nombre;
        user.username = username;
        if(clean(data.password)){
            user.password = clean(data.password);
        }
        user.rol = rol;
        user.estado = estado;
        save(db);
        return user;
    }

    function deleteUser(id, currentUsername){
        const db = load();
        const user = findUser(db, id);
        if(!user) throw new Error("Usuario no encontrado.");
        if(user.username === currentUsername){
            throw new Error("No puede eliminar el usuario en sesion.");
        }
        if(user.rol === "Administrador" && user.estado === "Activo" && !hasAnotherActiveAdmin(db, user.id)){
            throw new Error("Debe quedar al menos un administrador activo.");
        }
        db.usuarios = db.usuarios.filter(item => Number(item.id) !== Number(id));
        save(db);
    }

    function createAuthor(data){
        const db = load();
        const nombres = clean(data.nombres);
        const apellidos = clean(data.apellidos);
        const nacionalidad = clean(data.nacionalidad);

        if(!nombres || !apellidos){
            throw new Error("Ingrese nombres y apellidos del autor.");
        }

        const id = nextId(db.autores);
        db.autores.push({id, nombres, apellidos, nacionalidad});
        save(db);
        return id;
    }

    function updateAuthor(id, data){
        const db = load();
        const author = getAuthor(db, id);
        if(!author) throw new Error("Autor no encontrado.");
        author.nombres = clean(data.nombres);
        author.apellidos = clean(data.apellidos);
        author.nacionalidad = clean(data.nacionalidad);
        if(!author.nombres || !author.apellidos) throw new Error("Ingrese nombres y apellidos del autor.");
        save(db);
    }

    function deleteAuthor(id){
        const db = load();
        if(db.libros.some(book => Number(book.autorId) === Number(id))){
            throw new Error("No se puede eliminar un autor asociado a libros.");
        }
        db.autores = db.autores.filter(item => Number(item.id) !== Number(id));
        save(db);
    }

    function createStudent(data){
        const db = load();
        const carnet = clean(data.carnet);
        if(!carnet || !clean(data.nombres) || !clean(data.apellidos) || !clean(data.carrera)){
            throw new Error("Complete carnet, nombres, apellidos y carrera.");
        }
        if(findStudentByCarnet(db, carnet)){
            throw new Error("Ya existe un estudiante con ese carnet.");
        }
        const id = nextId(db.estudiantes);
        db.estudiantes.push({
            id,
            carnet,
            nombres:clean(data.nombres),
            apellidos:clean(data.apellidos),
            carrera:clean(data.carrera),
            correo:clean(data.correo),
            telefono:clean(data.telefono),
            estado:data.estado || "Activo"
        });
        save(db);
        return id;
    }

    function updateStudent(id, data){
        const db = load();
        const student = getStudent(db, id);
        if(!student) throw new Error("Estudiante no encontrado.");
        const carnet = clean(data.carnet);
        const duplicated = db.estudiantes.some(item => item.id !== student.id && upper(item.carnet) === upper(carnet));
        if(duplicated) throw new Error("Ya existe un estudiante con ese carnet.");
        if(!carnet || !clean(data.nombres) || !clean(data.apellidos) || !clean(data.carrera)){
            throw new Error("Complete carnet, nombres, apellidos y carrera.");
        }
        Object.assign(student, {
            carnet,
            nombres:clean(data.nombres),
            apellidos:clean(data.apellidos),
            carrera:clean(data.carrera),
            correo:clean(data.correo),
            telefono:clean(data.telefono),
            estado:data.estado || "Activo"
        });
        save(db);
    }

    function deleteStudent(id){
        const db = load();
        if(db.prestamos.some(loan => Number(loan.estudianteId) === Number(id) && loan.estado === "Activo")){
            throw new Error("No se puede eliminar un estudiante con prestamos activos.");
        }
        db.estudiantes = db.estudiantes.filter(item => Number(item.id) !== Number(id));
        save(db);
    }

    function createBook(data){
        const db = load();
        const codigo = clean(data.codigo);
        const isbn = clean(data.isbn);
        const total = Number(data.total);

        if(!codigo || !isbn || !clean(data.titulo) || !Number(data.autorId) || !total || total < 1){
            throw new Error("Complete codigo, ISBN, titulo, autor y cantidad total.");
        }
        if(db.libros.some(item => upper(item.codigo) === upper(codigo))){
            throw new Error("Ya existe un libro con ese codigo.");
        }
        if(findBookByISBN(db, isbn)){
            throw new Error("Ya existe un libro con ese ISBN.");
        }

        const id = nextId(db.libros);
        const estado = data.estado || "Disponible";
        const disponibles = estado === "Mantenimiento" ? 0 : total;

        db.libros.push(normalizeBookState({
            id,
            codigo,
            isbn,
            titulo:clean(data.titulo),
            autorId:Number(data.autorId),
            categoria:clean(data.categoria),
            editorial:clean(data.editorial),
            anio:Number(data.anio) || "",
            total,
            disponibles,
            ubicacion:clean(data.ubicacion),
            estado
        }));
        save(db);
        return id;
    }

    function updateBook(id, data){
        const db = load();
        const book = getBook(db, id);
        if(!book) throw new Error("Libro no encontrado.");

        const codigo = clean(data.codigo);
        const isbn = clean(data.isbn);
        const total = Number(data.total);
        const activeLoans = db.prestamos.filter(loan => Number(loan.libroId) === Number(id) && loan.estado === "Activo").length;
        const duplicatedCode = db.libros.some(item => item.id !== book.id && upper(item.codigo) === upper(codigo));
        const duplicatedISBN = db.libros.some(item => item.id !== book.id && upper(item.isbn) === upper(isbn));

        if(!codigo || !isbn || !clean(data.titulo) || !Number(data.autorId) || !total || total < activeLoans){
            throw new Error("Complete los campos del libro y respete los prestamos activos.");
        }
        if(duplicatedCode) throw new Error("Ya existe un libro con ese codigo.");
        if(duplicatedISBN) throw new Error("Ya existe un libro con ese ISBN.");

        Object.assign(book, {
            codigo,
            isbn,
            titulo:clean(data.titulo),
            autorId:Number(data.autorId),
            categoria:clean(data.categoria),
            editorial:clean(data.editorial),
            anio:Number(data.anio) || "",
            total,
            disponibles:Math.max(0, total - activeLoans),
            ubicacion:clean(data.ubicacion),
            estado:data.estado || "Disponible"
        });
        normalizeBookState(book);
        if(data.estado === "Mantenimiento"){
            book.estado = "Mantenimiento";
            book.disponibles = 0;
        }
        save(db);
    }

    function deleteBook(id){
        const db = load();
        if(db.prestamos.some(loan => Number(loan.libroId) === Number(id) && loan.estado === "Activo")){
            throw new Error("No se puede eliminar un libro con prestamos activos.");
        }
        db.libros = db.libros.filter(item => Number(item.id) !== Number(id));
        save(db);
    }

    function lookupStudent(carnet){
        const db = load();
        return findStudentByCarnet(db, carnet) || null;
    }

    function lookupBook(isbn){
        const db = load();
        const book = findBookByISBN(db, isbn);
        if(!book) return null;
        return {
            ...book,
            autor:authorName(getAuthor(db, book.autorId))
        };
    }

    function createLoan(data){
        const db = load();
        const student = findStudentByCarnet(db, data.carnet);
        const book = findBookByISBN(db, data.isbn);

        if(!student) throw new Error("No existe un estudiante con ese carnet.");
        if(student.estado !== "Activo") throw new Error("El estudiante no esta activo.");
        if(!book) throw new Error("No existe un libro con ese ISBN.");
        if(book.estado === "Mantenimiento") throw new Error("El libro esta en mantenimiento.");
        if(Number(book.disponibles) < 1) throw new Error("No hay ejemplares disponibles de ese libro.");
        if(!data.fechaPrestamo || !data.fechaEsperadaDevolucion){
            throw new Error("Ingrese fecha de entrega y fecha esperada de devolucion.");
        }
        if(data.fechaEsperadaDevolucion < data.fechaPrestamo){
            throw new Error("La fecha esperada de devolucion no puede ser anterior al prestamo.");
        }

        const id = nextId(db.prestamos);
        db.prestamos.push({
            id,
            estudianteId:student.id,
            libroId:book.id,
            fechaPrestamo:data.fechaPrestamo,
            fechaEsperadaDevolucion:data.fechaEsperadaDevolucion,
            fechaDevolucionReal:null,
            estado:"Activo",
            observaciones:clean(data.observaciones)
        });

        book.disponibles = Number(book.disponibles) - 1;
        normalizeBookState(book);
        save(db);
        return id;
    }

    function returnLoan(id, fechaDevolucion){
        const db = load();
        const loan = db.prestamos.find(item => Number(item.id) === Number(id));
        if(!loan) throw new Error("No existe un prestamo con ese ID.");
        if(loan.estado !== "Activo") throw new Error("Ese prestamo ya fue devuelto.");
        if(!fechaDevolucion) throw new Error("Ingrese la fecha de devolucion.");

        loan.estado = "Devuelto";
        loan.fechaDevolucionReal = fechaDevolucion;

        const book = getBook(db, loan.libroId);
        if(book){
            book.disponibles = Math.min(Number(book.total), Number(book.disponibles) + 1);
            normalizeBookState(book);
        }
        save(db);
    }

    window.BiblioDB = {
        load,
        save,
        reset,
        authenticate,
        createUser,
        updateUser,
        deleteUser,
        authorName,
        studentName,
        listBooksWithAuthor,
        listLoanRows,
        listHistoryRows,
        lookupStudent,
        lookupBook,
        createAuthor,
        updateAuthor,
        deleteAuthor,
        createStudent,
        updateStudent,
        deleteStudent,
        createBook,
        updateBook,
        deleteBook,
        createLoan,
        returnLoan
    };
})(window);
