<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTasksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
        
        'description' => [
            'type' => 'LONGTEXT',
           // 'constraint' => '100',
            'null' => true
        ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'default' => 'pending'
            ],
            'due_date' => [
                'type' => 'DATE',
               // 'constraint' => '100',
                'null' => true
            ],
            // Add other fields as needed
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('tasks');
    }

    public function down()
    {
        $this->forge->dropTable('tasks');
    }
}
