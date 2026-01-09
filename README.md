# Visits Application – Bex Soluciones

Este proyecto es una aplicación web desarrollada como prueba técnica para **Bex Soluciones**.  
Permite registrar y visualizar visitas geolocalizadas mediante una API REST construida con Laravel y una interfaz frontend con Vue 3 y Leaflet.

> **Nota importante:** Este proyecto fue desarrollado utilizando **Laragon** como entorno de desarrollo local. Las URLs proporcionadas por Laragon siguen el formato `http://visits-app.test/`.

---

## 🛠️ Stack tecnológico

### Backend
- **Laravel 10**
- **PHP 8.1**
- **Laravel Sanctum** (autenticación vía tokens)
- **MySQL**

### Frontend
- **Vue 3** (Composition API)
- **Vite**
- **Leaflet** (mapas)
- **Tailwind CSS v4**

---

## 📌 Funcionalidades principales

### API REST
- Autenticación mediante token (Sanctum)
- CRUD completo de visitas
- Validación de datos en backend
- Respuestas en formato JSON
- Protección de endpoints sensibles

### Frontend
- Dashboard con identidad visual de **Bex Soluciones**
- Mapa interactivo con marcadores geográficos
- Listado de visitas con buscador
- Sincronización entre lista y mapa
- Interfaz responsive

### CLI (Artisan)
- Comando para crear visitas desde consola usando **Laravel Prompts**

---

## 🗂️ Estructura del proyecto

app/
└── Http/Controllers/Api
└── Models/Visit.php
database/
└── migrations/
resources/
└── js/
│ └── components/
│ └── VisitsDashboard.vue
└── views/
│ └── visits-map.blade.php
postman/
└── Visits API.postman_collection.json
public/
└── brand/
    └── LogoBexSoluciones.svg
    └── IconBexSoluciones.svg

---

## 🚀 Instalación y ejecución

### 1. Clonar el repositorio
```bash
git clone https://github.com/Dacco0/Visits-app.git
cd visits-app
```

### 2. Instalar dependencias backend
```bash
composer install
```

### 3. Configurar entorno
```bash
cp .env.example .env
php artisan key:generate
```

Edita el archivo `.env` y configura tu conexión a la base de datos.

### 4. Migraciones
```bash
php artisan migrate
```

### 5. Crear usuario administrador

Es necesario crear un usuario administrador para poder autenticarse en la API. Utiliza Laravel Tinker para crear el usuario:

```bash
php artisan tinker
```

Una vez dentro de Tinker, ejecuta el siguiente código para crear el usuario admin:

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@test.com',
    'password' => \Illuminate\Support\Facades\Hash::make('password123')
]);
```

**JSON para Postman (Login):**
```json
{
  "email": "admin@test.com",
  "password": "password123"
}
```

Sal de Tinker escribiendo `exit` o presionando `Ctrl+C`.

### 6. Instalar dependencias frontend
```bash
npm install
npm run dev
```

**Nota:** Asegúrate de tener el servidor de desarrollo de Vite corriendo (`npm run dev`) mientras trabajas en el proyecto. Para producción usa `npm run build`.

---

## 🔐 Autenticación (Sanctum)

Para autenticarte, necesitas hacer login primero:

**POST** `/api/login`

**Body (JSON):**
```json
{
  "email": "user@example.com",
  "password": "password"
}
```

La respuesta incluye un `token` Bearer que debes incluir en el header `Authorization: Bearer {token}` para acceder a los endpoints protegidos.

## 📡 Endpoints principales

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| POST | `/api/login` | Login y obtener token | ❌ |
| GET | `/api/visits` | Listar todas las visitas | ❌ |
| GET | `/api/visits/{id}` | Ver una visita específica | ❌ |
| POST | `/api/visits` | Crear nueva visita | ✅ |
| PUT | `/api/visits/{id}` | Actualizar visita | ✅ |
| DELETE | `/api/visits/{id}` | Eliminar visita | ✅ |

Los endpoints marcados con ✅ requieren autenticación mediante token Bearer.

## 🧪 Pruebas con Postman

Incluí una colección de Postman lista para usar en la carpeta `postman/`. Para usarla:

1. Abre Postman e importa el archivo `Visits API.postman_collection.json`
2. **Configura la variable `base_url` en la colección con la URL de Laragon:**
   - Si estás usando Laragon, la URL base será: `http://visits-app.test`
   - Configura esta variable en la colección de Postman (Variables → base_url)
3. Ejecuta el request "Authentication Login" primero - automáticamente guarda el token en una variable
4. Ya puedes probar los demás endpoints, los que requieren auth usan el token automáticamente

**Importante:** Asegúrate de haber creado el usuario administrador con Tinker antes de intentar hacer login (ver sección "Crear usuario administrador").

La colección incluye todos los endpoints principales y el script de login guarda el token para que no tengas que copiarlo manualmente.

## 🗺️ Frontend – Mapa

Accede a la aplicación en `/visits-map`.

- El mapa usa Leaflet con tiles de OpenStreetMap
- Cada visita aparece como un marcador en el mapa
- Al hacer clic en una visita del listado:
  - El mapa hace zoom automático a esa ubicación
  - Se abre un popup con la información del visitante

El mapa y la lista están sincronizados, así que puedes interactuar desde cualquiera de los dos.

## 💻 Comando Artisan

Puedes crear visitas directamente desde la consola con:

```bash
php artisan app:create-visit
```

El comando te pedirá interactivamente:
- Nombre del visitante
- Email
- Latitud (coordenada geográfica)
- Longitud (coordenada geográfica)

Usa Laravel Prompts para una experiencia más amigable en consola. 

## 👤 Autor

**Daniel Cortés**  
Prueba técnica – Bex Soluciones