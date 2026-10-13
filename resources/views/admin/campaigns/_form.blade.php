@php($selected = array_map('intval', old('product_ids', $selected)))

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card mb-4">
            <div class="card-header">Campaign Details</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label class="form-label required" for="title">Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title', $campaign->title) }}" data-slug-source placeholder="e.g. Mega Grocery Week"
                            class="form-control @error('title') is-invalid @enderror">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $campaign->slug) }}" data-slug-target placeholder="Auto from title"
                            class="form-control @error('slug') is-invalid @enderror">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="subtitle">Subtitle</label>
                    <input type="text" id="subtitle" name="subtitle" value="{{ old('subtitle', $campaign->subtitle) }}" placeholder="Short line shown under the title"
                        class="form-control @error('subtitle') is-invalid @enderror">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $campaign->description) }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label required" for="discount_percent">Discount (%)</label>
                        <input type="number" min="1" max="90" id="discount_percent" name="discount_percent" value="{{ old('discount_percent', $campaign->discount_percent) }}"
                            class="form-control @error('discount_percent') is-invalid @enderror">
                        @error('discount_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required" for="starts_at">Starts at</label>
                        <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at', $campaign->starts_at?->format('Y-m-d\TH:i')) }}"
                            class="form-control @error('starts_at') is-invalid @enderror">
                        @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label required" for="ends_at">Ends at</label>
                        <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at', $campaign->ends_at?->format('Y-m-d\TH:i')) }}"
                            class="form-control @error('ends_at') is-invalid @enderror">
                        @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Banner &amp; Visibility</div>
            <div class="card-body">
                @if ($campaign->banner_url)
                    <img src="{{ $campaign->banner_url }}" {!! img_fallback() !!} class="img-fluid rounded mb-2" style="max-height:140px" alt="">
                @endif
                <input type="file" name="banner" accept="image/*" class="form-control mb-1 @error('banner') is-invalid @enderror">
                @error('banner')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text mb-3">Optional, wide image (e.g. 1200×400). Without a banner a colourful background is used.</div>

                <input type="hidden" name="status" value="0">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" @checked(old('status', $campaign->status))>
                    <label class="form-check-label" for="status">Active</label>
                </div>
                <input type="hidden" name="is_featured" value="0">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1" @checked(old('is_featured', $campaign->is_featured))>
                    <label class="form-check-label" for="is_featured">Show on home page (with countdown)</label>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Products <span class="badge bg-primary" id="selectedCount">{{ count($selected) }}</span></span>
                <input type="search" class="form-control form-control-sm w-50" placeholder="Filter..." id="productFilter">
            </div>
            <div class="card-body campaign-products" style="max-height: 520px; overflow-y: auto;">
                @error('product_ids')<div class="alert alert-danger py-2 small">{{ $message }}</div>@enderror
                @foreach ($categories as $category)
                    <div class="mb-3" data-group>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-uppercase text-muted">{{ $category->name }}</span>
                            <button type="button" class="btn btn-link btn-sm p-0" data-select-group>Select all</button>
                        </div>
                        @foreach ($category->products as $product)
                            <div class="form-check" data-product>
                                <input class="form-check-input" type="checkbox" name="product_ids[]" value="{{ $product->id }}" id="p{{ $product->id }}"
                                    @checked(in_array($product->id, $selected, true))>
                                <label class="form-check-label small" for="p{{ $product->id }}">{{ $product->name }} <span class="text-muted">({{ money($product->price, 0) }})</span></label>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> {{ $campaign->exists ? 'Update' : 'Create' }} Campaign</button>
    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-light">Cancel</a>
</div>

@push('scripts')
    <script>
        (() => {
            const box = document.querySelector('.campaign-products');
            const count = () => (document.getElementById('selectedCount').textContent = box.querySelectorAll('input:checked').length);
            box.addEventListener('change', count);
            box.querySelectorAll('[data-select-group]').forEach((btn) => btn.addEventListener('click', () => {
                const inputs = btn.closest('[data-group]').querySelectorAll('[data-product]:not(.d-none) input');
                const all = [...inputs].every((i) => i.checked);
                inputs.forEach((i) => (i.checked = !all));
                count();
            }));
            document.getElementById('productFilter').addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                box.querySelectorAll('[data-product]').forEach((el) => el.classList.toggle('d-none', !el.textContent.toLowerCase().includes(term)));
            });
        })();
    </script>
@endpush
