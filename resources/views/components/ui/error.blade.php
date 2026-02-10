<!-- resources/views/components/ui/error.blade.php -->
<!-- This component displays an error message from laravel controllers in a styled alert box.in bottom right corner of page that auto hides in 5 seconds -->
<script>
    setTimeout(() => {
        document.querySelector('#error_global').remove();
    }, 5000);
</script>
<div>
    @if ($errors->any())
        <div id="error_global"
            class="fixed bottom-4 right-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded shadow">
            <strong class="font-bold">Error!</strong>
            <ul class="mt-2 text-sm list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

</div>