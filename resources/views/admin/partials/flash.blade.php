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

    @if ($errors->any())
        <div class="toast align-items-center text-white bg-danger border-0 mb-2" role="alert" data-autoshow>
            <div class="d-flex">
                <div class="toast-body">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 ps-3 small">
                        @foreach (array_slice($errors->all(), 0, 4) as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 mt-2" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif
</div>
