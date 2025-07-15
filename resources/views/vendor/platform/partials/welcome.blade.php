<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Estética Aesthetic</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #ffffff;
            color: #333333;
            line-height: 1.6;
        }
        
        .header-bar {
            background: #f8f9fa;
            padding: 20px 40px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 16px;
        }
        
        .header-icon:hover {
            background: #dee2e6;
            transform: scale(1.1);
        }
        
        .content-area {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .welcome-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 16px;
            padding: 50px;
            margin-bottom: 50px;
            border: 1px solid #e9ecef;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(0, 122, 204, 0.1) 0%, transparent 70%);
            animation: float 8s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .welcome-title {
            color: #007acc;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .welcome-subtitle {
            color: #6c757d;
            font-size: 18px;
            line-height: 1.8;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 20px;
        }
        
        .feature-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            padding: 35px;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            border-color: #007acc;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #007acc, #0056b3);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .feature-card:hover::before {
            transform: scaleX(1);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            background: #f8f9fa;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 25px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            background: #007acc;
            border-color: #007acc;
            color: white;
            transform: scale(1.1);
        }
        
        .feature-title {
            color: #333333;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 18px;
            transition: color 0.3s ease;
        }
        
        .feature-card:hover .feature-title {
            color: #007acc;
        }
        
        .feature-description {
            color: #6c757d;
            font-size: 15px;
            line-height: 1.7;
        }
        
        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #007acc;
            margin-bottom: 8px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 14px;
            font-weight: 500;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
        }
        
        .action-btn {
            background: #007acc;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .action-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 122, 204, 0.3);
        }
        
        .action-btn.secondary {
            background: #6c757d;
        }
        
        .action-btn.secondary:hover {
            background: #5a6268;
        }
        
        @media (max-width: 768px) {
            .content-area {
                padding: 20px;
            }
            
            .welcome-section {
                padding: 30px 20px;
            }
            
            .welcome-title {
                font-size: 24px;
            }
            
            .welcome-subtitle {
                font-size: 16px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .feature-card {
                padding: 25px;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>    
    <div class="content-area">
        <div class="welcome-section">
            <h1 class="welcome-title">¡Bienvenido a tu Centro de Estética Aesthetic!</h1>
            <p class="welcome-subtitle">
                Administra tu centro de belleza con facilidad y elegancia. Gestiona citas, servicios y 
                clientes desde un solo lugar. Tu camino hacia la excelencia en belleza comienza aquí.
            </p>
        </div>     
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3 class="feature-title">Gestión de Citas</h3>
                <p class="feature-description">
                    Administra fácilmente las citas de tus clientes. Programa, reprograma y cancela citas con unos 
                    pocos clics. Visualiza tu agenda diaria, semanal o mensual para optimizar tu tiempo y recursos.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">👥</div>
                <h3 class="feature-title">Gestión de Clientes</h3>
                <p class="feature-description">
                    Mantén un registro detallado de tus clientes. Historial de tratamientos, preferencias personales y 
                    datos de contacto siempre a tu alcance para ofrecer un servicio personalizado y de calidad.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">✨</div>
                <h3 class="feature-title">Catálogo de Servicios</h3>
                <p class="feature-description">
                    Gestiona tu catálogo de servicios de belleza y estética. Actualiza precios, duración y descripción de 
                    cada servicio. Organiza por categorías para facilitar la búsqueda y reserva por parte de tus clientes.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3 class="feature-title">Estadísticas y Reportes</h3>
                <p class="feature-description">
                    Analiza el rendimiento de tu centro de estética con informes detallados. Visualiza los servicios más solicitados, 
                    ingresos por período y fidelización de clientes para tomar decisiones estratégicas basadas en datos reales.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔔</div>
                <h3 class="feature-title">Recordatorios Automáticos</h3>
                <p class="feature-description">
                    Reduce las insistencias con recordatorios automáticos de citas. Envía notificaciones a tus clientes para 
                    confirmar su asistencia y mantenlos informados sobre promociones especiales y nuevos servicios.
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⚙️</div>
                <h3 class="feature-title">Configuración Personalizada</h3>
                <p class="feature-description">
                    Adapta el sistema a las necesidades específicas de tu centro de estética. Configura horarios de atención, días 
                    festivos, profesionales disponibles y mucho más para que la plataforma funcione exactamente como necesitas.
                </p>
            </div>
        </div>     
    <script>
        // Efectos hover para tarjetas
        document.querySelectorAll('.feature-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
        
        // Efectos para botones de acción
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'translateY(-2px)';
                }, 150);
            });
        });
        
        // Animación para las estadísticas
        document.querySelectorAll('.stat-card').forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    </script>
</body>
</html>