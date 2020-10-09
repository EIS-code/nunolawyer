<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>

<script type="text/javascript">
    document.addEventListener('readystatechange', () => {    
        if (document.readyState == 'complete') {
            $(".client_id").select2({
                placeholder: "{{ __('Select Client') }}",
                allowClear: true,
                width: '100%'
            });
        }
    });
</script>