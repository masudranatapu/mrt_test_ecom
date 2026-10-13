<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Category Information</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label required">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" data-slug-source
                            class="form-control @error('name') is-invalid @enderror">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" data-slug-target
                            class="form-control @error('slug') is-invalid @enderror" placeholder="Auto from name">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="parent_id" class="form-label">Parent Category</label>
                        <select id="parent_id" name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                            <option value="">— None (top level) —</option>
                            @foreach ($parents as $id => $name)
                                <option value="{{ $id }}" @selected(old('parent_id', $category->parent_id) == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="icon" class="form-label">Icon</label>
                        <input type="text" id="icon" name="icon" value="{{ old('icon', $category->icon) }}"
                            class="form-control @error('icon') is-invalid @enderror" placeholder="bi-basket">
                        <div class="form-text"><a href="https://icons.getbootstrap.com" target="_blank">Bootstrap icon</a> name</div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" min="0" id="sort_order" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}"
                            class="form-control @error('sort_order') is-invalid @enderror">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="3"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">Image</div>
            <div class="card-body text-center">
                <img src="{{ $category->exists ? $category->image_url : default_image() }}" {!! img_fallback() !!}
                    id="imagePreview" class="rounded mb-3 img-fluid" style="max-height: 160px" alt="">
                <input type="file" name="image" accept="image/*" data-preview="#imagePreview"
                    class="form-control form-control-sm @error('image') is-invalid @enderror">
                @error('image')
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
                        @checked(old('status', $category->status))>
                    <label class="form-check-label" for="status">Active</label>
                </div>
                <input type="hidden" name="is_featured" value="0">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_featured" name="is_featured" value="1"
                        @checked(old('is_featured', $category->is_featured))>
                    <label class="form-check-label" for="is_featured">Show on home page</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> {{ $category->exists ? 'Update' : 'Create' }} Category</button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a>
</div>
