/**
 * InmoLegal - Widget de Formulario de Contratos
 * Versión: 1.0.0
 * 
 * Este script permite embeber el formulario de contratos de InmoLegal
 * en cualquier sitio web mediante un iframe responsive.
 * 
 * Uso básico:
 * <div id="inmolegal-form"></div>
 * <script src="https://tu-dominio.com/inmolegal-widget.js"></script>
 * <script>
 *   InmoLegalWidget.init({
 *     containerId: 'inmolegal-form',
 *     height: '800px'
 *   });
 * </script>
 */

(function(window) {
    'use strict';

    const InmoLegalWidget = {
        version: '1.0.0',
        config: {
            // URL base del servidor (cambiar en producción)
            baseUrl: window.location.origin,
            // ID del contenedor por defecto
            containerId: 'inmolegal-form',
            // Altura del iframe
            height: 'auto',
            // Ancho del iframe
            width: '100%',
            // Bordes
            border: 'none',
            // Border radius
            borderRadius: '8px',
            // Box shadow
            boxShadow: '0 4px 6px rgba(0, 0, 0, 0.1)',
            // Permitir scroll
            scrolling: 'yes',
            // Clase CSS personalizada
            customClass: '',
            // Callback cuando se carga el iframe
            onLoad: null,
            // Callback cuando se envía el formulario
            onSubmit: null,
            // Mostrar loader mientras carga
            showLoader: true
        },

        /**
         * Inicializar el widget
         * @param {Object} options - Opciones de configuración
         */
        init: function(options) {
            // Combinar opciones con configuración por defecto
            this.config = Object.assign({}, this.config, options || {});

            // Validar que existe el contenedor
            const container = document.getElementById(this.config.containerId);
            if (!container) {
                console.error(`InmoLegal Widget: No se encontró el contenedor con ID "${this.config.containerId}"`);
                return false;
            }

            // Crear el iframe
            this.createIframe(container);

            // Configurar listener para mensajes del iframe
            this.setupMessageListener();

            return true;
        },

        /**
         * Crear el iframe y agregarlo al contenedor
         * @param {HTMLElement} container - Elemento contenedor
         */
        createIframe: function(container) {
            // Limpiar contenido existente
            container.innerHTML = '';

            // Crear loader si está habilitado
            if (this.config.showLoader) {
                const loader = this.createLoader();
                container.appendChild(loader);
            }

            // Crear wrapper para el iframe
            const wrapper = document.createElement('div');
            wrapper.style.cssText = `
                position: relative;
                width: ${this.config.width};
                min-height: ${this.config.height === 'auto' ? '600px' : this.config.height};
                border-radius: ${this.config.borderRadius};
                box-shadow: ${this.config.boxShadow};
                overflow: hidden;
                background: #f9fafb;
            `;

            // Crear iframe
            const iframe = document.createElement('iframe');
            iframe.id = 'inmolegal-iframe-' + Date.now();
            iframe.src = this.config.baseUrl + '/contrato';
            iframe.style.cssText = `
                width: 100%;
                height: ${this.config.height === 'auto' ? '600px' : this.config.height};
                border: ${this.config.border};
                display: block;
                transition: opacity 0.3s ease;
                opacity: 0;
            `;
            iframe.setAttribute('scrolling', this.config.scrolling);
            iframe.setAttribute('allowtransparency', 'true');

            // Aplicar clase personalizada si existe
            if (this.config.customClass) {
                wrapper.className = this.config.customClass;
            }

            // Evento cuando carga el iframe
            iframe.onload = () => {
                // Ocultar loader
                const loader = container.querySelector('.inmolegal-loader');
                if (loader) {
                    loader.style.opacity = '0';
                    setTimeout(() => loader.remove(), 300);
                }

                // Mostrar iframe con fade-in
                iframe.style.opacity = '1';

                // Ajustar altura automáticamente si está configurado
                if (this.config.height === 'auto') {
                    this.adjustHeight(iframe);
                }

                // Callback onLoad
                if (typeof this.config.onLoad === 'function') {
                    this.config.onLoad(iframe);
                }

                console.log('InmoLegal Widget: Formulario cargado exitosamente');
            };

            // Agregar iframe al wrapper
            wrapper.appendChild(iframe);

            // Agregar wrapper al contenedor
            container.appendChild(wrapper);

            // Guardar referencia al iframe
            this.iframe = iframe;
        },

        /**
         * Crear loader animado
         * @returns {HTMLElement} Elemento del loader
         */
        createLoader: function() {
            const loader = document.createElement('div');
            loader.className = 'inmolegal-loader';
            loader.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                z-index: 10;
                transition: opacity 0.3s ease;
            `;

            const spinner = document.createElement('div');
            spinner.style.cssText = `
                width: 50px;
                height: 50px;
                border: 4px solid rgba(255, 255, 255, 0.3);
                border-top-color: white;
                border-radius: 50%;
                animation: inmolegal-spin 1s linear infinite;
            `;

            const text = document.createElement('p');
            text.textContent = 'Cargando formulario...';
            text.style.cssText = `
                color: white;
                margin-top: 20px;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                font-size: 16px;
            `;

            // Agregar keyframes para la animación
            if (!document.getElementById('inmolegal-styles')) {
                const style = document.createElement('style');
                style.id = 'inmolegal-styles';
                style.textContent = `
                    @keyframes inmolegal-spin {
                        to { transform: rotate(360deg); }
                    }
                `;
                document.head.appendChild(style);
            }

            loader.appendChild(spinner);
            loader.appendChild(text);

            return loader;
        },

        /**
         * Ajustar altura del iframe automáticamente
         * @param {HTMLIFrameElement} iframe - Elemento iframe
         */
        adjustHeight: function(iframe) {
            try {
                // Intentar obtener la altura del contenido
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                const height = iframeDoc.body.scrollHeight;

                if (height > 0) {
                    iframe.style.height = height + 'px';
                }
            } catch (e) {
                // Cross-origin, no se puede acceder al contenido
                console.warn('InmoLegal Widget: No se puede ajustar altura automáticamente (cross-origin)');
            }

            // Escuchar cambios de tamaño
            window.addEventListener('resize', () => {
                try {
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    const height = iframeDoc.body.scrollHeight;
                    if (height > 0) {
                        iframe.style.height = height + 'px';
                    }
                } catch (e) {
                    // Ignorar errores cross-origin
                }
            });
        },

        /**
         * Configurar listener para mensajes del iframe
         */
        setupMessageListener: function() {
            window.addEventListener('message', (event) => {
                // Validar origen
                if (event.origin !== this.config.baseUrl) {
                    return;
                }

                // Procesar mensaje
                const data = event.data;

                if (data.type === 'inmolegal_form_submit') {
                    console.log('InmoLegal Widget: Formulario enviado', data);

                    // Callback onSubmit
                    if (typeof this.config.onSubmit === 'function') {
                        this.config.onSubmit(data.payload);
                    }
                }

                if (data.type === 'inmolegal_height_change') {
                    // Ajustar altura del iframe
                    if (this.iframe && data.height) {
                        this.iframe.style.height = data.height + 'px';
                    }
                }
            });
        },

        /**
         * Recargar el iframe
         */
        reload: function() {
            if (this.iframe) {
                this.iframe.src = this.iframe.src;
            }
        },

        /**
         * Destruir el widget
         */
        destroy: function() {
            const container = document.getElementById(this.config.containerId);
            if (container) {
                container.innerHTML = '';
            }
            this.iframe = null;
        }
    };

    // Exponer al scope global
    window.InmoLegalWidget = InmoLegalWidget;

    // Auto-inicializar si existe un contenedor con ID por defecto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            const defaultContainer = document.getElementById('inmolegal-form');
            if (defaultContainer && !defaultContainer.hasAttribute('data-manual-init')) {
                InmoLegalWidget.init();
            }
        });
    } else {
        const defaultContainer = document.getElementById('inmolegal-form');
        if (defaultContainer && !defaultContainer.hasAttribute('data-manual-init')) {
            InmoLegalWidget.init();
        }
    }

})(window);
