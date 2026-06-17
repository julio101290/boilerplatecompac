<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLogTableAddModuleFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('log', [
            'module' => [
                'type'       => 'VARCHAR',
                'constraint' => 8,
                'null'       => true,
            ],
            'idMovModule' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('log', 'module');
        $this->forge->dropColumn('log', 'idMovModule');
    }
}