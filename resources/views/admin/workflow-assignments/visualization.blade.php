@extends('layouts.admin')

@section('content')
<div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-bottom: 20px;">
    <div class="card-header" style="background: rgba(255,255,255,0.1); border-bottom: 1px solid rgba(255,255,255,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <h4 style="color: white; margin: 0;">
                <i class="fas fa-project-diagram"></i> Workflow Process Visualization
            </h4>
            <a href="{{ route('admin.workflow-assignments.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Assignments
            </a>
        </div>
    </div>
    <div class="card-body" style="padding: 30px;">
        <div class="workflow-diagram" style="position: relative; min-height: 500px;">
            <!-- Step 1: Submission -->
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="background: white; color: #333; padding: 20px; border-radius: 10px; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1); min-width: 200px;">
                    <div style="font-size: 40px; margin-bottom: 10px;">👤</div>
                    <h5 style="margin: 0; color: #333;">Step 1: Faculty Submission</h5>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">User submits publication/grant/RTN</p>
                    <div style="margin-top: 10px; padding: 5px 10px; background: #e3f2fd; border-radius: 5px; display: inline-block;">
                        <small style="color: #1976d2;"><strong>Status:</strong> Draft → Submitted</small>
                    </div>
                </div>
            </div>

            <!-- Arrow -->
            <div style="text-align: center; margin: -10px 0 20px 0;">
                <div style="display: inline-block; width: 3px; height: 40px; background: white; border-radius: 2px;"></div>
                <div style="display: inline-block; width: 0; height: 0; border-left: 10px solid transparent; border-right: 10px solid transparent; border-top: 15px solid white; margin-left: -6px;"></div>
            </div>

            <!-- Step 2: Coordinator Check -->
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="background: white; color: #333; padding: 20px; border-radius: 10px; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1); min-width: 250px; position: relative;">
                    <div style="font-size: 40px; margin-bottom: 10px;">🔍</div>
                    <h5 style="margin: 0; color: #333;">Step 2: Find Coordinator</h5>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">System searches for Research Coordinator</p>
                    <div style="margin-top: 10px; padding: 10px; background: #fff3cd; border-radius: 5px; text-align: left;">
                        <small style="color: #856404;"><strong>Matching Logic:</strong></small><br>
                        <small style="color: #856404;">• Role: research_coordinator</small><br>
                        <small style="color: #856404;">• College: Match or NULL (all)</small><br>
                        <small style="color: #856404;">• Department: Match or NULL (all)</small><br>
                        <small style="color: #856404;">• Status: Active</small>
                    </div>
                </div>
            </div>

            <!-- Decision Point -->
            <div style="text-align: center; margin: 20px 0;">
                <div style="display: inline-block; background: rgba(255,255,255,0.2); padding: 15px 30px; border-radius: 50px; border: 2px solid white;">
                    <strong>Coordinator Found?</strong>
                </div>
            </div>

            <!-- Two Paths -->
            <div style="display: flex; justify-content: space-around; align-items: flex-start; margin-top: 20px; flex-wrap: wrap;">
                <!-- Path 1: Coordinator Found -->
                <div style="flex: 1; min-width: 300px; margin: 10px;">
                    <div style="text-align: center; margin-bottom: 15px;">
                        <div style="background: #22c55e; color: white; padding: 10px 20px; border-radius: 20px; display: inline-block;">
                            <strong>✅ YES</strong>
                        </div>
                    </div>
                    <div style="background: white; color: #333; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="font-size: 35px; margin-bottom: 10px; text-align: center;">👔</div>
                        <h5 style="margin: 0; color: #333; text-align: center;">Step 3a: Coordinator Review</h5>
                        <p style="margin: 10px 0; color: #666; font-size: 14px; text-align: center;">Assigned to Research Coordinator</p>
                        <div style="margin-top: 10px; padding: 8px; background: #d1fae5; border-radius: 5px;">
                            <small style="color: #065f46;"><strong>Status:</strong> pending_coordinator</small><br>
                            <small style="color: #065f46;"><strong>Step:</strong> 2</small>
                        </div>
                    </div>
                    <div style="text-align: center; margin: 15px 0;">
                        <div style="display: inline-block; width: 3px; height: 30px; background: white; border-radius: 2px;"></div>
                        <div style="display: inline-block; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 12px solid white; margin-left: -5px;"></div>
                    </div>
                    <div style="background: white; color: #333; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="font-size: 35px; margin-bottom: 10px; text-align: center;">🎓</div>
                        <h5 style="margin: 0; color: #333; text-align: center;">Step 4: Dean Review</h5>
                        <p style="margin: 10px 0; color: #666; font-size: 14px; text-align: center;">After coordinator approval</p>
                        <div style="margin-top: 10px; padding: 8px; background: #dbeafe; border-radius: 5px;">
                            <small style="color: #1e40af;"><strong>Status:</strong> pending_dean</small><br>
                            <small style="color: #1e40af;"><strong>Step:</strong> 3</small>
                        </div>
                    </div>
                </div>

                <!-- Path 2: No Coordinator -->
                <div style="flex: 1; min-width: 300px; margin: 10px;">
                    <div style="text-align: center; margin-bottom: 15px;">
                        <div style="background: #f59e0b; color: white; padding: 10px 20px; border-radius: 20px; display: inline-block;">
                            <strong>❌ NO</strong>
                        </div>
                    </div>
                    <div style="background: white; color: #333; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="font-size: 35px; margin-bottom: 10px; text-align: center;">⚡</div>
                        <h5 style="margin: 0; color: #333; text-align: center;">Step 3b: Fallback to Dean</h5>
                        <p style="margin: 10px 0; color: #666; font-size: 14px; text-align: center;">Skip coordinator, go directly to Dean</p>
                        <div style="margin-top: 10px; padding: 8px; background: #fef3c7; border-radius: 5px;">
                            <small style="color: #92400e;"><strong>Status:</strong> pending_dean</small><br>
                            <small style="color: #92400e;"><strong>Step:</strong> 3</small><br>
                            <small style="color: #92400e;"><strong>Fallback:</strong> true</small>
                        </div>
                    </div>
                    <div style="text-align: center; margin: 15px 0;">
                        <div style="display: inline-block; width: 3px; height: 30px; background: white; border-radius: 2px;"></div>
                        <div style="display: inline-block; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 12px solid white; margin-left: -5px;"></div>
                    </div>
                    <div style="background: white; color: #333; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="font-size: 35px; margin-bottom: 10px; text-align: center;">🎓</div>
                        <h5 style="margin: 0; color: #333; text-align: center;">Step 4: Dean Review</h5>
                        <p style="margin: 10px 0; color: #666; font-size: 14px; text-align: center;">Direct assignment to Dean</p>
                        <div style="margin-top: 10px; padding: 8px; background: #dbeafe; border-radius: 5px;">
                            <small style="color: #1e40af;"><strong>Status:</strong> pending_dean</small><br>
                            <small style="color: #1e40af;"><strong>Step:</strong> 3</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Final Step -->
            <div style="text-align: center; margin-top: 30px;">
                <div style="display: inline-block; width: 3px; height: 40px; background: white; border-radius: 2px;"></div>
                <div style="display: inline-block; width: 0; height: 0; border-left: 10px solid transparent; border-right: 10px solid transparent; border-top: 15px solid white; margin-left: -6px;"></div>
            </div>
            <div style="text-align: center;">
                <div style="background: white; color: #333; padding: 25px; border-radius: 10px; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.1); min-width: 250px;">
                    <div style="font-size: 50px; margin-bottom: 10px;">✅</div>
                    <h4 style="margin: 0; color: #22c55e;">Final: Approved</h4>
                    <p style="margin: 10px 0 0 0; color: #666; font-size: 14px;">Submission approved & points allocated</p>
                    <div style="margin-top: 15px; padding: 10px; background: #d1fae5; border-radius: 5px;">
                        <small style="color: #065f46;"><strong>Status:</strong> approved</small><br>
                        <small style="color: #065f46;"><strong>Points:</strong> Calculated & Locked</small><br>
                        <small style="color: #065f46;"><strong>Submission:</strong> Finalized</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Active Assignments -->
        @if($assignments->count() > 0)
        <div style="margin-top: 40px; padding: 20px; background: rgba(255,255,255,0.15); border-radius: 10px; border: 2px solid rgba(255,255,255,0.3);">
            <h5 style="color: white; margin-bottom: 15px;">
                <i class="fas fa-users"></i> Current Active Workflow Assignments
            </h5>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                @foreach($assignments as $assignment)
                <div style="background: white; color: #333; padding: 15px; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                        <div>
                            <strong>{{ $assignment->user->name ?? 'N/A' }}</strong>
                            <br><small style="color: #666;">{{ $assignment->user->email ?? 'N/A' }}</small>
                        </div>
                        <div style="text-align: right;">
                            <div style="background: #22c55e; color: white; padding: 5px 10px; border-radius: 5px; display: inline-block; font-size: 12px;">
                                Active
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
                        <div style="margin-bottom: 5px;">
                            <span class="badge badge-info" style="background: #3b82f6; color: white; padding: 3px 8px; border-radius: 3px; font-size: 11px;">
                                {{ ucfirst(str_replace('_', ' ', $assignment->role)) }}
                            </span>
                        </div>
                        <small style="color: #666;">
                            <strong>College:</strong> {{ $assignment->college ?? 'All Colleges (Global)' }}<br>
                            <strong>Department:</strong> {{ $assignment->department ?? 'All Departments' }}
                        </small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Additional Info -->
        <div style="margin-top: 30px; padding: 20px; background: rgba(255,255,255,0.1); border-radius: 10px;">
            <h5 style="color: white; margin-bottom: 15px;">
                <i class="fas fa-info-circle"></i> Key Features
            </h5>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <div style="background: rgba(255,255,255,0.15); padding: 15px; border-radius: 8px;">
                    <div style="font-size: 24px; margin-bottom: 8px;">🔄</div>
                    <strong style="color: white;">Auto-escalation</strong>
                    <p style="margin: 5px 0 0 0; color: rgba(255,255,255,0.9); font-size: 13px;">
                        If pending > 7 days, automatically escalates to next level
                    </p>
                </div>
                <div style="background: rgba(255,255,255,0.15); padding: 15px; border-radius: 8px;">
                    <div style="font-size: 24px; margin-bottom: 8px;">🎯</div>
                    <strong style="color: white;">Smart Matching</strong>
                    <p style="margin: 5px 0 0 0; color: rgba(255,255,255,0.9); font-size: 13px;">
                        Matches by college/department or uses global assignments
                    </p>
                </div>
                <div style="background: rgba(255,255,255,0.15); padding: 15px; border-radius: 8px;">
                    <div style="font-size: 24px; margin-bottom: 8px;">📝</div>
                    <strong style="color: white;">Full History</strong>
                    <p style="margin: 5px 0 0 0; color: rgba(255,255,255,0.9); font-size: 13px;">
                        Complete audit trail of all actions and status changes
                    </p>
                </div>
                <div style="background: rgba(255,255,255,0.15); padding: 15px; border-radius: 8px;">
                    <div style="font-size: 24px; margin-bottom: 8px;">🔒</div>
                    <strong style="color: white;">Point Locking</strong>
                    <p style="margin: 5px 0 0 0; color: rgba(255,255,255,0.9); font-size: 13px;">
                        Once approved, points are locked and cannot be changed
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
