<?= $this->include('julio101290\boilerplate\Views\load\select2') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>
<?= $this->include('julio101290\boilerplatecompac\Views\modulesEmpleados\modalFotos') ?>

<meta name="csrf-token" content="<?= csrf_hash() ?>">

<style>
    .card-container-flex {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
        background: #111116;
        padding: 25px;
        border-radius: 8px;
    }
    .credential-card-preview {
        width: 324px;
        height: 513.6px;
        background: #ffffff;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 16px rgba(0,0,0,0.4);
        font-family: Arial, sans-serif;
        color: #000000;
    }
    .header-band {
        width: 100%;
        height: 50px;
        background: #cc1111;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 14px;
        text-transform: uppercase;
    }
    .photo-area {
        width: 130px;
        height: 165px;
        background: #e0e0e0;
        margin: 30px auto 15px auto;
        border: 3px solid #cc1111;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .photo-area img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .info-area {
        text-align: center;
        padding: 0 15px;
    }
    .emp-name {
        font-size: 16px;
        font-weight: bold;
        color: #111;
        line-height: 1.2;
        margin-bottom: 4px;
        text-transform: uppercase;
    }
    .emp-puesto {
        font-size: 12px;
        color: #555;
        font-weight: 500;
        text-transform: uppercase;
    }
    .emp-id {
        font-size: 20px;
        font-weight: bold;
        color: #cc1111;
        margin-top: 25px;
        letter-spacing: 1px;
    }
    .reverso-content {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        padding: 20px 15px;
        background: #fafafa;
    }
    .reverso-header {
        font-size: 11px;
        font-weight: bold;
        text-align: center;
        border-bottom: 2px solid #cc1111;
        padding-bottom: 5px;
    }
    .reverso-datos {
        font-size: 10px;
        margin-top: 15px;
        line-height: 1.6;
        color: #333;
    }
    .reverso-politicas {
        font-size: 8px;
        color: #777;
        text-align: justify;
        margin-top: 10px;
        border-top: 1px dashed #ccc;
        padding-top: 8px;
    }
    .reverso-pie {
        width: 100%;
        background: #cc1111;
        color: white;
        font-size: 8px;
        text-align: center;
        padding: 6px 0;
        position: absolute;
        bottom: 0;
        left: 0;
        font-weight: bold;
    }
</style>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= $title ?></h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="selectCompacDB">Selecciona la Base de Datos:</label>
                    <select class="form-control select2" id="selectCompacDB" style="width: 100%;">
                        <option value="">-- Selecciona una base de datos --</option>
                        <?php foreach ($databases as $db): ?>
                            <option value="<?= $db['id'] ?>"><?= esc($db['database']) ?> (<?= esc($db['host']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <hr>

        <div class="table-responsive">
            <table id="tableCompacEmpleados" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Puesto</th>
                        <th>Correo Electrónico</th>
                        <th>Localidad</th>
                        <th style="width: 10%;">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCredencial" tabindex="-1" role="dialog" aria-labelledby="modalCredencialLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalCredencialLabel"><i class="fas fa-id-card"></i> Diseñador & Exportador de Credenciales</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light">
                <div class="row">
                    <div class="col-lg-7 mb-3">
                        <label class="d-block text-center font-weight-bold mb-2">Vista Previa de Impresión (Frente / Reverso)</label>
                        <div class="card-container-flex">
                            <div class="credential-card-preview" id="card-front">
                                <div class="header-band">CONSTRUCTORA GUSA</div>
                                <div class="photo-area">
                                    <img id="view-photo" src="https://via.placeholder.com/130x165?text=Sin+Foto" alt="Empleado">
                                </div>
                                <div class="info-area">
                                    <div class="emp-name" id="view-name">JUAN PEREZ LOPEZ</div>
                                    <div class="emp-puesto" id="view-puesto">RESIDENTE DE OBRA</div>
                                    <div class="emp-id" id="view-id">GUSA-0000</div>
                                </div>
                            </div>
                            <div class="credential-card-preview" id="card-back">
                                <div class="reverso-content">
                                    <div>
                                        <div class="reverso-header">DATOS COMPLEMENTARIOS</div>
                                        <div class="reverso-datos">
                                            <strong>Correo:</strong> <span id="view-back-correo">S/C</span><br>
                                            <strong>Localidad:</strong> <span id="view-back-localidad">Mochis</span><br>
                                            <strong>Vigencia:</strong> Permanente
                                        </div>
                                    </div>
                                    <div class="reverso-politicas">
                                        Esta credencial es intransferible y acredita al portador como empleado de esta compañía. En caso de extravío o hallazgo, favor de reportarlo al departamento de Recursos Humanos.
                                    </div>
                                    <div class="reverso-pie" id="view-back-pie">PROPIEDAD DE CONSTRUCTORA GUSA SA DE CV</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <label class="font-weight-bold">Payload de Configuración Estructural (JSON)</label>
                        <div class="form-group">
                            <textarea id="jsonConfigArea" class="form-control" rows="16" style="font-family: Courier, monospace; font-size: 12px; background: #1a1a1a; color: #f8f8f2;" readonly></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success font-weight-bold" id="btnDescargarPDF"><i class="fas fa-file-pdf"></i> Exportar PDF de Impresión</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    var tableCompacEmpleados;
    var localStream = null; // Definida para evitar fugas globales latentes

    $(document).ready(function () {
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

        if ($.fn.select2) {
            $('.select2').select2();
        }

        tableCompacEmpleados = $('#tableCompacEmpleados').DataTable({
            "language": {"url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"},
            "responsive": true
        });

        $('#selectCompacDB').on('change', function () {
            var idCompacDB = $(this).val();
            if (!idCompacDB) {
                tableCompacEmpleados.clear().draw();
                return;
            }

            let datos = new FormData();
            datos.append("idCompacDB", idCompacDB);

            $.ajax({
                url: "<?= base_url('admin/compacEmpleados/getEmpleados') ?>",
                method: "POST",
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (respuesta) {
                    if (respuesta.csrf_hash) {
                        updateCSRF(respuesta.csrf_hash);
                    }
                    tableCompacEmpleados.clear();

                    if (respuesta.status === 200 && respuesta.data.length > 0) {
                        var dataSet = respuesta.data.map(function (emp) {
                            var codigo = emp.codigoempleado || emp.CodigoEmpleado || '';
                            var botonAccion = `
<div class="btn-group btn-group-sm d-flex">
    <a href="<?= base_url('admin/compacEmpleados/credencialGenerator') ?>?idCompacDB=${idCompacDB}&codigoempleado=${codigo}" target="_blank" class="btn btn-outline-danger w-100"><i class="fas fa-id-card"></i> Configurar</a>
    <button type="button" class="btn btn-dark" onclick="abrirModalCamara('${codigo}', '${idCompacDB}')" title="Subir o Tomar Foto"><i class="fas fa-camera"></i> / <i class="fas fa-upload"></i></button>
</div>`;
                            return [
                                codigo,
                                emp.nombrelargo || emp.NombreLargo || '',
                                emp.puesto || emp.Puesto || 'Sin puesto',
                                emp.CorreoElectronico || 'Sin correo',
                                emp.localidad || 'Sin localidad',
                                botonAccion
                            ];
                        });
                        tableCompacEmpleados.rows.add(dataSet).draw();
                    }
                }
            });
        });

        $('#tableCompacEmpleados').on('click', '.btn-credencial', function () {
            var codigoEmpleado = $(this).data('codigo');
            var idCompacDB = $('#selectCompacDB').val();

            let datos = new FormData();
            datos.append("idCompacDB", idCompacDB);
            datos.append("codigoempleado", codigoEmpleado);

            $.ajax({
                url: "<?= base_url('admin/compacEmpleados/getCredencialConfig') ?>",
                method: "POST",
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                success: function (respuesta) {
                    if (respuesta.csrf_hash) {
                        updateCSRF(respuesta.csrf_hash);
                    }

                    if (respuesta.status === 200) {
                        let cfg = respuesta.config;

                        $('#view-name').text(cfg['f-nombre']);
                        $('#view-puesto').text(cfg['f-puesto']);
                        $('#view-id').text(cfg['f-id']);
                        $('#view-back-correo').text(cfg['f-correo']);
                        $('#view-back-localidad').text($('#selectCompacDB option:selected').text());

                        if (cfg['photo-src']) {
                            $('#view-photo').attr('src', cfg['photo-src']);
                        } else {
                            $('#view-photo').attr('src', 'https://via.placeholder.com/130x165?text=Sin+Foto');
                        }

                        $('#jsonConfigArea').val(JSON.stringify(cfg, null, 4));
                        $('#modalCredencial').modal('show');
                    }
                }
            });
        });

        $('#btnDescargarPDF').on('click', async function () {
            const {jsPDF} = window.jspdf;
            const nombreEmpleado = $('#view-name').text().trim() || 'Empleado';

            Swal.fire({
                title: 'Generando PDF de alta definición...',
                text: 'Procesando vectores y matrices de imagen',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: [54, 85.6]
                });

                const frontCanvas = await html2canvas(document.getElementById('card-front'), {scale: 3, useCORS: true});
                pdf.addImage(frontCanvas.toDataURL('image/png'), 'PNG', 0, 0, 54, 85.6);

                pdf.addPage([54, 85.6]);

                const backCanvas = await html2canvas(document.getElementById('card-back'), {scale: 3, useCORS: true});
                pdf.addImage(backCanvas.toDataURL('image/png'), 'PNG', 0, 0, 54, 85.6);

                pdf.save(`credencial_${nombreEmpleado}.pdf`);
                Swal.close();
                Toast.fire({icon: 'success', title: 'PDF exportado correctamente'});
            } catch (error) {
                Swal.close();
                Swal.fire({icon: 'error', title: 'Fallo al procesar PDF', text: error.message});
            }
        });
    });

    function updateCSRF(hash) {
        $('meta[name="csrf-token"]').attr('content', hash);
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': hash}});
    }

    function abrirModalCamara(idEmpleado, idDB) {
        $('#camera_id_empleado').val(idEmpleado);
        $('#camera_id_db').val(idDB);
        $('#photoMethodTabs a[href="#panel-upload"]').tab('show');
        resetVistasFoto();
        $('#modalTomarFoto').modal('show');
    }

    function resetVistasFoto() {
        detenerCamara();
        $('#inputFileDirecto').val('');
        $('#photoPreviewCanvas').hide();
        $('#wrapperPreviewGlobal').hide();
        $('.tab-content').show();
        $('#photoMethodTabs').css('pointer-events', 'auto').removeClass('disabled');
        $('#btnGuardarFotoServidor').prop('disabled', true);
    }

    function inicializarHardwareCamara() {
        resetVistasFoto();
        navigator.mediaDevices.getUserMedia({
            video: {width: {ideal: 640}, height: {ideal: 480}, facingMode: "user"},
            audio: false
        })
        .then(function (stream) {
            localStream = stream;
            var video = document.getElementById('cameraStream');
            video.srcObject = stream;
            video.play();
        })
        .catch(function (err) {
            console.error("Fallo de acceso a cámara: ", err);
            Swal.fire('Error de Dispositivo', 'No se puede acceder a la cámara web o los privilegios fueron denegados.', 'error');
            $('#photoMethodTabs a[href="#panel-upload"]').tab('show');
        });
    }

    function procesarArchivoLocal(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = new Image();
                img.onload = function () {
                    var canvas = document.getElementById('photoPreviewCanvas');
                    var ctx = canvas.getContext('2d');

                    canvas.width = 450;
                    canvas.height = 570;

                    var imgRatio = img.width / img.height;
                    var targetRatio = canvas.width / canvas.height;
                    var sx, sy, sw, sh;

                    if (imgRatio > targetRatio) {
                        sh = img.height;
                        sw = img.height * targetRatio;
                        sx = (img.width - sw) / 2;
                        sy = 0;
                    } else {
                        sw = img.width;
                        sh = img.width / targetRatio;
                        sx = 0;
                        sy = (img.height - sh) / 2;
                    }

                    ctx.drawImage(img, sx, sy, sw, sh, 0, 0, canvas.width, canvas.height);
                    congelarUIConFoto();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function capturarFotograma() {
        var video = document.getElementById('cameraStream');
        var canvas = document.getElementById('photoPreviewCanvas');
        var ctx = canvas.getContext('2d');

        canvas.width = 450;
        canvas.height = 570;

        var sourceWidth = video.videoHeight * 0.78;
        var sourceX = (video.videoWidth - sourceWidth) / 2;

        ctx.drawImage(
            video,
            sourceX, 0, sourceWidth, video.videoHeight,
            0, 0, canvas.width, canvas.height
        );

        detenerCamara();
        congelarUIConFoto();
    }

    function congelarUIConFoto() {
        $('.tab-content').hide();
        $('#photoMethodTabs').css('pointer-events', 'none').addClass('disabled');
        $('#photoPreviewCanvas').show();
        $('#wrapperPreviewGlobal').show();
        $('#btnGuardarFotoServidor').prop('disabled', false);
    }

    function detenerCamara() {
        if (localStream) {
            localStream.getTracks().forEach(track => track.stop());
            localStream = null;
        }
    }

    function subirFotoServidor() {
        var canvas = document.getElementById('photoPreviewCanvas');
        var base64Data = canvas.toDataURL('image/jpeg', 0.9);
        var idEmpleado = $('#camera_id_empleado').val();
        var idDB = $('#camera_id_db').val();

        let datos = new FormData();
        datos.append("id_empleado", idEmpleado);
        datos.append("idCompacDB", idDB);
        datos.append("fotoBase64", base64Data);

        Swal.fire({
            title: 'Guardando fotografía...',
            text: 'Escribiendo binarios y actualizando registros',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: "<?= base_url('admin/compacEmpleados/guardarFotoEmpleado') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                Swal.close();
                if (respuesta.csrf_hash) {
                    updateCSRF(respuesta.csrf_hash);
                }

                if (respuesta.status === 200) {
                    $('#modalTomarFoto').modal('hide');
                    Swal.fire('¡Éxito!', 'La fotografía se procesó y guardó de forma correcta.', 'success');
                    if (tableCompacEmpleados) $('#selectCompacDB').trigger('change');
                } else {
                    Swal.fire('Error', respuesta.message || 'No se pudo guardar la imagen.', 'error');
                }
            },
            error: function (err) {
                Swal.close();
                Swal.fire('Fallo del Servidor', 'Ocurrió un error crítico de red durante la transferencia.', 'error');
            }
        });
    }
</script>
<?= $this->endSection() ?>