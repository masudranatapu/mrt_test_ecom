<div class="toast-stack">
    @foreach (['success' => 'bg-success', 'error' => 'bg-danger', 'warning' => 'bg-warning'] as $key => $class)
        @if (session($key))
            <div class="toast align-items-center text-white {{ $class }} border-0 mb-2" role="alert" data-autoshow>
                <div class="d-flex">
                    <div class="toast-body">{{ session($key) }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
    @endforeach
</div>
