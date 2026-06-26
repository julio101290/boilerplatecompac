<?php

namespace julio101290\boilerplatecompac\Models;

use CodeIgniter\Model;

class EmpleadoFotosModel extends Model {

    protected $table = 'empleado_fotos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true; // Activado ya que cuentas con deleted_at en la migración
    // Campos que se permiten registrar o modificar mediante insert() o update()
    protected $allowedFields = [
        'id_empleado',
        'idCompacDB', // Sugerido si interceptas a qué conexión pertenece el código de empleado
        'rutaFoto'
    ];
    // Fechas y Timestamps automáticos de CodeIgniter
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    // Reglas de validación base
    protected $validationRules = [
        'id_empleado' => 'required|max_length[32]',
        'rutaFoto' => 'permit_empty|max_length[255]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
