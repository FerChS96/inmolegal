# 🏢 InmoLegal - Sistema de Contratos de Arrendamiento<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>



Sistema completo para la generación automatizada de contratos de arrendamiento con integración de pagos via Clip y generación de PDFs.<p align="center">

<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>

---<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>

<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>

## 📋 Características Principales<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>

</p>

### ✅ Formulario Web

- 32 campos estructurados (nombres, direcciones separadas)## About Laravel

- Integración con API de Zippopotam para códigos postales

- Validación en tiempo realLaravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- Diseño responsive y moderno

- Soporte para fiador obligatorio y opcional- [Simple, fast routing engine](https://laravel.com/docs/routing).

- [Powerful dependency injection container](https://laravel.com/docs/container).

### ✅ Pagos con Clip- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.

- Integración con Clip Payment Gateway v2- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).

- Entorno sandbox para testing- Database agnostic [schema migrations](https://laravel.com/docs/migrations).

- Webhook automático para actualización de estados- [Robust background job processing](https://laravel.com/docs/queues).

- Redirecciones configurables (success/error/cancel)- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

- Soporte para tarjetas de prueba

Laravel is accessible, powerful, and provides tools required for large, robust applications.

### ✅ Generación de PDFs

- Recibo de pago con diseño compacto## Learning Laravel

- Contrato legal completo con cláusulas

- Generación on-demand (sin almacenamiento en servidor)Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

- Conversión de montos a texto en español

- Descarga automática tras pago exitosoYou may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.



### ✅ Panel de AdministraciónIf you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

- Login con contraseña única

- Dashboard con estadísticas## Laravel Sponsors

- Grid de contratos con filtros

- Grid de pagos con búsquedaWe would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

- Vista detallada de cada contrato

- Descarga de PDFs desde admin### Premium Partners



### ✅ Widget Embebible- **[Vehikl](https://vehikl.com/)**

- JavaScript standalone para iframe- **[Tighten Co.](https://tighten.co)**

- Integración en cualquier sitio web- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**

- Responsive y personalizable- **[64 Robots](https://64robots.com)**

- Loader animado- **[Cubet Techno Labs](https://cubettech.com)**

- Callbacks configurables- **[Cyber-Duck](https://cyber-duck.co.uk)**

- **[Many](https://www.many.co.uk)**

---- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**

- **[DevSquad](https://devsquad.com)**

## 🌐 URLs del Sistema- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**

- **[OP.GG](https://op.gg)**

### Frontend- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**

- **Formulario**: `/contrato`- **[Lendio](https://lendio.com)**

- **Éxito**: `/clip/success/{token}`

## Contributing

### PDFs

- **Recibo**: `/pdf/recibo/{token}`Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

- **Contrato**: `/pdf/contrato/{token}`

## Code of Conduct

### Admin

- **Login**: `/admin/login` (Password: ver .env)In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

- **Panel**: `/admin`

- **Contratos**: `/admin/contratos`## Security Vulnerabilities

- **Pagos**: `/admin/pagos`

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

### Widget

- **Script JS**: `/inmolegal-widget.js`## License

- **Ejemplos**: `/widget-examples.html`

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

### Webhook
- **Clip**: `/webhook/clip` (POST)

---

## 🔧 Uso del Widget

### Integración Básica

```html
<!-- En tu sitio web -->
<div id="inmolegal-form"></div>
<script src="https://tu-dominio.com/inmolegal-widget.js"></script>
```

### Integración Personalizada

```html
<div id="mi-formulario"></div>
<script src="https://tu-dominio.com/inmolegal-widget.js"></script>
<script>
  InmoLegalWidget.init({
    containerId: 'mi-formulario',
    height: '800px',
    onLoad: function(iframe) {
      console.log('Formulario cargado');
    }
  });
</script>
```

Ver más ejemplos en: `/widget-examples.html`

---

## 💳 Tarjetas de Prueba (Clip Sandbox)

| Banco | Número | CVV | Exp |
|-------|--------|-----|-----|
| Banamex | 4766944332216006 | 123 | 12/26 |
| BBVA | 4555128482797669 | 123 | 12/26 |
| Santander | 5177136199824515 | 123 | 12/26 |

---

## 🚀 Deployment en IIS

Ver guía completa en: **IIS-SETUP.md**

Pasos principales:
1. Habilitar IIS con URL Rewrite
2. Configurar PHP FastCGI
3. Copiar proyecto a `C:\inetpub\wwwroot\inmolegal`
4. Configurar permisos en `storage` y `bootstrap/cache`
5. Crear sitio apuntando a carpeta `public`
6. Configurar SSL/HTTPS

---

## 📝 Workflow Completo

1. Usuario llena formulario
2. Se crea contrato con token único
3. Redirección a Clip para pago
4. Webhook actualiza estado automáticamente
5. Generación y descarga de PDFs
6. Admin puede consultar todo en panel

---

## 📄 Documentación Adicional

- **IIS-SETUP.md** - Guía completa de configuración IIS
- **WEBHOOK-SETUP.md** - Configuración webhook de Clip
- **widget-examples.html** - Ejemplos de integración del widget

---

**Versión**: 1.0.0  
**Última actualización**: Noviembre 2025
