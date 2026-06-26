<?php

namespace julio101290\boilerplatecompac\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDatabaseToCompacDB extends Migration
{
    public function up()
    {
        $fields = [
            'database' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
                'after'      => 'password', // Lo posiciona de manera organizada
            ],
        ];

        $this->forge->addColumn('compacDB', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('compacDB', 'database');
    }
}