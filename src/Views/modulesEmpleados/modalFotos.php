<div class="modal fade" id="modalTomarFoto" tabindex="-1" role="dialog" aria-labelledby="modalTomarFotoLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="modalTomarFotoLabel"><i class="fas fa-user-circle"></i> Gestionar Foto de Empleado</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" onclick="detenerCamara()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <input type="hidden" id="camera_id_empleado">
                <input type="hidden" id="camera_id_db">

                <ul class="nav nav-pills nav-justified mb-3" id="photoMethodTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold" id="tab-upload-direct" data-toggle="tab" href="#panel-upload" role="tab" onclick="detenerCamara(); resetVistasFoto();"><i class="fas fa-upload"></i> Subir Archivo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link font-weight-bold" id="tab-camera-direct" data-toggle="tab" href="#panel-camera" role="tab" onclick="inicializarHardwareCamara();"><i class="fas fa-camera"></i> Usar Cámara</a>
                    </li>
                </ul>

                <div class="tab-content border p-3 rounded bg-light">
                    <div class="tab-pane fade show active" id="panel-upload" role="tablist">
                        <div class="p-4 border-dashed rounded bg-white text-secondary d-flex flex-column align-items-center justify-content-center" 
                             style="border: 2px dashed #ccc; cursor: pointer; min-height: 180px;"
                             onclick="document.getElementById('inputFileDirecto').click()">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                            <p class="mb-1 font-weight-bold" style="font-size:14px;">Haz clic para buscar o arrastra una imagen</p>
                            <span class="text-xs text-muted" style="font-size:11px;">Formatos aceptados: JPG, JPEG, PNG</span>
                        </div>
                        <input type="file" id="inputFileDirecto" accept="image/*" class="d-none" onchange="procesarArchivoLocal(this)">
                    </div>

                    <div class="tab-pane fade" id="panel-camera" role="tablist">
                        <div class="camera-viewport mb-2" id="viewportCamara">
                            <video id="cameraStream" autoplay playsinline></video>
                            <div class="camera-overlay"></div>
                        </div>
                        <button type="button" class="btn btn-primary font-weight-bold btn-block mt-2" id="btnDispararCamara" onclick="capturarFotograma()">
                            <i class="fas fa-circle"></i> Capturar Fotografía
                        </button>
                    </div>
                </div>

                <div id="wrapperPreviewGlobal" class="mt-3 text-center" style="display:none;">
                    <label class="text-success font-weight-bold mb-1 d-block"><i class="fas fa-check-circle"></i> Vista Previa del Recorte</label>
                    <canvas id="photoPreviewCanvas" style="max-width: 180px; height: auto; margin: 0 auto; border: 3px solid #10b981; border-radius: 6px; box-shadow: 0 4px 8px rgba(0,0,0,0.15); display: block;"></canvas>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="btnRemoverFoto" onclick="resetVistasFoto()">
                        <i class="fas fa-undo"></i> Cambiar Imagen / Reintentar
                    </button>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="detenerCamara()">Cancelar</button>
                <button type="button" class="btn btn-success font-weight-bold" id="btnGuardarFotoServidor" disabled onclick="subirFotoServidor()">
                    <i class="fas fa-save"></i> Guardar e Integrar Foto
                </button>
            </div>
        </div>
    </div>
</div>