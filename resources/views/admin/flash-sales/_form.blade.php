@php
    $rows = old('items', $flashSale->exists
        ? $flashSale->items->map(fn ($i) => ['product_id' => $i->product_id, 'price' => $i->price, 'quantity_limit' => $i->quantity_limit])->all()
        : [['product_id' => '', 'price' => '', 'quantity_limit' => '']]);
@endphp

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Details</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label required" for="title">Title</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $flashSale->title) }}" placeholder="e.g. Weekend Flash Sale"
                        class="form-control @error('title') is-invalid @enderror">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label required" for="starts_at">Starts at</label>
                    <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at', $flashSale->starts_at?->format('Y-m-d\TH:i')) }}"
                        class="form-control @error('starts_at') is-invalid @enderror">
                    @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label required" for="ends_at">Ends at</label>
                    <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at', $flashSale->ends_at?->format('Y-m-d\TH:i')) }}"
                        class="form-control @error('ends_at') is-invalid @enderror">
                    @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <input type="hidden" name="status" value="0">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" @checked(old('status', $flashSale->status))>
                    <label class="form-check-label" for="status">Active</label>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Products &amp; Flash Prices</span>
                <button type="button" class="btn btn-sm btn-outline-primary" data-add-row><i class="bi bi-plus-lg"></i> Add product</button>
            </div>
            <div class="card-body">
                @error('items')<div class="alert alert-danger py-2 small">{{ $message }}</div>@enderror
                <div class="row g-2 small text-muted fw-semibold mb-1 d-none d-md-flex">
                    <div class="col-md-6">Product (regular price)</div>
                    <div class="col-md-3">Flash price (৳)</div>
                    <div class="col-md-2">Qty limit</div>
                </div>
                <div id="flashRows">
                    @foreach ($rows as $i => $row)
                        <div class="row g-2 mb-2 align-items-start" data-row>
                            <div class="col-md-6">
                                <select name="items[{{ $i }}][product_id]" class="form-select @error("items.$i.product_id") is-invalid @enderror">
                                    <option value="">— Select product —</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" @selected((string) ($row['product_id'] ?? '') === (string) $product->id)>
                                            {{ $product->name }} ({{ money($product->price, 0) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error("items.$i.product_id")<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6 col-md-3">
                                <input type="number" step="0.01" min="1" name="items[{{ $i }}][price]" value="{{ $row['price'] ?? '' }}" placeholder="Price"
                                    class="form-control @error("items.$i.price") is-invalid @enderror">
                                @error("items.$i.price")<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-4 col-md-2">
                                <input type="number" min="1" name="items[{{ $i }}][quantity_limit]" value="{{ $row['quantity_limit'] ?? '' }}" placeholder="No limit"
                                    class="form-control @error("items.$i.quantity_limit") is-invalid @enderror">
                            </div>
                            <div class="col-2 col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100" data-remove-row title="Remove"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="form-text">Leave "Qty limit" empty for unlimited. When the limit is sold, the product goes back to its normal price.</div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> {{ $flashSale->exists ? 'Update' : 'Create' }} Flash Sale</button>
    <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-light">Cancel</a>
</div>

@push('scripts')
    <script>
        (() => {
            const wrap = document.getElementById('flashRows');
            let index = wrap.querySelectorAll('[data-row]').length;

            document.querySelector('[data-add-row]').addEventListener('click', () => {
                const row = wrap.querySelector('[data-row]').cloneNode(true);
                row.querySelectorAll('select, input').forEach((el) => {
                    el.name = el.name.replace(/items\[\d+\]/, `items[${index}]`);
                    el.value = '';
                    el.classList.remove('is-invalid');
                });
                row.querySelectorAll('.invalid-feedback').forEach((el) => el.remove());
                wrap.appendChild(row);
                index++;
            });

            wrap.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-remove-row]');
                if (btn && wrap.querySelectorAll('[data-row]').length > 1) {
                    btn.closest('[data-row]').remove();
                }
            });
        })();
    </script>
@endpush
