<?php

namespace julio101290\boilerplatecompac\Database\Migrations;

use CodeIgniter\Database\Migration;

class CompacDB extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'idEmpresa' => ['type' => 'int', 'constraint' => 11, 'null' => true],
            'host' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'user' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'password' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'port' => ['type' => 'int', 'constraint' => 32, 'null' => true],

            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('compacDB', true);
    }

    public function down()
    {
        $this->forge->dropTable('compacDB', true);
    }
}