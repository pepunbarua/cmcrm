<x-dashboard-layout title="Bulk Import Leads - CheckMate Events">
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Bulk Import Leads</h1>
                    <p class="text-gray-600 dark:text-white/60">Import multiple leads from Excel or CSV file</p>
                </div>
                <a href="{{ route('leads.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Back to Leads
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Instructions -->
            <div class="lg:col-span-1">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Import Instructions
                    </h3>
                    
                    <div class="space-y-4 text-sm text-gray-600 dark:text-white/60">
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Step 1: Download Template</h4>
                            <p>Download our sample template to ensure your data is formatted correctly.</p>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Step 2: Fill Your Data</h4>
                            <p>Add your leads data following the template format. Each row represents one lead.</p>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Step 3: Upload File</h4>
                            <p>Upload your completed Excel or CSV file using the form.</p>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-white/10">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Required Fields:</h4>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Client Name</li>
                                <li>Client Phone</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Optional Fields:</h4>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Vendor ID</li>
                                <li>Client Email</li>
                                <li>Event Type (wedding, birthday, corporate, portrait, other)</li>
                                <li>Event Date (YYYY-MM-DD format)</li>
                                <li>Budget Range</li>
                                <li>Package Interest</li>
                                <li>Status (new, contacted, follow_up, qualified, converted, lost)</li>
                                <li>Notes</li>
                            </ul>
                        </div>

                        <div class="pt-4 border-t border-gray-200 dark:border-white/10">
                            <a href="{{ route('leads.template') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Download Template
                            </a>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Upload Form -->
            <div class="lg:col-span-2">
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upload File</h3>
                    
                    <form id="importForm" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-6">
                            <!-- File Upload Area -->
                            <div class="border-2 border-dashed border-gray-300 dark:border-white/20 rounded-lg p-8 text-center">
                                <div id="upload-area" class="space-y-4">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <div>
                                        <label for="file-upload" class="cursor-pointer">
                                            <span class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-medium">
                                                Click to upload
                                            </span>
                                            <span class="text-gray-600 dark:text-white/60"> or drag and drop</span>
                                        </label>
                                        <input id="file-upload" name="file" type="file" accept=".xlsx,.xls,.csv" class="hidden">
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-white/60">
                                        Excel (.xlsx, .xls) or CSV files only. Max 10MB
                                    </p>
                                </div>
                                
                                <div id="file-info" class="hidden space-y-3">
                                    <div class="flex items-center justify-center gap-3">
                                        <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div class="text-left">
                                            <p id="file-name" class="font-medium text-gray-900 dark:text-white"></p>
                                            <p id="file-size" class="text-sm text-gray-600 dark:text-white/60"></p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="clearFile()" class="text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                        Remove file
                                    </button>
                                </div>
                            </div>

                            <!-- Import Options -->
                            <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-sm text-blue-800 dark:text-blue-300">
                                        <p class="font-medium mb-1">Important Notes:</p>
                                        <ul class="list-disc list-inside space-y-1">
                                            <li>Duplicate emails will be skipped</li>
                                            <li>Invalid data rows will show errors</li>
                                            <li>All imported leads will be assigned to you</li>
                                            <li>You can import up to 1000 leads at once</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Display -->
                            <div id="error-display" class="hidden bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 rounded-lg p-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="font-medium text-red-800 dark:text-red-300 mb-2">Import Errors:</p>
                                        <div id="error-list" class="text-sm text-red-700 dark:text-red-400 space-y-1"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex gap-3">
                                <button type="submit" id="preview-btn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Preview Data</span>
                                </button>
                                <a href="{{ route('leads.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </x-card>
            </div>
        </div>

        <!-- Preview Modal -->
        <div id="preview-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-7xl w-full max-h-[90vh] flex flex-col">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Import Preview</h2>
                        <p class="text-sm text-gray-600 dark:text-white/60 mt-1">
                            Review your data before importing. 
                            <span id="preview-stats" class="font-medium"></span>
                        </p>
                    </div>
                    <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-auto px-6 py-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0">
                                <tr>
                                    <th class="px-3 py-2 text-left text-gray-700 dark:text-gray-300 font-medium">#</th>
                                    <th class="px-3 py-2 text-left text-gray-700 dark:text-gray-300 font-medium">Client Name</th>
                                    <th class="px-3 py-2 text-left text-gray-700 dark:text-gray-300 font-medium">Email</th>
                                    <th class="px-3 py-2 text-left text-gray-700 dark:text-gray-300 font-medium">Phone</th>
                                    <th class="px-3 py-2 text-left text-gray-700 dark:text-gray-300 font-medium">Event Type</th>
                                    <th class="px-3 py-2 text-left text-gray-700 dark:text-gray-300 font-medium">Status</th>
                                    <th class="px-3 py-2 text-left text-gray-700 dark:text-gray-300 font-medium">Validation</th>
                                </tr>
                            </thead>
                            <tbody id="preview-table-body" class="divide-y divide-gray-200 dark:divide-white/10">
                                <!-- Rows will be inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-white/10 flex gap-3 justify-end">
                    <button onclick="closePreview()" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        Cancel
                    </button>
                    <button id="confirm-import-btn" onclick="confirmImport()" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Confirm & Import</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const fileInput = document.getElementById('file-upload');
        const uploadArea = document.getElementById('upload-area');
        const fileInfo = document.getElementById('file-info');
        const submitBtn = document.getElementById('submit-btn');
        const errorDisplay = document.getElementById('error-display');
        const errorList = document.getElementById('error-list');

        // File input change
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                displayFileInfo(file);
            }
        });

        // Drag and drop
        const dropZone = document.querySelector('.border-dashed');
        
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-purple-500', 'bg-purple-50', 'dark:bg-purple-500/10');
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-purple-500', 'bg-purple-50', 'dark:bg-purple-500/10');
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-purple-500', 'bg-purple-50', 'dark:bg-purple-500/10');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                displayFileInfo(files[0]);
            }
        });

        function displayFileInfo(file) {
            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = formatFileSize(file.size);
            uploadArea.classList.add('hidden');
            fileInfo.classList.remove('hidden');
            errorDisplay.classList.add('hidden');
        }

        function clearFile() {
            fileInput.value = '';
            uploadArea.classList.remove('hidden');
            fileInfo.classList.add('hidden');
            errorDisplay.classList.add('hidden');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Form submission - Show Preview
        document.getElementById('importForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Check if file is selected
            if (!fileInput.files || fileInput.files.length === 0) {
                window.showToast('Please select a file to upload', 'error');
                return;
            }
            
            const formData = new FormData();
            const file = fileInput.files[0];
            formData.append('file', file);
            
            console.log('File to upload:', file.name, 'Type:', file.type, 'Size:', file.size);
            
            const previewBtn = document.getElementById('preview-btn');
            
            previewBtn.disabled = true;
            const originalHTML = previewBtn.innerHTML;
            previewBtn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg> Loading...';
            errorDisplay.classList.add('hidden');
            
            try {
                console.log('Sending preview request...');
                const response = await fetch('{{ route('leads.preview') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                console.log('Response status:', response.status);
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);
                
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response:', text.substring(0, 500));
                    throw new Error('Server returned HTML instead of JSON. Check Laravel logs.');
                }
                
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    showPreview(data);
                } else {
                    window.showToast(data.message || 'Preview failed!', 'error');
                }
            } catch (error) {
                console.error('Fetch error:', error);
                window.showToast('Error processing file: ' + error.message, 'error');
            } finally {
                previewBtn.disabled = false;
                previewBtn.innerHTML = originalHTML;
            }
        });

        function showPreview(data) {
            const modal = document.getElementById('preview-modal');
            const stats = document.getElementById('preview-stats');
            const tbody = document.getElementById('preview-table-body');
            const confirmBtn = document.getElementById('confirm-import-btn');
            
            // Update stats
            stats.innerHTML = `<span class="text-green-600 dark:text-green-400">${data.valid} valid</span>, <span class="text-red-600 dark:text-red-400">${data.invalid} errors</span> (${data.total} total)`;
            
            // Build table rows
            tbody.innerHTML = data.preview.map(row => `
                <tr class="${row.valid ? '' : 'bg-red-50 dark:bg-red-500/5'}">
                    <td class="px-3 py-2 text-gray-900 dark:text-white">${row.row}</td>
                    <td class="px-3 py-2 text-gray-900 dark:text-white">${row.data.client_name || '-'}</td>
                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400 text-xs">${row.data.client_email || '-'}</td>
                    <td class="px-3 py-2 text-gray-900 dark:text-white">${row.data.client_phone || '-'}</td>
                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">${row.data.event_type || '-'}</td>
                    <td class="px-3 py-2 text-gray-600 dark:text-gray-400">${row.data.status || 'new'}</td>
                    <td class="px-3 py-2">
                        ${row.valid 
                            ? '<span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-400 rounded text-xs font-medium"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> Valid</span>' 
                            : `<div class="space-y-1">${row.errors.map(err => `<div class="text-xs text-red-600 dark:text-red-400">• ${err}</div>`).join('')}</div>`
                        }
                    </td>
                </tr>
            `).join('');
            
            // Disable import if there are errors
            if (data.invalid > 0) {
                confirmBtn.disabled = true;
                confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
                confirmBtn.title = 'Fix all errors before importing';
            } else {
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                confirmBtn.title = '';
            }
            
            // Show modal
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePreview() {
            const modal = document.getElementById('preview-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        async function confirmImport() {
            const confirmBtn = document.getElementById('confirm-import-btn');
            confirmBtn.disabled = true;
            const originalHTML = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg> Importing...';
            
            try {
                const response = await fetch('{{ route('leads.import') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.showToast(data.message, 'success');
                    closePreview();
                    if (data.redirect) {
                        setTimeout(() => window.location.href = data.redirect, 1500);
                    }
                } else {
                    window.showToast(data.message || 'Import failed!', 'error');
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = originalHTML;
                }
            } catch (error) {
                window.showToast('Error importing data', 'error');
                console.error(error);
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalHTML;
            }
        }
    </script>
</x-dashboard-layout>
