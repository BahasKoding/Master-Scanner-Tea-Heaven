@extends('layouts.main')

@section('title', 'Choices')
@section('breadcrumb-item', 'Forms')

@section('breadcrumb-item-active', 'Choices')

@section('css')
@endsection

@section('content')
<!-- [ Main content ] start -->
<div class="row">
    <!-- [ form-element ] start -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>Multiple select input</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3 row">
                        <label class="col-form-label col-lg-4 col-sm-12 text-lg-end">With remove button</label>
                        <div class="col-lg-6 col-md-11 col-sm-12">
                            <select
                                class="form-control"
                                name="choices-multiple-remove-button"
                                id="choices-multiple-remove-button"
                                multiple>
                                <option value="Choice 1" selected>Choice 1</option>
                                <option value="Choice 2">Choice 2</option>
                                <option value="Choice 3">Choice 3</option>
                                <option value="Choice 4">Choice 4</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- [ form-element ] end -->
</div>
<!-- [ Main content ] end -->
@endsection

@section('scripts')
<!-- [Page Specific JS] start -->
<!-- tagify -->
<script src="{{ URL::asset('build/js/plugins/choices.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        var multipleCancelButton = new Choices('#choices-multiple-remove-button', {
            removeItemButton: true
        });

    });
</script>
<!-- [Page Specific JS] end -->
@endsection