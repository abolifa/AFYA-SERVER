<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Center;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Patient;
use App\Models\Product;
use App\Models\Schedule;
use App\Models\Supplier;
use App\Models\TransferInvoice;
use App\Models\TransferInvoiceItem;
use App\Models\User;
use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Abdurahman',
            'email' => 'admin@gmail.com',
            'password' => '091091',
            'can_see_other_records' => true,
        ]);


        $this->call(ShieldSeeder::class);

        $user = User::where('email', 'admin@gmail.com')->first();
        if ($user) {
            $user->assignRole('super_admin');
        }

        $products = Product::factory(50)->create();
        $suppliers = Supplier::factory(30)->create();

// ✅ Create 100 centers with full structure
        $centers = Center::factory(100)
            ->has(Schedule::factory(7), 'schedules')
            ->create();

        foreach ($centers as $center) {
            // Add users
            User::factory(rand(1, 3))->create(['center_id' => $center->id]);

            // Patients
            $patients = Patient::factory(15)->create(['center_id' => $center->id]);

            // Appointments for patients
            foreach ($patients as $patient) {
                Appointment::factory(rand(2, 5))->create([
                    'center_id' => $center->id,
                    'patient_id' => $patient->id,
                ]);
            }

            // Orders with order items
            foreach ($patients as $patient) {
                $appointment = Appointment::where('patient_id', $patient->id)->inRandomOrder()->first();

                $order = Order::factory()->create([
                    'center_id' => $center->id,
                    'patient_id' => $patient->id,
                    'appointment_id' => $appointment?->id,
                ]);

                OrderItem::factory(rand(1, 3))->create([
                    'order_id' => $order->id,
                    'product_id' => $products->random()->id,
                ]);
            }

            // Invoices
            foreach ($suppliers->random(rand(2, 4)) as $supplier) {
                $invoice = Invoice::factory()->create([
                    'center_id' => $center->id,
                    'supplier_id' => $supplier->id,
                ]);

                InvoiceItem::factory(rand(1, 4))->create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $products->random()->id,
                ]);
            }

            // Transfer invoices to other centers
            $otherCenters = $centers->where('id', '!=', $center->id)->random(2);
            foreach ($otherCenters as $toCenter) {
                $transfer = TransferInvoice::factory()->create([
                    'from_center_id' => $center->id,
                    'to_center_id' => $toCenter->id,
                ]);

                TransferInvoiceItem::factory(rand(1, 3))->create([
                    'transfer_invoice_id' => $transfer->id,
                    'product_id' => $products->random()->id,
                ]);
            }
        }

    }
}
