@extends('layouts.app')

@section('content')

    <div x-data="clientModal()" x-init="init()" class="container mx-auto px-4 py-8">
        <!-- wrapper que se oscurece y bloquea interacciones cuando el modal está abierto -->
        <div id="form-wrapper" :class="showClientModal ? 'opacity-60 pointer-events-none' : ''"
            class="transition duration-200">
            <h1 class="text-2xl font-bold mb-4">Registrar Prueba</h1>

            <form id="test-form" action="{{ route('tests.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <!-- hidden client fields that will be populated when modal se envíe -->
                <input type="hidden" name="client_id" id="hidden-client-id" value="{{ old('client_id', '') }}">
                <input type="hidden" name="name" id="hidden-client-name" value="{{ old('name', '') }}">
                <input type="hidden" name="identifier" id="hidden-client-identifier" value="{{ old('identifier', '') }}">
                <input type="hidden" name="email" id="hidden-client-email" value="{{ old('email', '') }}">
                <input type="hidden" name="phone" id="hidden-client-phone" value="{{ old('phone', '') }}">
                <input type="hidden" name="birthdate" id="hidden-client-birthdate" value="{{ old('birthdate', '') }}">
                <input type="hidden" name="height_cm" id="hidden-client-height_cm" value="{{ old('height_cm', '') }}">
                <input type="hidden" name="weight_kg" id="hidden-client-weight_kg" value="{{ old('weight_kg', '') }}">

                <!-- Section: De quien se hace la prueba -->
                <div class="mb-6 bg-white dark:bg-gray-800 p-4 rounded-md shadow-sm">
                    <div class="flex items-end gap-3 mb-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-1">Cliente</label>
                            <select id="client-select" name="client_id"
                                class="mt-1 block w-full rounded-md border-gray-300">
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
                                <!-- Al abrir nuevo cliente inicializamos en cero -->
                                <button type="button" @click="openNew()"
                                    class="px-3 py-2 bg-primary text-white rounded-md">Crear
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
                            <input id="subject_weight_kg" name="subject_weight_kg" step="0.01" type="number"
                                min="0" class="mt-1 block w-full rounded-md border-gray-300"
                                value="{{ old('subject_weight_kg') }}">
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
                <!-- hidden client_id para asegurar envío aun si se deshabilitan controles -->
                <input type="hidden" id="client-id-hidden" name="client_id" value="{{ old('client_id') }}">

                <div class="mt-4">
                    <button id="submit-btn" type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-md flex items-center gap-2">
                        <svg id="submit-spinner" class="hidden animate-spin h-4 w-4 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span id="submit-text">Guardar</span>
                    </button>
                    <a href="{{ route('tests.index') }}" class="ml-2 text-muted">Cancelar</a>
                </div>
            </form>
        </div> <!-- /wrapper oscuro -->

        <!-- Small modal for creating/editing client -->
        <div x-cloak>
            <div x-show="showClientModal" class="fixed inset-0 z-40 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" @click="close()"></div>
                <div x-show="showClientModal" x-transition
                    class="bg-white dark:bg-gray-800 rounded-md p-4 z-50 w-11/12 max-w-md">
                    <h3 class="text-lg font-semibold mb-3" x-text="editingClientId > 0 ? 'Editar cliente' : 'Crear cliente'"></h3>
                    <form @submit.prevent="submit()">
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-sm">Nombre</label>
                                <input x-model="form.name" x-bind:disabled="submitting" id="client-name"
                                    class="mt-1 block w-full rounded-md border-gray-300" required>
                            </div>
                            <div>
                                <label class="block text-sm">Identificador</label>
                                <input x-model="form.identifier" x-bind:disabled="submitting" id="client-identifier"
                                    class="mt-1 block w-full rounded-md border-gray-300">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm">Email</label>
                                    <input x-model="form.email" x-bind:disabled="submitting" type="email"
                                        id="client-email" class="mt-1 block w-full rounded-md border-gray-300" required>
                                </div>
                                <div>
                                    <label class="block text-sm">Teléfono</label>
                                    <input x-model="form.phone" x-bind:disabled="submitting" id="client-phone"
                                        class="mt-1 block w-full rounded-md border-gray-300">
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm">Fecha de nacimiento</label>
                                    <input x-model="form.birthdate" x-bind:disabled="submitting" type="date"
                                        id="client-birthdate" class="mt-1 block w-full rounded-md border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm">Altura (cm)</label>
                                    <input x-model.number="form.height_cm" x-bind:disabled="submitting" type="number"
                                        id="client-height" min="0" class="mt-1 block w-full rounded-md border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm">Peso (kg)</label>
                                    <input x-model.number="form.weight_kg" x-bind:disabled="submitting" step="0.01"
                                        id="client-weight" type="number" min="0"
                                        class="mt-1 block w-full rounded-md border-gray-300">
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" @click="close()" class="px-3 py-1 rounded border">Cancelar</button>
                            <button x-bind:disabled="submitting" type="submit"
                                class="px-3 py-1 rounded bg-primary text-white flex items-center gap-2">
                                <span x-text="submitting ? 'Guardando...' : (editingClientId > 0 ? 'Aplicar' : 'Usar y cerrar')"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ensure x-cloak actually hides before Alpine initializes -->
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        <!-- Se eliminaron jQuery / Select2 includes y código asociado (no necesarios) -->

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            function clientModal() {
                return {
                    showClientModal: false,
                    submitting: false,
                    editMode: false,
                    editingClientId: null,
                    form: {
                        name: '',
                        identifier: '',
                        email: '',
                        phone: '',
                        birthdate: '',
                        height_cm: null,
                        weight_kg: null,
                    },
                    init() {
                        // escucha evento para abrir modal en modo edición (desde select change)
                        window.addEventListener('open-client-edit', (e) => {
                            const id = e?.detail?.id;
                            if (!id) return;
                            const opt = document.querySelector('#client-select option[value="' + id + '"]');
                            if (!opt) return;
                            const nameText = opt.textContent || '';
                            this.form.name = nameText.replace(/\s*\(yo\)\s*$/, '').trim();
                            this.form.identifier = opt.getAttribute('data-identifier') || '';
                            this.form.email = opt.getAttribute('data-email') || '';
                            this.form.phone = opt.getAttribute('data-phone') || '';
                            this.form.birthdate = opt.getAttribute('data-birthdate') || '';
                            this.form.height_cm = opt.getAttribute('data-height') || null;
                            this.form.weight_kg = opt.getAttribute('data-weight') || null;
                            this.editMode = true;
                            this.editingClientId = id;
                            this.showClientModal = true;
                        });
                    },
                    openNew() {
                        this.showClientModal = true;
                        this.editMode = false;
                        this.editingClientId = 0; // inicializar en cero para nuevo cliente
                        this.form = {
                            name: '',
                            identifier: '',
                            email: '',
                            phone: '',
                            birthdate: '',
                            height_cm: null,
                            weight_kg: null
                        };
                    },
                    close() {
                        this.showClientModal = false;
                        this.submitting = false;
                        this.editMode = false;
                        this.editingClientId = null;
                    },
                    async submit() {
                        // limpieza de errores (si están)
                        ['name','identifier','email','phone','birthdate','height_cm','weight_kg'].forEach(f => {
                            const el = document.getElementById('error-' + f);
                            if (el) el.textContent = '';
                        });

                        this.submitting = true;
                        try {
                            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                            let url, method;
                            if (this.editMode && this.editingClientId) {
                                const clientsBase = '{{ url('clients') }}';
                                url = clientsBase + '/' + this.editingClientId;
                                method = 'POST';
                            } else {
                                url = '{{ route('clients.store') }}';
                                method = 'POST';
                            }
                            const resp = await fetch(url, {
                                method: method,
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify(this.form)
                            });
                            if (resp.status === 422) {
                                const data422 = await resp.json().catch(() => null);
                                const errors = data422 && data422.errors ? data422.errors : null;
                                if (errors) {
                                    Object.entries(errors).forEach(([k, v]) => {
                                        const el = document.getElementById('error-' + k);
                                        if (el) el.textContent = Array.isArray(v) ? v.join(' ') : v;
                                    });
                                }
                                this.submitting = false;
                                Swal.fire({ icon: 'error', title: 'Errores de validación', text: 'Corrige los errores.' });
                                return;
                            }

                            if (!resp.ok) {
                                this.submitting = false;
                                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo guardar el cliente.' });
                                return;
                            }

                            const data = await resp.json().catch(() => null);
                            this.submitting = false;
                            if (data && data.success && data.client) {
                                const client = data.client;
                                const sel = document.getElementById('client-select');
                                let opt = sel.querySelector('option[value="' + client.id + '"]');
                                const text = client.name + (client.is_self ? ' (yo)' : '');
                                if (!opt) {
                                    opt = document.createElement('option');
                                    sel.appendChild(opt);
                                }
                                opt.value = client.id;
                                opt.text = text;
                                if (client.birthdate) opt.setAttribute('data-birthdate', client.birthdate);
                                else opt.removeAttribute('data-birthdate');
                                if (client.height_cm) opt.setAttribute('data-height', client.height_cm);
                                else opt.removeAttribute('data-height');
                                if (client.weight_kg) opt.setAttribute('data-weight', client.weight_kg);
                                else opt.removeAttribute('data-weight');
                                if (client.email) opt.setAttribute('data-email', client.email); else opt.removeAttribute('data-email');
                                if (client.phone) opt.setAttribute('data-phone', client.phone); else opt.removeAttribute('data-phone');
                                if (client.identifier) opt.setAttribute('data-identifier', client.identifier); else opt.removeAttribute('data-identifier');
                                
                                // seleccionar cliente creado/actualizado
                                sel.value = client.id;
                                // actualizar hidden client_id para el formulario principal
                                const hidden = document.getElementById('client-id-hidden');
                                if (hidden) hidden.value = client.id;

                                // poblar campos secundarios (edad/altura/peso)
                                const addedOpt = document.querySelector('#client-select option[value="' + client.id + '"]');
                                if (addedOpt) populateSubjectFieldsFromOption(addedOpt);

                                this.close();
                                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: this.editMode ? 'Cliente actualizado' : 'Cliente creado', showConfirmButton: false, timer: 2000 });
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error', text: (data && data.message) ? data.message : 'No se pudo guardar el cliente' });
                            }
                        } catch (e) {
                            this.submitting = false;
                            Swal.fire({ icon: 'error', title: 'Error', text: 'Error al guardar cliente' });
                        }
                    }
                }
            }
        </script>

        <script>
            // Drag & drop / file select handlers (mínimo)
            document.addEventListener('DOMContentLoaded', function() {
                const drop = document.getElementById('drop-zone');
                const input = document.getElementById('attachment-input');
                const btn = document.getElementById('select-file-btn');
                const label = document.getElementById('selected-file');
                if (!drop || !input) return;
                if (btn) btn.addEventListener('click', () => input.click());
                input.addEventListener('change', (e) => {
                    const f = e.target.files && e.target.files[0];
                    if (!f) { label.textContent = ''; return; }
                    if (f.type !== 'application/pdf') { alert('Solo se aceptan archivos PDF'); input.value = null; label.textContent = ''; return; }
                    label.textContent = f.name + ' (' + Math.round(f.size / 1024) + ' KB)';
                });
                drop.addEventListener('dragover', (e) => { e.preventDefault(); drop.classList.add('opacity-80'); });
                ['dragleave', 'dragend'].forEach(evt => drop.addEventListener(evt, () => drop.classList.remove('opacity-80')));
                drop.addEventListener('drop', (e) => {
                    e.preventDefault(); drop.classList.remove('opacity-80');
                    const f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0]; if (!f) return;
                    if (f.type !== 'application/pdf') { alert('Solo se aceptan archivos PDF'); return; }
                    input.files = e.dataTransfer.files;
                    label.textContent = f.name + ' (' + Math.round(f.size / 1024) + ' KB)';
                });
            });
        </script>

        <script>
            // change listener para abrir modal si falta birthdate, y para poblar campos
            document.addEventListener('DOMContentLoaded', function() {
                const sel = document.getElementById('client-select');
                if (!sel) return;
                sel.addEventListener('change', function() {
                    const val = sel.value;
                    const opt = sel.options[sel.selectedIndex];
                    if (!opt) return;
                    const birth = opt.getAttribute('data-birthdate') || '';
                    if (!birth && val) {
                        window.dispatchEvent(new CustomEvent('open-client-edit', { detail: { id: val } }));
                    } else {
                        populateSubjectFieldsFromOption(opt);
                        // mantener hidden actualizado
                        const hidden = document.getElementById('client-id-hidden');
                        if (hidden) hidden.value = val;
                    }
                });

                // initial population si ya había selección
                if (sel && sel.value) {
                    const opt0 = sel.options[sel.selectedIndex];
                    if (opt0) {
                        const birth0 = opt0.getAttribute('data-birthdate') || '';
                        if (!birth0 && sel.value) {
                            window.dispatchEvent(new CustomEvent('open-client-edit', { detail: { id: sel.value } }));
                        } else {
                            populateSubjectFieldsFromOption(opt0);
                        }
                    }
                }
            });
        </script>

        <script>
            function computeAge(birthdateStr) {
                if (!birthdateStr) return '';
                const b = new Date(birthdateStr);
                if (isNaN(b)) return '';
                const today = new Date();
                let age = today.getFullYear() - b.getFullYear();
                const m = today.getMonth() - b.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < b.getDate())) age--;
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

        <script>
            // Al enviar el form: actualizamos hidden client_id y mostramos spinner; no dependemos de enviar campos deshabilitados.
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('test-form');
                const submitBtn = document.getElementById('submit-btn');
                const spinner = document.getElementById('submit-spinner');
                const wrapper = document.getElementById('form-wrapper');
                if (!form) return;
                form.addEventListener('submit', function() {
                    // asegurar cliente enviado
                    const sel = document.getElementById('client-select');
                    const hidden = document.getElementById('client-id-hidden');
                    if (hidden && sel) hidden.value = sel.value || '';
                    // mostrar spinner y bloquear acción de botón
                    if (spinner) spinner.classList.remove('hidden');
                    if (submitBtn) submitBtn.disabled = true;
                    if (wrapper) wrapper.classList.add('opacity-60', 'pointer-events-none');
                    // no deshabilitar inputs para no perder envío de archivos; se confía en hidden client_id
                }, { once: true });
            });
        </script>
    </div>
@endsection
