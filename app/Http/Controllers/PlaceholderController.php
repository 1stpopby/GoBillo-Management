<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlaceholderController extends Controller
{
    public function ganttCharts()
    {
        return view('placeholder.gantt-charts', [
            'title' => 'Gantt Charts & Scheduling',
            'description' => 'Advanced project scheduling with Gantt charts, timeline management, and resource allocation.',
            'features' => [
                'Interactive Gantt Chart View',
                'Task Dependencies & Critical Path',
                'Resource Allocation Planning',
                'Timeline Milestone Tracking',
                'Schedule Template Library',
                'Progress Visualization',
                'Export to PDF/Excel',
                'Team Calendar Integration'
            ],
            'icon' => 'bi-calendar3',
            'badge' => 'NEW',
            'badge_color' => 'success'
        ]);
    }

    public function resourcePlanning()
    {
        return view('placeholder.resource-planning', [
            'title' => 'Resource Planning',
            'description' => 'Comprehensive resource management for optimal project allocation and utilization.',
            'features' => [
                'Resource Availability Calendar',
                'Skill-based Resource Matching',
                'Capacity Planning & Forecasting',
                'Equipment & Material Tracking',
                'Cost Analysis & Budgeting',
                'Utilization Reports',
                'Resource Conflict Detection',
                'Mobile Resource Management'
            ],
            'icon' => 'bi-person-workspace',
            'badge' => 'PRO',
            'badge_color' => 'primary'
        ]);
    }

    public function timeTracking()
    {
        return view('placeholder.time-tracking', [
            'title' => 'Time Tracking',
            'description' => 'Accurate time tracking with automated reporting and payroll integration.',
            'features' => [
                'One-click Time Entry',
                'GPS Location Tracking',
                'Project & Task Time Allocation',
                'Timesheet Approval Workflow',
                'Overtime & Break Management',
                'Payroll System Integration',
                'Productivity Analytics',
                'Mobile Time Tracking App'
            ],
            'icon' => 'bi-stopwatch',
            'badge' => 'NEW',
            'badge_color' => 'success'
        ]);
    }

    public function mobileApp()
    {
        return view('placeholder.mobile-app', [
            'title' => 'Mobile App Access',
            'description' => 'Full-featured mobile application for field teams and remote project management.',
            'features' => [
                'Native iOS & Android Apps',
                'Offline Data Synchronization',
                'Photo & Document Upload',
                'GPS Check-in/Check-out',
                'Push Notifications',
                'Voice-to-Text Notes',
                'Barcode/QR Code Scanning',
                'Real-time Team Communication'
            ],
            'icon' => 'bi-phone',
            'badge' => 'API',
            'badge_color' => 'info'
        ]);
    }

    public function messaging()
    {
        return view('placeholder.messaging', [
            'title' => 'Messaging System',
            'description' => 'Integrated communication platform for seamless team collaboration.',
            'features' => [
                'Real-time Team Chat',
                'Project-based Channels',
                'File & Media Sharing',
                'Video Call Integration',
                'Message Threading',
                'Notification Management',
                'Message Search & Archive',
                'External Email Integration'
            ],
            'icon' => 'bi-chat-dots',
            'badge' => 'NEW',
            'badge_color' => 'success'
        ]);
    }

    public function payments()
    {
        return view('placeholder.payments', [
            'title' => 'Payment Processing',
            'description' => 'Secure payment processing with multiple payment methods and automated invoicing.',
            'features' => [
                'Credit Card Processing',
                'ACH Bank Transfers',
                'Online Payment Links',
                'Recurring Payment Setup',
                'Payment Gateway Integration',
                'Fraud Protection',
                'Payment Analytics',
                'Multi-currency Support'
            ],
            'icon' => 'bi-credit-card-2-front',
            'badge' => 'NEW',
            'badge_color' => 'success'
        ]);
    }
} 