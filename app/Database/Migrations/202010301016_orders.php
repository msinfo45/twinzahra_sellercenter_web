<?php 
namespace App\Database\Migrations;

class Orders extends \CodeIgniter\Database\Migration{

    public function up(){
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ],
            'order_id' =>[
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'order_number' => [
                'type' => 'TEXT',
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'marketplace' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'branch_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'warehouse_code' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'customer_first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'customer_last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'price' => [
                'type' => 'INT',
                'constraint' => 20,
             ],
            'items_count' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
             ],
            'voucher' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'voucher_code' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
             ],
            'voucher_platform' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'voucher_seller' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],

            'gift_option' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
           ],
            'gift_message' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
             ],
            'shipping_fee' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'shipping_fee_discount_seller' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
             ],
            'shipping_fee_discount_platform' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'promised_shipping_times' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],

            'national_registration_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'tax_code' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'extra_attributes' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'remarks' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
             'delivery_info' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
             'statuses' => [
                'type' => 'INT',
                'constraint' => 2,
            ],
            'created_at' =>[
                'type' => 'DATETIME',
            ],
            'updated_at' =>[
                'type' => 'DATETIME',
            ]
        ]);

        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
    }
}