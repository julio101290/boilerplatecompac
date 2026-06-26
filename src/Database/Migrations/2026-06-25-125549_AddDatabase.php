<?php

namespace julio101290\boilerplatecompac\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdCompacDBToEmpleadoFotos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('empleado_fotos', [
            'idCompacDB' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'id'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('empleado_fotos', 'idCompacDB');
    }
}