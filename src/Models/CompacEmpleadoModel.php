<?php
namespace julio101290\boilerplatecompac\Models;
use CodeIgniter\Model;

class CompacEmpleadoModel extends Model {
    protected $returnType = 'array';
    public function getEmpleadosPaginado(array $registro, int $start, int $length, string $search = ''): array {
        $host     = $registro['host'] ?? '127.0.0.1';
        $puerto   = $registro['port'] ?? 1433;
        $database = $registro['database'] ?? '';
        $user     = $registro['user'] ?? '';
        $password = $registro['password'] ?? '';
    
        $dsn = "sqlsrv:Server={$host},{$puerto};Database={$database};"
             . "LoginTimeout=5;ConnectionPooling=1;TrustServerCertificate=1;"
             . "MultipleActiveResultSets=false";
    
        try {
            $pdo = new \PDO($dsn, $user, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $pdo->setAttribute(\PDO::SQLSRV_ATTR_ENCODING, \PDO::SQLSRV_ENCODING_UTF8);
    
            // Crear vista si no existe
            $vistaExiste = $pdo->query("SELECT COUNT(*) as total FROM sys.views WHERE name = 'vw_empleados_sin_foto'")->fetch()['total'];
            if (!$vistaExiste) {
                $pdo->exec("
                    CREATE VIEW vw_empleados_sin_foto AS
                    SELECT 
                         a.idempleado
                        ,a.iddepartamento
                        ,a.idpuesto
                        ,a.nombre
                        ,a.apellidopaterno
                        ,a.apellidomaterno
                        ,a.codigoempleado
                        ,a.nombrelargo
                        ,b.descripcion AS puesto
                        ,a.CorreoElectronico
                        ,c.localidad
                    FROM nom10001 a
                    INNER JOIN nom10006 b ON a.idpuesto = b.idpuesto
                    CROSS JOIN nom10000 c
                ");
            }
    
            // WHERE dinámico para búsqueda
            $where = '';
            $params = [];
            if (!empty($search)) {
                $where = "WHERE nombrelargo LIKE ? OR codigoempleado LIKE ? OR puesto LIKE ?";
                $params = ["%{$search}%", "%{$search}%", "%{$search}%"];
            }
    
            // Total sin filtro
            $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM vw_empleados_sin_foto FOR JSON PATH");
            $totalJson = '';
            while ($row = $totalStmt->fetch(\PDO::FETCH_NUM)) $totalJson .= $row[0];
            $totalRecords = count(json_decode($totalJson, true) ?? []);
    
            // Total con filtro
            $totalFiltrado = $totalRecords;
            if (!empty($search)) {
                $filtStmt = $pdo->prepare("SELECT COUNT(*) as total FROM vw_empleados_sin_foto {$where}");
                $filtStmt->execute($params);
                $totalFiltrado = (int)$filtStmt->fetch()['total'];
            }
    
            // Datos paginados con FOR JSON
            $sqlPag = "
                SELECT * FROM (
                    SELECT *, ROW_NUMBER() OVER (ORDER BY codigoempleado) AS rn
                    FROM vw_empleados_sin_foto
                    {$where}
                ) sub
                WHERE rn BETWEEN ? AND ?
                FOR JSON PATH
            ";
            $params[] = $start + 1;
            $params[] = $start + $length;
    
            $stmt = $pdo->prepare($sqlPag);
            $stmt->execute($params);
    
            $json = '';
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) $json .= $row[0];
            $data = json_decode($json, true) ?? [];
    
            return [
                'total'    => $totalRecords,
                'filtered' => $totalFiltrado,
                'data'     => $data
            ];
    
        } catch (\Throwable $e) {
            log_message('error', '[COMPAC PDO PAGINADO]: ' . $e->getMessage());
            throw $e;
        } finally {
            $pdo = null;
        }
    }


    public function getEmpleadoPorCodigo(array $registro, string $codigo): ?array {
        $host     = $registro['host'] ?? '127.0.0.1';
        $puerto   = $registro['port'] ?? 1433;
        $database = $registro['database'] ?? '';
        $user     = $registro['user'] ?? '';
        $password = $registro['password'] ?? '';
    
        $dsn = "sqlsrv:Server={$host},{$puerto};Database={$database};"
             . "LoginTimeout=5;ConnectionPooling=1;TrustServerCertificate=1;"
             . "MultipleActiveResultSets=false";
    
        try {
            $pdo = new \PDO($dsn, $user, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::SQLSRV_ATTR_ENCODING, \PDO::SQLSRV_ENCODING_UTF8);
    
            // Traer datos sin foto primero con FOR JSON
            $stmt = $pdo->prepare("
                SELECT
                     a.idempleado
                    ,a.iddepartamento
                    ,a.idpuesto
                    ,a.nombre
                    ,a.apellidopaterno
                    ,a.apellidomaterno
                    ,a.codigoempleado
                    ,a.nombrelargo
                    ,b.descripcion AS puesto
                    ,a.CorreoElectronico
                    ,c.localidad
                FROM nom10001 a
                INNER JOIN nom10006 b ON a.idpuesto = b.idpuesto
                CROSS JOIN nom10000 c
                WHERE a.codigoempleado = ?
                FOR JSON PATH
            ");
            $stmt->execute([$codigo]);
    
            $json = '';
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) $json .= $row[0];
            $data = json_decode($json, true) ?? [];
    
            if (empty($data)) return null;
    
            $empleado = $data[0];
    
            // Traer foto por separado en query independiente
            $stmtFoto = $pdo->prepare("
                SELECT fotografia FROM nom10001 WHERE codigoempleado = ?
            ");
            $stmtFoto->execute([$codigo]);
            $fotoRow = $stmtFoto->fetch(\PDO::FETCH_ASSOC);
            $empleado['fotografia'] = $fotoRow['fotografia'] ?? null;
    
            return $empleado;
    
        } catch (\Throwable $e) {
            log_message('error', '[COMPAC getEmpleadoPorCodigo]: ' . $e->getMessage());
            throw $e;
        } finally {
            $pdo = null;
        }
    }
}