@extends('layouts.admin')

@section('page-title', 'Access restricted')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">Access restricted</h4>
                    </div>
                    <div class="card-body">
                        <p class="lead mb-3">
                            Your account does not have any role assigned.
                        </p>
                        <p class="mb-4">
                            Please contact the system administrator to assign an appropriate role to your account.
                            Once a role is assigned, you will be able to access the dashboard and other modules.
                        </p>
                        <a href="{{ route('welcome') }}" class="btn btn-outline-secondary">
                            Back to homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

