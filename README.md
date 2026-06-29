# CodeIgniter 4 Boilerplate Compac

[![Versión Estable](https://img.shields.io/packagist/v/julio101290/boilerplatecompac?style=flat-square)](https://packagist.org/packages/julio101290/boilerplatecompac)
[![Descargas Totales](https://img.shields.io/packagist/dt/julio101290/boilerplatecompac?style=flat-square)](https://packagist.org/packages/julio101290/boilerplatecompac)
[![Licencia](https://img.shields.io/packagist/l/julio101290/boilerplatecompac?style=flat-square)](https://packagist.org/packages/julio101290/boilerplatecompac)
[![PHP Soportado](https://img.shields.io/packagist/dependency-v/julio101290/boilerplatecompac/php?style=flat-square)](https://packagist.org/packages/julio101290/boilerplatecompac)

## 📦 Descripción

**Boilerplate Compac** es un paquete para **CodeIgniter 4** que proporciona una base sólida para integrar sistemas de nóminas con **CONTPAQi Nóminas (Nomipac)**. Facilita la gestión de empleados, percepciones, deducciones, registros de incidencias y el envío de timbrados, todo mientras mantiene un registro detallado de cambios en cada módulo mediante un sistema de logging integrado.

Está pensado para acelerar el desarrollo de aplicaciones empresariales que requieran comunicación con el entorno fiscal mexicano y procesos de nómina complejos.

---

## 🚀 Requisitos

- PHP >= 7.4 (o la versión soportada por CodeIgniter 4)
- CodeIgniter 4.1.0 o superior
- Composer
- Base de datos MySQL/MariaDB (u otros motores soportados por CI4)

---

## 📥 Instalación

### 1. Instalar vía Composer

Ejecuta el siguiente comando:

composer require julio101290/boilerplatecompac

### 2. Ejecutar el instalador (migraciones y seeders)

El paquete incluye un comando que corre migraciones y datos iniciales automáticamente:

php spark boilerplatecompac:installcompac

Este comando:
- Crea las tablas necesarias para empleados, nóminas, incidencias, logs, etc.
- Inserta los datos de prueba (si existen) para comenzar rápidamente.

> **Nota:** Asegúrate de haber configurado correctamente tu base de datos en el archivo `.env` antes de ejecutar este paso.

---

## 🧩 Integración con el Menú

Una vez instalado, el paquete agrega entradas al menú lateral de tu panel de administración (si usas el layout por defecto de CodeIgniter 4). Podrás acceder directamente a:

- **Dashboard de Nóminas**
- **Gestión de Empleados**
- **Percepciones y Deducciones**
- **Incidencias**
- **Timbrado CFDI**
- **Bitácora de cambios (logs)**

---

## 🖥️ Capturas de pantalla

A continuación, algunas vistas del módulo:

![Vista principal](https://github.com/user-attachments/assets/8d22ee9b-08af-4efa-a058-a9075726e8eb)

![Lista de empleados](https://github.com/user-attachments/assets/ddfd18da-7bb2-4b26-9fff-404c7f40b396)

![Formulario de captura](https://github.com/user-attachments/assets/f6090b5c-92ea-4b75-ab8b-011aff4d2b88)

![Dashboard](https://github.com/user-attachments/assets/e899f564-7592-40e8-baa8-c17ced4c0ec7)

![Reporte de nómina](https://github.com/user-attachments/assets/dfa8ca98-6a03-4cdc-bbe5-11c724c3cfb2)

![Log de cambios](https://github.com/user-attachments/assets/16b0b82a-2097-4bd4-a353-04489e523147)

![Configuración de Nomipac](https://github.com/user-attachments/assets/841a5ec6-f468-455d-917f-4e97b196f8b4)

---

## ⚙️ Uso y Personalización

El paquete proporciona controladores, modelos, vistas y rutas listas para usar. Puedes extender o modificar cualquier componente según las necesidades de tu proyecto.

### 🔍 Estructura de rutas

Las rutas principales se registran automáticamente en `app/Config/Routes.php` a través del archivo de configuración del paquete. Para revisarlas, puedes inspeccionar el archivo `vendor/julio101290/boilerplatecompac/src/Config/Routes.php`.

### 📄 Logs de actividad

Todos los cambios (altas, bajas, modificaciones) en los módulos principales quedan registrados en la tabla `bitacora`. Puedes consultar el historial desde la interfaz o mediante consultas directas.

### 🧪 Datos de prueba

El seeder incluye datos de muestra para que puedas probar la funcionalidad sin tener que crear todo desde cero. Puedes desactivar los seeders modificando el comando de instalación o ejecutándolos por separado.

### 🔧 Configuración avanzada

Si necesitas ajustar parámetros como la conexión a Nomipac o los umbrales de log, publica el archivo de configuración con:

php spark boilerplatecompac:publish-config

Esto copiará `boilerplatecompac.php` a tu carpeta `app/Config/`, donde podrás editarlo libremente.

---

## 📚 Documentación Adicional

Para conocer más sobre la integración con CONTPAQi Nóminas, consulta la [documentación oficial de Nomipac](https://www.conTPAQi.com/). Si encuentras algún error o quieres proponer mejoras, revisa la sección de Contribución.

---

## 📋 Registro de Cambios

Todos los cambios importantes en cada versión se documentan en el archivo [CHANGELOG.md](CHANGELOG.md).

---

## 🤝 Cómo Contribuir

Las contribuciones son bienvenidas. Si deseas reportar un bug, solicitar una nueva funcionalidad o enviar un pull request, por favor:

1. Abre un [issue](https://github.com/julio101290/boilerplatecompac/issues) describiendo el problema o mejora.
2. Si envías código, asegúrate de seguir el estándar de codificación de CodeIgniter 4 y de agregar pruebas cuando sea posible.

---

## 📄 Licencia

Este paquete es software libre y se distribuye bajo los términos de la [Licencia MIT](LICENSE.md).

---

**¡Feliz codificación!** 🚀
