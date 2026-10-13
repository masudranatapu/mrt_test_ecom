{{--
    Shared "Adjust Stock" modal. Open it with a button like:
    <button data-stock-adjust data-url="..." data-name="..." data-stock="12" data-unit="pcs">
--}}
<div class="modal fade" id="stockAdjustModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="#" class="modal-content" id="stockAdjustForm" novalidate>
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-boxes me-1"></i> Adjust Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="bg-light rounded-3 p-3 mb-3">
                    <div class="fw-semibold" data-field="name"></div>
                    <div class="small text-muted">Current stock: <strong data-field="stock"></strong> <span data-field="unit"></span></div>
                </div>

                <label class="form-label">Action</label>
                <div class="btn-group w-100 mb-3" role="group">
                    <input type="radio" class="btn-check" name="type" id="typeIn" value="in" checked>
                    <label class="btn btn-outline-success" for="typeIn"><i class="bi bi-plus-circle me-1"></i> Stock In</label>
                    <input type="radio" class="btn-check" name="type" id="typeOut" value="out">
                    <label class="btn btn-outline-danger" for="typeOut"><i class="bi bi-dash-circle me-1"></i> Stock Out</label>
                </div>

                <div class="mb-3">
                    <label for="adjustQuantity" class="form-label required">Quantity</label>
                    <input type="number" min="1" name="quantity" id="adjustQuantity" class="form-control" required>
                </div>

                <div>
                    <label for="adjustNote" class="form-label">Note / Reason</label>
                    <input type="text" name="note" id="adjustNote" class="form-control" maxlength="255"
                        placeholder="e.g. New shipment received, damaged items">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check2 me-1"></i> Save</button>
            </div>
        </form>
    </div>
</div>
