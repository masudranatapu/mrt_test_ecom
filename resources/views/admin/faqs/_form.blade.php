<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label for="question" class="form-label required">Question</label>
                    <input type="text" id="question" name="question" value="{{ old('question', $faq->question) }}"
                        class="form-control @error('question') is-invalid @enderror">
                    @error('question')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label for="answer" class="form-label required">Answer</label>
                    <textarea id="answer" name="answer" rows="7" class="form-control @error('answer') is-invalid @enderror">{{ old('answer', $faq->answer) }}</textarea>
                    @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Plain text. Line breaks are kept.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label for="category" class="form-label required">Topic</label>
                    <select id="category" name="category" class="form-select @error('category') is-invalid @enderror">
                        @foreach (\App\Models\Faq::CATEGORIES as $key => $category)
                            <option value="{{ $key }}" @selected(old('category', $faq->category) === $key)>{{ $category['label'] }}</option>
                        @endforeach
                    </select>
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="sort_order" class="form-label">Sort order</label>
                    <input type="number" min="0" id="sort_order" name="sort_order" value="{{ old('sort_order', $faq->sort_order) }}"
                        class="form-control @error('sort_order') is-invalid @enderror">
                </div>
                <input type="hidden" name="status" value="0">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" @checked(old('status', $faq->status))>
                    <label class="form-check-label" for="status">Show on FAQ page</label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> {{ $faq->exists ? 'Update' : 'Save' }} FAQ</button>
    <a href="{{ route('admin.faqs.index') }}" class="btn btn-light">Cancel</a>
</div>
