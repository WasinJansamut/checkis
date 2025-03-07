@if (isset($redirect) && $redirect)
    <script>
        window.location.href = "{{ route('home') }}";
    </script>
@endif
