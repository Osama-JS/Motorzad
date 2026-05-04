<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('التحقق من الهوية (KYC)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="mb-8 border-b pb-4">
                        <h3 class="text-lg font-bold mb-2">مستوى التحقق الحالي: 
                            <span class="px-2 py-1 bg-blue-500 text-white rounded text-sm">Level {{ $user->kyc_level }}</span>
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">حالة الحساب: 
                            @if($user->status == 'approved')
                                <span class="text-green-500 font-bold">مقبول ✅</span>
                            @elseif($user->status == 'rejected')
                                <span class="text-red-500 font-bold">مرفوض ❌</span>
                            @else
                                <span class="text-yellow-500 font-bold">قيد الانتظار ⏳</span>
                            @endif
                        </p>
                    </div>

                    @if(!$latestRequest || $latestRequest->status == 'rejected')
                        <form action="{{ route('kyc.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            
                            @if($latestRequest && $latestRequest->status == 'rejected')
                                <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 mb-6">
                                    <p class="font-bold">ملاحظة الإدارة بخصوص الرفض السابق:</p>
                                    <p>{{ $latestRequest->admin_note }}</p>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium mb-1">الاسم الكامل (كما في الهوية)</label>
                                    <input type="text" name="full_name" value="{{ old('full_name', $user->name) }}" class="w-full rounded-md border-gray-300 dark:bg-gray-700" required>
                                    @error('full_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">الدولة</label>
                                    <input type="text" name="country" value="{{ old('country', $user->country) }}" class="w-full rounded-md border-gray-300 dark:bg-gray-700" required>
                                    @error('country') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">رقم الهوية / الإقامة</label>
                                    <input type="text" name="id_number" value="{{ old('id_number', $user->id_number) }}" class="w-full rounded-md border-gray-300 dark:bg-gray-700" required>
                                    @error('id_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div class="border-2 border-dashed p-4 rounded-lg text-center">
                                    <label class="block text-sm font-bold mb-2">صورة الهوية</label>
                                    <input type="file" name="id_image" class="w-full" accept="image/*" required>
                                    <p class="text-xs text-gray-500 mt-2">يرجى رفع صورة واضحة للوجه الأمامي للهوية.</p>
                                    @error('id_image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="border-2 border-dashed p-4 rounded-lg text-center">
                                    <label class="block text-sm font-bold mb-2">صورة سيلفي</label>
                                    <input type="file" name="selfie_image" class="w-full" accept="image/*" required>
                                    <p class="text-xs text-gray-500 mt-2">يرجى رفع صورة سيلفي واضحة وأنت تحمل الهوية بجانب وجهك.</p>
                                    @error('selfie_image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mt-8 text-center">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold transition">
                                    إرسال طلب التحقق
                                </button>
                            </div>
                        </form>
                    @elseif($latestRequest->status == 'pending')
                        <div class="text-center py-10">
                            <div class="inline-block p-4 bg-yellow-100 rounded-full mb-4">
                                <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">طلبك قيد المراجعة</h3>
                            <p class="text-gray-600">شكراً لك. نحن نقوم حالياً بمراجعة مستنداتك. سيتم إشعارك فور الانتهاء.</p>
                        </div>
                    @elseif($latestRequest->status == 'approved')
                        <div class="text-center py-10">
                            <div class="inline-block p-4 bg-green-100 rounded-full mb-4">
                                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">تم التحقق بنجاح</h3>
                            <p class="text-gray-600">حسابك الآن موثق بالكامل. يمكنك البدء في استخدام كافة مميزات المنصة.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
