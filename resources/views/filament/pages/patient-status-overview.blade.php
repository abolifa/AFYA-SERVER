<x-filament-panels::page>
    <x-filament::section icon="fas-user-injured" heading="بيانات المريض">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="flex items-center justify-center p-4 border rounded-lg">
                <img
                    src="{{ $this->patient->image ? asset('storage/' . $this->patient->image) : asset('placeholder.webp') }}"
                    alt="{{ $this->patient->name }}"
                    class="w-32 h-32 rounded-full object-cover"
                />
            </div>

            <div class="p-4 border rounded-lg">
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm leading-6">
                    <dt class="font-semibold">إسم المريض:</dt>
                    <dd>{{ $this->patient->name }}</dd>

                    <dt class="font-semibold">رقم الملف:</dt>
                    <dd>{{ $this->patient->file_number }}</dd>

                    <dt class="font-semibold">الرقم الوطني:</dt>
                    <dd>{{ $this->patient->national_id }}</dd>

                    <dt class="font-semibold">رقم الهاتف:</dt>
                    <dd>{{ $this->patient->phone }}</dd>

                    <dt class="font-semibold">تاريخ الميلاد:</dt>
                    <dd>
                        {{ $this->patient->dob ? $this->patient->dob->format('d/m/Y') : '-' }}
                    </dd>

                    <dt class="font-semibold">الجنس:</dt>
                    <dd>
                        {{ $this->patient->gender === 'male' ? 'ذكر' : 'أنثى' }}
                    </dd>
                </dl>
            </div>

            <!-- Notes -->
            <div class="p-4 border rounded-lg">
                <h3 class="font-semibold text-base mb-2">ملاحظات</h3>
                <p class="text-sm">
                    {{ $this->patient->notes ?? 'لا توجد ملاحظات متاحة.' }}
                </p>
            </div>
        </div>
    </x-filament::section>

    @livewire('overview-appointments-table', ['patientId' => $this->patient->id])
    @livewire(\App\Livewire\PatientVitalsChart::class, ['patientId' => $this->patient->id])
    @livewire('overview-vitals-table', ['patientId' => $this->patient->id])
    @livewire('overview-prescriptions-table', ['patientId' => $this->patient->id])


</x-filament-panels::page>
