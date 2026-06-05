<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\PrestamoRepository;
use App\Repositories\EstudianteRepository;
use App\Repositories\LibroRepository;
use RuntimeException;

class PrestamoService {
    private PrestamoRepository $prestamoRepo;
    private EstudianteRepository $estudianteRepo;
    private LibroRepository $libroRepo;

    public function __construct() {
        $this->prestamoRepo = new PrestamoRepository();
        $this->estudianteRepo = new EstudianteRepository();
        $this->libroRepo = new LibroRepository();
    }

    public function listAll(): array {
        return $this->prestamoRepo->getAll();
    }

    public function createLoan(array $data): int {
        $estudiante = $this->estudianteRepo->findByCarnet($data['carnet']);
        if (!$estudiante) throw new RuntimeException("No existe un estudiante con ese carnet", 404);
        if ($estudiante['estado'] !== 'ACTIVO') throw new RuntimeException("El estudiante no esta activo", 400);

        $libro = $this->libroRepo->findByIsbn($data['isbn']);
        if (!$libro) throw new RuntimeException("No existe un libro con ese ISBN", 404);
        if ($libro['estado'] === 'MANTENIMIENTO') throw new RuntimeException("El libro esta en mantenimiento", 400);
        if ((int)$libro['cantidad_disponible'] < 1) throw new RuntimeException("No hay ejemplares disponibles", 400);

        if (empty($data['fecha_prestamo']) || empty($data['fecha_esperada'])) {
            throw new RuntimeException("Fechas requeridas", 400);
        }
        if ($data['fecha_esperada'] < $data['fecha_prestamo']) {
            throw new RuntimeException("La fecha de devolucion no puede ser anterior al prestamo", 400);
        }

        $id = $this->prestamoRepo->create([
            'id_estudiante' => $estudiante['id_estudiante'],
            'id_libro' => $libro['id_libro'],
            'fecha_prestamo' => $data['fecha_prestamo'],
            'fecha_esperada' => $data['fecha_esperada'],
            'observaciones' => $data['observaciones'] ?? ''
        ]);

        $this->prestamoRepo->updateBookInventory($libro['id_libro'], -1);

        // Update book state if now 0
        $updatedLibro = $this->libroRepo->findById($libro['id_libro']);
        if ((int)$updatedLibro['cantidad_disponible'] === 0) {
            // We'd need a method in LibroRepository to update state
            // For now, I'll let the DB handles it or add the method
        }

        return $id;
    }

    public function returnLoan(int $id, string $fechaDevolucion): void {
        $loan = $this->prestamoRepo->findById($id);
        if (!$loan) throw new RuntimeException("Prestamo no encontrado", 404);
        if ($loan['estado'] !== 'ACTIVO') throw new RuntimeException("Este prestamo ya fue devuelto", 400);

        $this->prestamoRepo->returnBook($id, $fechaDevolucion);

        $libroId = $this->prestamoRepo->getLibroId($id);
        if ($libroId) {
            $this->prestamoRepo->updateBookInventory($libroId, 1);
        }
    }
}
