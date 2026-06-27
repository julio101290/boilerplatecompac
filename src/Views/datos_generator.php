<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Generador de Credencial</title>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

        <style>
            /* === RESET & CONFIGURACIÓN CORE === */
            *{
                box-sizing:border-box;
                margin:0;
                padding:0;
                border:none;
                outline:none;
            }
            body{
                font-family:Arial,sans-serif;
                background:#0f0f1a;
                min-height:100vh;
                display:flex;
                overflow:hidden;
                height:100vh;
            }

            /* PANEL IZQUIERDO DE DISEÑO */
            #panel{
                width:350px;
                background:#16213e;
                border-right:1px solid #1e3a6e;
                overflow-y:auto;
                flex-shrink:0;
                display:flex;
                flex-direction:column;
                box-shadow:4px 0 15px rgba(0,0,0,0.5);
                z-index:10;
            }
            .panel-header{
                background:#CC1111;
                padding:16px;
                color:#fff;
            }
            .panel-header h1{
                font-size:16px;
                font-weight:700;
                text-transform:uppercase;
                letter-spacing:0.5px;
            }
            .panel-header p{
                font-size:11px;
                color:rgba(255,255,255,0.8);
                margin-top:2px;
            }

            /* TABS */
            .tabs{
                display:flex;
                background:#0f1829;
                border-bottom:1px solid #1e3a6e;
            }
            .tab{
                flex:1;
                padding:12px;
                font-size:11px;
                font-weight:700;
                color:#64748b;
                border:none;
                background:none;
                cursor:pointer;
                border-bottom:2px solid transparent;
                transition:all 0.2s;
                text-transform:uppercase;
                text-align:center;
            }
            .tab.active{
                color:#fff;
                border-bottom-color:#CC1111;
                background:rgba(255,255,255,0.02);
            }
            .tab-content{
                display:none;
                padding:16px;
                flex-direction:column;
                gap:16px;
                overflow-y:auto;
                flex:1;
            }
            .tab-content.active{
                display:flex;
            }

            /* FORMULARIOS */
            .section-title{
                font-size:10px;
                font-weight:700;
                color:#64748b;
                text-transform:uppercase;
                letter-spacing:1px;
                margin-bottom:4px;
                border-bottom:1px solid #1e3a6e;
                padding-bottom:4px;
                margin-top:10px;
            }
            .field-group{
                display:flex;
                flex-direction:column;
                gap:4px;
                margin-bottom:6px;
                width:100%;
            }
            .field-group label{
                font-size:11px;
                color:#94a3b8;
                font-weight:500;
            }
            .field-group input[type=text], .field-group input[type=number], .field-group select {
                width:100%;
                background:#0f1829;
                border:1px solid #1e3a6e;
                color:#e2e8f0;
                padding:8px 12px;
                border-radius:6px;
                font-size:12px;
                outline:none;
            }
            .field-group input:focus, .field-group select:focus{
                border-color:#CC1111;
            }

            /* TIPOGRAFÍA */
            .typo-controls{
                display:flex;
                align-items:center;
                gap:6px;
                margin-top:4px;
            }
            .typo-controls input[type=number]{
                width:52px;
                padding:5px;
                text-align:center;
                background:#0f1829;
                border:1px solid #1e3a6e;
                color:#fff;
                border-radius:4px;
                font-size:11px;
            }
            .typo-controls input[type=color]{
                width:32px;
                height:28px;
                border:1px solid #1e3a6e;
                cursor:pointer;
                background:#0f1829;
                padding:0;
                border-radius:4px;
            }
            .btn-style{
                background:#0f1829;
                border:1px solid #1e3a6e;
                color:#94a3b8;
                width:28px;
                height:28px;
                border-radius:4px;
                cursor:pointer;
                font-size:12px;
                font-weight:bold;
                display:inline-flex;
                align-items:center;
                justify-content:center;
            }
            .btn-style.on{
                background:#CC1111;
                border-color:#CC1111;
                color:#fff;
            }

            /* PREVIEW AREA */
            #preview-area{
                flex:1;
                background:#0f0f1a;
                overflow-y:auto;
                display:flex;
                justify-content:center;
                align-items:center;
                gap:40px;
                padding:40px;
                flex-wrap:wrap;
                position:relative;
            }
            .view-box{
                display:flex;
                flex-direction:column;
                align-items:center;
                gap:10px;
            }
            .view-label{
                font-size:11px;
                font-weight:700;
                color:#64748b;
                text-transform:uppercase;
                letter-spacing:1px;
            }

            /* TARJETA ZEBRA */
            .zebra-card{
                width:204px;
                height:324px;
                background:#fff;
                border-radius:8px;
                position:relative;
                overflow:hidden;
                box-shadow:0 12px 36px rgba(0,0,0,0.6);
                color:#000;
                font-family:Arial,sans-serif;
                border:none;
            }

            /* CAPAS FRENTE */
            #layer-header{
                position:absolute;
                top:0;
                left:0;
                right:0;
                background:#fff;
                display:flex;
                flex-direction:column;
                align-items:center;
                padding:6px 4px 4px 4px;
                z-index:2;
                border:none;
            }
            .gusa-logo-container{
                width:100%;
                height:48px;
                display:flex;
                align-items:center;
                justify-content:center;
                overflow:hidden;
            }
            .gusa-logo-container img{
                max-width:90%;
                max-height:100%;
                object-fit:contain;
                border:none;
            }
            .header-line-bar{
                width:100%;
                height:3px;
                background:#CC1111;
                margin-top:4px;
                position:relative;
                border:none;
            }
            .header-line-bar::after{
                content:'';
                position:absolute;
                right:25px;
                top:0;
                width:15px;
                height:100%;
                background:#4A90D9;
                border:none;
            }

            #layer-photo-frame{
                position:absolute;
                background:#e2e8f0;
                border:none;
                border-radius:4px;
                overflow:hidden;
                display:flex;
                align-items:center;
                justify-content:center;
                left:0;
                right:0;
                margin-left:auto;
                margin-right:auto;
            }
            #layer-photo-frame img{
                width:100%;
                height:100%;
                object-fit:cover;
                border:none;
            }

            .layer-text-field{
                position:absolute;
                left:4px;
                right:4px;
                text-align:center;
                line-height:1.1;
                word-wrap:break-word;
                border:none;
                background:transparent;
            }

            .badge-id-container{
                position:absolute;
                background:#CC1111;
                display:flex;
                align-items:center;
                justify-content:center;
                border-radius:12px;
                padding:2px 12px;
                white-space:nowrap;
                border:none;
                left:0;
                right:0;
                width:fit-content;
                margin-left:auto;
                margin-right:auto;
            }
            .badge-id-container span{
                color:#fff;
                font-weight:bold;
                border:none;
            }

            #layer-footer-front{
                position:absolute;
                bottom:0;
                left:0;
                right:0;
                height:18px;
                background:#1a1a1a;
                display:flex;
                align-items:center;
                justify-content:center;
                border:none;
                padding: 0 4px;
            }
            #layer-footer-front span{
                font-size:5.5px;
                color:#94a3b8;
                letter-spacing:0.3px;
                font-weight:bold;
                text-align:center;
                white-space:nowrap;
                overflow:hidden;
                text-overflow:ellipsis;
                width:100%;
            }

            /* CAPAS REVERSO */
            #layer-header-back{
                position:absolute;
                top:0;
                left:0;
                right:0;
                height:42px;
                background:#fff;
                display:flex;
                flex-direction:row;
                align-items:center;
                justify-content:space-between;
                padding:4px 10px 0 10px;
                border:none;
            }
            .gusa-logo-container-back {
    width: 55%;
    height: 29px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}
            .gusa-logo-container-back img {
                max-width:100%;
                max-height:100%;
                object-fit:contain;
            }
   

            #layer-qr-container{
                position:absolute;
                background:#fff;
                padding:4px;
                border:none;
                border-radius:4px;
                display:flex;
                flex-direction:column;
                align-items:center;
                justify-content:center;
                left:0;
                right:0;
                width:fit-content;
                margin-left:auto;
                margin-right:auto;
            }
            #qr-target{
                border:none;
            }
            #qr-target img, #qr-target canvas {
                border: none !important;
                outline: none !important;
            }

            /* ESTILOS LEYENDAS INFORMATIVAS DEL QR */
            .qr-label-main {
                font-size: 7px;
                color: #CC1111;
                font-weight: bold;
                text-transform: uppercase;
                margin-top: 4px;
                text-align: center;
                width: 100%;
                letter-spacing: 0.5px;
            }
            .qr-label-sub {
                font-size: 5.5px;
                color: #000000;
                font-weight: normal;
                margin-top: 1px;
                text-align: center;
                width: 100%;
            }

            #layer-data-block{
                position:absolute;
                left:14px;
                right:14px;
                display:flex;
                flex-direction:column;
                border:none;
            }
            .data-row{
                font-size:6px;
                color:#334155;
                line-height:1.2;
                text-transform:uppercase;
                border:none;
                background:transparent;
            }

            #layer-permissions{
                position:absolute;
                left:0;
                right:0;
                display:flex;
                justify-content:center;
                gap:6px;
                border:none;
            }

            /* MODIFICACIÓN: Ocultar los iconos por completo si no están activos */
            .perm-icon-box{
                display:none;
                flex-direction:column;
                align-items:center;
                border:none;
            }
            .perm-icon-box.active{
                display:flex;
            }
            .p-ico{
                font-size:10px;
            }
            .p-ico-span{
                font-family: "Segoe UI Emoji", "Apple Color Emoji", Arial, sans-serif;
            }
            .p-txt{
                font-size:4.5px;
                font-weight:bold;
                color:#000;
                text-transform:uppercase;
                margin-top:1px;
            }

            #layer-footer-back{
                position:absolute;
                bottom:0;
                left:0;
                right:0;
                height:18px;
                display:flex;
                align-items:center;
                justify-content:center;
                border:none;
            }
            #layer-footer-back span{
                font-size:5.5px;
                font-weight:bold;
                letter-spacing:0.2px;
            }

            .btn-action-export{
                background:#10b981;
                color:#fff;
                border:none;
                padding:12px;
                font-weight:bold;
                font-size:12px;
                border-radius:6px;
                cursor:pointer;
                text-transform:uppercase;
                transition:background 0.2s;
                display:flex;
                align-items:center;
                justify-content:center;
                gap:8px;
                margin-top:auto;
            }
            .btn-action-export:hover{
                background:#059669;
            }
        </style>
    </head>
    <body>

        <div id="panel">
            <div class="panel-header">
                <h1>Diseñador Credenciales v8</h1>
                <p>Ajuste Fino de Matrices Vectoriales</p>
            </div>

            <div class="tabs">
                <button class="tab active" id="tab-btn-frente" onclick="switchTab('frente')">Frente</button>
                <button class="tab" id="tab-btn-reverso" onclick="switchTab('reverso')">Reverso</button>
            </div>

            <div class="tab-content active" id="sec-frente">
                <div class="section-title">Caja Fotográfica</div>
                <div class="field-group">
                    <label>Ancho × Alto (px)</label>
                    <div style="display:flex; gap:6px;">
                        <input type="number" id="photo-w" oninput="render()">
                        <input type="number" id="photo-h" oninput="render()">
                    </div>
                </div>
                <div class="field-group">
                    <label>Posición Vertical Y (px)</label>
                    <input type="number" id="photo-y" oninput="render()">
                </div>

                <div class="section-title">Logo / Entidad (Nombre de Archivo)</div>
                <div class="field-group">
                    <input type="text" id="f-logo" oninput="render()">
                </div>

                <div class="section-title">Nombre Empleado</div>
                <div class="field-group">
                    <input type="text" id="f-nombre" oninput="render()">
                    <div class="typo-controls">
                        <input type="number" id="fs-nombre" oninput="render()">
                        <button class="btn-style" id="fbtn-nombre" onclick="toggleStyle('nombre')">B</button>
                        <input type="color" id="fc-nombre" oninput="render()">
                        <span style="font-size:11px;color:#64748b;margin-left:4px;">Y:</span>
                        <input type="number" id="fy-nombre" oninput="render()">
                    </div>
                </div>

                <div class="section-title">Puesto / Cargo</div>
                <div class="field-group">
                    <input type="text" id="f-puesto" oninput="render()">
                    <div class="typo-controls">
                        <input type="number" id="fs-puesto" oninput="render()">
                        <button class="btn-style" id="fbtn-puesto" onclick="toggleStyle('puesto')">B</button>
                        <input type="color" id="fc-puesto" oninput="render()">
                        <span style="font-size:11px;color:#64748b;margin-left:4px;">Y:</span>
                        <input type="number" id="fy-puesto" oninput="render()">
                    </div>
                </div>

                <div class="section-title">Departamento Frente</div>
                <div class="field-group">
                    <input type="text" id="f-depto-f" oninput="render()">
                    <div class="typo-controls">
                        <input type="number" id="fs-depto-f" oninput="render()">
                        <button class="btn-style" id="fbtn-depto-f" onclick="toggleStyle('depto-f')">B</button>
                        <input type="color" id="fc-depto-f" oninput="render()">
                        <span style="font-size:11px;color:#64748b;margin-left:4px;">Y:</span>
                        <input type="number" id="fy-depto-f" oninput="render()">
                    </div>
                </div>

                <div class="section-title">Matriz ID Badge</div>
                <div class="field-group">
                    <input type="text" id="f-id" oninput="renderQR(); render();">
                    <div class="typo-controls">
                        <input type="number" id="fs-id" oninput="render()">
                        <button class="btn-style" id="fbtn-id" onclick="toggleStyle('id')">B</button>
                        <input type="color" id="fc-id" oninput="render()">
                        <span style="font-size:11px;color:#64748b;margin-left:4px;">Y:</span>
                        <input type="number" id="fy-id" oninput="render()">
                    </div>
                </div>

                <div class="section-title">Localidades / Sucursales</div>
                <div class="field-group">
                    <input type="text" id="f-ciudades" oninput="render()">
                </div>
            </div>

            <div class="tab-content" id="sec-reverso">
                <div class="section-title">Módulo QR Code</div>
                <div class="field-group">
                    <label>Contenido URL / Token (Omitido: Usando ID de Empleado)</label>
                    <input type="text" id="f-qr" value="" disabled style="opacity: 0.5; background: #1f293d;" placeholder="Heredando automáticamente del ID del frente">
                </div>
                <div style="display:flex; gap:6px;">
                    <div class="field-group" style="flex:1;"><label>Dimensión (px)</label><input type="number" id="qr-size" oninput="renderQR(); render();"></div>
                    <div class="field-group" style="flex:1;"><label>Posición Y</label><input type="number" id="qr-y" oninput="render()"></div>
                </div>

                <div class="section-title">Bloque Informativo Remoto</div>
                <div class="field-group"><label>Departamento (Reverso)</label><input type="text" id="f-depto-r" oninput="render()"></div>
                <div class="field-group"><label>Teléfono</label><input type="text" id="f-tel" oninput="render()"></div>
                <div class="field-group"><label>Correo Electrónico</label><input type="text" id="f-correo" oninput="render()"></div>
                <div class="field-group"><label>Centro de Trabajo</label><input type="text" id="f-centro" oninput="render()"></div>
                <div class="field-group"><label>Vigencia Documental</label><input type="text" id="f-vigencia" oninput="render()"></div>
                <div style="display:flex; gap:6px;">
                    <div class="field-group" style="flex:1;"><label>Eje Y Bloque</label><input type="number" id="datos-y" oninput="render()"></div>
                    <div class="field-group" style="flex:1;"><label>Separación</label><input type="number" id="datos-gap" oninput="render()"></div>
                </div>

                <div class="section-title">Matriz de Privilegios Operativos</div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; font-size:12px; color:#94a3b8;">
                    <label><input type="checkbox" id="pc-0" onchange="render()"> <span id="lbl-pt-0">Oficinas</span></label>
                    <label><input type="checkbox" id="pc-1" onchange="render()"> <span id="lbl-pt-1">Sistemas</span></label>
                    <label><input type="checkbox" id="pc-2" onchange="render()"> <span id="lbl-pt-2">RFID Est.</span></label>
                    <label><input type="checkbox" id="pc-3" onchange="render()"> <span id="lbl-pt-3">Planta/Obra</span></label>
                </div>
                <div class="field-group" style="margin-top:8px;"><label>Posición Y Permisos</label><input type="number" id="perm-y" oninput="render()"></div>
            </div>

            <button class="btn-action-export" onclick="ejecutarExportacionPDF()">Generar PDF Zebra</button>
        </div>

        <div id="preview-area">
            <div class="view-box">
                <span class="view-label">FRENTE</span>
                <div class="zebra-card" id="card-front">
                    <div id="layer-header">
                        <div class="gusa-logo-container">
                            <img id="view-logo-img" src="" alt=" ">
                        </div>
                        <div class="header-line-bar"></div>
                    </div>
                    <div id="layer-photo-frame">
                        <img id="img-photo" src="" alt=" " style="display: block; width: 100%; height: 100%; object-fit: cover; border: none; background: transparent;">
                    </div>
                    <div class="layer-text-field" id="view-nombre"></div>
                    <div class="layer-text-field" id="view-puesto"></div>
                    <div class="layer-text-field" id="view-depto-f"></div>
                    <div class="badge-id-container" id="view-id-wrap"><span id="view-id"></span></div>
                    <div id="layer-footer-front">
                        <span id="view-ciudades"></span>
                    </div>
                </div>
            </div>

            <div class="view-box">
                <span class="view-label">REVERSO</span>
                <div class="zebra-card" id="card-back">
                    <div id="layer-header-back">
                        <div class="gusa-logo-container-back">
                            <img id="view-logo-img-back" src="" alt=" ">
                        </div>
                       
                    </div>
                    <div id="layer-qr-container">
                        <div id="qr-target"></div>
                        <div class="qr-label-main">ESCÁNEAME</div>
                        <div class="qr-label-sub">para validar información</div>
                    </div>

                    <div id="layer-data-block">

                        <div class="data-row" id="row-id"><strong>NO. EMPLEADO:</strong> <span id="v-id-r"></span></div>
                        <div class="data-row" id="row-depto"><strong>DEPARTAMENTO:</strong> <span id="v-depto-r"></span></div>
                        <div class="data-row" id="row-tel"><strong>TELÉFONO:</strong> <span id="v-tel"></span></div>
                        <div class="data-row" id="row-correo"><strong>CORREO:</strong> <span id="v-correo"></span></div>
                        <div class="data-row" id="row-centro"><strong>CENTRO DE TRABAJO:</strong> <span id="v-centro"></span></div>
                        <div class="data-row" id="row-vigencia"><strong>VIGENCIA:</strong> <span id="v-vigencia"></span></div>
                    </div>

                    <div id="layer-permissions">
                        <div class="perm-icon-box" id="p-box-0"><span class="p-ico p-ico-span">🏢</span><span class="p-txt" id="txt-p-0">Ofic</span></div>
                        <div class="perm-icon-box" id="p-box-1"><span class="p-ico p-ico-span">💻</span><span class="p-txt" id="txt-p-1">Sist</span></div>
                        <div class="perm-icon-box" id="p-box-2"><span class="p-ico p-ico-span">🚗</span><span class="p-txt" id="txt-p-2">RFID</span></div>
                        <div class="perm-icon-box" id="p-box-3"><span class="p-ico p-ico-span">⚡</span><span class="p-txt" id="txt-p-3">Obra</span></div>
                    </div>
                    <div id="layer-footer-back"><span id="v-pie-r"></span></div>
                </div>
            </div>
        </div>

        <script>
            var qrInstancia = null;
            var ST = {};
            var globalConfig = null;

            function switchTab(t) {
                document.querySelectorAll('.tab, .tab-content').forEach(e => e.classList.remove('active'));
                document.getElementById('tab-btn-' + t).classList.add('active');
                document.getElementById('sec-' + t).classList.add('active');
            }

            function toggleStyle(id) {
                ST[id].b = !ST[id].b;
                document.getElementById('fbtn-' + id).classList.toggle('on', ST[id].b);
                render();
            }

            function renderQR() {
                const val = document.getElementById('f-qr').value || '0000';
                const sz = parseInt(document.getElementById('qr-size').value) || 100;
                document.getElementById('qr-target').innerHTML = '';
                qrInstancia = new QRCode(document.getElementById("qr-target"), {
                    text: val, width: sz, height: sz,
                    colorDark: "#000000", colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.H
                });
            }

            function render() {
                if (!globalConfig)
                    return;

                const logoFilename = document.getElementById('f-logo').value || '';
                const logoImgFront = document.getElementById('view-logo-img');
                const logoImgBack = document.getElementById('view-logo-img-back');

                if (logoFilename) {
                    const logoUrl = `<?= base_url('images/logo/') ?>` + logoFilename;
                    logoImgFront.src = logoUrl;
                    logoImgBack.src = logoUrl;
                    logoImgFront.style.display = 'block';
                    logoImgBack.style.display = 'block';
                } else {
                    logoImgFront.removeAttribute('src');
                    logoImgBack.removeAttribute('src');
                }

                const fields = ['nombre', 'puesto', 'depto-f', 'id'];
                fields.forEach(f => {
                    const txt = document.getElementById('f-' + f).value;
                    const view = document.getElementById('view-' + f);
                    if (view) {
                        if (f === 'id') {
                            view.innerText = `ID: ${txt}`;
                        } else if (f === 'nombre') {
                            view.innerText = txt.toUpperCase();
                        } else {
                            view.innerText = txt;
                        }

                        const sizeEl = document.getElementById('fs-' + f);
                        view.style.fontSize = sizeEl ? sizeEl.value + 'px' : (globalConfig.fields['fs-' + f] || '12') + 'px';

                        const colorEl = document.getElementById('fc-' + f);
                        view.style.color = colorEl ? colorEl.value : (globalConfig.fields['fc-' + f] || '#000000');

                        view.style.fontWeight = ST[f]?.b ? 'bold' : 'normal';

                        if (globalConfig.fields['align-' + f]) {
                            view.style.textAlign = globalConfig.fields['align-' + f];
                        }

                        if (globalConfig.fields['vis-' + f] !== undefined) {
                            view.style.display = globalConfig.fields['vis-' + f] ? 'block' : 'none';
                        }

                        if (f !== 'id') {
                            const topEl = document.getElementById('fy-' + f);
                            view.style.top = topEl ? topEl.value + 'px' : (globalConfig.fields['fy-' + f] || '200') + 'px';
                        } else {
                            const wrap = document.getElementById('view-id-wrap');
                            const topEl = document.getElementById('fy-' + f);
                            wrap.style.top = topEl ? topEl.value + 'px' : (globalConfig.fields['fy-' + f] || '260') + 'px';
                            if (globalConfig.fields['vis-id'] !== undefined) {
                                wrap.style.display = "ID" + globalConfig.fields['vis-id'] ? 'flex' : 'none';
                            }
                        }
                    }
                });

                const fw = document.getElementById('layer-photo-frame');
                fw.style.width = document.getElementById('photo-w').value + 'px';
                fw.style.height = document.getElementById('photo-h').value + 'px';
                fw.style.top = document.getElementById('photo-y').value + 'px';

                document.getElementById('layer-qr-container').style.top = document.getElementById('qr-y').value + 'px';

                // MODIFICACIÓN: Inyectar dinámicamente las localidades mapeadas al pie del frente
                const inputCiudades = document.getElementById('f-ciudades');
                const viewCiudades = document.getElementById('view-ciudades');
                if (inputCiudades && viewCiudades) {
                    viewCiudades.innerText = inputCiudades.value;
                }

                // Renderizado dinámico cruzado de la estructura del reverso
                document.getElementById('v-id-r').innerText = document.getElementById('f-id').value;
                document.getElementById('v-depto-r').innerText = document.getElementById('f-depto-r').value || document.getElementById('f-depto-f').value;
                document.getElementById('v-tel').innerText = document.getElementById('f-tel').value || '';
                document.getElementById('v-correo').innerText = document.getElementById('f-correo').value;
                document.getElementById('v-centro').innerText = document.getElementById('f-centro').value;
                document.getElementById('v-vigencia').innerText = document.getElementById('f-vigencia').value;

                // Controles de visibilidad heredados
                if (document.getElementById('row-depto'))
                    document.getElementById('row-depto').style.display = globalConfig.fields['vis-depto-f'] !== false ? 'block' : 'none';
                document.getElementById('row-correo').style.display = globalConfig.fields['vis-correo'] ? 'block' : 'none';
                document.getElementById('row-centro').style.display = globalConfig.fields['vis-centro'] ? 'block' : 'none';
                document.getElementById('row-vigencia').style.display = globalConfig.fields['vis-vigencia'] ? 'block' : 'none';

                const db = document.getElementById('layer-data-block');
                db.style.top = document.getElementById('datos-y').value + 'px';

                const gap = parseInt(document.getElementById('datos-gap').value) || 0;
                document.querySelectorAll('.data-row').forEach(r => r.style.marginBottom = gap + 'px');

                document.getElementById('layer-permissions').style.top = document.getElementById('perm-y').value + 'px';
                for (let i = 0; i <= 3; i++) {
                    document.getElementById('p-box-' + i).classList.toggle('active', document.getElementById('pc-' + i).checked);
                }
            }

            function applyConfig(cfg) {
                if (!cfg)
                    return;
                globalConfig = cfg;

                Object.entries(cfg.st || {}).forEach(([k, v]) => {
                    ST[k] = v;
                    const btn = document.getElementById('fbtn-' + k);
                    if (btn)
                        btn.classList.toggle('on', !!v.b);
                });

                Object.entries(cfg.fields || {}).forEach(([id, val]) => {
                    const el = document.getElementById(id);
                    if (!el)
                        return;
                    if (el.type === 'checkbox')
                        el.checked = !!val;
                    else
                        el.value = val;
                });

                for (let i = 0; i <= 3; i++) {
                    if (cfg.fields['pt-' + i]) {
                        if (document.getElementById('lbl-pt-' + i))
                            document.getElementById('lbl-pt-' + i).innerText = cfg.fields['pt-' + i];
                        if (document.getElementById('txt-p-' + i))
                            document.getElementById('txt-p-' + i).innerText = cfg.fields['pt-' + i].substring(0, 5);
                    }
                }

                if (cfg.fields && cfg.fields['f-pie-r']) {
                    const fBack = document.getElementById('layer-footer-back');
                    fBack.style.background = cfg.fields['fbg-pie-r'] || '#CC1111';
                    const txtPie = document.getElementById('v-pie-r');
                    txtPie.innerText = cfg.fields['f-pie-r'];
                    txtPie.style.fontSize = cfg.fields['fs-pie-r'] + 'px';
                    txtPie.style.color = cfg.fields['fc-pie-r'] || '#ffffff';
                }

                if (cfg.fields && cfg.fields['photo-src']) {
                    document.getElementById('img-photo').src = cfg.fields['photo-src'];
                }

                renderQR();
                render();
            }

            async function ejecutarExportacionPDF() {
                const {jsPDF} = window.jspdf;
                const nombre = (document.getElementById('f-nombre').value || 'Empleado').trim();

                const pdf = new jsPDF({orientation: 'portrait', unit: 'mm', format: [54, 85.6]});

                const opcionesH2C = {
                    scale: 4,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: null,
                    width: 204,
                    height: 324,
                    logging: false
                };

                try {
                    const c1 = await html2canvas(document.getElementById('card-front'), opcionesH2C);
                    pdf.addImage(c1.toDataURL('image/png'), 'PNG', 0, 0, 54, 85.6);

                    pdf.addPage([54, 85.6]);

                    const c2 = await html2canvas(document.getElementById('card-back'), opcionesH2C);
                    pdf.addImage(c2.toDataURL('image/png'), 'PNG', 0, 0, 54, 85.6);

                    pdf.save(`credencial_${nombre.replace(/\s+/g, '_')}.pdf`);
                } catch (error) {
                    console.error("Error al generar la matriz del PDF:", error);
                }
            }

<?php if (isset($config)): ?>
                const configPayload = <?= json_encode($config) ?>;
                applyConfig(configPayload);
<?php endif; ?>
        </script>
    </body>
</html>