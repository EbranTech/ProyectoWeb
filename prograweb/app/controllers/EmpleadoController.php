<?php

declare(strict_types=1);

class EmpleadoController
{
    public function __construct(
        private Closure $makeEmpleadoService,
        private EmpleadoValidator $empleadoValidator
    ) {
    }

    public function index(Request $request): void
    {
        Response::json([
            'success' => true,
            'message' => 'Lista de empleados obtenida correctamente',
            'data' => $this->empleadoService()->list(),
        ]);
    }

    public function show(Request $request): void
    {
        $employeeId = $this->getValidId($request);

        if ($employeeId === null) {
            return;
        }

        $employee = $this->empleadoService()->getById($employeeId);

        if ($employee === false) {
            Response::json([
                'success' => false,
                'message' => 'Empleado no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Empleado obtenido correctamente',
            'data' => $employee,
        ]);
    }

    public function store(Request $request): void
    {
        $payload = $request->getBody();
        $validationErrors = $this->empleadoValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $employee = $this->empleadoService()->create($payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Empleado creado correctamente',
            'data' => $employee,
        ], 201);
    }

    public function update(Request $request): void
    {
        $employeeId = $this->getValidId($request);

        if ($employeeId === null) {
            return;
        }

        $payload = $request->getBody();
        $validationErrors = $this->empleadoValidator->validate($payload);

        if ($validationErrors !== []) {
            Response::json([
                'success' => false,
                'message' => 'Errores de validacion',
                'errors' => $validationErrors,
            ], 400);
            return;
        }

        try {
            $updatedEmployee = $this->empleadoService()->update($employeeId, $payload);
        } catch (RuntimeException $exception) {
            Response::json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 400);
            return;
        }

        if ($updatedEmployee === false) {
            Response::json([
                'success' => false,
                'message' => 'Empleado no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Empleado actualizado correctamente',
            'data' => $updatedEmployee,
        ]);
    }

    public function destroy(Request $request): void
    {
        $employeeId = $this->getValidId($request);

        if ($employeeId === null) {
            return;
        }

        $deleted = $this->empleadoService()->delete($employeeId);

        if ($deleted === false) {
            Response::json([
                'success' => false,
                'message' => 'Empleado no encontrado',
            ], 404);
            return;
        }

        Response::json([
            'success' => true,
            'message' => 'Empleado eliminado correctamente',
        ]);
    }

    private function getValidId(Request $request): ?int
    {
        $id = $request->getParam('id');

        if (filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) === false) {
            Response::json([
                'success' => false,
                'message' => 'El id debe ser un numero entero valido',
            ], 400);
            return null;
        }

        return (int) $id;
    }

    private function empleadoService(): EmpleadoService
    {
        return ($this->makeEmpleadoService)();
    }
}
