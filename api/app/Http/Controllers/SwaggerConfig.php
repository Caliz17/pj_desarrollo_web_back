<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Título de tu API",
 *     version="1.0.0",
 *     description="Descripción de tu API",
 *     @OA\Contact(
 *         name="Nombre del Contacto",
 *         email="contacto@ejemplo.com"
 *     ),
 *     @OA\License(
 *         name="Licencia",
 *         url="http://www.ejemplo.com/licencia"
 *     )
 * )
 */
class SwaggerConfig
{
    // Este archivo puede estar vacío, la anotación @OA\Info es lo que importa.
}
