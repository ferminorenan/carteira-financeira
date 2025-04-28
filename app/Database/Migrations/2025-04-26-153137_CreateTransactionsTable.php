<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionsTable extends Migration
{

    public function up()
    {
        $this->db->query("CREATE TYPE transaction_type AS ENUM ('deposit', 'transfer', 'reversal');");
        $this->db->query("CREATE TYPE transaction_status AS ENUM ('completed', 'pending', 'failed', 'reversed_by_receiver', 'reversed_by_sender');");
        // ---------------------------------------------

        $this->forge->addField([
            'id' => [
                'type'           => 'SERIAL',
                'null'           => false,
            ],
            'sender_id' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'receiver_id' => [
                'type'       => 'INT',
                'null'       => false,
            ],
            'type' => [
                'type'       => 'transaction_type',
                'null'       => false,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
            'status' => [
                'type'       => 'transaction_status',
                'default'    => 'pending',
                'null'       => false,
            ],
            'reference_transaction_id' => [
                'type'       => 'INT',
                'null'       => true,
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id', 'pk_transactions');
        $this->forge->addForeignKey('sender_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('receiver_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('reference_transaction_id', 'transactions', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('transactions', true);
    }

    public function down()
    {
        $this->forge->dropTable('transactions', true);

        $this->db->query("DROP TYPE IF EXISTS transaction_type CASCADE;");
        $this->db->query("DROP TYPE IF EXISTS transaction_status CASCADE;");
    }
}
