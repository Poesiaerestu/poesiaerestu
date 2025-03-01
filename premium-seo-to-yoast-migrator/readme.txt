=== Premium SEO to Yoast Migrator ===
Contributors: tunombre
Donate link: https://tudominio.com/donar
Tags: seo, migration, yoast, premium seo pack
Requires at least: 5.6
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Migra todos los datos SEO de Premium SEO Pack a Yoast SEO de forma segura y eficiente.

== Descripción ==

Un plugin profesional para migrar metadatos SEO entre los plugins Premium SEO Pack y Yoast SEO en WordPress. Características principales:

* Migración completa de títulos SEO, metadescripciones y palabras clave
* Soporte para datos serializados de versiones antiguas de Premium SEO Pack
* Sistema de rollback integrado
* Interfaz de usuario intuitiva con progreso en tiempo real
* Compatible con posts, páginas y Custom Post Types

== Instalación ==

1. Sube el archivo ZIP del plugin a través de 'Plugins > Añadir Nuevo'
2. Activa el plugin a través del menú de Plugins
3. Ve a 'SEO Migrator' en el menú administrativo
4. Haz clic en 'Iniciar Migración' y espera a que complete el proceso
5. Verifica los resultados en el frontend y herramientas SEO

== Capturas de pantalla ==

1. Interfaz principal de migración
2. Progreso de la migración en tiempo real
3. Reporte post-migración con estadísticas

== Preguntas frecuentes ==

= ¿Qué sucede con los datos originales de Premium SEO Pack? =
Los datos originales se mantienen hasta confirmar la migración exitosa. Puedes eliminarlos manualmente después de verificar.

= ¿Es compatible con WooCommerce? =
Sí, migra productos y taxonomías de WooCommerce automáticamente.

= ¿Cómo maneja datos corruptos? =
Registra errores detallados en el log de depuración y permite reintentar migraciones parciales.

= ¿Puedo migrar solo contenido específico? =
Sí, usa los parámetros WP-CLI para filtrar por post_type o rango de IDs.

== Changelog ==

= 1.0.2 - 01/03/2025 =
* Corrección de conflicto con PHP 8.2
* Mejoras en el sistema de logging

= 1.0.1 - 28/02/2025 =
* Primera versión estable pública
* Soporte básico de migración

== Soporte técnico ==

Soporte prioritario disponible en [soporte@tudominio.com](mailto:soporte@tudominio.com) o visita nuestro [centro de ayuda](https://ayuda.tudominio.com).

== Contribuir ==

¿Quieres contribuir al desarrollo? Visita nuestro [repositorio GitHub](https://github.com/tu-usuario/premium-seo-migrator).

== Aviso legal ==

Este plugin no está afiliado a Yoast SEO ni Premium SEO Pack. Realiza siempre backups completos antes de migraciones.