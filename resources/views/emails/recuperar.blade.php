<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔑 Restablecer Contraseña - Ninja Park</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .header {
            background-color: #4c1d95; /* Morado corporativo */
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            margin: 0;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .content {
            padding: 40px 30px;
            color: #1f2937;
            line-height: 1.6;
        }
        .content h2 {
            font-size: 20px;
            margin-top: 0;
            color: #111827;
        }
        .btn-container {
            text-align: center;
            margin: 35px 0;
        }
        .btn-ninja {
            background-color: #f59e0b; /* Amarillo/naranja corporativo */
            color: #ffffff !important;
            padding: 14px 30px;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
            transition: all 0.2s ease-in-out;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #f3f4f6;
        }
        .text-warning {
            font-size: 13px;
            color: #b45309;
            background-color: #fffbeb;
            border: 1px solid #fef3c7;
            padding: 12px;
            border-radius: 8px;
            margin-top: 25px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <h1>NINJA PARK KIDS</h1>
        </div>

        <!-- Contenido -->
        <div class="content">
            <h2>¡Hola, {{ $nombre }}!</h2>
            <p>Hemos recibido una solicitud para restablecer la contraseña de acceso a tu cuenta en el **Portal Staff de Ninja Park**.</p>
            <p>Para continuar con el proceso, haz clic en el siguiente botón seguro de recuperación:</p>
            
            <div class="btn-container">
                <a href="{{ $link }}" class="btn-ninja" target="_blank">🔑 Restablecer Contraseña</a>
            </div>

            <p>Si el botón de arriba no funciona, puedes copiar y pegar la siguiente dirección URL directamente en tu navegador:</p>
            <p style="word-break: break-all; font-size: 13px; color: #4c1d95; background: #f3f4f6; padding: 10px; border-radius: 8px;">
                {{ $link }}
            </p>

            <div class="text-warning">
                <strong>⚠️ Importante:</strong> Este enlace de recuperación es de un solo uso y expirará automáticamente en <strong>60 minutos</strong> por motivos de seguridad. Si tú no realizaste esta solicitud, puedes ignorar este correo de forma segura y tu contraseña actual seguirá funcionando normalmente.
            </div>
        </div>

        <!-- Pie de página -->
        <div class="footer">
            &copy; {{ date('Y') }} Ninja Park Kids. Todos los derechos reservados.<br>
            Este es un correo automático del sistema, por favor no respondas a esta dirección.
        </div>
    </div>
</body>
</html>
