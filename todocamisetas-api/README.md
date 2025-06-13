# TodoCamisetas API

TodoCamisetas es un mayorista chileno especializado en la distribución de camisetas de fútbol nacionales e internacionales, orientado a ventas B2B para tiendas minoristas. Este proyecto provee una API RESTful desarrollada en PHP puro (sin frameworks) para la gestión eficiente de inventario y clientes.

**Incluye:**
- Código fuente completo de la API.
- Archivo `todocamisetas_api.sql` para la creación de la base de datos y carga de datos de ejemplo.

## 🚀 Características

- ✅ **PHP Puro**: Sin frameworks, máximo rendimiento
- ✅ **RESTful**: Cumple con estándares REST
- ✅ **PDO**: Conexiones seguras con consultas preparadas
- ✅ **Validaciones**: Sistema robusto de validación de datos
- ✅ **CORS**: Configurado para peticiones cross-origin
- ✅ **Rate Limiting**: Headers de control de velocidad
- ✅ **Documentación**: Swagger y Postman incluidos

## 📋 Requisitos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache con mod_rewrite habilitado
- XAMPP (recomendado para desarrollo local)

## 🛠️ Instalación

### 1. Clonar o extraer el proyecto
```bash
# Copiar el directorio todocamisetas-api a la carpeta htdocs de XAMPP
cp -r todocamisetas-api /path/to/xampp/htdocs/
```

### 2. Configurar la base de datos
```sql
-- Crear la base de datos (ejecutar en phpMyAdmin o MySQL)
CREATE DATABASE IF NOT EXISTS todocamisetas_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Importar la estructura y datos desde el archivo SQL proporcionado
```

### 3. Configurar la conexión
El archivo `config/database.php` está preconfigurado para XAMPP local:
```php
private $host = 'localhost';
private $database = 'todocamisetas_api';
private $username = 'root';
private $password = '';
```

### 4. Verificar permisos
```bash
# Asegurar que Apache tenga permisos de lectura
chmod -R 755 todocamisetas-api/
```

## 🌐 URL Base

```
http://localhost/todocamisetas-api
```

## 📚 Endpoints

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET    | /api/camisetas | Lista todas las camisetas con tallas |
| GET    | /api/camisetas/{id} | Obtiene una camiseta específica |
| GET    | /api/camisetas/{id}/precio/{cliente_id} | Calcula el precio final para un cliente |
| POST   | /api/camisetas | Crea una nueva camiseta |
| PUT    | /api/camisetas/{id} | Actualiza una camiseta |
| DELETE | /api/camisetas/{id} | Elimina una camiseta |
| GET    | /api/camisetas/{id}/tallas | Obtiene tallas de una camiseta |
| POST   | /api/camisetas/{id}/tallas | Asigna talla a camiseta |
| PUT    | /api/camisetas/{camiseta_id}/tallas/{talla_id} | Actualiza el stock de una talla en una camiseta |
| DELETE | /api/camisetas/{camiseta_id}/tallas/{talla_id} | Remueve la asignación de una talla a una camiseta |
| GET    | /api/clientes | Lista todos los clientes |
| GET    | /api/clientes/{id} | Obtiene un cliente específico |
| POST   | /api/clientes | Crea un nuevo cliente |
| PUT    | /api/clientes/{id} | Actualiza un cliente |
| DELETE | /api/clientes/{id} | Elimina un cliente |
| GET    | /api/clientes/{id}/camisetas | Lista camisetas disponibles para un cliente (con precios calculados) |
| GET    | /api/tallas | Lista todas las tallas |
| GET    | /api/tallas/{id} | Obtiene una talla específica |
| POST   | /api/tallas | Crea una nueva talla |
| PUT    | /api/tallas/{id} | Actualiza una talla |
| DELETE | /api/tallas/{id} | Elimina una talla |

## 🎯 Ejemplos de Uso

### Obtener precio final calculado
```bash
GET /api/camisetas/1/precio/1
```

**Respuesta:**
```json
{
  "precio_final": 32300.00
}
```

### Crear una camiseta
```bash
POST /api/camisetas
Content-Type: application/json

{
  "titulo": "Camiseta Local 2025 – Selección Chilena",
  "club": "Selección Chilena",
  "pais": "Chile",
  "tipo": "Local",
  "color": "Rojo y Azul",
  "precio": 45000.00,
  "precio_oferta": 38000.00,
  "detalles": "Edición aniversario 2025",
  "codigo_producto": "SCL2025L"
}
```

### Crear un cliente
```bash
POST /api/clientes
Content-Type: application/json

{
  "nombre_comercial": "90minutos",
  "rut": "12345678-9",
  "direccion": "Providencia, Santiago",
  "categoria": "Preferencial",
  "contacto_nombre": "Juan Pérez",
  "contacto_email": "juan@90minutos.cl",
  "porcentaje_oferta": 15.00
}
```

## 🔧 Lógica de Negocio

### Cálculo de Precio Final
```
1. Si cliente.categoria = "Preferencial" Y camiseta.precio_oferta IS NOT NULL:
   precio_base = camiseta.precio_oferta
2. Sino:
   precio_base = camiseta.precio
3. Aplicar descuento del cliente:
   precio_final = precio_base * (1 - cliente.porcentaje_oferta/100)
```

### Validaciones de Integridad
- **Clientes**: No se pueden eliminar si tienen camisetas asociadas
- **Tallas**: No se pueden eliminar si están asignadas a camisetas
- **RUT**: Validación de formato y dígito verificador chileno
- **Códigos únicos**: Verificación de duplicados

## 📋 Códigos de Estado HTTP

| Código | Descripción |
|--------|-------------|
| `200` | OK - Operación exitosa |
| `201` | Created - Recurso creado |
| `400` | Bad Request - Datos inválidos |
| `404` | Not Found - Recurso no encontrado |
| `409` | Conflict - Violación de integridad |
| `429` | Too Many Requests - Rate limiting |
| `500` | Internal Server Error - Error del servidor |

## 🔒 Cabeceras de Respuesta

Todas las respuestas incluyen:
```
Content-Type: application/json
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 99
X-RateLimit-Reset: 60
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
Access-Control-Allow-Headers: Content-Type, Authorization
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
```

## 📖 Documentación Adicional

- **Swagger**: `docs/swagger.yaml`
- **Postman**: `docs/TodoCamisetas_API.postman_collection.json`

## 🐛 Debugging

### Logs de Error
Los errores se registran en el log de Apache:
```bash
tail -f /path/to/xampp/apache/logs/error.log
```

### Modo Desarrollo
Para ver errores detallados, verificar en `config/config.php`:
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```