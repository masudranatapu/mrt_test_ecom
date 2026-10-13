<div class="row g-4">
    <div class="col-lg-8">
        {{-- Basic info --}}
        <div class="card mb-4">
            <div class="card-header">Product Information</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label required">Product Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" data-slug-source
                        class="form-control @error('name') is-invalid @enderror">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $product->slug) }}" data-slug-target
                            class="form-control @error('slug') is-invalid @enderror" placeholder="Auto from name">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sku" @class(['form-label', 'required' => $product->exists])>SKU</label>
                        <input type="text" id="sku" name="sku" value="{{ old('sku', $product->sku) }}"
                            class="form-control @error('sku') is-invalid @enderror" placeholder="{{ $product->exists ? '' : 'Auto-generated if empty' }}">
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label required">Category</label>
                    <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">— Select category —</option>
                        @foreach ($categories as $id => $name)
                            <option value="{{ $id }}" @selected(old('category_id', $product->category_id) == $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label">Short Description</label>
                    <textarea id="short_description" name="short_description" rows="2" maxlength="500"
                        class="form-control @error('short_description') is-invalid @enderror">{{ old('short_description', $product->short_description) }}</textarea>
                    @error('short_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="description" class="form-label">Full Description</label>
                    <textarea id="description" name="description" rows="6"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Pricing & stock --}}
        <div class="card mb-4">
            <div class="card-header">Pricing &amp; Inventory</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="price" class="form-label required">Regular Price (৳)</label>
                        <input type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price', $product->price) }}"
                            class="form-control @error('price') is-invalid @enderror">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="sale_price" class="form-label">Sale Price (৳)</label>
                        <input type="number" step="0.01" min="0" id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}"
                            class="form-control @error('sale_price') is-invalid @enderror">
                        @error('sale_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="unit" class="form-label required">Unit</label>
                        <input type="text" id="unit" name="unit" value="{{ old('unit', $product->unit) }}" list="unitOptions"
                            class="form-control @error('unit') is-invalid @enderror">
                        <datalist id="unitOptions">
                            @foreach (['pcs', 'kg', 'g', 'litre', 'ml', 'pack', 'box', 'carton', 'dozen'] as $unit)
                                <option value="{{ $unit }}">
                            @endforeach
                        </datalist>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="stock_qty" @class(['form-label', 'required' => ! $product->exists])>
                            {{ $product->exists ? 'Current Stock' : 'Opening Stock' }}
                        </label>
                        @if ($product->exists)
                            <input type="text" class="form-control" value="{{ $product->stock_qty }} {{ $product->unit }}" disabled>
                            <div class="form-text">Use <strong>Adjust Stock</strong> on the product page to change it.</div>
                        @else
                            <input type="number" min="0" id="stock_qty" name="stock_qty" value="{{ old('stock_qty', $product->stock_qty) }}"
                                class="form-control @error('stock_qty') is-invalid @enderror">
                            @error('stock_qty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="low_stock_alert" class="form-label required">Low Stock Alert</label>
                        <input type="number" min="0" id="low_stock_alert" name="low_stock_alert" value="{{ old('low_stock_alert', $product->low_stock_alert) }}"
                            class="form-control @error('low_stock_alert') is-invalid @enderror">
                        @error('low_stock_alert')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="best_before_date" class="form-label">Best Before Date</label>
                        <input type="date" id="best_before_date" name="best_before_date"
                            value="{{ old('best_before_date', $product->best_before_date?->format('Y-m-d')) }}"
                            class="form-control @error('best_before_date') is-invalid @enderror">
                        @error('best_before_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Gallery --}}
        <div class="card">
            <div class="card-header">Gallery Images</div>
            <div class="card-body">
                @if ($product->exists && $product->images->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach ($product->images as $image)
                            <div class="position-relative">
                                <img src="{{ $image->url }}" {!! img_fallback() !!} class="rounded border" style="width: 90px; height: 90px; object-fit: cover" alt="">
                                <button type="submit" form="deleteImage{{ $image->id }}" class="btn btn-danger btn-sm position-absolute top-0 end-0 py-0 px-1"
                                    title="Remove"><i class="bi bi-x"></i></button>
                            </div>
                        @endforeach
                    </div>
                @endif
                <input type="file" name="images[]" accept="image/*" multiple
                    class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror">
                @error('images')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                @error('images.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Up to 8 images. JPG, PNG or WEBP, max 2MB each.</div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">Main Image</div>
            <div class="card-body text-center">
                <img src="{{ $product->exists ? $product->thumbnail_url : default_image() }}" {!! img_fallback() !!}
                    id="thumbPreview" class="rounded mb-3 img-fluid" style="max-height: 220px" alt="">
                <input type="file" name="thumbnail" accept="image/*" data-preview="#thumbPreview"
                    class="form-control form-control-sm @error('thumbnail') is-invalid @enderror">
                @error('thumbnail')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="card">
            <div class="card-header">Visibility</div>
            <div class="card-body">
                <input type="hidden" name="status" value="0">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1"
                        @checked(old('status', $product->status))>
                    <label class="form-check-label" for="status">Active (visible in store)</label>
                </div>
                <input type="hidden" name="is_featured" value="0">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1"
                        @checked(old('is_featured', $product->is_featured))>
                    <label class="form-check-label" for="is_featured">Featured product</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> {{ $product->exists ? 'Update' : 'Create' }} Product</button>
    <a href="{{ $product->exists ? route('admin.products.show', $product) : route('admin.products.index') }}" class="btn btn-light">Cancel</a>
</div>
