<?php

namespace julio101290\boilerplatecompac\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmpleadoFotosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_empleado' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => false], // O INT si manejas IDs numéricos puros
            'rutaFoto'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('id_empleado'); // Índice secundario para agilizar consultas por empleado
        $this->forge->createTable('empleado_fotos', true);
    }

    public function down()
    {
        $this->forge->dropTable('empleado_fotos', true);
    }
}