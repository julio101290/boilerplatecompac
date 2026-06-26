<?php

namespace julio101290\boilerplatecompac\Models;

use CodeIgniter\Model;

class CompacEmpleadoModel extends Model {

    protected $returnType = 'array';

    /**
     * Recibe los datos de conexión guardados en compacDB
     * y regresa la lista de empleados desde SQL Server.
     */
    public function getEmpleadosPorConexion(array $registro): array {
        $config = [
            'DBDriver' => 'SQLSRV',
            'hostname' => $registro['host'] ?? '',
            'database' => $registro['database'] ?? '',
            'username' => $registro['user'] ?? '',
            'password' => $registro['password'] ?? '',
            'port' => $registro['port'] ?? 1433,
        ];

        if (empty($config['hostname']) || empty($config['database'])) {
            throw new \RuntimeException('Faltan datos de conexión.');
        }

        $db = \Config\Database::connect($config, false);

        try {
            $sql = "
                SELECT 
                a.idempleado
               ,a.iddepartamento
               ,a.idpuesto
               ,a.nombre
               ,a.apellidopaterno
               ,a.apellidomaterno
               ,a.codigoempleado
               ,a.nombrelargo
               ,a.fotografia
               ,b.descripcion AS puesto
               ,a.CorreoElectronico
               ,c.localidad
           FROM nom10001 a
               ,nom10006 b
               ,nom10000 c
           WHERE a.idpuesto = b.idpuesto
            ";

            $query = $db->query($sql);

            return $query ? $query->getResultArray() : [];
        } finally {
            $db->close();
        }
    }
}
