@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="mb-4">Form Blocker Test Page</h1>
            <p class="text-muted mb-4">This page demonstrates the Form Submit Blocker in action.</p>

            <!-- Test 1: Regular Form Submission -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Test 1: Regular Form Submission</h5>
                </div>
                <div class="card-body">
                    <p>This form will show the loading state and automatically block duplicate submissions.</p>
                    <form action="{{ route('admin.dashboard') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Form</button>
                    </form>
                </div>
            </div>

            <!-- Test 2: AJAX Form -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Test 2: AJAX Form (jQuery)</h5>
                </div>
                <div class="card-body">
                    <p>This form uses AJAX. The button will be re-enabled after the request completes.</p>
                    <form id="ajaxForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success">Submit via AJAX</button>
                    </form>
                    <div id="ajaxResult" class="mt-3"></div>
                </div>
            </div>

            <!-- Test 3: Excluded Form -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Test 3: Excluded Form (no-block)</h5>
                </div>
                <div class="card-body">
                    <p>This form has the <code>no-block</code> class, so it won't be blocked.</p>
                    <form class="no-block" action="{{ route('admin.dashboard') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label">Search Query</label>
                            <input type="text" name="q" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-warning">Search (Not Blocked)</button>
                    </form>
                </div>
            </div>

            <!-- Test 4: Multiple Buttons -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Test 4: Multiple Submit Buttons</h5>
                </div>
                <div class="card-body">
                    <p>When you click any button, all submit buttons in the form are disabled.</p>
                    <form action="{{ route('admin.dashboard') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Document</label>
                            <input type="text" name="document" class="form-control" required>
                        </div>
                        <button type="submit" name="action" value="save" class="btn btn-secondary">Save Draft</button>
                        <button type="submit" name="action" value="publish" class="btn btn-info">Publish</button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary">Submit for Approval</button>
                    </form>
                </div>
            </div>

            <!-- Test 5: Manual Control -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Test 5: Manual Control API</h5>
                </div>
                <div class="card-body">
                    <p>Test the manual control API for the Form Blocker.</p>
                    <button id="testBtn" class="btn btn-primary" type="button">Test Button</button>
                    <div class="mt-3">
                        <button class="btn btn-sm btn-success" onclick="FormBlocker.disable(document.getElementById('testBtn'))">
                            Disable Button
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="FormBlocker.enable(document.getElementById('testBtn'))">
                            Enable Button
                        </button>
                    </div>
                </div>
            </div>

            <!-- Configuration Info -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Current Configuration</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th>Loading Text:</th>
                                <td><code id="configText"></code></td>
                            </tr>
                            <tr>
                                <th>Timeout:</th>
                                <td><code id="configTimeout"></code></td>
                            </tr>
                            <tr>
                                <th>Spinner Class:</th>
                                <td><code id="configSpinner"></code></td>
                            </tr>
                            <tr>
                                <th>Disabled Class:</th>
                                <td><code id="configDisabled"></code></td>
                            </tr>
                            <tr>
                                <th>Exclude Class:</th>
                                <td><code id="configExclude"></code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Display current configuration
    $(document).ready(function() {
        $('#configText').text(FormBlocker.config.loadingText);
        $('#configTimeout').text(FormBlocker.config.timeout + 'ms');
        $('#configSpinner').text('.' + FormBlocker.config.spinnerClass);
        $('#configDisabled').text('.' + FormBlocker.config.disabledClass);
        $('#configExclude').text('.' + FormBlocker.config.excludeClass);
    });

    // Test AJAX form
    $('#ajaxForm').on('submit', function(e) {
        e.preventDefault();
        
        $('#ajaxResult').html('<div class="alert alert-info">Sending request...</div>');
        
        // Simulate AJAX call with 2-second delay
        setTimeout(function() {
            $.ajax({
                url: '{{ route('admin.dashboard') }}',
                method: 'GET',
                success: function(response) {
                    $('#ajaxResult').html('<div class="alert alert-success">✓ AJAX request completed! Button should be re-enabled now.</div>');
                },
                error: function(xhr) {
                    $('#ajaxResult').html('<div class="alert alert-danger">✗ AJAX request failed! Button should still be re-enabled.</div>');
                }
            });
        }, 2000);
    });
</script>
@endsection
