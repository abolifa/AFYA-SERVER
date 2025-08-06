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
use App\Models\Supplier;
use App\Models\TransferInvoice;
use App\Models\TransferInvoiceItem;
use App\Models\User;
use App\Models\Vital;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create some centers first
        Center::factory()->count(5)->create();

        // Helpers to pull existing IDs
        $randCenter = fn() => Center::inRandomOrder()->first()->id;
        $randPatient = fn() => Patient::inRandomOrder()->first()->id;
        $randDoctor = fn() => User::where('is_doctor', true)->inRandomOrder()->first()->id;
        $randProduct = fn() => Product::inRandomOrder()->first()->id;
        $randSupplier = fn() => Supplier::inRandomOrder()->first()->id;

        // 2. Create your admin user (override center_id)
        User::factory()
            ->state(fn() => ['center_id' => $randCenter()])
            ->create([
                'name' => 'Abdurahman',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('091091'),
                'can_see_other_records' => true,
            ]);

        // 3. Create more users (override center_id on each)
        User::factory()
            ->count(20)
            ->state(fn() => ['center_id' => $randCenter()])
            ->create();

        // 4. Create patients (override center_id on each)
        Patient::factory()
            ->count(50)
            ->state(fn() => ['center_id' => $randCenter()])
            ->create();

        // 5. Products & Suppliers have no closure-fields, so they’re safe
        Product::factory(30)->create();
        Supplier::factory(10)->create();

        // 6. Appointments (override all three fk’s)
        Appointment::factory(100)
            ->state(fn() => [
                'center_id' => $randCenter(),
                'patient_id' => $randPatient(),
                'doctor_id' => $randDoctor(),
            ])
            ->create();

        // 7. Orders + OrderItems
        Order::factory(80)
            ->state(fn() => [
                'center_id' => $randCenter(),
                'patient_id' => $randPatient(),
            ])
            ->create()
            ->each(function (Order $order) use ($randProduct) {
                OrderItem::factory(rand(1, 5))
                    ->state(fn() => [
                        'order_id' => $order->id,
                        'product_id' => $randProduct(),
                    ])
                    ->create();
            });

        // 8. Invoices + InvoiceItems
        Invoice::factory(40)
            ->state(fn() => [
                'center_id' => $randCenter(),
                'supplier_id' => $randSupplier(),
            ])
            ->create()
            ->each(function (Invoice $inv) use ($randProduct) {
                InvoiceItem::factory(rand(1, 4))
                    ->state(fn() => [
                        'invoice_id' => $inv->id,
                        'product_id' => $randProduct(),
                    ])
                    ->create();
            });

        // 9. TransferInvoices + TransferInvoiceItems
        TransferInvoice::factory(20)
            ->state(fn() => [
                'from_center_id' => $randCenter(),
                'to_center_id' => $randCenter(),
            ])
            ->create()
            ->each(function (TransferInvoice $ti) use ($randProduct) {
                TransferInvoiceItem::factory(rand(1, 3))
                    ->state(fn() => [
                        'transfer_invoice_id' => $ti->id,
                        'product_id' => $randProduct(),
                    ])
                    ->create();
            });

        // 10. Vitals for a subset of patients
        Patient::inRandomOrder()->take(30)->each(function (Patient $patient) {
            Vital::factory(rand(1, 3))
                ->state(['patient_id' => $patient->id])
                ->create();
        });
    }
}
