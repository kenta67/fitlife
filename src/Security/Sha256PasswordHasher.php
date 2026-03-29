<?php

namespace App\Security;

use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

/**
 * Hasher personalizado para contraseñas con SHA2(256).
 * 
 * Este hasher es compatible con contraseñas que fueron hasheadas
 * utilizando SHA2(256) directamente en la base de datos.
 */
class Sha256PasswordHasher implements PasswordHasherInterface
{
    /**
     * Hashea una contraseña en texto plano usando SHA2(256).
     * 
     * @param string $plainPassword Contraseña en texto plano
     * @param string|null $salt Ignorado (no se usa con SHA2 directo)
     * @return string Contraseña hasheada en hexadecimal
     */
    public function hash(string $plainPassword, ?string $salt = null): string
    {
        if (empty($plainPassword)) {
            throw new InvalidPasswordException('La contraseña no puede estar vacía.');
        }

        return hash('sha256', $plainPassword);
    }

    /**
     * Verifica si una contraseña en texto plano coincide con el hash almacenado.
     * 
     * @param string $hashedPassword Hash almacenado en BD (en hexadecimal)
     * @param string $plainPassword Contraseña ingresada en el login
     * @param string|null $salt Ignorado
     * @return bool true si coinciden, false si no
     */
    public function verify(string $hashedPassword, string $plainPassword, ?string $salt = null): bool
    {
        if (empty($plainPassword)) {
            return false;
        }

        // Hashea la contraseña ingresada y la compara
        $computedHash = hash('sha256', $plainPassword);

        // Comparación segura contra timing attacks
        return hash_equals($hashedPassword, $computedHash);
    }

    /**
     * Indica si el hash debe ser actualizado.
     * No necesitamos rehashear con SHA2, así que siempre retorna false.
     * 
     * @param string $hashedPassword Hash almacenado
     * @return bool false (nunca rehashear)
     */
    public function needsRehash(string $hashedPassword): bool
    {
        // SHA2(256) no necesita rehashing automático
        return false;
    }
}
