<!-- Modal CompacDB -->
<div class="modal fade" id="modalAddCompacDB" tabindex="-1" role="dialog" aria-labelledby="modalAddCompacDB" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= lang('compacDB.createEdit') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-compacDB" class="form-horizontal">
                    <?= csrf_field() ?>
                    <input type="hidden" id="idCompacDB" name="idCompacDB" value="0">

                    <div class="form-group row">
                        <label for="idEmpresa" class="col-sm-2 col-form-label">Empresa</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <select class="form-control idEmpresa" name="idEmpresa" id="idEmpresa" style="width:80%;">
                                    <option value="0">Seleccione empresa</option>
                                    <?php
                                    foreach ($empresas as $key => $value) {
                                        echo "<option value='$value[id]' selected>$value[id] - $value[nombre] </option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>                    <div class="form-group row">
                        <label for="host" class="col-sm-2 col-form-label"><?= lang('compacDB.fields.host') ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <input type="text" name="host" id="host" class="form-control <?= session('error.host') ? 'is-invalid' : '' ?>" value="<?= old('host') ?>" placeholder="<?= lang('compacDB.fields.host') ?>" autocomplete="off">
                            </div>
                        </div>
                    </div>                    <div class="form-group row">
                        <label for="user" class="col-sm-2 col-form-label"><?= lang('compacDB.fields.user') ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <input type="text" name="user" id="user" class="form-control <?= session('error.user') ? 'is-invalid' : '' ?>" value="<?= old('user') ?>" placeholder="<?= lang('compacDB.fields.user') ?>" autocomplete="off">
                            </div>
                        </div>
                    </div> 

                    <div class="form-group row">
                        <label for="password" class="col-sm-2 col-form-label">
                            <?= lang('compacDB.fields.password') ?>
                        </label>

                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-pencil-alt"></i>
                                    </span>
                                </div>

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control <?= session('error.password') ? 'is-invalid' : '' ?>"
                                    value="<?= old('password') ?>"
                                    placeholder="<?= lang('compacDB.fields.password') ?>"
                                    autocomplete="off"
                                    >

                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>   

                    <div class="form-group row">
                        <label for="port" class="col-sm-2 col-form-label"><?= lang('compacDB.fields.database') ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <input type="text" name="database" id="database" class="form-control <?= session('error.port') ? 'is-invalid' : '' ?>" value="<?= old('database') ?>" placeholder="<?= lang('compacDB.fields.database') ?>" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="port" class="col-sm-2 col-form-label"><?= lang('compacDB.fields.port') ?></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <input type="text" name="port" id="port" class="form-control <?= session('error.port') ? 'is-invalid' : '' ?>" value="<?= old('port') ?>" placeholder="<?= lang('compacDB.fields.port') ?>" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?= lang('boilerplate.global.close') ?></button>
                <button type="button" class="btn btn-primary btn-sm" id="btnSaveCompacDB"><?= lang('boilerplate.global.save') ?></button>
            </div>
        </div>
    </div>
</div>

<?= $this->section('js') ?>
<script>
    $(document).on('click', '.btnAddCompacDB', function (e) {
        $(".form-control").val("");
        $("#idCompacDB").val("0");
        $("#btnSaveCompacDB").removeAttr("disabled");
        $("#idEmpresa").val(0).trigger("change");


    });

    $(document).on('click', '.btnEditCompacDB', function (e) {
        var idCompacDB = $(this).attr("idCompacDB");
        $(".form-control").val("");
        $("#idCompacDB").val(idCompacDB);
        $("#btnSaveCompacDB").removeAttr("disabled");
    });

    $("#idEmpresa").select2();

    $("#idEmpresa").trigger("change");


    document.getElementById('togglePassword').addEventListener('click', function () {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');

        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

</script>
<?= $this->endSection() ?>