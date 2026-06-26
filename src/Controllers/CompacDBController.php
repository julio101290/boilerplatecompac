<?php

namespace julio101290\boilerplatecompac\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatecompanies\Models\EmpresasModel;

class CompacDBController extends BaseController {

    use ResponseTrait;

    protected $log;
    protected $compacDB;
    protected $empresa;

    public function __construct() {
        $this->compacDB = new \julio101290\boilerplatecompac\Models\CompacDBModel();
        $this->log = new LogModel();
        $this->empresa = new EmpresasModel();
        helper(['menu', 'utilerias']);
    }

    /**
     * Muestra la vista principal y procesa solicitudes AJAX para DataTables
     */
    public function index() {
        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");

        if ($this->request->isAJAX()) {
            $request = service('request');

            $draw = (int) $request->getGet('draw');
            $start = (int) $request->getGet('start');
            $length = (int) $request->getGet('length');
            $searchValue = $request->getGet('search')['value'] ?? '';
            $orderColumnIndex = (int) $request->getGet('order')[0]['column'] ?? 0;
            $orderDir = $request->getGet('order')[0]['dir'] ?? 'asc';

            $fields = $this->compacDB->allowedFields;
            $orderField = $fields[$orderColumnIndex] ?? 'id';

            $builder = $this->compacDB->mdlGetCompacDB($empresasID);

            $total = clone $builder;
            $recordsTotal = $total->countAllResults(false);

            if (!empty($searchValue)) {
                $builder->groupStart();
                foreach ($fields as $field) {
                    $builder->orLike("a." . $field, $searchValue);
                }
                $builder->groupEnd();
            }

            $filteredBuilder = clone $builder;
            $recordsFiltered = $filteredBuilder->countAllResults(false);

            $data = $builder->orderBy("a." . $orderField, $orderDir)
                    ->get($length, $start)
                    ->getResultArray();

            return $this->response->setJSON([
                        'draw' => $draw,
                        'recordsTotal' => $recordsTotal,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $data,
            ]);
        }

        $titulos["title"] = lang('compacDB.title');
        $titulos["subtitle"] = lang('compacDB.subtitle');
        return view('julio101290\boilerplatecompac\Views\compacDB', $titulos);
    }

    /**
     * Obtiene un registro específico para edición (vía AJAX)
     */
    public function getCompacDB() {
        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");

        $idCompacDB = $this->request->getPost("idCompacDB");
        $dato = $this->compacDB->whereIn('idEmpresa', $empresasID)
                ->where('id', $idCompacDB)
                ->first();

        return $this->response->setJSON($dato);
    }

    /**
     * Guarda o actualiza un registro (con protección CSRF automática)
     */
    public function save() {
        helper('auth');

        $userName = user()->username;
        $datos = $this->request->getPost();

        // Eliminar campos de timestamp para evitar conflictos
        unset($datos['created_at'], $datos['updated_at'], $datos['deleted_at']);

        $idKey = $datos["idCompacDB"] ?? 0;

        if ($idKey == 0) {
            try {
                if (!$this->compacDB->save($datos)) {
                    $errores = implode(" ", $this->compacDB->errors());
                    return $this->respond([
                                'status' => 400,
                                'message' => $errores,
                                'csrf_hash' => csrf_hash()
                                    ], 400);
                }
                $this->log->save([
                    "description" => lang("compacDB.logDescription") . json_encode($datos),
                    "user" => $userName
                ]);
                return $this->respond([
                            'status' => 201,
                            'message' => 'Guardado correctamente',
                            'csrf_hash' => csrf_hash()
                                ], 201);
            } catch (\Throwable $ex) {
                return $this->respond([
                            'status' => 500,
                            'message' => 'Error al guardar: ' . $ex->getMessage(),
                            'csrf_hash' => csrf_hash()
                                ], 500);
            }
        } else {
            if (!$this->compacDB->update($idKey, $datos)) {
                $errores = implode(" ", $this->compacDB->errors());
                return $this->respond([
                            'status' => 400,
                            'message' => $errores,
                            'csrf_hash' => csrf_hash()
                                ], 400);
            }
            $this->log->save([
                "description" => lang("compacDB.logUpdated") . json_encode($datos),
                "user" => $userName
            ]);
            return $this->respond([
                        'status' => 200,
                        'message' => 'Actualizado correctamente',
                        'csrf_hash' => csrf_hash()
                            ], 200);
        }
    }

    /**
     * Elimina un registro (soft delete)
     */
    public function delete($id) {
        helper('auth');

        $userName = user()->username;
        $registro = $this->compacDB->find($id);

        if (!$this->compacDB->delete($id)) {
            return $this->respond([
                        'status' => 404,
                        'message' => lang("compacDB.msg.msg_get_fail"),
                        'csrf_hash' => csrf_hash()
                            ], 404);
        }

        $this->compacDB->purgeDeleted();
        $this->log->save([
            "description" => lang("compacDB.logDeleted") . json_encode($registro),
            "user" => $userName
        ]);

        return $this->respondDeleted([
                    'data' => $registro,
                    'message' => lang("compacDB.msg_delete"),
                    'csrf_hash' => csrf_hash()
        ]);
    }

    public function testConnection() {
        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");

        $idCompacDB = $this->request->getPost('idCompacDB');

        if (empty($idCompacDB)) {
            return $this->respond([
                        'status' => 400,
                        'message' => 'Falta el ID del registro.',
                        'csrf_hash' => csrf_hash()
                            ], 400);
        }

        $registro = $this->compacDB->whereIn('idEmpresa', $empresasID)
                ->where('id', $idCompacDB)
                ->first();

        if (!$registro) {
            return $this->respond([
                        'status' => 404,
                        'message' => 'Registro no encontrado.',
                        'csrf_hash' => csrf_hash()
                            ], 404);
        }

        try {
            // Ajusta estos nombres de campos según tu tabla real
            $config = [
                'DBDriver' => 'SQLSRV',
                'hostname' => $registro['host'] ?? '',
                'database' => $registro['database'] ?? '',
                'username' => $registro['user'] ?? '',
                'password' => $registro['password'] ?? '',
                'port' => $registro['port'] ?? 1433,
            ];

            if (empty($config['hostname']) || empty($config['database'])) {
                return $this->respond([
                            'status' => 400,
                            'message' => 'Faltan datos de conexión: servidor o base de datos.',
                            'csrf_hash' => csrf_hash()
                                ], 400);
            }

            $db = \Config\Database::connect($config, false);

            $query = $db->query('SELECT 1 AS test_connection');
            $resultado = $query ? $query->getRowArray() : null;

            $db->close();

            if ($resultado && isset($resultado['test_connection'])) {
                return $this->respond([
                            'status' => 200,
                            'message' => 'Conexión correcta a SQL Server.',
                            'data' => $resultado,
                            'csrf_hash' => csrf_hash()
                                ], 200);
            }

            return $this->respond([
                        'status' => 500,
                        'message' => 'Se conectó, pero no se pudo validar la consulta de prueba.',
                        'csrf_hash' => csrf_hash()
                            ], 500);
        } catch (\Throwable $e) {
            return $this->respond([
                        'status' => 500,
                        'message' => 'Error de conexión: ' . $e->getMessage(),
                        'csrf_hash' => csrf_hash()
                            ], 500);
        }
    }
}
