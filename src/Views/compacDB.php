<?= $this->include('julio101290\boilerplate\Views\load\select2') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= $this->include('julio101290\boilerplate\Views\load\nestable') ?>
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>
<meta name="csrf-token" content="<?= csrf_hash() ?>">

<?= $this->section('content') ?>
<?= $this->include('julio101290\\boilerplatecompac\\Views\\modulesCompacDB\\modalCaptureCompacDB') ?>

<div class="card card-default">
    <div class="card-header">
        <div class="float-right">
            <div class="btn-group">
                <button class="btn btn-primary btnAddCompacDB" data-toggle="modal" data-target="#modalAddCompacDB">
                    <i class="fa fa-plus"></i> <?= lang('compacDB.add') ?>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="tableCompacDB" class="table table-striped table-hover va-middle tableCompacDB">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?= lang('compacDB.fields.idEmpresa') ?></th>
                                <th><?= lang('compacDB.fields.host') ?></th>
                                <th><?= lang('compacDB.fields.user') ?></th>
                                <th><?= lang('compacDB.fields.password') ?></th>
                                <th><?= lang('compacDB.fields.database') ?></th>
                                <th><?= lang('compacDB.fields.port') ?></th>
                                <th><?= lang('compacDB.fields.created_at') ?></th>
                                <th><?= lang('compacDB.fields.updated_at') ?></th>
                                <th><?= lang('compacDB.fields.deleted_at') ?></th>

                                <th><?= lang('compacDB.fields.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var csrfName = '<?= csrf_token() ?>';
    var tableCompacDB = $('#tableCompacDB').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        order: [[1, 'asc']],
        ajax: {
            url: '<?= base_url('admin/compacDB') ?>',
            method: 'GET',
            dataType: "json"
        },
        columnDefs: [{
                orderable: false,
                searchable: false,
                targets: [9]
            }],
        columns: [
            {'data': 'id'},
            {'data': 'idEmpresa'},
            {'data': 'host'},
            {'data': 'user'},
            {'data': 'password'},
            {'data': 'database'},
            {'data': 'port'},
            {'data': 'created_at'},
            {'data': 'updated_at'},
            {'data': 'deleted_at'},

            {
                "data": function (data) {
                    return `
                    <div class="btn-group btn-group-sm">

                        <button class="btn btn-warning btnEditCompacDB"
                                data-toggle="modal"
                                idCompacDB="${data.id}"
                                data-target="#modalAddCompacDB"
                                title="Editar">
                            <i class="fa fa-edit"></i>
                        </button>

                        <button class="btn btn-info btnTestConnection"
                                data-id="${data.id}"
                                title="Probar conexión">
                            <i class="fas fa-plug"></i>
                        </button>

                        <button class="btn btn-danger btn-delete"
                                data-id="${data.id}"
                                title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>

                    </div>`;
                }
            }
        ]
    });

    $(document).on('click', '#btnSaveCompacDB', function (e) {
        e.preventDefault();
        var idCompacDB = $("#idCompacDB").val();
        var idEmpresa = $("#idEmpresa").val();
        var host = $("#host").val();
        var user = $("#user").val();
        var password = $("#password").val();
        var port = $("#port").val();
        var database = $("#database").val();

        $("#btnSaveCompacDB").attr("disabled", true);
        var datos = new FormData();
        datos.append("idCompacDB", idCompacDB);
        datos.append("idEmpresa", idEmpresa);
        datos.append("host", host);
        datos.append("user", user);
        datos.append("password", password);
        datos.append("database", database);
        datos.append("port", port);


        var csrfHash = $('input[name="' + csrfName + '"]').val();
        datos.append(csrfName, csrfHash);

        $.ajax({
            url: "<?= base_url('admin/compacDB/save') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                if (respuesta?.message?.includes("Guardado") || respuesta?.message?.includes("Actualizado")) {
                    Toast.fire({
                        icon: 'success',
                        title: respuesta.message
                    });
                    tableCompacDB.ajax.reload();
                    $("#btnSaveCompacDB").removeAttr("disabled");
                    $('#modalAddCompacDB').modal('hide');
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: respuesta.message || "Error desconocido"
                    });
                    $("#btnSaveCompacDB").removeAttr("disabled");
                }
                // Actualizar el token CSRF si la respuesta lo incluye (tanto éxito como error)
                if (respuesta.csrf_hash) {
                    $('input[name="' + csrfName + '"]').val(respuesta.csrf_hash);
                    $('meta[name="csrf-token"]').attr('content', respuesta.csrf_hash);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                // Intentar obtener la respuesta JSON aunque haya error HTTP
                try {
                    var respuesta = jqXHR.responseJSON;
                    if (respuesta && respuesta.csrf_hash) {
                        $('input[name="' + csrfName + '"]').val(respuesta.csrf_hash);
                        $('meta[name="csrf-token"]').attr('content', respuesta.csrf_hash);
                    }
                    var mensaje = (respuesta && respuesta.message) ? respuesta.message : jqXHR.responseText;
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: mensaje
                    });
                } catch (e) {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: jqXHR.responseText
                    });
                }
                $("#btnSaveCompacDB").removeAttr("disabled");
            }
        });
    });

    $(".tableCompacDB").on("click", ".btnEditCompacDB", function () {
        var idCompacDB = $(this).attr("idCompacDB");
        var datos = new FormData();
        datos.append("idCompacDB", idCompacDB);
        $.ajax({
            url: "<?= base_url('admin/compacDB/getCompacDB') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                $("#idCompacDB").val(respuesta["id"]);
                $("#idEmpresa").val(respuesta["idEmpresa"]).trigger("change");
                $("#host").val(respuesta["host"]);
                $("#user").val(respuesta["user"]);
                $("#password").val(respuesta["password"]);
                $("#database").val(respuesta["database"]);
                $("#port").val(respuesta["port"]);

            }
        });
    });

    $(".tableCompacDB").on("click", ".btn-delete", function () {
        var idCompacDB = $(this).attr("data-id");
        Swal.fire({
            title: '<?= lang('boilerplate.global.sweet.title') ?>',
            text: "<?= lang('boilerplate.global.sweet.text') ?>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<?= lang('boilerplate.global.sweet.confirm_delete') ?>'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: `<?= base_url('admin/compacDB') ?>/` + idCompacDB,
                    method: 'DELETE',
                }).done((data, textStatus, jqXHR) => {
                    Toast.fire({
                        icon: 'success',
                        title: jqXHR.statusText,
                    });
                    tableCompacDB.ajax.reload();
                }).fail((error) => {
                    Toast.fire({
                        icon: 'error',
                        title: error.responseJSON?.message || error.responseText,
                    });
                });
            }
        });
    });

    $(function () {
        $("#modalAddCompacDB").draggable();
    });

    $(".tableCompacDB").on("click", ".btnTestConnection", function () {

        let idCompacDB = $(this).attr("data-id");

        let datos = new FormData();
        datos.append("idCompacDB", idCompacDB);

        let csrfHash = $('meta[name="csrf-token"]').attr('content');
        datos.append(csrfName, csrfHash);

        Swal.fire({
            title: 'Validando conexión...',
            text: 'Espere un momento',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "<?= base_url('admin/compacDB/testConnection') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",

            success: function (respuesta) {

                if (respuesta.csrf_hash) {
                    $('meta[name="csrf-token"]').attr('content', respuesta.csrf_hash);
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Conexión exitosa',
                    text: respuesta.message
                });
            },

            error: function (jqXHR) {

                let respuesta = jqXHR.responseJSON;
                
                console.log( jqXHR.responseJSON);

                if (respuesta && respuesta.csrf_hash) {
                    $('meta[name="csrf-token"]').attr('content', respuesta.csrf_hash);
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: respuesta?.message ?? 'No fue posible conectar al servidor SQL Server'
                });
            }
        });
    });
</script>
<?= $this->endSection() ?>