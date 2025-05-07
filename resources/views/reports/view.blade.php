@extends('layouts.app')

@section('title', 'Reports')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 py-2">
                <h2>Reports</h2>
                <h4>Generate and view system reports</h4>
                <hr>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Report Tabs -->
                        <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="inventory-logs-tab" data-bs-toggle="tab" 
                                    data-bs-target="#inventory-logs" type="button" role="tab" 
                                    aria-controls="inventory-logs" aria-selected="true">
                                    Inventory Logs
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="sales-tab" data-bs-toggle="tab" 
                                    data-bs-target="#sales" type="button" role="tab" 
                                    aria-controls="sales" aria-selected="false">
                                    Sales Report
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cashier-tab" data-bs-toggle="tab" 
                                    data-bs-target="#cashier" type="button" role="tab" 
                                    aria-controls="cashier" aria-selected="false">
                                    Cashier Performance
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Contents -->
                        <div class="tab-content" id="reportTabsContent">
                            <!-- Inventory Logs Tab -->
                            <div class="tab-pane fade show active" id="inventory-logs" role="tabpanel" 
                                aria-labelledby="inventory-logs-tab">
                                <livewire:reports.inventory-logs />
                            </div>

                            <!-- Sales Report Tab -->
                            <div class="tab-pane fade" id="sales" role="tabpanel" 
                                aria-labelledby="sales-tab">
                                <div class="alert alert-info">
                                    Sales report will be displayed here. Coming soon!
                                </div>
                            </div>

                            <!-- Cashier Performance Tab -->
                            <div class="tab-pane fade" id="cashier" role="tabpanel" 
                                aria-labelledby="cashier-tab">
                                <div class="alert alert-info">
                                    Cashier performance report will be displayed here. Coming soon!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection