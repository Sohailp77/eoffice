<!-- resources/views/components/ui/success.blade.php -->
<!-- This component displays a success message from laravel controllers in a styled alert box.in bottom right corner of page that auto hides in 5 sec -->
<script>
    setTimeout(() => {
        document.querySelector('#success_global').remove();
    }, 5000);
</script>
<div>
    @if (session('success'))
        <div id="success_global"
            class="fixed bottom-4 right-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded shadow">
            <strong class="font-bold">Success!</strong>
            <span class="ml-2">{{ session('success') }}</span>
        </div>
    @endif
</div>