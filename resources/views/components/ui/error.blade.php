<!-- resources/views/components/ui/error.blade.php -->
<!-- This component displays an error message from laravel controllers in a styled alert box.in top right corner of page that auto hides in 5 seconds with ability to close it manually -->
<script>
    setTimeout(() => {
        document.querySelector('#error_global').remove();
    }, 10000);
</script>
<div>
@if (session('error') || $errors->any())
        <div id="error_global"
            class="z-50 fixed top-4 right-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded shadow">
            <strong class="font-bold">Error!</strong>
            <ul class="mt-2 text-sm list-disc list-inside">
                @if(session('error'))
                    <li>{{ session('error') }}</li>
                @endif
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button onclick="document.querySelector('#error_global').remove()" class="absolute top-2 right-2 text-red-700 dark:text-red-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

</div>