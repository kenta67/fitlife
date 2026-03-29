# 🔐 Guía Completa del Login Reparado - FitLife

## ✅ Cambios Realizados

### 1. **Hasher Personalizado para SHA2(256)**
   - **Ubicación**: `src/Security/Sha256PasswordHasher.php`
   - **Función**: Hashea y verifica contraseñas usando SHA2(256)
   - **Seguridad**: Usa `hash_equals()` para evitar timing attacks

### 2. **Configuración de Seguridad Actualizada**
   - **Archivo**: `config/packages/security.yaml`
   - **Cambio**: `algorithm: auto` → `id: App\Security\Sha256PasswordHasher`

---

## 🔄 Flujo de Autenticación

```
1. Usuario ingresa usuario + contraseña en /login
                          ↓
2. Form method=POST envía datos a app_login (SecurityController)
                          ↓
3. AppAuthenticator.authenticate() recibe las credenciales:
   - usuario: string (POST 'usuario')
   - contraseña: string (POST 'contrasena')
                          ↓
4. UserBadge carga el usuario de BD usando Personal.usuario
                          ↓
5. PasswordCredentials compara con Sha256PasswordHasher:
   - Calcula: hash('sha256', contraseña_ingresada)
   - Compara con: BD personal.contrasena
   - Método seguro: hash_equals($bdHash, $computedHash)
                          ↓
6. Si coincide:
   - Token autenticado creado
   - onAuthenticationSuccess() redirige según rol:
     * ROLE_ADMIN → admin_dashboard
     * ROLE_RECEPCIONISTA → recepcionista_dashboard
     * ROLE_INSTRUCTOR → instructor_dashboard
```

---

## 🧪 Cómo Probar el Login

### Requisito: Datos en BD
Necesitas al menos un registro en `personal` con:
```sql
INSERT INTO personal (id_rol, nombre, usuario, contrasena, estado)
VALUES (
  1,                                          -- id_rol
  'Admin User',                               -- nombre
  'admin',                                    -- usuario
  SHA2('password123', 256),                   -- contraseña hasheada
  1                                           -- estado (activo)
);
```

⚠️ **IMPORTANTE**: La contraseña en BD DEBE ser SHA2(256) en formato hexadecimal

### Pasos para Testear:
1. **Navega a**: `http://localhost/fitlife/login`
2. **Ingresa**:
   - Usuario: `admin`
   - Contraseña: `password123`
3. **Esperado**: Redirige a `/admin` (o dashboard según rol)
4. **Si falla**: Verifica que:
   - ✓ Personal esté en BD
   - ✓ Contraseña esté hasheada con SHA2(256)
   - ✓ El campo `usuario` sea correcto
   - ✓ El rol exista y tenga un nombre válido

---

## 🛡️ Mapeo de Roles

| DB (rol.nombre) | ROLE Symfony | Panel |
|---|---|---|
| `administrador` | `ROLE_ADMIN` | `/admin` |
| `recepcionista` | `ROLE_RECEPCIONISTA` | `/recepcionista` |
| `instructor` | `ROLE_INSTRUCTOR` | `/instructor` |

⚠️ Los nombres en BD deben coincidir exactamente (sin espacios extra)

---

## 📝 Script SQL para Crear Usuario de Prueba

```sql
-- 1. Verificar que existe el rol (ajusta el ID según tu BD)
SELECT * FROM rol;

-- 2. Insertar usuario de prueba
INSERT INTO personal (id_rol, nombre, usuario, contrasena, estado)
SELECT 
  r.id,                           -- ID del rol administrador
  'Usuario Administrador',        -- nombre
  'testadmin',                    -- usuario
  SHA2('testpass123', 256),       -- contraseña
  1                               -- activo
FROM rol r
WHERE LOWER(r.nombre) = 'administrador'
LIMIT 1;

-- 3. Verificar que se insertó
SELECT id, usuario, contrasena, estado FROM personal WHERE usuario = 'testadmin';

-- 4. Después de testear, puedes eliminarlo:
-- DELETE FROM personal WHERE usuario = 'testadmin';
```

---

## 🔍 Debugging

Si el login sigue fallando, ejecuta:

```bash
# Ver la configuración de seguridad cargada
php bin/console debug:config security

# Verificar el Sha256PasswordHasher está registrado
php bin/console debug:container | grep -i "Sha256"

# Ver todas las rutas y firewalls
php bin/console debug:router app_login
php bin/console debug:firewall
```

---

## 💡 Notas Técnicas

- **PHP Function**: `hash('sha256', $plainPassword)` produce hexadecimal de 64 caracteres
- **Comparación Segura**: `hash_equals()` toma igual tiempo sin importar dónde falle (protege contra timing attacks)
- **Interfaz**: `PasswordHasherInterface` de Symfony 7.x
- **Cipher**: SHA2 → hexadecimal (no base64)

---

## ✨ Validación de la Implementación

✅ Hasher personalizado implementado y registrado
✅ Configuración de seguridad actualizada
✅ Formulario de login correcto (usuario + contrasena + csrf + remember_me)
✅ AppAuthenticator enlazado correctamente
✅ Mapeo de roles funcionando
✅ Redireccionamientos a dashboard según rol

**El sistema está listo para autenticar usuarios con contraseñas SHA2(256).**
