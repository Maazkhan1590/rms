<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentication - RMS')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --su-blue: #0056b3;
            --su-dark-blue: #003d82;
            --su-light-blue: #4d8bff;
            --primary: var(--su-blue);
            --danger: #dc3545;
            --success: #28a745;
            --warning: #ffc107;
            --info: #17a2b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--su-dark-blue) 0%, var(--su-blue) 50%, var(--su-light-blue) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .auth-header {
            margin-bottom: 32px;
        }

        .su-logo img {
            animation: scaleIn 0.5s ease-in;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.8);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .text-primary {
            color: var(--su-blue) !important;
        }

        .btn-primary {
            background-color: var(--su-blue);
            border-color: var(--su-blue);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--su-dark-blue);
            border-color: var(--su-dark-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 86, 179, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .form-floating > label {
            color: #6c757d;
        }

        .form-control:focus {
            border-color: var(--su-light-blue);
            box-shadow: 0 0 0 0.2rem rgba(77, 139, 255, 0.25);
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            padding: 0;
            border: none;
            background: none;
            color: #6c757d;
            z-index: 10;
        }

        .form-floating {
            position: relative;
        }

        .divider {
            position: relative;
            text-align: center;
            margin: 24px 0;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: #dee2e6;
        }

        .divider-text {
            position: relative;
            background: white;
            padding: 0 16px;
            color: #6c757d;
            font-size: 14px;
        }

        .social-login .btn {
            transition: all 0.3s ease;
        }

        .social-login .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .alert {
            border-radius: 8px;
            animation: slideDown 0.3s ease-in;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-check-input:checked {
            background-color: var(--su-blue);
            border-color: var(--su-blue);
        }

        a {
            color: var(--su-blue);
            transition: color 0.2s ease;
        }

        a:hover {
            color: var(--su-dark-blue);
        }

        /* Loading spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            border-width: 0.15em;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .auth-card {
                padding: 24px;
            }

            .auth-container {
                padding: 10px;
            }
        }

        /* Progress bar for multi-step forms */
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 32px;
            position: relative;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            flex: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .progress-step.active .step-circle {
            background: var(--su-blue);
            border-color: var(--su-blue);
            color: white;
        }

        .progress-step.completed .step-circle {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .step-label {
            margin-top: 8px;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }

        .progress-step.active .step-label {
            color: var(--su-blue);
            font-weight: 600;
        }
    </style>

    @stack('styles')
</head>
<body>
    @yield('content')

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
