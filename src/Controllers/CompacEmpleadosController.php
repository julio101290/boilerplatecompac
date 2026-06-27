<?php

namespace julio101290\boilerplatecompac\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatecompanies\Models\EmpresasModel;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatecompac\Models\CompacDBModel;
use julio101290\boilerplatecompac\Models\CompacEmpleadoModel;
use julio101290\boilerplatecompac\Models\EmpleadoFotosModel;

class CompacEmpleadosController extends BaseController {

    use ResponseTrait;

    protected $log;
    protected $compacDB;
    protected $empresa;
    protected $empleado;
    protected $empleadoFotosMdl;

    public function __construct() {
        $this->compacDB = new CompacDBModel();
        $this->empresa = new EmpresasModel();
        $this->log = new LogModel();
        $this->empleado = new CompacEmpleadoModel();
        $this->empleadoFotosMdl = new EmpleadoFotosModel();

        helper(['menu', 'utilerias']);
    }

    public function index() {
        helper('auth');
        $idUser = user()->id;

        $empresas = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($empresas) === 0 ? [0] : array_column($empresas, 'id');

        $titulos["empresas"] = $empresas;
        $titulos["databases"] = $this->compacDB->whereIn('idEmpresa', $empresasID)->findAll();

        $titulos["title"] = lang('compacDB.title') ?? 'Empleados COMPAC';
        $titulos["subtitle"] = lang('compacDB.subtitle') ?? 'Consulta de empleados';

        return view('julio101290\boilerplatecompac\Views\empleadosCompac', $titulos);
    }

    public function getEmpleados() {
        helper('auth');
        $idUser = user()->id;
        $empresas = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($empresas) === 0 ? [0] : array_column($empresas, 'id');

        $idCompacDB = (int) $this->request->getPost('idCompacDB');

        if ($idCompacDB <= 0) {
            return $this->respond(['status' => 400, 'message' => 'Falta el ID de la base de datos.', 'csrf_hash' => csrf_hash()], 400);
        }

        $registro = $this->compacDB->whereIn('idEmpresa', $empresasID)->where('id', $idCompacDB)->first();

        if (!$registro) {
            return $this->respond(['status' => 404, 'message' => 'No se encontró la base de datos.', 'csrf_hash' => csrf_hash()], 404);
        }

        try {
            $empleados = $this->empleado->getEmpleadosPorConexion($registro);

            // REMOVER O LIMPIAR LOS BINARIOS ANTES DE PASAR AL PARSER UTF-8
            foreach ($empleados as $key => $emp) {
                if (isset($emp['fotografia']))
                    unset($empleados[$key]['fotografia']);
                if (isset($emp['Fotografia']))
                    unset($empleados[$key]['Fotografia']);
            }

            $empleadosLimpios = $this->utf8EncodeRecursive($empleados);

            return $this->respond([
                        'status' => 200,
                        'message' => 'Empleados obtenidos correctamente.',
                        'data' => $empleadosLimpios,
                        'csrf_hash' => csrf_hash()
                            ], 200);
        } catch (\Throwable $e) {
            return $this->respond(['status' => 500, 'message' => 'Error: ' . $e->getMessage(), 'csrf_hash' => csrf_hash()], 500);
        }
    }
    public function getCredencialConfig() {
        helper('auth');
        $idUser     = user()->id;
        $empresas   = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($empresas) === 0 ? [0] : array_column($empresas, 'id');
    
        $idCompacDB     = (int) $this->request->getPost('idCompacDB');
        $codigoEmpleado = $this->request->getPost('codigoempleado');
    
        $registro = $this->compacDB->whereIn('idEmpresa', $empresasID)->where('id', $idCompacDB)->first();
        if (!$registro) {
            return $this->respond(['status' => 404, 'message' => 'Base de datos inválida.'], 404);
        }
    
        try {
            $emp = $this->empleado->getEmpleadoPorCodigo($registro, $codigoEmpleado);
    
            if (!$emp) {
                return $this->respond(['status' => 404, 'message' => 'Empleado no encontrado.'], 404);
            }
    
            $rawFoto = $emp['fotografia'] ?? null;
            unset($emp['fotografia']);
            $emp = $this->utf8EncodeRecursive($emp);
    
            // Prioridad: foto local > foto CONTPAQi
            $photoSrc  = "";
            $fotoLocal = $this->empleadoFotosMdl
                ->where('idCompacDB', $idCompacDB)
                ->where('id_empleado', $codigoEmpleado)
                ->first();
    
            if ($fotoLocal && !empty($fotoLocal['rutaFoto'])) {
                $realPath = WRITEPATH . str_replace('uploads/', '', $fotoLocal['rutaFoto']);
                if (file_exists($realPath)) {
                    $photoSrc = "data:" . mime_content_type($realPath) . ";base64," . base64_encode(file_get_contents($realPath));
                }
            }
    
            if (empty($photoSrc) && !empty($rawFoto)) {
                $photoSrc = $this->convertBmpToPngBase64($rawFoto);
            }
    
            $configuracionCredencial = [
                "st"       => [
                    "nombre" => ["b" => true, "i" => false, "u" => false],
                    "puesto" => ["b" => true, "i" => false, "u" => false],
                    "id"     => ["b" => true, "i" => false, "u" => false]
                ],
                "f-nombre"  => $emp['nombrelargo'] ?? '',
                "f-puesto"  => $emp['puesto'] ?? 'GENERAL',
                "f-id"      => $codigoEmpleado,
                "f-correo"  => $emp['CorreoElectronico'] ?? 'S/C',
                "fs-nombre" => "10", "fc-nombre" => "#ffffff",
                "fs-puesto" => "8",  "fc-puesto" => "#dddddd",
                "fs-id"     => "12", "fc-id"     => "#cc1111",
                "f-qr"      => $codigoEmpleado,
                "f-scan"    => "ESCÁNÉAME", "fs-scan" => "7", "fc-scan" => "#cc1111",
                "f-pie-r"   => "PROPIEDAD DE CONSTRUCTORA GUSA SA DE CV",
                "fs-pie-r"  => "6", "fc-pie-r" => "#ffffff", "fbg-pie-r" => "#cc1111",
                "photo-w"   => "97", "photo-h" => "130", "photo-y" => "66",
                "qr-size"   => "100", "qr-y" => "54",
                "photo-src" => $photoSrc
            ];
    
            return $this->respond(['status' => 200, 'config' => $configuracionCredencial, 'csrf_hash' => csrf_hash()]);
    
        } catch (\Throwable $e) {
            return $this->respond(['status' => 500, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function credencialGenerator() {
        helper('auth');
        $idUser     = user()->id;
        $empresas   = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($empresas) === 0 ? [0] : array_column($empresas, 'id');
    
        $idCompacDB     = (int) $this->request->getGet('idCompacDB');
        $codigoEmpleado = $this->request->getGet('codigoempleado');
    
        if (!$idCompacDB || !$codigoEmpleado) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Faltan parámetros.');
        }
    
        $registro = $this->compacDB->whereIn('idEmpresa', $empresasID)->where('id', $idCompacDB)->first();
        if (!$registro) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Base de datos no encontrada.');
        }
    
        // Datos de empresa
        $datosEmpresaObj = null;
        $datosEmpresa    = null;
        if (!empty($registro['idEmpresa'])) {
            $datosEmpresaObj = $this->empresa->where("id", $registro['idEmpresa'])->asObject()->first();
            if ($datosEmpresaObj) $datosEmpresa = (array) $datosEmpresaObj;
        }
    
        // Localidades — solo de la conexión actual, no loop de todas
        $ciudadesConcatenadas = 'LOS MOCHIS | LOS CABOS | TIJUANA | MAZATLAN';
        try {
            $localidad = $this->empleado->getLocalidadPorConexion($registro);
            if (!empty($localidad)) $ciudadesConcatenadas = mb_strtoupper($localidad);
        } catch (\Throwable $e) {
            log_message('error', 'Error localidad: ' . $e->getMessage());
        }
    
        try {
            // Un solo empleado, query directo por código
            $emp = $this->empleado->getEmpleadoPorCodigo($registro, $codigoEmpleado);
    
            if (!$emp) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Empleado no encontrado.');
            }
    
            $rawFoto = $emp['fotografia'] ?? null;
            unset($emp['fotografia']);
            $emp = $this->utf8EncodeRecursive($emp);
    
            // Foto local tiene prioridad
            $photoSrc  = "";
            $fotoLocal = $this->empleadoFotosMdl
                ->where('idCompacDB', $idCompacDB)
                ->where('id_empleado', $codigoEmpleado)
                ->first();
    
            if ($fotoLocal && !empty($fotoLocal['rutaFoto'])) {
                $realPath = WRITEPATH . $fotoLocal['rutaFoto'];
                if (file_exists($realPath)) {
                    $photoSrc = "data:" . mime_content_type($realPath) . ";base64," . base64_encode(file_get_contents($realPath));
                }
            }
    
            if (empty($photoSrc) && !empty($rawFoto)) {
                $photoSrc = $this->convertBmpToPngBase64($rawFoto);
            }
    
            $nombrelargo = trim(($emp['nombre'] ?? '') . ' ' . ($emp['apellidopaterno'] ?? '') . ' ' . ($emp['apellidomaterno'] ?? ''));
    
            $configBase = [
                "st"     => [
                    "nombre"   => ["b" => true,  "i" => false, "u" => false],
                    "puesto"   => ["b" => true,  "i" => false, "u" => false],
                    "depto-f"  => ["b" => true,  "i" => false, "u" => false],
                    "id"       => ["b" => true,  "i" => false, "u" => false],
                    "ciudades" => ["b" => false, "i" => false, "u" => false],
                    "id-r"     => ["b" => true,  "i" => false, "u" => false],
                    "depto-r"  => ["b" => true,  "i" => false, "u" => false],
                    "tel"      => ["b" => true,  "i" => false, "u" => false],
                    "correo"   => ["b" => true,  "i" => false, "u" => false],
                    "centro"   => ["b" => true,  "i" => false, "u" => false],
                    "vigencia" => ["b" => false, "i" => false, "u" => false],
                    "scan"     => ["b" => true,  "i" => false, "u" => false],
                    "scan-sub" => ["b" => false, "i" => false, "u" => false],
                    "pie-r"    => ["b" => true,  "i" => false, "u" => false],
                ],
                "fields" => [
                    "f-nombre"     => $nombrelargo,
                    "fs-nombre"    => "12", "fc-nombre" => "#000000", "fy-nombre" => "204",
                    "f-puesto"     => $emp['puesto'] ?? '',
                    "fs-puesto"    => "8",  "fc-puesto" => "#cc1111", "fy-puesto" => "237",
                    "f-depto-f"    => $emp['departamento'] ?? 'SISTEMAS',
                    "fs-depto-f"   => "7",  "fc-depto-f" => "#000000", "fy-depto-f" => "250",
                    "f-id"         => $codigoEmpleado,
                    "fs-id"        => "9",  "fc-id" => "#ffffff", "fy-id" => "263",
                    "f-ciudades"   => $ciudadesConcatenadas,
                    "fs-ciudades"  => "5",  "fc-ciudades" => "#aaaaaa",
                    "f-id-r"       => " " . $codigoEmpleado,
                    "fs-id-r"      => "6",  "fc-id-r" => "#000000",
                    "f-depto-r"    => " " . ($emp['departamento'] ?? 'SISTEMAS'),
                    "fs-depto-r"   => "6",  "fc-depto-r" => "#000000",
                    "f-tel"        => " 668 1 361423",
                    "fs-tel"       => "6",  "fc-tel" => "#000000",
                    "f-correo"     => " " . ($emp['CorreoElectronico'] ?? 'S/C'),
                    "fs-correo"    => "6",  "fc-correo" => "#000000",
                    "f-centro"     => " LOS MOCHIS, SIN.",
                    "fs-centro"    => "6",  "fc-centro" => "#000000",
                    "f-vigencia"   => " 31/12/2026",
                    "fs-vigencia"  => "6",  "fc-vigencia" => "#000000",
                    "f-qr"         => 'https://servidorgisa.dyndns.org:4431/gusa2025/admin/compacEmpleados/datosGenerator?idCompacDB=1&codigoempleado=' . $codigoEmpleado,
                    "f-scan"       => "ESCÁNÉAME",
                    "fs-scan"      => "7",  "fc-scan" => "#cc1111",
                    "f-scan-sub"   => "Para validar información",
                    "fs-scan-sub"  => "6",  "fc-scan-sub" => "#000000",
                    "f-pie-r"      => "CREDENCIAL OFICIAL",
                    "fs-pie-r"     => "6",  "fc-pie-r" => "#ffffff", "fbg-pie-r" => "#cc1111",
                    "qr-size"      => "100", "qr-y" => "54",
                    "scan-y"       => "158", "datos-y" => "200", "datos-gap" => "4", "perm-y" => "264",
                    "pc-0" => false, "pc-1" => false, "pc-2" => false, "pc-3" => false,
                    "pt-0" => "Acceso Oficinas", "pt-1" => "Acceso Sistema",
                    "pt-2" => "RFID Activo",    "pt-3" => "Seguridad Industrial",
                    "vis-id-r"      => true,  "align-id-r"      => "left",
                    "vis-depto-r"   => true,  "align-depto-r"   => "left",
                    "vis-tel"       => true,  "align-tel"       => "left",
                    "vis-correo"    => true,  "align-correo"    => "left",
                    "vis-centro"    => true,  "align-centro"    => "left",
                    "vis-vigencia"  => false, "align-vigencia"  => "left",
                    "vis-scan"      => true,  "align-scan"      => "center",
                    "vis-scan-sub"  => true,  "align-scan-sub"  => "center",
                    "photo-w"       => "97", "photo-h" => "130", "photo-y" => "66",
                    "photo-src"     => $photoSrc,
                    "f-logo"        => $datosEmpresa['logo'] ?? $datosEmpresaObj->logo ?? ''
                ],
                "customFields" => []
            ];
    
            return view('julio101290\boilerplatecompac\Views\credencial_generator', [
                'config'         => $configBase,
                'datosEmpresa'   => $datosEmpresa,
                'datosEmpresaObj'=> $datosEmpresaObj
            ]);
    
        } catch (\Throwable $e) {
            log_message('error', 'Error en credencialGenerator: ' . $e->getMessage());
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Error al cargar el generador.');
        }
    }
    
    
    
     public function datosGenerator() {

    
        $idCompacDB     = (int) $this->request->getGet('idCompacDB');
        $codigoEmpleado = $this->request->getGet('codigoempleado');
    
        if (!$idCompacDB || !$codigoEmpleado) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Faltan parámetros.');
        }
    
        $registro = $this->compacDB->where('id', $idCompacDB)->first();
        if (!$registro) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Base de datos no encontrada.');
        }
    
        // Datos de empresa
        $datosEmpresaObj = null;
        $datosEmpresa    = null;
        if (!empty($registro['idEmpresa'])) {
            $datosEmpresaObj = $this->empresa->where("id", $registro['idEmpresa'])->asObject()->first();
            if ($datosEmpresaObj) $datosEmpresa = (array) $datosEmpresaObj;
        }
    
        // Localidades — solo de la conexión actual, no loop de todas
        $ciudadesConcatenadas = 'LOS MOCHIS | LOS CABOS | TIJUANA | MAZATLAN';
        try {
            $localidad = $this->empleado->getLocalidadPorConexion($registro);
            if (!empty($localidad)) $ciudadesConcatenadas = mb_strtoupper($localidad);
        } catch (\Throwable $e) {
            log_message('error', 'Error localidad: ' . $e->getMessage());
        }
    
        try {
            // Un solo empleado, query directo por código
            $emp = $this->empleado->getEmpleadoPorCodigo($registro, $codigoEmpleado);
    
            if (!$emp) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Empleado no encontrado.');
            }
    
            $rawFoto = $emp['fotografia'] ?? null;
            unset($emp['fotografia']);
            $emp = $this->utf8EncodeRecursive($emp);
    
            // Foto local tiene prioridad
            $photoSrc  = "";
            $fotoLocal = $this->empleadoFotosMdl
                ->where('idCompacDB', $idCompacDB)
                ->where('id_empleado', $codigoEmpleado)
                ->first();
    
            if ($fotoLocal && !empty($fotoLocal['rutaFoto'])) {
                $realPath = WRITEPATH . $fotoLocal['rutaFoto'];
                if (file_exists($realPath)) {
                    $photoSrc = "data:" . mime_content_type($realPath) . ";base64," . base64_encode(file_get_contents($realPath));
                }
            }
    
            if (empty($photoSrc) && !empty($rawFoto)) {
                $photoSrc = $this->convertBmpToPngBase64($rawFoto);
            }
    
            $nombrelargo = trim(($emp['nombre'] ?? '') . ' ' . ($emp['apellidopaterno'] ?? '') . ' ' . ($emp['apellidomaterno'] ?? ''));
    
            $configBase = [
                "st"     => [
                    "nombre"   => ["b" => true,  "i" => false, "u" => false],
                    "puesto"   => ["b" => true,  "i" => false, "u" => false],
                    "depto-f"  => ["b" => true,  "i" => false, "u" => false],
                    "id"       => ["b" => true,  "i" => false, "u" => false],
                    "ciudades" => ["b" => false, "i" => false, "u" => false],
                    "id-r"     => ["b" => true,  "i" => false, "u" => false],
                    "depto-r"  => ["b" => true,  "i" => false, "u" => false],
                    "tel"      => ["b" => true,  "i" => false, "u" => false],
                    "correo"   => ["b" => true,  "i" => false, "u" => false],
                    "centro"   => ["b" => true,  "i" => false, "u" => false],
                    "vigencia" => ["b" => false, "i" => false, "u" => false],
                    "scan"     => ["b" => true,  "i" => false, "u" => false],
                    "scan-sub" => ["b" => false, "i" => false, "u" => false],
                    "pie-r"    => ["b" => true,  "i" => false, "u" => false],
                ],
                "fields" => [
                    "f-nombre"     => $nombrelargo,
                    "fs-nombre"    => "12", "fc-nombre" => "#000000", "fy-nombre" => "204",
                    "f-puesto"     => $emp['puesto'] ?? '',
                    "fs-puesto"    => "8",  "fc-puesto" => "#cc1111", "fy-puesto" => "237",
                    "f-depto-f"    => $emp['departamento'] ?? 'SISTEMAS',
                    "fs-depto-f"   => "7",  "fc-depto-f" => "#000000", "fy-depto-f" => "250",
                    "f-id"         => $codigoEmpleado,
                    "fs-id"        => "9",  "fc-id" => "#ffffff", "fy-id" => "263",
                    "f-ciudades"   => $ciudadesConcatenadas,
                    "fs-ciudades"  => "5",  "fc-ciudades" => "#aaaaaa",
                    "f-id-r"       => " " . $codigoEmpleado,
                    "fs-id-r"      => "6",  "fc-id-r" => "#000000",
                    "f-depto-r"    => " " . ($emp['departamento'] ?? 'SISTEMAS'),
                    "fs-depto-r"   => "6",  "fc-depto-r" => "#000000",
                    "f-tel"        => " 668 1 361423",
                    "fs-tel"       => "6",  "fc-tel" => "#000000",
                    "f-correo"     => " " . ($emp['CorreoElectronico'] ?? 'S/C'),
                    "fs-correo"    => "6",  "fc-correo" => "#000000",
                    "f-centro"     => " LOS MOCHIS, SIN.",
                    "fs-centro"    => "6",  "fc-centro" => "#000000",
                    "f-vigencia"   => " 31/12/2026",
                    "fs-vigencia"  => "6",  "fc-vigencia" => "#000000",
                    "f-qr"         => 'https://servidorgisa.dyndns.org:4431/gusa2025/admin/compacEmpleados/datosGenerator?idCompacDB=1&codigoempleado=' . $codigoEmpleado,
                    "f-scan"       => "ESCÁNÉAME",
                    "fs-scan"      => "7",  "fc-scan" => "#cc1111",
                    "f-scan-sub"   => "Para validar información",
                    "fs-scan-sub"  => "6",  "fc-scan-sub" => "#000000",
                    "f-pie-r"      => "CREDENCIAL OFICIAL",
                    "fs-pie-r"     => "6",  "fc-pie-r" => "#ffffff", "fbg-pie-r" => "#cc1111",
                    "qr-size"      => "100", "qr-y" => "54",
                    "scan-y"       => "158", "datos-y" => "200", "datos-gap" => "4", "perm-y" => "264",
                    "pc-0" => false, "pc-1" => false, "pc-2" => false, "pc-3" => false,
                    "pt-0" => "Acceso Oficinas", "pt-1" => "Acceso Sistema",
                    "pt-2" => "RFID Activo",    "pt-3" => "Seguridad Industrial",
                    "vis-id-r"      => true,  "align-id-r"      => "left",
                    "vis-depto-r"   => true,  "align-depto-r"   => "left",
                    "vis-tel"       => true,  "align-tel"       => "left",
                    "vis-correo"    => true,  "align-correo"    => "left",
                    "vis-centro"    => true,  "align-centro"    => "left",
                    "vis-vigencia"  => false, "align-vigencia"  => "left",
                    "vis-scan"      => true,  "align-scan"      => "center",
                    "vis-scan-sub"  => true,  "align-scan-sub"  => "center",
                    "photo-w"       => "97", "photo-h" => "130", "photo-y" => "66",
                    "photo-src"     => $photoSrc,
                    "f-logo"        => $datosEmpresa['logo'] ?? $datosEmpresaObj->logo ?? ''
                ],
                "customFields" => []
            ];
    
            return view('julio101290\boilerplatecompac\Views\datos_generator', [
                'config'         => $configBase,
                'datosEmpresa'   => $datosEmpresa,
                'datosEmpresaObj'=> $datosEmpresaObj
            ]);
    
        } catch (\Throwable $e) {
            log_message('error', 'Error en credencialGenerator: ' . $e->getMessage());
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Error al cargar el generador.');
        }
    }
    
    public function getEmpleadosServerSide() {
        helper('auth');
        $idUser   = user()->id;
        $empresas = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($empresas) === 0 ? [0] : array_column($empresas, 'id');
    
        $idCompacDB = (int) $this->request->getPost('idCompacDB');
        $start      = (int) $this->request->getPost('start');
        $length     = (int) $this->request->getPost('length');
        $search     = $this->request->getPost('search')['value'] ?? '';
        $draw       = (int) $this->request->getPost('draw');
    
        if ($idCompacDB <= 0) {
            return $this->respond(['draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        }
    
        $registro = $this->compacDB->whereIn('idEmpresa', $empresasID)->where('id', $idCompacDB)->first();
        if (!$registro) {
            return $this->respond(['draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        }
    
        try {
            $resultado = $this->empleado->getEmpleadosPaginado($registro, $start, $length, $search);
    
            $data = array_map(function($emp) use ($idCompacDB) {
                $codigo = $emp['codigoempleado'] ?? '';
                return [
                    $codigo,
                    $emp['nombrelargo'] ?? '',
                    $emp['puesto'] ?? 'Sin puesto',
                    $emp['CorreoElectronico'] ?? 'Sin correo',
                    $emp['localidad'] ?? 'LOS MOCHIS | LOS CABOS | TIJUANA | MAZATLAN',
                    $codigo // para botones en el JS
                ];
            }, $resultado['data']);
    
            return $this->respond([
                'draw'            => $draw,
                'recordsTotal'    => $resultado['total'],
                'recordsFiltered' => $resultado['filtered'],
                'data'            => $data,
                'csrf_hash'       => csrf_hash()
            ]);
    
        } catch (\Throwable $e) {
            return $this->respond(['draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => [], 'error' => $e->getMessage()]);
        }
    }

   

    private function utf8EncodeRecursive($data) {
        if (is_string($data)) {
            $converted = @mb_convert_encoding($data, 'UTF-8', 'Windows-1252, ISO-8859-1, UTF-8');

            if ($converted === false) {
                $converted = iconv('UTF-8', 'UTF-8//IGNORE', $data);
            }

            $converted = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $converted);

            if (!mb_check_encoding($converted, 'UTF-8')) {
                $converted = mb_convert_encoding($converted, 'UTF-8', 'UTF-8');
            }

            return $converted;
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                // CORRECCIÓN: strtolower($key) capturará tanto 'fotografia' como 'Fotografia'
                if (strtolower($key) === 'fotografia' || is_resource($value)) {
                    continue;
                }
                $data[$key] = $this->utf8EncodeRecursive($value);
            }
        } else if (is_object($data)) {
            foreach ($data as $key => $value) {
                if (strtolower($key) === 'fotografia' || is_resource($value)) {
                    continue;
                }
                $data->$key = $this->utf8EncodeRecursive($value);
            }
        }

        return $data;
    }

    private function convertBmpToPngBase64($binaryData) {
        try {
            if (is_resource($binaryData)) {
                $content = '';
                while (!feof($binaryData)) {
                    $content .= fread($binaryData, 8192);
                }
                $binaryData = $content;
            }

            if (empty($binaryData)) {
                return "";
            }

            $pos = strpos($binaryData, "BM");
            if ($pos !== false && $pos > 0) {
                $binaryData = substr($binaryData, $pos);
            }

            $stream = fopen('php://memory', 'r+');
            fwrite($stream, $binaryData);
            rewind($stream);
            $rawBuffer = stream_get_contents($stream);
            fclose($stream);

            $image = imagecreatefromstring($rawBuffer);

            if ($image !== false) {
                imageinterlace($image, 0);
                imagesavealpha($image, true);

                ob_start();
                imagepng($image, null, 0);
                $pngData = ob_get_clean();
                imagedestroy($image);

                return 'data:image/png;base64,' . base64_encode($pngData);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error procesando BMP limpio de CONTPAQi: ' . $e->getMessage());
        }

        return 'data:image/bmp;base64,' . base64_encode($binaryData);
    }

    public function uploadLocalFoto() {
        $idCompacDB = $this->request->getPost('idCompacDB');
        $idEmpleado = $this->request->getPost('id_empleado');
        $fileFoto = $this->request->getFile('foto');

        if (!$idCompacDB || !$idEmpleado) {
            return $this->respond(['status' => 400, 'message' => 'Faltan parámetros requeridos (idCompacDB o id_empleado).', 'csrf_hash' => csrf_hash()], 400);
        }

        if (!$fileFoto || !$fileFoto->isValid() || $fileFoto->hasMoved()) {
            return $this->respond(['status' => 400, 'message' => 'Archivo de imagen inválido o no recibido.', 'csrf_hash' => csrf_hash()], 400);
        }

        if (!in_array($fileFoto->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'])) {
            return $this->respond(['status' => 400, 'message' => 'El formato del archivo debe ser JPG, PNG o WEBP.', 'csrf_hash' => csrf_hash()], 400);
        }

        try {
            $safeIdEmpleado = preg_replace('/[^A-Za-z0-9_\-]/', '', $idEmpleado);
            $folderPath = WRITEPATH . 'uploads/compac/empleado_' . $safeIdEmpleado . '/';

            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            $newName = $fileFoto->getRandomName();
            $fileFoto->move($folderPath, $newName);

            $relativeRoute = 'uploads/compac/empleado_' . $safeIdEmpleado . '/' . $newName;

            $registroPrevio = $this->empleadoFotosMdl->where('idCompacDB', $idCompacDB)
                    ->where('id_empleado', $idEmpleado)
                    ->first();

            if ($registroPrevio) {
                $oldFile = WRITEPATH . str_replace('uploads/', '', $registroPrevio['rutaFoto']);
                if (!empty($registroPrevio['rutaFoto']) && file_exists($oldFile)) {
                    @unlink($oldFile);
                }

                $this->empleadoFotosMdl->update($registroPrevio['id'], [
                    'rutaFoto' => $relativeRoute
                ]);
            } else {
                $this->empleadoFotosMdl->insert([
                    'idCompacDB' => $idCompacDB,
                    'id_empleado' => $idEmpleado,
                    'rutaFoto' => $relativeRoute
                ]);
            }

            $fullPathString = $folderPath . $newName;
            $base64 = '';
            if (file_exists($fullPathString)) {
                $base64 = 'data:' . mime_content_type($fullPathString) . ';base64,' . base64_encode(file_get_contents($fullPathString));
            }

            return $this->respond([
                        'status' => 200,
                        'message' => 'Fotografía guardada exitosamente de forma local.',
                        'foto_src' => $base64,
                        'csrf_hash' => csrf_hash()
                            ], 200);
        } catch (\Throwable $e) {
            return $this->respond(['status' => 500, 'message' => 'Error al guardar fotografía: ' . $e->getMessage(), 'csrf_hash' => csrf_hash()], 500);
        }
    }

    public function guardarFotoEmpleado() {
        $idCompacDB = $this->request->getPost('idCompacDB');
        $idEmpleado = $this->request->getPost('id_empleado');
        $fotoBase64 = $this->request->getPost('fotoBase64'); // Recibimos el string del canvas

        if (!$idCompacDB || !$idEmpleado || !$fotoBase64) {
            return $this->respond([
                        'status' => 400,
                        'message' => 'Faltan parámetros requeridos (idCompacDB, id_empleado o fotoBase64).',
                        'csrf_hash' => csrf_hash()
                            ], 400);
        }

        try {
            // 1. Sanitizar el ID del empleado para la carpeta
            $safeIdEmpleado = preg_replace('/[^A-Za-z0-9_\-]/', '', $idEmpleado);
            $folderPath = WRITEPATH . 'uploads/compac/empleado_' . $safeIdEmpleado . '/';

            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            // 2. Procesar y decodificar el String Base64
            // El formato suele ser: data:image/jpeg;base64,/9j/4AAQSkZJRg...
            if (!preg_match('/^data:image\/(\w+);base64,/', $fotoBase64, $type)) {
                return $this->respond(['status' => 400, 'message' => 'El formato de los datos de imagen no es válido.', 'csrf_hash' => csrf_hash()], 400);
            }

            $ext = strtolower($type[1]); // jpeg, png, etc.
            if (!in_array($ext, ['jpeg', 'jpg', 'png', 'webp'])) {
                return $this->respond(['status' => 400, 'message' => 'El formato debe ser JPG, PNG o WEBP.', 'csrf_hash' => csrf_hash()], 400);
            }

            // Remover la cabecera del DataURL para dejar solo el binario codificado
            $dataBase64 = substr($fotoBase64, strpos($fotoBase64, ',') + 1);
            $decodedImg = base64_decode($dataBase64);

            if ($decodedImg === false) {
                return $this->respond(['status' => 400, 'message' => 'Fallo la decodificación de la imagen.', 'csrf_hash' => csrf_hash()], 400);
            }

            // Generar nombre aleatorio único
            $newName = bin2hex(random_bytes(16)) . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
            $fullPathString = $folderPath . $newName;

            // Guardar físicamente el archivo en el WRITEPATH
            file_put_contents($fullPathString, $decodedImg);

            $relativeRoute = 'uploads/compac/empleado_' . $safeIdEmpleado . '/' . $newName;

            // 3. Gestión de Base de Datos (Mismo flujo que tenías)
            $registroPrevio = $this->empleadoFotosMdl->where('idCompacDB', $idCompacDB)
                    ->where('id_empleado', $idEmpleado)
                    ->first();

            if ($registroPrevio) {
                $oldFile = WRITEPATH . str_replace('uploads/', '', $registroPrevio['rutaFoto']);
                if (!empty($registroPrevio['rutaFoto']) && file_exists($oldFile)) {
                    @unlink($oldFile);
                }

                $this->empleadoFotosMdl->update($registroPrevio['id'], [
                    'rutaFoto' => $relativeRoute
                ]);
            } else {
                $this->empleadoFotosMdl->insert([
                    'idCompacDB' => $idCompacDB,
                    'id_empleado' => $idEmpleado,
                    'rutaFoto' => $relativeRoute
                ]);
            }

            return $this->respond([
                        'status' => 200,
                        'message' => 'Fotografía guardada exitosamente de forma local.',
                        'foto_src' => $fotoBase64, // Regresamos el mismo base64 para render inmediato
                        'csrf_hash' => csrf_hash()
                            ], 200);
        } catch (\Throwable $e) {
            return $this->respond(['status' => 500, 'message' => 'Error al guardar fotografía: ' . $e->getMessage(), 'csrf_hash' => csrf_hash()], 500);
        }
    }

    public function getLocalFotoBase64() {
        $idCompacDB = $this->request->getPost('idCompacDB');
        $idEmpleado = $this->request->getPost('id_empleado');

        if (!$idCompacDB || !$idEmpleado) {
            return $this->respond(['status' => 400, 'message' => 'Parámetros insuficientes.'], 400);
        }

        $registro = $this->empleadoFotosMdl->where('idCompacDB', $idCompacDB)
                ->where('id_empleado', $idEmpleado)
                ->first();

        if ($registro && !empty($registro['rutaFoto'])) {
            $realPath = WRITEPATH . str_replace('uploads/', '', $registro['rutaFoto']);

            if (file_exists($realPath)) {
                $mimeType = mime_content_type($realPath);
                $base64Data = base64_encode(file_get_contents($realPath));

                return $this->respond([
                            'status' => 200,
                            'exists' => true,
                            'foto_src' => "data:{$mimeType};base64,{$base64Data}",
                            'csrf_hash' => csrf_hash()
                                ], 200);
            }
        }

        return $this->respond([
                    'status' => 200,
                    'exists' => false,
                    'foto_src' => '',
                    'csrf_hash' => csrf_hash()
                        ], 200);
    }
}
