@extends('layouts.admin')

@section('page-title', 'Reports')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Generate Reports</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            <h4>Coming Soon</h4>
            <p>Report generation will be available here. This module will allow you to:</p>
            <ul>
                <li>Generate comprehensive research reports</li>
                <li>Export data to Excel, PDF, and CSV</li>
                <li>Create custom reports with filters</li>
                <li>Schedule automated reports</li>
                <li>View URC dashboard reports</li>
            </ul>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                        <h5>Excel Reports</h5>
                        <p class="text-muted">Export data to Excel format</p>
                        <button class="btn btn-success" disabled>Coming Soon</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                        <h5>PDF Reports</h5>
                        <p class="text-muted">Generate PDF reports</p>
                        <button class="btn btn-danger" disabled>Coming Soon</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-bar fa-3x text-primary mb-3"></i>
                        <h5>Analytics</h5>
                        <p class="text-muted">View analytics and statistics</p>
                        <button class="btn btn-primary" disabled>Coming Soon</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

