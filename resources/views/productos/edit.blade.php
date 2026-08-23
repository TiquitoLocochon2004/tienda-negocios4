<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                <!-- Botón de Regresar -->
                <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>

                <!-- Tarjeta del Formulario -->
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h2 class="h4 fw-bold mb-3 text-dark">Editar Producto</h2>
                    <hr class="text-muted mb-4">

                    <!-- Errores de Validación -->
                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('productos.update', $producto->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nombre" class="form-label fw-semibold">Nombre del Producto:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $producto->nombre) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="precio" class="form-label fw-semibold">Precio ($):</label>
                            <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="{{ old('precio', $producto->precio) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="stock" class="form-label fw-semibold">Stock:</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', $producto->stock) }}" min="0" required>
                        </div>

                        <div class="mb-4">
                            <label for="categoria_id" class="form-label fw-semibold">Categoría:</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-semibold shadow-sm">
                                <i class="bi bi-check-lg"></i> Actualizar Producto
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
