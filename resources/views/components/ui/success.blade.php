<!-- resources/views/components/ui/success.blade.php -->
<!-- This component displays a success message from laravel controllers in a styled alert box.in top right corner of page that auto hides in 5 sec -->
<script>
    setTimeout(() => {
        document.querySelector('#success_global').remove();
    }, 10000);
</script>
<div>
    @if (session('success'))
        <div id="success_global"
            class="z-50 fixed top-4 right-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded shadow">
            <strong class="font-bold">Success!</strong>
            <span class="ml-2">{{ session('success') }}</span>
            <button onclick="document.querySelector('#success_global').remove()" class="absolute top-2 right-2 text-green-700 dark:text-green-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif
</div>