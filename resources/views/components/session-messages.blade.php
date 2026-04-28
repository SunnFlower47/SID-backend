@if(session('success'))
@noncescript
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Berhasil!',
        text: '{{ session('success') }}',
        icon: 'success',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    }).then(() => {
        // Clear the session message after showing
        fetch('{{ route("clear-session-message") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({type: 'success'})
        }).catch(err => console.log('Failed to clear session message'));
    });
});
@endnoncescript
@endif

@if(session('error'))
@noncescript
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Error!',
        text: '{{ session('error') }}',
        icon: 'error',
        confirmButtonText: 'OK'
    });
});
@endnoncescript
@endif

@if(session('warning'))
@noncescript
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Peringatan!',
        text: '{{ session('warning') }}',
        icon: 'warning',
        timer: 4000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
});
@endnoncescript
@endif

@if(session('info'))
@noncescript
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Informasi!',
        text: '{{ session('info') }}',
        icon: 'info',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
});
@endnoncescript
@endif

@if($errors->any())
@noncescript
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Terjadi Kesalahan Validasi!',
        html: '<ul class="text-left">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#ef4444'
    });
});
@endnoncescript
@endif

