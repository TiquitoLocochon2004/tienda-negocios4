# Tienda de Negocios - Entrega 3
## Exposición de la Tienda como API REST — Carrito, Checkout y DTOs

¡Proyecto correspondiente a la tercera entrega, convirtiendo la tienda en una API REST robusta, consumible por cualquier cliente (Probado con Postman) con gestión de inventario y DTOs!

---

### 🎯 1. Objetivo del Trabajo

Construir la API REST de la tienda: exponer productos, carrito y proceso de
compra como endpoints HTTP siguiendo buenas prácticas de diseño de API,
con manejo estandarizado de errores, DTOs y formato JSON, verificados con
Postman.

---

### 🌐 2. Principios y Estándares REST

Para cumplir con los estándares de la arquitectura REST en esta entrega, aplicamos los siguientes conceptos clave:

* **Arquitectura Cliente-Servidor:** La interfaz de cliente (en este caso, simulada y probada mediante Postman) está totalmente desacoplada de la lógica del servidor y de la base de datos. La única vía de comunicación es a través de peticiones HTTP estandarizadas.
* **Comunicación sin estado (Stateless):** Cada petición HTTP contiene toda la información necesaria para que el servidor la procese, garantizando la escalabilidad de la API.
* **Uso correcto de Verbos HTTP sobre los Recursos:**
  * `GET`: Se utiliza para consultar recursos de forma segura y sin alterar el estado (ej. listar productos, ver el contenido del carrito, consultar el resumen de compra).
  * `POST`: Se utiliza para la creación de nuevos recursos o ejecución de procesos de negocio (ej. crear un producto, agregar un ítem al carrito, confirmar el checkout final).
  * `PUT`: Se emplea para la actualización completa o modificación del estado de un recurso existente (ej. actualizar la cantidad de un producto específico en el carrito).
  * `DELETE`: Se utiliza para remover recursos del servidor (ej. eliminar un producto del carrito o vaciarlo por completo).

---

### ⚙️ 3. Endpoints de la API REST (`routes/api.php`)

La API expone los siguientes endpoints organizados por recursos:

#### 🔹 Categorías y Productos (CRUD Completo - Resource Controllers)
* `GET /api/categorias` - Lista todas las categorías.
* `POST /api/categorias` - Crea una nueva categoría.
* `GET /api/categorias/{id}` - Muestra una categoría específica.
* `PUT /api/categorias/{id}` - Actualiza una categoría.
* `DELETE /api/categorias/{id}` - Elimina una categoría.
* `GET /api/productos` - Lista todos los productos con su respectiva categoría.
* `POST /api/productos` - Crea un nuevo producto (con validación estricta y limpieza de espacios).
* `GET /api/productos/{id}` - Muestra el detalle de un producto.
* `PUT /api/productos/{id}` - Actualiza los datos de un producto.
* `DELETE /api/productos/{id}` - Elimina un producto.

#### 🛒 Carrito de Compras (Persistencia en Base de Datos)
* `GET /api/carrito` - Muestra los ítems actuales del carrito y calcula el subtotal formateado a dos decimales.
* `POST /api/carrito` - Agrega un producto al carrito (valida existencia e inventario disponible).
* `PUT /api/carrito/{id}` - Actualiza la cantidad de un producto en el carrito.
* `DELETE /api/carrito/{id}` - Elimina un producto específico del carrito.
* `DELETE /api/carrito` - Vacía por completo el carrito del usuario.

#### 💳 Checkout y Resumen de Compra
* `GET /api/checkout/resumen` - Devuelve el desglose detallado de la compra (subtotal, impuestos, costo de envío y total general) estructurado mediante DTOs.
* `POST /api/checkout/confirmar` - Valida el stock final, procesa la compra con datos de envío y pago, descuenta el inventario de la base de datos, vacía el carrito y retorna la orden confirmada.

---

### 📦 4. Uso de Data Transfer Objects (DTOs)

Para estructurar de manera limpia y tipada los datos que viajan desde el servidor hacia el cliente en las respuestas más complejas, se implementaron dos DTOs principales dentro de la carpeta `app/DTOs/`:

1. **`CheckoutDataDTO`**: Estructura el resumen financiero de la compra, separando el bloque de totales formateados estrictamente con dos decimales (subtotal, impuestos, costo de envío y total) y el listado detallado de los ítems involucrados.
2. **`OrdenConfirmadaDTO`**: Estructura el recibo y comprobante final de la transacción exitosa, encapsulando el ID de la orden simulada, el estado del pago, la dirección de envío, el total pagado y la fecha exacta de la operación.

---

### 🛡️ 5. Manejo de Inventario y Códigos de Respuesta HTTP

* **Control de Stock:** La API valida en tiempo real la disponibilidad de stock tanto al agregar productos al carrito (`POST /api/carrito`, controlando que la suma acumulada no supere el inventario) como al momento de confirmar la compra (`POST /api/checkout/confirmar`, realizando una doble validación de seguridad antes de descontar las unidades de la base de datos).
* **Códigos HTTP Estandarizados:** Las respuestas se emiten de manera uniforme en formato JSON acompañadas de códigos de estado precisos:
  * `200 OK`: Operaciones exitosas de lectura, actualización o eliminación.
  * `201 Created`: Creación exitosa de recursos o adición al carrito.
  * `400 Bad Request`: Peticiones inválidas o intento de operaciones con el carrito vacío.
  * `404 Not Found`: Recursos no encontrados en la base de datos o el carrito.
  * `422 Unprocessable Entity`: Errores de validación de datos o falta de stock disponible.

---

### 🚀 6. Instrucciones de Instalación y Pruebas

Para poner en marcha la API en tu entorno local:

1. **Requisitos previos:**
   Asegúrate de tener PHP, Composer y un servidor MySQL activo (como XAMPP).

2. **Instalar dependencias:**
   Abre una terminal en la raíz del proyecto y ejecuta:
   ```Bash
   composer install
   ```

3. **Configurar el entorno:**
   El proyecto ya cuenta con el archivo de configuración `.env` vinculado correctamente al servidor local y a la base de datos en phpMyAdmin (`tienda-negocios3`).

4. **Generar clave y poblar la base de datos:**
   Ejecuta los siguientes comandos en la terminal para asegurar la llave de seguridad y preparar los registros en la base de datos:

   ```Bash
   php artisan key:generate
   ```

   ```Bash
   php artisan migrate --seed
   ```

5. **Iniciar el servidor de la API:**

   ```Bash
   php artisan serve
   ```
   El servidor quedará activo en `http://127.0.0.1:8000`.

6. **Pruebas con Postman:**
   Importa la colección de Postman provista en el repositorio (`.json`) para probar todos los endpoints documentados con ejemplos de request y response listos para usar.

### 🛸 7. Autor del Trabajo

- **Nombre y Apellido**: Agustina Martinez Godoy
- **Email**: martinezgodoyagustin@gmail.com 

> ¡Disfrute del código!