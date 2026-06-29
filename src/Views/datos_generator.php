<?php
// Simulación de datos para previsualización en caso de que no vengan del backend
$config_demo = [
    "fields" => [
        "photo-src" => "https://via.placeholder.com/150", // Reemplazar por la ruta real de la foto
        "f-logo" => "logo.png", // Nombre de tu archivo de logo
        "f-id" => "GUSA-2026-089",
        "f-nombre" => "Juan Pérez Gómez",
        "f-puesto" => "Ingeniero de Software Senior",
        "f-tel" => "+52 (668) 123-4567",
        "f-depto-f" => "Sistemas y TI",
        "f-correo" => "juan.perez@gusa.com",
        "f-centro" => "Planta Central Los Mochis",
    ]
];

// Si existe una configuración real desde tu controlador, la usamos, si no, la demo
$datos = isset($config) ? $config['fields'] : $config_demo['fields'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Empleado - GUSA</title>
    <style>
        /* === CONFIGURACIÓN GENERAL === */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #0f0f1a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        /* === CONTENEDOR PRINCIPAL TARJETA === */
        .profile-container {
            background: #16213e;
            width: 100%;
            max-width: 450px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            border: 1px solid #1e3a6e;
            overflow: hidden;
        }

        /* === ENCABEZADO (LOGO) === */
        .profile-header {
            background: #ffffff;
            padding: 20px;
            text-align: center;
            border-bottom: 4px solid #CC1111;
            position: relative;
        }
        .profile-header::after {
            content: '';
            position: absolute;
            bottom: -4px;
            right: 40px;
            width: 30px;
            height: 4px;
            background: #4A90D9;
        }
        .logo-img {
            max-height: 50px;
            max-width: 80%;
            object-fit: contain;
        }

        /* === BLOQUE DE FOTO Y NOMBRE PRINCIPAL === */
        .profile-hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px 20px 20px;
            background: linear-gradient(to bottom, #16213e, #0f1829);
            text-align: center;
        }
        .photo-wrapper {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #CC1111;
            box-shadow: 0 8px 16px rgba(0,0,0,0.4);
            background: #e2e8f0;
            margin-bottom: 15px;
        }
        .photo-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .employee-name {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .employee-post {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* === GRILLA DE INFORMACIÓN DE DETALLE === */
        .profile-body {
            padding: 24px;
            background: #0f1829;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .info-section-title {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            border-bottom: 1px solid #1e3a6e;
            padding-bottom: 6px;
            margin-top: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
        }
        /* Responsivo de dos columnas para pantallas un poco más anchas */
        @media(min-width: 400px) {
            .info-grid-2col {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 14px;
            }
        }
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .info-label {
            font-size: 11px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 13.5px;
            color: #e2e8f0;
            font-weight: 500;
        }
        /* Resaltado especial para el ID de Empleado */
        .info-value.highlight {
            color: #ff4d4d;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .info-value.empty {
            color: #475569;
            font-style: italic;
        }

        /* === PIE DE PÁGINA DE LA TARJETA === */
        .profile-footer {
            background: #1a1a1a;
            padding: 12px;
            text-align: center;
            font-size: 10px;
            color: #64748b;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <div class="profile-header">
            <?php if (!empty($datos['f-logo'])): ?>
                <img class="logo-img" src="<?= !empty($datos['f-logo']) ? "images/logo/".$datos['f-logo'] : '' ?>" alt="Logo Empresa">
            <?php else: ?>
                <div style="color:#000; font-weight:bold; font-size:14px;">GUSA CORPORATIVO</div>
            <?php endif; ?>
        </div>

        <div class="profile-hero">
            <div class="photo-wrapper">
                <img src="<?= !empty($datos['photo-src']) ? $datos['photo-src'] : 'https://via.placeholder.com/150' ?>" alt="Foto Empleado">
            </div>
            <div class="employee-name"><?= htmlspecialchars($datos['f-nombre'] ?? 'SÍN NOMBRE') ?></div>
            <div class="employee-post"><?= htmlspecialchars($datos['f-puesto'] ?? 'Puesto No Asignado') ?></div>
        </div>

        <div class="profile-body">
            
            <div class="info-section-title">Datos Laborales y de Contacto</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Número de Empleado</span>
                    <span class="info-value highlight"><?= htmlspecialchars($datos['f-id'] ?? '--') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Departamento</span>
                    <span class="info-value"><?= htmlspecialchars($datos['f-depto-f'] ?? '--') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Centro de Trabajo</span>
                    <span class="info-value"><?= htmlspecialchars($datos['f-centro'] ?? '--') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Teléfono de Contacto</span>
                    <span class="info-value"><?= htmlspecialchars($datos['f-tel'] ?? '--') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Correo Electrónico</span>
                    <span class="info-value" style="word-break: break-all;"><?= htmlspecialchars($datos['f-correo'] ?? '--') ?></span>
                </div>
            </div>

            <div class="info-section-title">Información Adicional (Pendiente)</div>
            <div class="info-grid-2col">
                <div class="info-item">
                    <span class="info-label">Tipo de Sangre</span>
                    <span class="info-value empty">--</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Num. Afiliación</span>
                    <span class="info-value empty">--</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fecha Nacimiento</span>
                    <span class="info-value empty">--</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Estatus</span>
                    <span class="info-value empty">--</span>
                </div>
            </div>

        </div>

        <div class="profile-footer">
            Sistema de Identificación Corporativa v8
        </div>
    </div>

</body>
</html>