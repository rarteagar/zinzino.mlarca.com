@extends('layouts.app')

@section('content')
    <div x-data="clientModal()" class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-4">Registrar Prueba</h1>

        <form action="{{ route('tests.store') }}" method="post" enctype="multipart/form-data">
            @csrf

            <!-- Section: De quien se hace la prueba -->
            <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-md shadow-sm">
                <div class="flex items-end gap-3 mb-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-1">Cliente</label>
                        <select id="client-select" name="client_id" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">-- Seleccionar cliente --</option>
                            @if (!empty($clients) && $clients->isNotEmpty())
                                @foreach ($clients as $c)
                                    <option value="{{ $c->id }}"
                                        data-birthdate="{{ $c->birthdate?->toDateString() }}"
                                        data-height="{{ $c->height_cm }}" data-weight="{{ $c->weight_kg }}"
                                        {{ old('client_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}@if ($c->is_self)
                                            (yo)
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="self-end">
                        <div>
                            <button type="button" @click="open()" class="px-3 py-2 bg-primary text-white rounded-md">Crear
                                cliente</button>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end mb-4">
                    <div>
                        <label class="block text-sm font-medium">Edad</label>
                        <input id="client-age" name="subject_age" readonly
                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50"
                            value="{{ old('subject_age') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Altura (cm)</label>
                        <input id="subject_height_cm" name="subject_height_cm" type="number" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300" value="{{ old('subject_height_cm') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Peso (kg)</label>
                        <input id="subject_weight_kg" name="subject_weight_kg" step="0.01" type="number" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300" value="{{ old('subject_weight_kg') }}">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Retos de salud actuales</label>
                    <textarea id="health_challenges" name="health_challenges" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300">{{ old('health_challenges') }}</textarea>
                </div>
                <div class="mb-4">
                    <!-- File upload: drag & drop or select -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-2">Adjuntar documento (PDF)</label>
                        <div id="drop-zone"
                            class="border-2 border-dashed border-gray-300 rounded-md p-6 text-center bg-white dark:bg-gray-800">
                            <p class="text-sm text-muted mb-3">Arrastra un archivo PDF aquí o</p>
                            <button type="button" id="select-file-btn"
                                class="px-3 py-2 bg-primary text-white rounded-md">Seleccionar archivo</button>
                            <input id="attachment-input" name="attachment" type="file" accept="application/pdf"
                                class="hidden">
                            <div id="selected-file" class="mt-3 text-sm"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md">Guardar</button>
                <a href="{{ route('tests.index') }}" class="ml-2 text-muted">Cancelar</a>
            </div>
        </form>

        <!-- Small modal for creating client -->
        <div x-cloak>
            <div x-show="showClientModal" style="display:none" class="fixed inset-0 z-40 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" @click="close()" style="display:none"></div>
                <div x-show="showClientModal" x-transition style="display:none"
                    class="bg-white dark:bg-gray-800 rounded-md p-4 z-50 w-11/12 max-w-md">
                    <h3 class="text-lg font-semibold mb-3">Crear cliente</h3>
                    <form @submit.prevent="submit()">
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-sm">Nombre</label>
                                <input x-model="form.name" x-bind:disabled="submitting" id="client-name" name="name"
                                    class="mt-1 block w-full rounded-md border-gray-300" required>
                                <p class="text-red-500 text-sm mt-1" id="error-name"></p>
                            </div>
                            <div>
                                <label class="block text-sm">Identificador</label>
                                <input x-model="form.identifier" x-bind:disabled="submitting" id="client-identifier"
                                    name="identifier" class="mt-1 block w-full rounded-md border-gray-300">
                                <p class="text-red-500 text-sm mt-1" id="error-identifier"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm">Email</label>
                                    <input x-model="form.email" x-bind:disabled="submitting" type="email"
                                        id="client-email" name="email"
                                        class="mt-1 block w-full rounded-md border-gray-300" required>
                                    <p class="text-red-500 text-sm mt-1" id="error-email"></p>
                                </div>
                                <div>
                                    <label class="block text-sm">Teléfono</label>
                                    <input x-model="form.phone" x-bind:disabled="submitting" id="client-phone"
                                        name="phone" class="mt-1 block w-full rounded-md border-gray-300" required>
                                    <p class="text-red-500 text-sm mt-1" id="error-phone"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm">Fecha de nacimiento</label>
                                    <input x-model="form.birthdate" x-bind:disabled="submitting" type="date"
                                        id="client-birthdate" name="birthdate"
                                        class="mt-1 block w-full rounded-md border-gray-300" required>
                                    <p class="text-red-500 text-sm mt-1" id="error-birthdate"></p>
                                </div>
                                <div>
                                    <label class="block text-sm">Altura (cm)</label>
                                    <input x-model.number="form.height_cm" x-bind:disabled="submitting" type="number"
                                        id="client-height" name="height_cm" min="0"
                                        class="mt-1 block w-full rounded-md border-gray-300" required>
                                    <p class="text-red-500 text-sm mt-1" id="error-height_cm"></p>
                                </div>
                                <div>
                                    <label class="block text-sm">Peso (kg)</label>
                                    <input x-model.number="form.weight_kg" x-bind:disabled="submitting" step="0.01"
                                        id="client-weight" name="weight_kg" type="number" min="0"
                                        class="mt-1 block w-full rounded-md border-gray-300" required>
                                    <p class="text-red-500 text-sm mt-1" id="error-weight_kg"></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" @click="close()" class="px-3 py-1 rounded border">Cancelar</button>
                            <button x-bind:disabled="submitting" type="submit"
                                class="px-3 py-1 rounded bg-primary text-white flex items-center gap-2">
                                <svg x-show="submitting" class="animate-spin h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                                    </path>
                                </svg>
                                <span x-text="submitting ? 'Creando...' : 'Crear'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Select2 + jQuery (CDN) -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function clientModal() {
                return {
                    showClientModal: false,
                    submitting: false,
                    form: {
                        name: '',
                        identifier: '',
                        email: '',
                        phone: '',
                        birthdate: '',
                        height_cm: null,
                        weight_kg: null,
                        is_self: false,
                    },
                    close() {
                        this.showClientModal = false;
                        this.form = {
                            name: '',
                            identifier: '',
                            email: '',
                            phone: '',
                            birthdate: '',
                            height_cm: null,
                            weight_kg: null,
                            is_self: false
                        };
                        this.submitting = false;
                    },
                    open() {
                        this.showClientModal = true;
                    },
                    async submit() {
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        try {
                            this.submitting = true;
                            const resp = await fetch('{{ route('clients.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify(this.form)
                            });
                            const data = await resp.json().catch(() => null);
                            this.submitting = false;
                            if (data && data.success && data.client) {
                                if (window.jQuery && $('#client-select').length) {
                                    const newOption = new Option(data.client.name + (data.client.is_self ? ' (yo)' : ''),
                                        data.client.id, true, true);
                                    $('#client-select').append(newOption).trigger('change');
                                    // set data attributes on the appended option
                                    const optEl = document.querySelector('#client-select option[value="' + data.client.id +
                                        '"]');
                                    if (optEl) {
                                        if (data.client.birthdate) optEl.setAttribute('data-birthdate', data.client
                                            .birthdate);
                                        if (data.client.height_cm) optEl.setAttribute('data-height', data.client.height_cm);
                                        if (data.client.weight_kg) optEl.setAttribute('data-weight', data.client.weight_kg);
                                    }
                                } else {
                                    const sel = document.getElementById('client-select');
                                    const opt = document.createElement('option');
                                    opt.value = data.client.id;
                                    opt.text = data.client.name + (data.client.is_self ? ' (yo)' : '');
                                    if (data.client.birthdate) opt.setAttribute('data-birthdate', data.client.birthdate);
                                    if (data.client.height_cm) opt.setAttribute('data-height', data.client.height_cm);
                                    if (data.client.weight_kg) opt.setAttribute('data-weight', data.client.weight_kg);
                                    sel.appendChild(opt);
                                    sel.value = data.client.id;
                                }
                                // populate the subject fields from the new client
                                const addedOpt = document.querySelector('#client-select option[value="' + data.client.id +
                                    '"]');
                                if (addedOpt) populateSubjectFieldsFromOption(addedOpt);
                                this.close();
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Cliente creado',
                                    showConfirmButton: false,
                                    timer: 2500,
                                    timerProgressBar: true
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: (data && data.message) ? data.message : 'No se pudo crear el cliente'
                                });
                            }
                        } catch (e) {
                            this.submitting = false;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al crear cliente'
                            });
                        }
                    }
                }
            }
        </script>

        <script>
            // Drag & drop / file select handlers for attachment
            document.addEventListener('DOMContentLoaded', function() {
            const drop = document.getElementById('drop-zone');
            const input = document.getElementById('attachment-input');
            const btn = document.getElementById('select-file-btn');
            const label = document.getElementById('selected-file');

            if (!drop || !input) return;

            async submitClient() {
                    this.clearErrors()
                    this.submitting = true
                    try {
                        const res = await fetch("{{ route('clients.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify(this.form)
                        })

                        const data = await res.json()
                        if (!res.ok) {
                            if (res.status === 422 && data.errors) {
                                this.handleValidationErrors(data.errors)
                                this.submitting = false
                                return
                            }
                            throw data
                        }

                        // add to select2
                        const newOption = new Option(data.name, data.id, false, true)
                        $('.client-select').append(newOption).trigger('change')

                        this.reset()
                        this.submitting = false
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Cliente creado',
                            showConfirmButton: false,
                            timer: 2000
                        })
                    } catch (err) {
                        console.error(err)
                        this.submitting = false
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: err.message || 'Error',
                            showConfirmButton: false,
                            timer: 3000
                        })
                    }
                },
                handleValidationErrors(errors) {
                    for (const field in errors) {
                        const el = document.getElementById('error-' + field)
                        if (el) el.textContent = errors[field].join(' ')
                        const input = document.querySelector('[name="' + field + '"]')
                        if (input) input.classList.add('border-red-500')
                    }
                },
                clearErrors() {
                    const fields = ['name', 'identifier', 'email', 'phone', 'birthdate', 'height_cm', 'weight_kg']
                    fields.forEach(f => {
                        const el = document.getElementById('error-' + f)
                        if (el) el.textContent = ''
                        const input = document.querySelector('[name="' + f + '"]')
                        if (input) input.classList.remove('border-red-500')
                    })
                }
            if (f.type !== 'application/pdf') {
                alert('Solo se aceptan archivos PDF');
                input.value = null;
                label.textContent = '';
                return;
            }
            label.textContent = f.name + ' (' + Math.round(f.size / 1024) + ' KB)';
            });
            });
        </script>

        <script>
            // Initialize Select2 on the client select when DOM ready
            document.addEventListener('DOMContentLoaded', function() {
                if (window.jQuery && $('#client-select').length) {
                    $('#client-select').select2({
                        placeholder: '-- Seleccionar cliente --',
                        allowClear: true,
                        width: '100%'
                    });
                    // when using select2, listen to change events
                    $('#client-select').on('change', function() {
                        const val = $(this).val();
                        const opt = document.querySelector('#client-select option[value="' + val + '"]');
                        if (opt) populateSubjectFieldsFromOption(opt);
                    });
                }
                // also populate if there's an initial selection
                const initial = document.querySelector('#client-select');
                if (initial && initial.value) {
                    const opt0 = initial.options[initial.selectedIndex];
                    if (opt0) populateSubjectFieldsFromOption(opt0);
                }
            });

            function computeAge(birthdateStr) {
                if (!birthdateStr) return '';
                const b = new Date(birthdateStr);
                if (isNaN(b)) return '';
                const today = new Date();
                let age = today.getFullYear() - b.getFullYear();
                const m = today.getMonth() - b.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < b.getDate())) {
                    age--;
                }
                return age;
            }

            function populateSubjectFieldsFromOption(opt) {
                const birth = opt.getAttribute('data-birthdate') || '';
                const height = opt.getAttribute('data-height') || '';
                const weight = opt.getAttribute('data-weight') || '';
                const age = computeAge(birth);
                const ageEl = document.getElementById('client-age');
                const heightEl = document.getElementById('subject_height_cm');
                const weightEl = document.getElementById('subject_weight_kg');
                if (ageEl) ageEl.value = age;
                if (heightEl && height !== '') heightEl.value = height;
                if (weightEl && weight !== '') weightEl.value = weight;
            }
        </script>
    </div>
@endsection
