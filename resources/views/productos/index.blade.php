<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Productos</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <div class="container py-5">
        <!-- Encabezado de la Sección -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold text-dark">Gestión de Productos</h1>
                <p class="text-muted m-0">Administra el inventario de tu tienda de forma rápida y segura.</p>
            </div>
            <div>
                <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary shadow-sm me-2">
                    <i class="bi bi-tags me-1"></i> Ir a Categorías
                </a>
                <a href="{{ route('productos.create') }}" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo Producto
                </a>
            </div>
        </div>

        <!-- Alerta de Éxito -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Tarjeta Contenedora de la Tabla -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark text-uppercase fs-7">
                            <tr>
                                <th class="ps-4">#ID</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Categoría</th>
                                <th class="text-end pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productos as $producto)
                                <tr>
                                    <td class="ps-4 fw-semibold text-secondary">#{{ $producto->id }}</td>
                                    <td class="fw-bold text-dark">{{ $producto->nombre }}</td>
                                    <td class="text-success fw-semibold">${{ number_format($producto->precio, 2) }}</td>
                                    <td>
                                        <!-- Tonalidad azul-grisácea sobria en lugar del azul chillón -->
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                            {{ $producto->stock }} unidades
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                            {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <!-- Botón Editar -->
                                        <a href="{{ route('productos.edit', $producto->id) }}" class="btn btn-outline-warning btn-sm me-1" title="Editar">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </a>

                                        <!-- Botón Eliminar -->
                                        <form action="{{ route('productos.destroy', $producto->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este producto?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No hay productos registrados todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
