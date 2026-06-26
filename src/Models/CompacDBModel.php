<?php

namespace julio101290\boilerplatecompac\Models;

use CodeIgniter\Model;

class CompacDBModel extends Model
{
    protected $table            = 'compacDB';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['idEmpresa', 'host', 'database', 'user', 'password', 'port'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules    = [
        'idEmpresa' => 'required|integer|greater_than[0]'
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // Eventos para asegurar que deleted_at siempre sea null
    protected $beforeInsert = ['setDeletedAtNull'];
    protected $beforeUpdate = ['setDeletedAtNull'];

    protected function setDeletedAtNull(array $data)
    {
        if (isset($data['data']['deleted_at'])) {
            unset($data['data']['deleted_at']);
        }
        return $data;
    }

    /**
     * Obtiene los registros filtrando por empresas del usuario
     *
     * @param array $idEmpresas
     * @return \CodeIgniter\Database\BaseBuilder
     */
    public function mdlGetCompacDB(array $idEmpresas)
    {
        return $this->db->table('compacDB a')
            ->join('empresas b', 'a.idEmpresa = b.id')
            ->select("a.id, a.idEmpresa, a.host, a.user, a.database, a.password, a.port, a.created_at, a.updated_at, a.deleted_at, b.nombre AS nombreEmpresa")
            ->whereIn('a.idEmpresa', $idEmpresas);
    }
}