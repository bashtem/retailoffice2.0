@if(session('info'))
<script>
    $(document).ready(function(){
        {!!session('info')!!}
    })
</script>
@endif