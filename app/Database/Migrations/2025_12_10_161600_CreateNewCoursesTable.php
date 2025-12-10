<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNewCoursesTable extends Migration
{
    public function up()
    {
        // First, drop the table if it exists
        $this->forge->dropTable('courses', true);

        // Now create the table with all fields
        $this->forge->addField([
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'course_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'default' => 'DEFAULT_CODE',
            ],
            'course_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'cn_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'schedule_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'schedule_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'room' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'course_instructor' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);

        $this->forge->addPrimaryKey('course_id');
        $this->forge->addForeignKey('course_instructor', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('courses', true); // The second parameter adds IF NOT EXISTS
    }

    public function down()
    {
        $this->forge->dropTable('courses', true);
    }
}