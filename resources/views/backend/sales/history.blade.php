@extends('layouts.main')

@section('title', 'History Sales')
@section('breadcrumb-item', $item)

@section('css')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Ensure proper mobile viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- [Page specific CSS] start -->
    <!-- data tables css -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/datatables/dataTables.bootstrap5.min.css') }}">
    <!-- Choices css for Select2-like functionality -->
    <link rel="stylesheet" href="{{ URL::asset('build/css/plugins/choices.min.css') }}">
    <!-- [Page specific CSS] end -->
    <style>
        /* ========== VARIABEL & ROOT ========== */
        :root {
            --primary-color: #4CAF50;
            --primary-light: rgba(76, 175, 80, 0.25);
            --danger-color: #dc3545;
            --secondary-color: #6c757d;
            --light-bg: #f8f9fa;
            --border-color: #dee2e6;
            --focus-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25);
            --card-shadow: 0 0 20px rgba(0, 0, 0, 0.08);
            --transition-normal: all 0.3s ease;
            --border-radius-normal: 4px;
            --border-radius-large: 8px;
            --form-element-height: 44px;
        }

        /* ========== UTILITY CLASSES ========== */
        .transition {
            transition: var(--transition-normal);
        }

        /* Help text styling */
        .form-help-text {
            font-size: 0.85rem;
            color: var(--secondary-color);
            margin-top: 0.25rem;
        }

        /* ========== CHOICES.JS CUSTOM STYLING ========== */
        .choices__inner {
            min-height: var(--form-element-height);
            padding: 4px 8px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-normal);
            background-color: #fff;
            transition: var(--transition-normal);
            width: 100%;
        }

        .choices__inner:focus-within {
            border-color: var(--primary-color);
            box-shadow: var(--focus-shadow);
        }

        .choices__list--dropdown .choices__item {
            padding: 8px 12px;
            font-size: 14px;
            line-height: 1.4;
        }

        .choices__list--dropdown .choices__item--highlighted {
            background-color: var(--primary-color);
            color: #fff;
        }

        .is-open .choices__inner {
            border-radius: var(--border-radius-normal) var(--border-radius-normal) 0 0;
        }

        .choices[data-type*="select-one"] .choices__inner {
            padding-bottom: 4px;
        }

        .choices__list--single .choices__item {
            display: flex;
            align-items: center;
            line-height: 1.4;
        }

        /* SKU select container styling */
        .sku-select-container {
            width: 100%;
            position: relative;
        }

        .choices.sku-choices {
            width: 100%;
            min-width: 300px;
        }

        .choices.sku-choices .choices__inner {
            min-height: var(--form-element-height);
            width: 100%;
        }

        /* Enhanced dropdown styling for better product info display */
        .choices__list--dropdown {
            max-height: 300px;
            overflow-y: auto;
        }

        .choices__item--choice {
            padding: 10px 12px !important;
            border-bottom: 1px solid #f0f0f0;
        }

        .choices__item--choice:last-child {
            border-bottom: none;
        }

        .product-choice-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .product-choice-sku {
            font-weight: bold;
            color: var(--primary-color);
            font-size: 14px;
        }

        .product-choice-name {
            color: #333;
            font-size: 13px;
            margin-bottom: 2px;
        }

        .product-choice-meta {
            color: #666;
            font-size: 11px;
        }

        /* Mode toggle styling for SKU input */
        .sku-input-mode-toggle {
            position: absolute;
            top: -25px;
            right: 0;
            font-size: 0.75rem;
            z-index: 10;
        }

        .sku-mode-indicator {
            font-size: 0.75rem;
            color: var(--secondary-color);
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
        }

        .sku-mode-indicator .mode-text {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ========== COMMON ELEMENTS ========== */
        .card {
            border: none;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid var(--border-color);
            padding: 15px;
        }

        .card-header .d-flex {
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .error-feedback {
            color: var(--danger-color);
            font-size: 0.875em;
            margin-top: 0.25rem;
            display: none;
        }

        /* ========== BUTTONS ========== */
        .btn {
            min-height: var(--form-element-height);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition-normal);
        }

        /* Button Group Styling */
        .btn-group {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* Button icon styling */
        .btn-icon {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 3px;
            transition: all 0.2s ease;
        }

        .btn-icon i {
            font-size: 1rem;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
        }

        /* Remove SKU button styling */
        .btn-remove-sku {
            grid-column: 3;
            width: 40px;
            height: var(--form-element-height);
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius-normal);
            transition: all 0.2s ease;
        }

        .btn-remove-sku:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
        }

        .btn-remove-sku:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* ========== FORM ELEMENTS ========== */
        input[type="text"],
        input[type="number"] {
            min-height: var(--form-element-height);
            transition: var(--transition-normal);
        }

        .scanner-active {
            border: 2px solid var(--primary-color) !important;
            box-shadow: 0 0 5px var(--primary-color);
        }

        .scanner-input:focus,
        .sku-input:focus,
        .qty-input:focus {
            border-color: var(--primary-color);
            box-shadow: var(--focus-shadow);
            outline: none;
        }

        .scanner-input {
            height: 45px;
        }

        .sku-input,
        .qty-input {
            height: var(--form-element-height);
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-normal);
            font-size: 16px;
        }

        .sku-input {
            min-width: 300px;
            flex: 1;
        }

        .sku-select {
            min-width: 300px;
            flex: 1;
        }

        /* ========== SKU INPUT CONTAINER GRID SYSTEM ========== */
        .sku-input-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
            padding: 15px;
            background-color: var(--light-bg);
            border-radius: var(--border-radius-large);
            border: 1px solid var(--border-color);
        }

        .sku-input-row {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 10px;
            align-items: center;
        }

        .sku-input-field {
            grid-column: 1;
            min-width: 0;
            /* Allow flexbox to shrink */
        }

        .qty-input {
            grid-column: 2;
            width: 80px;
            text-align: center;
            font-weight: bold;
        }

        .scanning-indicator {
            display: none;
            color: var(--primary-color);
            text-align: center;
            padding: 5px;
            font-size: 14px;
        }

        .scanning-indicator.active {
            display: block;
        }

        /* ========== INDICATORS & HELPERS ========== */
        .scanning-indicator {
            display: none;
            color: var(--primary-color);
            margin-left: 10px;
            text-align: center;
        }

        .scanning-indicator.active {
            display: inline-block;
        }

        /* Scanner toggle styling */
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .form-check-input:focus {
            border-color: var(--primary-color);
            box-shadow: var(--focus-shadow);
        }

        .form-check.form-switch .form-check-input {
            width: 3em;
            height: 1.5em;
            cursor: pointer;
        }

        .form-check-label {
            cursor: pointer;
            user-select: none;
        }

        #scannerStatus {
            font-weight: 500;
            transition: var(--transition-normal);
        }

        .scanner-inactive {
            color: var(--secondary-color);
            text-decoration: line-through;
        }

        .scanner-active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .scanner-mode-hint {
            font-size: 0.75rem;
            opacity: 0.8;
        }



        .table-scroll-indicator {
            display: none;
            margin-bottom: 10px;
            padding: 8px;
            background-color: #fff3cd;
            border-radius: var(--border-radius-normal);
            text-align: center;
            font-size: 0.9rem;
        }

        .current-date-info {
            display: block;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .current-date-info strong {
            color: var(--primary-color);
        }

        /* ========== SECTIONS & CONTAINERS ========== */
        .scanner-section {
            background-color: #fff;
            padding: 20px;
            border-radius: var(--border-radius-large);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        .form-section {
            background-color: var(--light-bg);
            padding: 20px;
            border-radius: var(--border-radius-normal);
            margin-bottom: 20px;
        }

        .sku-form-wrapper {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* ========== TABLE STYLES ========== */
        .table-wrapper {
            position: relative;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin: 0 -15px;
            padding: 0 15px;
            width: calc(100% + 30px);
        }

        .table-wrapper::-webkit-scrollbar {
            height: 6px;
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }

        #history-sales-table {
            min-width: 800px;
            width: 100%;
        }

        #history-sales-table th,
        #history-sales-table td {
            white-space: nowrap;
            vertical-align: middle;
            padding: 12px 8px;
        }

        /* Action buttons in table */
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            flex: 1;
            min-width: 80px;
            margin: 2px;
            padding: 6px 12px;
            white-space: nowrap;
        }

        /* ========== MODAL STYLES ========== */
        .modal-dialog {
            max-width: 95%;
            margin: 20px auto;
        }

        /* ========== RESPONSIVE STYLES ========== */
        /* Mobile styles */
        @media (max-width: 576px) {
            .card-body {
                padding: 15px;
            }

            /* Scanner toggle display on mobile */
            .card-header .d-flex {
                flex-direction: column;
                align-items: flex-start;
            }

            .d-flex.align-items-center {
                width: 100%;
                margin-top: 10px;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .form-check.form-switch {
                margin-bottom: 10px;
                width: 100%;
            }



            .scanner-mode-hint {
                display: inline-block !important;
                margin-left: 5px;
            }

            .sku-input-container {
                padding: 8px;
                border-radius: 6px;
            }

            .sku-mode-indicator {
                font-size: 0.6rem;
            }

            .sku-mode-indicator .btn {
                font-size: 0.7rem;
                padding: 2px 6px;
            }

            .scanning-indicator {
                margin-top: 5px;
                font-size: 13px;
            }

            /* Button responsiveness on mobile */
            .btn-group {
                width: 100%;
                flex-direction: column;
            }

            #submitManualBtn,
            #resetScannerBtn {
                width: 100%;
                margin: 5px 0;
            }

            /* Table styles for mobile */
            .table-scroll-indicator {
                display: block;
                font-weight: bold;
            }

            #history-sales-table {
                font-size: 14px;
            }

            #history-sales-table td:nth-child(3),
            #history-sales-table td:nth-child(4) {
                max-width: 150px;
                white-space: normal;
                word-break: break-word;
            }

            .btn-icon {
                width: 32px;
                height: 32px;
            }

            .btn-icon i {
                font-size: 0.875rem;
            }

            #history-sales-table .btn {
                display: inline-flex;
                width: auto;
                margin: 0 3px;
            }

            .d-flex.justify-content-center {
                display: flex !important;
                justify-content: center !important;
                gap: 8px;
            }

            /* Choices.js responsive adjustments */
            .choices {
                width: 100% !important;
            }

            .choices__inner {
                min-height: 40px !important;
                padding: 6px 8px !important;
            }

            .sku-mode-indicator {
                font-size: 0.7rem;
            }
        }

        /* Tablet styles */
        @media (min-width: 577px) and (max-width: 991px) {
            .card-header .d-flex {
                justify-content: space-between;
            }

            .form-check.form-switch {
                margin-right: 15px;
            }



            .sku-input-container {
                padding: 12px;
            }

            .sku-input-row {
                grid-template-columns: 1fr 80px 40px;
                gap: 10px;
            }

            .choices.sku-choices {
                min-width: 280px;
            }

            .sku-input {
                min-width: 280px;
            }

            .qty-input {
                width: 80px;
            }

            .sku-mode-indicator {
                font-size: 0.7rem;
            }

            .scanning-indicator {
                grid-column: span 3;
                text-align: center;
            }

            #history-sales-table td:nth-child(3),
            #history-sales-table td:nth-child(4) {
                max-width: 200px;
                white-space: normal;
                word-break: break-word;
            }

            .table-scroll-indicator {
                display: block;
            }

            .btn-group {
                width: 100%;
                margin-top: 10px;
            }

            .btn-group .btn {
                flex: 1;
                white-space: nowrap;
                padding: 8px;
                font-size: 14px;
            }

            .table-responsive {
                margin: 0 -15px;
            }

            .modal-dialog {
                margin: 10px;
                padding: 0;
            }

            .modal-content {
                border-radius: 10px;
            }

            .modal-body {
                padding: 15px;
            }
        }

        /* Desktop styles */
        @media (min-width: 992px) {
            .sku-input-container {
                grid-template-columns: 1fr 120px 40px;
            }

            .scanning-indicator {
                grid-column: span 3;
                text-align: center;
            }

            .modal-dialog {
                max-width: 700px;
            }
        }

        /* Input validation styling */
        .is-validating {
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24px" height="24px"><path fill="%23aaa" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"/><path fill="%23aaa" d="M12,4a8,8,0,0,0-8,8H6a6,6,0,1,1,6,6v2A8,8,0,0,0,12,4Z"><animateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/></path></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 20px;
            transition: background 0.3s;
        }

        /* Error field styling */
        .is-invalid {
            border-color: var(--danger-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            background-color: #fff5f5 !important;
            animation: shake 0.5s ease-in-out;
        }

        /* Valid field styling */
        .is-valid {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.2rem rgba(76, 175, 80, 0.25) !important;
            background-color: #f8fff8 !important;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        /* Table loading state */
        .table-loading {
            position: relative;
        }

        .table-loading:after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.7) url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44 44" width="44px" height="44px"><circle fill="none" stroke="%234CAF50" stroke-width="4" cx="22" cy="22" r="14"><animate attributeName="stroke-dasharray" dur="1.5s" calcMode="spline" values="0 100;100 100;0 100" keyTimes="0;0.5;1" keySplines="0.42,0,0.58,1;0.42,0,0.58,1" repeatCount="indefinite"/><animate attributeName="stroke-dashoffset" dur="1.5s" calcMode="spline" values="0;-100;-200" keyTimes="0;0.5;1" keySplines="0.42,0,0.58,1;0.42,0,0.58,1" repeatCount="indefinite"/></circle></svg>') center center no-repeat;
            background-size: 50px;
            z-index: 10;
            border-radius: var(--border-radius-large);
            animation: fadeIn 0.2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        /* Focus countdown styling */
        #focus-countdown,
        #sku-focus-countdown,
        #new-sku-focus-countdown {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 18px;
            border-radius: 8px;
            z-index: 9999;
            font-weight: bold;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            animation: slideInRight 0.3s ease-out;
            transition: all 0.3s ease;
        }

        #focus-countdown {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
        }

        #sku-focus-countdown {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
        }

        #new-sku-focus-countdown {
            background: linear-gradient(135deg, #FF9800, #F57C00);
            color: white;
            top: 60px;
            /* Position below other countdowns */
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Mobile responsive countdown */
        @media (max-width: 576px) {

            #focus-countdown,
            #sku-focus-countdown,
            #new-sku-focus-countdown {
                right: 10px;
                left: 10px;
                text-align: center;
                font-size: 13px;
                padding: 10px 15px;
            }

            #focus-countdown {
                top: 10px;
            }

            #sku-focus-countdown {
                top: 60px;
            }

            #new-sku-focus-countdown {
                top: 110px;
                /* Position below sku-focus-countdown on mobile */
            }
        }

        /* Large Desktop (≥1400px) - With sidebar */
        @media (min-width: 1400px) {
            .sku-input-row {
                grid-template-columns: 1fr 100px 45px;
                gap: 15px;
            }

            .choices.sku-choices {
                min-width: 400px;
            }

            .sku-input {
                min-width: 400px;
            }

            .qty-input {
                width: 100px;
            }
        }

        /* Desktop (992px-1399px) - With sidebar */
        @media (min-width: 992px) and (max-width: 1399px) {
            .sku-input-row {
                grid-template-columns: 1fr 90px 42px;
                gap: 12px;
            }

            .choices.sku-choices {
                min-width: 350px;
            }

            .sku-input {
                min-width: 350px;
            }

            .qty-input {
                width: 90px;
            }
        }

        /* Tablet landscape (768px-991px) - Sidebar collapsed or overlay */
        @media (min-width: 768px) and (max-width: 991px) {
            .sku-input-container {
                padding: 12px;
            }

            .sku-input-row {
                grid-template-columns: 1fr 80px 40px;
                gap: 10px;
            }

            .choices.sku-choices {
                min-width: 280px;
            }

            .sku-input {
                min-width: 280px;
            }

            .qty-input {
                width: 80px;
            }

            .sku-mode-indicator {
                font-size: 0.7rem;
            }
        }

        /* Tablet portrait and mobile (≤767px) - No sidebar, full width */
        @media (max-width: 767px) {
            .sku-input-container {
                padding: 10px;
                margin-bottom: 10px;
            }

            .sku-input-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .sku-input-field,
            .qty-input,
            .btn-remove-sku {
                grid-column: 1;
            }

            .qty-input {
                width: 100%;
                max-width: 120px;
                justify-self: start;
            }

            .btn-remove-sku {
                width: 100%;
                max-width: 120px;
                justify-self: start;
            }

            .choices.sku-choices {
                min-width: 100%;
                width: 100%;
            }

            .sku-input {
                min-width: 100%;
                width: 100%;
            }

            .sku-mode-indicator {
                font-size: 0.65rem;
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .scanning-indicator {
                margin-top: 5px;
                font-size: 13px;
            }
        }

        /* Small mobile (≤576px) */
        @media (max-width: 576px) {
            .card-body {
                padding: 15px;
            }

            .sku-input-container {
                padding: 8px;
                border-radius: 6px;
            }

            .sku-mode-indicator {
                font-size: 0.6rem;
            }

            .sku-mode-indicator .btn {
                font-size: 0.7rem;
                padding: 2px 6px;
            }
        }

        /* ========== SIDEBAR-AWARE RESPONSIVE ADJUSTMENTS ========== */

        /* When sidebar is collapsed on desktop */
        body.pc-sidebar-collapse .sku-input-row {
            grid-template-columns: 1fr 100px 45px;
            gap: 15px;
        }

        body.pc-sidebar-collapse .choices.sku-choices,
        body.pc-sidebar-collapse .sku-input {
            min-width: 450px;
        }

        /* Mobile sidebar active state */
        body.mob-sidebar-active .sku-input-container {
            margin-right: 0;
        }

        /* ========== FORM SECTION RESPONSIVE ========== */
        .scanner-section {
            background-color: #fff;
            padding: 20px;
            border-radius: var(--border-radius-large);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 767px) {
            .scanner-section {
                padding: 15px;
            }
        }

        @media (max-width: 576px) {
            .scanner-section {
                padding: 10px;
            }
        }

        /* ========== CHOICES.JS RESPONSIVE FIXES ========== */

        /* Ensure Choices.js container takes full width */
        .choices {
            width: 100% !important;
            max-width: 100% !important;
        }

        .choices__inner {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box;
        }

        /* Improve dropdown positioning on mobile */
        @media (max-width: 767px) {
            .choices__list--dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                z-index: 1050;
                max-height: 250px;
                overflow-y: auto;
                background: white;
                border: 1px solid var(--border-color);
                border-top: none;
                border-radius: 0 0 var(--border-radius-normal) var(--border-radius-normal);
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            }

            .choices.is-open .choices__inner {
                border-radius: var(--border-radius-normal) var(--border-radius-normal) 0 0;
            }
        }

        /* Ensure proper spacing in choice items */
        .choices__item--choice .product-choice-item {
            padding: 2px 0;
        }

        .choices__item--choice .product-choice-sku {
            font-size: 14px;
            line-height: 1.2;
        }

        .choices__item--choice .product-choice-name {
            font-size: 13px;
            line-height: 1.2;
            margin: 2px 0;
        }

        .choices__item--choice .product-choice-meta {
            font-size: 11px;
            line-height: 1.1;
        }

        /* Mobile-specific improvements */
        @media (max-width: 576px) {
            .choices__item--choice .product-choice-sku {
                font-size: 13px;
            }

            .choices__item--choice .product-choice-name {
                font-size: 12px;
            }

            .choices__item--choice .product-choice-meta {
                font-size: 10px;
            }

            .choices__list--dropdown {
                max-height: 200px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="row">
        <!-- Scanner Form start -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-md-0">
                            <h5 class="mb-0">Scan Barcode</h5>
                            <small class="text-muted">Pindai No Resi terlebih dahulu, kemudian pindai SKU barang</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="form-check form-switch me-3">
                                <input class="form-check-input" type="checkbox" id="scannerToggle" checked>
                                <label class="form-check-label" for="scannerToggle">
                                    <span id="scannerStatus">Resi Auto-Scan</span>
                                    <small class="d-block text-muted scanner-mode-hint">Mode pindai otomatis</small>
                                </label>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="card-body scanner-section">
                    <form id="historySaleForm" method="POST">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="mb-4">
                                    <label for="no_resi" class="form-label">
                                        <i class="fas fa-barcode me-1"></i> No Resi
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control scanner-input scanner-active"
                                            id="no_resi" name="no_resi" required autofocus
                                            placeholder="Ketik atau pindai nomor resi di sini...">
                                        <span class="scanning-indicator" id="resiScanningIndicator">
                                            <i class="fas fa-circle-notch fa-spin"></i> Memindai...
                                        </span>
                                    </div>
                                    <div class="error-feedback" id="resiError"></div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle"></i> Nomor resi harus unik dan belum pernah digunakan
                                        sebelumnya
                                    </small>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-boxes me-1"></i> No SKU & Jumlah
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div id="sku-container">
                                        <div class="sku-input-container">
                                            <div class="sku-mode-indicator">
                                                <div class="mode-text">
                                                    <i class="fas fa-keyboard"></i> Mode Input: <span
                                                        id="skuModeText">Scanner/Manual</span>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    id="toggleSkuMode" title="Toggle antara Scanner dan Select">
                                                    <i class="fas fa-exchange-alt"></i> Toggle
                                                </button>
                                            </div>

                                            <div class="sku-input-row">
                                                <div class="sku-input-field">
                                                    <!-- Text Input (default) -->
                                                    <input type="text" class="form-control scanner-input sku-input"
                                                        name="no_sku[]" required disabled id="sku-text-input-0"
                                                        placeholder="Ketik atau pindai nomor SKU di sini...">

                                                    <!-- Select2 Input (hidden by default) -->
                                                    <div class="sku-select-container" style="display: none;">
                                                        <select class="form-control sku-select" name="no_sku_select[]"
                                                            id="sku-select-input-0" disabled>
                                                            <option value="">-- Pilih atau cari SKU --</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <input type="number" class="form-control qty-input" name="qty[]"
                                                    value="1" min="1" title="Jumlah barang">

                                                <button type="button" class="btn btn-danger btn-remove-sku"
                                                    title="Hapus SKU" disabled>
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>

                                            <div class="scanning-indicator" id="skuScanningIndicator">
                                                <i class="fas fa-circle-notch fa-spin"></i> Memindai...
                                            </div>
                                        </div>
                                    </div>
                                    <div class="error-feedback" id="skuError"></div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-info-circle"></i> Masukkan nomor SKU produk dan jumlahnya. Isian
                                        baru akan muncul otomatis.
                                    </small>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-keyboard"></i> Klik tombol "Simpan Data" atau tekan
                                        <kbd>CTRL</kbd>+<kbd>ENTER</kbd> untuk menyimpan.
                                    </small>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-exchange-alt"></i> Klik tombol "Toggle" untuk beralih antara mode
                                        Scanner dan Select dropdown.
                                    </small>
                                </div>

                                <div class="text-center mt-4">
                                    <div class="btn-group">
                                        <button type="button" id="submitManualBtn" class="btn btn-primary btn-lg me-2">
                                            <i class="fas fa-save me-1"></i> Simpan Data
                                        </button>
                                        <button type="button" id="resetScannerBtn" class="btn btn-warning btn-lg">
                                            <i class="fas fa-redo me-1"></i> Reset Form
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Scanner Form end -->

        <!-- History Sales Table start -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="mb-0">Daftar Riwayat Penjualan</h5>
                            <span class="text-muted current-date-info">Menampilkan data:
                                <strong>{{ date('d F Y') }}</strong></span>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-primary active" id="showActive">
                                <i class="fas fa-list me-1 d-none d-sm-inline"></i>Data Aktif
                            </button>
                            <button type="button" class="btn btn-outline-warning" id="showArchived">
                                <i class="fas fa-archive me-1 d-none d-sm-inline"></i>Data Terarsip
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-scroll-indicator">
                        <i class="fas fa-arrows-left-right me-1"></i> Geser kanan-kiri untuk melihat data lengkap
                    </div>
                    <div class="table-wrapper">
                        <table id="history-sales-table" class="table table-striped table-bordered">
                            <thead id="main-table-header">
                                <tr>
                                    <th style="width:.50px;">NO</th>
                                    <th style="width: 120px;">NO RESI</th>
                                    <th>NO SKU</th>
                                    <th style="width: 80px;">JUMLAH</th>
                                    <th style="width: 150px;">DIBUAT</th>
                                    <th style="width: 150px;">DIPERBARUI</th>
                                    <th style="width: 100px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi oleh DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- History Sales Table end -->
    </div>
    <!-- [ Main Content ] end -->

    <!-- Edit History Sale Modal -->
    <div class="modal fade" id="editHistorySaleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit History Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editHistorySaleForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit_history_sale_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">No Resi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="no_resi" id="edit_no_resi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SKU & Quantity</label>
                            <div id="edit-sku-container">
                                <!-- SKU inputs will be added here -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-edit-sku-btn">
                                <i class="fas fa-plus"></i> Add SKU
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Core JS files -->
    <script src="{{ URL::asset('build/js/plugins/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/bootstrap.min.js') }}"></script>

    <!-- DataTables Core -->
    <script src="{{ URL::asset('build/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Choices JS for Select2-like functionality -->
    <script src="{{ URL::asset('build/js/plugins/choices.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ URL::asset('js/history-sales-edit.js') }}"></script>

    <script>
        /**
         * History Sales Scanner Implementation
         * ===================================
         * This implementation follows a specific workflow for scanning No Resi and SKUs:
         * 
         * 1. No Resi Scanning Process:
         *    - Input field focused by default
         *    - Shows "Scanning..." indicator
         *    - Immediate validation via AJAX
         *    - Validates uniqueness of No Resi only when auto-scan is enabled
         *    - On valid: enables SKU input
         *    - On invalid: shows error, resets after 3s
         * 
         * 2. SKU Scanning Process:
         *    - Shows "Scanning..." indicator
         *    - Waits 2.5 seconds before validation
         *    - Validates SKU exists in Product database
         *    - Checks for duplicate SKUs within form
         *    - On error: shows error, resets SKU field after 3s
         *    - On valid: adds ONE new input field
         * 
         * 3. Manual Submit Process:
         *    - User must manually click "Simpan Data" button
         *    - Or press CTRL+ENTER keyboard shortcut
         *    - Only non-empty SKUs will be processed
         *    - Shows success/error message
         *    - Resets form on completion
         */
        // Declare table variable globally
        var table;

        $(document).ready(function() {

            // Prevent DataTables from showing error messages in console
            $.fn.dataTable.ext.errMode = 'none';

            // Constants for timing and validation - consolidated in one place
            const CONFIG = {
                RESET_TIMEOUT: 3000, // 3s reset delay
                SCAN_DELAY: 100, // 100ms between scans
                MIN_SKU_LENGTH: 3, // Minimum SKU length
                NEW_FIELD_DELAY: 100, // 100ms wait before adding new SKU field
                TABLE_CHUNK_SIZE: 500, // Chunk size for loading large datasets
                EXPORT_LIMIT: 10000, // Maximum number of records for export
                FOCUS_DELAY_RESI: 3000, // 3s delay for auto focus to no_resi after successful insert
                FOCUS_DELAY_SKU: 3000, // 3s delay for auto focus to no_sku after valid resi
                SKU_VALIDATION_DELAY: 2500 // 2.5s delay before SKU validation (as requested by user)
            };

            // State tracking variables - consolidated and organized
            const STATE = {
                newFieldTimer: null,
                isProcessing: false,
                hasValidResi: false,
                isScannerActive: true,
                isAddingNewField: false,
                resiValidationTimer: null,
                skuInputMode: 'text', // 'text' or 'select'
                choicesInstances: {}, // Store Choices.js instances
                skuCounter: 0 // Counter for unique IDs
            };

            // Initialize scanner toggle
            function initScannerToggle() {
                $('#scannerToggle').on('change', function() {
                    STATE.isScannerActive = $(this).is(':checked');

                    if (STATE.isScannerActive) {
                        $('#scannerStatus').text('Resi Auto-Scan').removeClass('scanner-inactive').addClass(
                            'scanner-active');
                        $('.scanner-mode-hint').text('Mode pindai otomatis');
                        $('#no_resi').addClass('scanner-active');
                    } else {
                        $('#scannerStatus').text('Resi Manual').removeClass('scanner-active').addClass(
                            'scanner-inactive');
                        $('.scanner-mode-hint').text('Mode input manual');
                        $('#no_resi').removeClass('scanner-active');
                    }
                }).trigger('change'); // Initialize the state
            }

            // Initialize SKU mode toggle
            function initSkuModeToggle() {
                // Main toggle button (outside containers)
                $(document).on('click', '#toggleSkuMode', function() {
                    toggleSkuInputMode();
                });

                // Individual toggle buttons (inside containers) - handled in bindSkuFieldEvents
                $(document).on('click', '.toggle-sku-mode', function() {
                    toggleSkuInputMode();
                });

                // Initialize with text mode
                updateSkuModeDisplay();

                // Bind events to the initial SKU container
                const initialContainer = $('.sku-input-container:first');
                if (initialContainer.length) {
                    // Set initial index if not set
                    if (!initialContainer.data('sku-index')) {
                        initialContainer.attr('data-sku-index', '0');
                    }
                    bindSkuFieldEvents(initialContainer);
                    updateRemoveButtonStates();
                }
            }

            // Toggle between text input and select dropdown for SKU
            function toggleSkuInputMode() {
                STATE.skuInputMode = STATE.skuInputMode === 'text' ? 'select' : 'text';

                // Update all existing SKU containers
                $('.sku-input-container').each(function() {
                    const container = $(this);
                    const index = container.data('sku-index') || 0;
                    const textInput = container.find('.sku-input');
                    const selectContainer = container.find('.sku-select-container');
                    const selectInput = container.find('.sku-select');

                    if (STATE.skuInputMode === 'select') {
                        // Switch to select mode
                        textInput.hide().prop('disabled', true);
                        selectContainer.show();
                        selectInput.prop('disabled', !STATE.hasValidResi);

                        // Initialize Choices.js if not already initialized
                        if (!STATE.choicesInstances[index]) {
                            initializeChoicesForSelect(selectInput[0], index);
                        }
                    } else {
                        // Switch to text mode
                        selectContainer.hide();
                        selectInput.prop('disabled', true);
                        textInput.show().prop('disabled', !STATE.hasValidResi);

                        // Destroy Choices.js instance if it exists
                        if (STATE.choicesInstances[index]) {
                            try {
                                STATE.choicesInstances[index].destroy();
                                delete STATE.choicesInstances[index];
                            } catch (e) {
                                console.warn('Error destroying Choices instance:', e);
                            }
                        }
                    }
                });

                updateSkuModeDisplay();
            }

            // Update SKU mode display text
            function updateSkuModeDisplay() {
                const modeText = STATE.skuInputMode === 'text' ? 'Scanner/Manual' : 'Select Dropdown';
                $('.sku-mode-text').text(modeText);
                $('#skuModeText').text(modeText); // Fallback for existing elements

                // Update icon
                const icon = STATE.skuInputMode === 'text' ? 'fas fa-keyboard' : 'fas fa-list';
                $('.mode-text i').attr('class', icon);
            }

            // Initialize Choices.js for a select element
            function initializeChoicesForSelect(selectElement, index) {
                if (!selectElement) return;

                try {
                    const choices = new Choices(selectElement, {
                        searchEnabled: true,
                        searchPlaceholderValue: "Cari SKU atau nama produk...",
                        itemSelectText: '',
                        placeholder: true,
                        placeholderValue: "-- Pilih atau cari SKU --",
                        noResultsText: 'Tidak ada SKU yang ditemukan',
                        noChoicesText: 'Ketik untuk mencari SKU...',
                        allowHTML: false,
                        shouldSort: false,
                        searchResultLimit: 10,
                        searchFloor: 2, // Minimum 2 characters to search
                        classNames: {
                            containerOuter: 'choices sku-choices',
                        },
                        callbackOnCreateTemplates: function(template) {
                            return {
                                item: (classNames, data) => {
                                    if (data.value === '') {
                                        return template(`
                                            <div class="${classNames.item} ${classNames.placeholder}">${data.label}</div>
                                        `);
                                    }
                                    return template(`
                                        <div class="${classNames.item} ${data.highlighted ? classNames.highlightedState : classNames.itemSelectable}" data-item data-id="${data.id}" data-value="${data.value}" ${data.active ? 'aria-selected="true"' : ''} ${data.disabled ? 'aria-disabled="true"' : ''}>
                                            <div class="product-choice-item">
                                                <div class="product-choice-sku">${data.customProperties?.sku || data.value}</div>
                                                <div class="product-choice-name">${data.customProperties?.name || ''}</div>
                                            </div>
                                        </div>
                                    `);
                                },
                                choice: (classNames, data) => {
                                    if (data.value === '') {
                                        return template(`
                                            <div class="${classNames.item} ${classNames.itemChoice} ${classNames.placeholder}" data-choice data-id="${data.id}" data-value="${data.value}" role="option">
                                                ${data.label}
                                            </div>
                                        `);
                                    }
                                    return template(`
                                        <div class="${classNames.item} ${classNames.itemChoice} ${data.disabled ? classNames.itemDisabled : classNames.itemSelectable}" data-select-text="${this.config.itemSelectText}" data-choice ${data.disabled ? 'data-choice-disabled aria-disabled="true"' : 'data-choice-selectable'} data-id="${data.id}" data-value="${data.value}" ${data.groupId > 0 ? 'role="treeitem"' : 'role="option"'}>
                                            <div class="product-choice-item">
                                                <div class="product-choice-sku">${data.customProperties?.sku || data.value}</div>
                                                <div class="product-choice-name">${data.customProperties?.name || ''}</div>
                                                <div class="product-choice-meta">${data.customProperties?.category || ''} | ${data.customProperties?.packaging || ''}</div>
                                            </div>
                                        </div>
                                    `);
                                }
                            };
                        }
                    });

                    // Store the instance
                    STATE.choicesInstances[index] = choices;

                    // Add search functionality
                    let searchTimeout;
                    selectElement.addEventListener('search', function(event) {
                        const searchTerm = event.detail.value;

                        if (searchTerm.length < 2) {
                            choices.clearChoices();
                            return;
                        }

                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            searchProducts(searchTerm, choices);
                        }, 300);
                    });

                    // Handle selection
                    selectElement.addEventListener('choice', function(event) {
                        const selectedValue = event.detail.choice.value;
                        const selectedSku = event.detail.choice.customProperties?.sku || selectedValue;

                        // Validate the selected SKU
                        if (selectedSku && selectedValue !== '') {
                            // Update the actual value to be the SKU
                            $(selectElement).attr('data-selected-sku', selectedSku);

                            // Trigger validation and new field creation immediately (like scanner)
                            setTimeout(() => {
                                handleSkuSelectionLikeScanner($(selectElement), selectedSku, index);
                            }, 100);
                        }
                    });

                } catch (error) {
                    console.error('Error initializing Choices.js:', error);
                }
            }

            // Handle SKU selection from dropdown - LIKE SCANNER (auto-add new field)
            function handleSkuSelectionLikeScanner(selectElement, sku, index) {
                if (!sku || !STATE.hasValidResi) return;

                // Check for duplicates
                if (isDuplicateSkuInForm(sku, selectElement)) {
                    selectElement.addClass('is-invalid');
                    showAlert('error', 'SKU Duplikat!', 'SKU duplikat terdeteksi: ' + sku);

                    // Reset only this select
                    setTimeout(() => {
                        if (STATE.choicesInstances[index]) {
                            STATE.choicesInstances[index].removeActiveItems();
                        }
                        selectElement.removeClass('is-invalid');
                    }, 2000);
                    return;
                }

                // Check against No Resi
                const noResi = $('#no_resi').val().trim();
                if (sku === noResi && noResi !== '') {
                    selectElement.addClass('is-invalid');
                    showAlert('warning', 'Perhatian!', 'Nilai No Resi tidak boleh sama dengan No SKU');

                    // Reset only this select
                    setTimeout(() => {
                        if (STATE.choicesInstances[index]) {
                            STATE.choicesInstances[index].removeActiveItems();
                        }
                        selectElement.removeClass('is-invalid');
                    }, 2000);
                    return;
                }

                // Validate SKU exists in database
                validateSkuAndCreateNewField(sku, selectElement, index, true); // true = from select mode
            }

            // Handle SKU selection from dropdown - LEGACY (for backward compatibility)
            function handleSkuSelection(selectElement, sku, index) {
                if (!sku || !STATE.hasValidResi) return;

                // Check for duplicates
                if (isDuplicateSkuInForm(sku, selectElement)) {
                    selectElement.addClass('is-invalid');
                    showAlert('error', 'SKU Duplikat!', 'SKU duplikat terdeteksi: ' + sku);

                    // Reset the select
                    setTimeout(() => {
                        if (STATE.choicesInstances[index]) {
                            STATE.choicesInstances[index].removeActiveItems();
                        }
                        selectElement.removeClass('is-invalid');
                    }, 2000);
                    return;
                }

                // Check against No Resi
                const noResi = $('#no_resi').val().trim();
                if (sku === noResi && noResi !== '') {
                    selectElement.addClass('is-invalid');
                    showAlert('warning', 'Perhatian!', 'Nilai No Resi tidak boleh sama dengan No SKU');

                    // Reset the select
                    setTimeout(() => {
                        if (STATE.choicesInstances[index]) {
                            STATE.choicesInstances[index].removeActiveItems();
                        }
                        selectElement.removeClass('is-invalid');
                    }, 2000);
                    return;
                }

                // Validate SKU exists in database
                validateSkuFromSelect(selectElement, sku, index);
            }

            // Validate SKU selected from dropdown
            function validateSkuFromSelect(selectElement, sku, index) {
                selectElement.addClass('is-validating');

                $.ajax({
                    url: "{{ route('history-sales.validate-sku') }}",
                    method: 'POST',
                    data: {
                        sku: sku,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    timeout: 3000,
                    success: function(response) {
                        selectElement.removeClass('is-validating');

                        if (response.valid) {
                            selectElement.removeClass('is-invalid').addClass('is-valid');

                            // Remove success class after 2 seconds
                            setTimeout(() => {
                                selectElement.removeClass('is-valid');
                            }, 2000);

                            // Add new field if this is the last container
                            if (!STATE.isAddingNewField && selectElement.closest('.sku-input-container')
                                .is(':last-child')) {
                                STATE.isAddingNewField = true;
                                setTimeout(() => {
                                    addNewSkuField();
                                    STATE.isAddingNewField = false;
                                }, CONFIG.NEW_FIELD_DELAY);
                            }
                        } else {
                            selectElement.removeClass('is-valid').addClass('is-invalid');
                            showAlert('error', 'SKU Tidak Valid!', response.message);

                            setTimeout(() => {
                                if (STATE.choicesInstances[index]) {
                                    STATE.choicesInstances[index].removeActiveItems();
                                }
                                selectElement.removeClass('is-invalid');
                            }, 3000);
                        }
                    },
                    error: function(xhr) {
                        selectElement.removeClass('is-validating');

                        const errorData = xhr.responseJSON;
                        if (errorData && errorData.message) {
                            selectElement.addClass('is-invalid');
                            showAlert('error', 'SKU Error!', errorData.message);

                            setTimeout(() => {
                                if (STATE.choicesInstances[index]) {
                                    STATE.choicesInstances[index].removeActiveItems();
                                }
                                selectElement.removeClass('is-invalid');
                            }, 3000);
                        }
                    }
                });
            }

            // Validate SKU and create new field (unified function for both text and select modes)
            function validateSkuAndCreateNewField(sku, inputElement, index, isFromSelectMode = false) {
                if (!sku || !STATE.hasValidResi) return;

                // Show validation indicator
                const container = inputElement.closest('.sku-input-container');
                const indicator = container.find('.scanning-indicator');
                indicator.addClass('active').html('<i class="fas fa-circle-notch fa-spin"></i> Memvalidasi SKU...');

                // AJAX validation
                $.ajax({
                    url: "{{ route('history-sales.validate-sku') }}",
                    method: 'POST',
                    data: {
                        sku: sku,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        indicator.removeClass('active');

                        if (response.valid) {
                            // SKU is valid - mark as valid
                            inputElement.addClass('is-valid').removeClass('is-invalid');

                            // Show success feedback
                            showAlert('success', 'SKU Valid!', response.message, 1000);

                            // Auto-add new field (like scanner behavior)
                            setTimeout(() => {
                                addNewSkuField();

                                // Focus the new field
                                setTimeout(() => {
                                    if (STATE.skuInputMode === 'text') {
                                        $('.sku-input:last').focus();
                                    } else {
                                        // Focus the Choices.js input in the new field
                                        const lastChoicesInput = $(
                                            '.sku-input-container:last .choices__input--cloned'
                                        );
                                        if (lastChoicesInput.length) {
                                            lastChoicesInput.focus();
                                        }
                                    }
                                }, 200);
                            }, 500);

                        } else {
                            // SKU is invalid - show error and reset only this field
                            inputElement.addClass('is-invalid').removeClass('is-valid');
                            showAlert('error', 'SKU Error!', response.message);

                            // Reset only this specific field after 3 seconds
                            setTimeout(() => {
                                if (isFromSelectMode && STATE.choicesInstances[index]) {
                                    // Reset Choices.js select
                                    STATE.choicesInstances[index].removeActiveItems();
                                } else {
                                    // Reset text input
                                    inputElement.val('');
                                }
                                inputElement.removeClass('is-invalid');

                                // Focus back to the same field
                                setTimeout(() => {
                                    if (isFromSelectMode) {
                                        const choicesInput = container.find(
                                            '.choices__input--cloned');
                                        if (choicesInput.length) {
                                            choicesInput.focus();
                                        }
                                    } else {
                                        inputElement.focus();
                                    }
                                }, 100);
                            }, 3000);
                        }
                    },
                    error: function(xhr) {
                        indicator.removeClass('active');
                        inputElement.addClass('is-invalid').removeClass('is-valid');

                        const errorMessage = xhr.responseJSON?.message ||
                            'Terjadi kesalahan saat validasi SKU';
                        showAlert('error', 'Error Validasi!', errorMessage);

                        // Reset only this specific field
                        setTimeout(() => {
                            if (isFromSelectMode && STATE.choicesInstances[index]) {
                                STATE.choicesInstances[index].removeActiveItems();
                            } else {
                                inputElement.val('');
                            }
                            inputElement.removeClass('is-invalid');
                        }, 3000);
                    }
                });
            }

            // Search products for Choices.js dropdown
            function searchProducts(searchTerm, choicesInstance) {
                $.ajax({
                    url: "{{ route('history-sales.search-products') }}",
                    method: 'GET',
                    data: {
                        term: searchTerm
                    },
                    success: function(response) {
                        if (response.results && response.results.length > 0) {
                            const choices = response.results.map(product => ({
                                value: product.sku,
                                label: `${product.sku} - ${product.name}`,
                                customProperties: {
                                    sku: product.sku,
                                    name: product.name,
                                    category: product.category,
                                    packaging: product.packaging,
                                    label: product.label
                                }
                            }));

                            choicesInstance.setChoices(choices, 'value', 'label', true);
                        } else {
                            choicesInstance.setChoices([], 'value', 'label', true);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error searching products:', xhr);
                        choicesInstance.setChoices([], 'value', 'label', true);
                    }
                });
            }

            /**
             * No Resi Input Handler - refactored for clarity
             */
            function initNoResiHandler() {
                $('#no_resi').on('input', function() {
                    const noResi = $(this).val().trim();
                    clearTimeout(STATE.submitTimer);

                    // Always show scanning indicator
                    $('#resiScanningIndicator').addClass('active');

                    if (!noResi) {
                        STATE.hasValidResi = false;
                        return;
                    }

                    // Only auto-validate if scanner is active, otherwise wait for blur
                    if (STATE.isScannerActive) {
                        validateNoResi(noResi, false);
                    }
                });

                // Manual validation for No Resi when scanner is disabled
                $('#no_resi').on('blur', function() {
                    const noResi = $(this).val().trim();

                    if (!STATE.isScannerActive && noResi) {
                        validateNoResi(noResi, true);
                    }
                });
            }

            /**
             * SKU Input Handler - refactored for clarity with delayed validation
             */
            function initSkuHandler() {
                $(document).on('input', '.sku-input', function() {
                    if (STATE.isProcessing || !STATE.hasValidResi) return;

                    const input = $(this);
                    const currentSku = input.val().trim();

                    // Check if the SKU input matches the No Resi value
                    const noResi = $('#no_resi').val().trim();
                    if (currentSku === noResi && noResi !== '') {
                        input.val(''); // Clear the input
                        showAlert('warning', 'Perhatian!', 'Nilai No Resi tidak boleh sama dengan No SKU');
                        return; // Stop processing
                    }

                    handleSkuInputWithDelay(input, currentSku);
                });

                // Quantity Input Handler
                $(document).on('input', '.qty-input', function() {
                    if (STATE.isProcessing || !STATE.hasValidResi) return;
                    // No auto-submit functionality - user must manually save
                });

                // Remove SKU Button Handler
                $(document).on('click', '.btn-remove-sku', function() {
                    const container = $(this).closest('.sku-input-container');
                    const containerCount = $('.sku-input-container').length;

                    // Always maintain at least 1 SKU container
                    if (containerCount > 1) {
                        const index = container.data('sku-index');

                        // Destroy Choices.js instance if exists
                        if (STATE.choicesInstances[index]) {
                            try {
                                STATE.choicesInstances[index].destroy();
                                delete STATE.choicesInstances[index];
                            } catch (e) {
                                console.warn('Error destroying Choices instance:', e);
                            }
                        }

                        container.fadeOut(200, function() {
                            $(this).remove();
                            updateRemoveButtonStates();
                        });
                    } else {
                        // If this is the last container, just clear it but don't remove
                        container.find('.sku-input').val('').removeClass('is-valid is-invalid');
                        container.find('.qty-input').val('1');

                        // Clear select and reset Choices.js if in select mode
                        const selectElement = container.find('.sku-select');
                        const index = container.data('sku-index') || 0;

                        if (STATE.choicesInstances[index]) {
                            try {
                                STATE.choicesInstances[index].removeActiveItems();
                            } catch (e) {
                                console.warn('Error clearing Choices instance:', e);
                            }
                        }
                        selectElement.val('').removeAttr('data-selected-sku');

                        // Show tooltip near the button instead of popup alert
                        const removeBtn = $(this);
                        removeBtn.attr('data-bs-original-title',
                                'Field telah dibersihkan. Minimal harus ada 1 field SKU.')
                            .tooltip('show');

                        // Hide tooltip after 3 seconds
                        setTimeout(() => {
                            removeBtn.attr('data-bs-original-title',
                                    'Minimal harus ada 1 field SKU')
                                .tooltip('hide');
                        }, 3000);

                        // Focus the appropriate input
                        setTimeout(() => {
                            if (STATE.skuInputMode === 'text') {
                                container.find('.sku-input').focus();
                            } else {
                                const choicesInput = container.find('.choices__input--cloned');
                                if (choicesInput.length) {
                                    choicesInput.focus();
                                }
                            }
                        }, 100);
                    }
                });

                // Add paste event handler to prevent pasting No Resi into SKU
                $(document).on('paste', '.sku-input', function(e) {
                    const noResi = $('#no_resi').val().trim();

                    // Get pasted content
                    let pastedText;
                    if (window.clipboardData && window.clipboardData.getData) {
                        pastedText = window.clipboardData.getData('Text');
                    } else if (e.originalEvent && e.originalEvent.clipboardData && e.originalEvent
                        .clipboardData.getData) {
                        pastedText = e.originalEvent.clipboardData.getData('text/plain');
                    }

                    // Check if pasted content is No Resi
                    if (pastedText && pastedText.trim() === noResi && noResi !== '') {
                        e.preventDefault();
                        showAlert('warning', 'Perhatian!', 'Nilai No Resi tidak boleh sama dengan No SKU');
                    }
                });

                // Add scan detection for better handling of barcode scanner input
                $(document).on('keypress', '.sku-input', function(e) {
                    // If Enter key is pressed very shortly after input (typical for scanners)
                    if (e.which === 13) {
                        e.preventDefault(); // Prevent form submission
                        const input = $(this);
                        const currentSku = input.val().trim();
                        const noResi = $('#no_resi').val().trim();

                        if (currentSku === noResi && noResi !== '') {
                            input.val(''); // Clear the input
                            showAlert('warning', 'Perhatian!',
                                'Nilai No Resi tidak boleh sama dengan No SKU');
                            return; // Stop processing
                        }
                    }
                });

                // Add ENTER key event to manually submit the form - but only when explicitly pressed
                // as a separate action, not during scanning
                $(document).on('keydown', function(e) {
                    // Check if the key pressed is Enter (key code 13)
                    if (e.which === 13 && STATE.hasValidResi && hasSKUsWithContent()) {
                        // Get the active element to determine context
                        const activeElement = document.activeElement;

                        // Only process ENTER key when:
                        // 1. Focus is NOT on any input field (document body has focus) OR
                        // 2. User explicitly presses CTRL+ENTER in any input
                        if (
                            (activeElement === document.body) ||
                            (e.ctrlKey === true)
                        ) {
                            // This is an explicit submission request, not part of scanning
                            e.preventDefault();

                            // Show a brief flash notification
                            showAlert('info', 'Menyimpan Data', 'Menyimpan dengan tombol ENTER', 800);

                            // Submit the form
                            submitForm();
                        }
                    }
                });
            }

            // Check if any SKU inputs have content
            function hasSKUsWithContent() {
                let hasContent = false;
                $('.sku-input-container').each(function() {
                    const container = $(this);
                    let sku = '';

                    // Get SKU value based on current input mode
                    if (STATE.skuInputMode === 'text') {
                        sku = container.find('.sku-input').val().trim();
                    } else {
                        const selectElement = container.find('.sku-select');
                        sku = selectElement.val() || selectElement.attr('data-selected-sku') || '';
                        sku = sku.trim();
                    }

                    if (sku.length >= CONFIG.MIN_SKU_LENGTH) {
                        hasContent = true;
                        return false; // break the loop
                    }
                });
                return hasContent;
            }



            /**
             * Initialize form buttons
             */
            function initFormButtons() {
                // Reset scanner button
                $('#resetScannerBtn').on('click', function() {
                    resetForm();
                    // Ensure focus after manual reset
                    setTimeout(() => {
                        $('#no_resi').focus().select();
                    }, 100);
                });

                // Submit manual button
                $('#submitManualBtn').on('click', function() {
                    // Only submit if we have a valid resi and at least one SKU field has content
                    if (STATE.hasValidResi) {
                        if (hasSKUsWithContent()) {
                            submitForm();
                        } else {
                            showAlert('warning', 'Perhatian!',
                                'Anda harus mengisi minimal satu SKU untuk melanjutkan');
                        }
                    } else {
                        showAlert('warning', 'Perhatian!', 'No Resi harus diisi dan valid terlebih dahulu');
                        // Auto focus to no_resi when validation fails
                        setTimeout(() => {
                            $('#no_resi').focus().select();
                        }, 100);
                    }
                });

                // Prevent default form submission
                $('#historySaleForm').on('submit', function(e) {
                    e.preventDefault();
                });
            }

            /**
             * Initialize filter buttons
             */
            function initFilterButtons() {
                // Filter buttons handler
                $('#showActive').on('click', function() {
                    $(this).addClass('active').siblings().removeClass('active');
                    table.ajax.reload();
                });

                $('#showArchived').on('click', function() {
                    $(this).addClass('active').siblings().removeClass('active');
                    table.ajax.reload();
                });
            }

            /**
             * Helper Functions - Refactored for better organization
             */
            function validateNoResi(noResi, allowDuplicates) {
                // Tambahkan indikator loading pada input
                const $noResiInput = $('#no_resi');
                $noResiInput.addClass('is-validating');

                // Batasi permintaan dengan debounce sederhana
                if (STATE.resiValidationTimer) {
                    clearTimeout(STATE.resiValidationTimer);
                }

                STATE.resiValidationTimer = setTimeout(() => {
                    $.ajax({
                        url: "{{ route('history-sales.validate-no-resi') }}",
                        method: 'POST',
                        data: {
                            no_resi: noResi,
                            allow_duplicates: allowDuplicates
                        },
                        cache: false,
                        timeout: 3000, // 3 detik timeout
                        success: function(response) {
                            $noResiInput.removeClass('is-validating');
                            if (response.valid) {
                                handleValidResi();
                            } else {
                                handleInvalidResi(response.message);
                            }
                        },
                        error: function(xhr) {
                            $noResiInput.removeClass('is-validating');
                            handleResiError(xhr.responseJSON?.message ||
                                'Error validasi Nomor Resi');
                        }
                    });
                }, 300); // 300ms debounce
            }

            function handleValidResi() {
                STATE.hasValidResi = true;
                $('#resiError').hide();

                // Enable the appropriate SKU input based on current mode
                const firstContainer = $('.sku-input-container:first');
                if (STATE.skuInputMode === 'text') {
                    firstContainer.find('.sku-input').prop('disabled', false);
                } else {
                    firstContainer.find('.sku-select').prop('disabled', false);
                    // Initialize Choices.js for the first select if not already initialized
                    const firstSelectElement = firstContainer.find('.sku-select')[0];
                    if (firstSelectElement && !STATE.choicesInstances[0]) {
                        initializeChoicesForSelect(firstSelectElement, 0);
                    }
                }

                // Enable remove button
                firstContainer.find('.btn-remove-sku').prop('disabled', false);

                // Add a visual indicator showing scan direction
                $('#no_resi').removeClass('scanner-active');
                if (STATE.skuInputMode === 'text') {
                    firstContainer.find('.sku-input').addClass('scanner-active');
                } else {
                    // For select mode, add focus to the Choices.js container
                    firstContainer.find('.choices').addClass('scanner-active');
                }

                // Add a visual hint that we're now scanning SKUs
                showAlert('success', 'No Resi Valid', 'Silakan pindai No SKU', 1000);

                // Only auto-focus if scanner is active
                if (STATE.isScannerActive) {
                    // Auto focus to SKU with 3-second countdown
                    ensureSkuFocus(true);
                    $('#resiScanningIndicator').removeClass('active');
                } else {
                    $('#resiScanningIndicator').removeClass('active');
                    // Focus stays on No Resi field when scanner is inactive
                }
            }

            function handleInvalidResi(message) {
                showError('resiError', message || 'Nomor Resi sudah ada dalam sistem');
                setTimeout(() => {
                    resetForm();
                    // Ensure focus after invalid resi reset
                    setTimeout(() => {
                        $('#no_resi').focus().select();
                    }, 100);
                }, CONFIG.RESET_TIMEOUT);
            }

            function handleResiError(message) {
                showError('resiError', message || 'Error validasi Nomor Resi');
                setTimeout(() => {
                    resetForm();
                    // Ensure focus after resi error reset
                    setTimeout(() => {
                        $('#no_resi').focus().select();
                    }, 100);
                }, CONFIG.RESET_TIMEOUT);
            }

            /**
             * Handle SKU input with 2-3 second delay before validation
             * NEW FLOW IMPLEMENTATION:
             * 1. User inputs Resi -> moves to SKU input
             * 2. User inputs SKU -> waits 2-3 seconds 
             * 3. System validates SKU against Product.php
             * 4. If SKU error: reset SKU field
             * 5. If SKU valid: add new field for next SKU
             * 6. User must manually save with button or CTRL+ENTER
             */
            function handleSkuInputWithDelay(input, currentSku) {
                if (!currentSku) return;

                // Clear any existing timers
                clearTimeout(STATE.newFieldTimer);

                // Clear any existing validation timer for this input
                if (input.data('validationTimer')) {
                    clearTimeout(input.data('validationTimer'));
                }

                const container = input.closest('.sku-input-container');
                const indicator = container.find('.scanning-indicator');
                indicator.addClass('active');

                if (currentSku.length >= CONFIG.MIN_SKU_LENGTH) {
                    // Check for duplicates immediately (no delay needed for this)
                    if (isDuplicateSkuInForm(currentSku, input)) {
                        // Mark this specific input as invalid
                        input.addClass('is-invalid');
                        showAlert('error', 'SKU Duplikat!', 'SKU duplikat terdeteksi: ' + currentSku);

                        // Remove invalid class and clear field after delay
                        setTimeout(() => {
                            input.removeClass('is-invalid').val('').focus();
                        }, 2000);
                        indicator.removeClass('active');
                        return;
                    }

                    // Show waiting message for validation delay
                    indicator.html('<i class="fas fa-clock"></i> Menunggu validasi...');

                    // Set delay for validation (2-3 seconds as requested)
                    const validationTimer = setTimeout(() => {
                        const index = container.data('sku-index') || 0;
                        validateSkuAndCreateNewField(currentSku, input, index,
                            false); // false = from text mode
                    }, CONFIG.SKU_VALIDATION_DELAY); // Use config constant

                    // Store timer reference in input element
                    input.data('validationTimer', validationTimer);

                } else {
                    indicator.removeClass('active');
                }
            }

            /**
             * Helper function to reset scanning indicator to default state
             */
            function resetScanningIndicator() {
                $('#skuScanningIndicator').removeClass('active').html(
                    '<i class="fas fa-circle-notch fa-spin"></i> Memindai...');
            }

            /**
             * Original SKU handler (kept for compatibility if needed)
             */
            function handleSkuInput(input, currentSku) {
                if (!currentSku) return;

                clearTimeout(STATE.submitTimer);
                clearTimeout(STATE.newFieldTimer);

                $('#skuScanningIndicator').addClass('active');

                if (currentSku.length >= CONFIG.MIN_SKU_LENGTH) {
                    // Check for duplicates
                    if (isDuplicateSkuInForm(currentSku, input)) {
                        // Mark this specific input as invalid
                        input.addClass('is-invalid');
                        showAlert('error', 'SKU Duplikat!', 'SKU duplikat terdeteksi: ' + currentSku);

                        // Remove invalid class and clear field after delay
                        setTimeout(() => {
                            input.removeClass('is-invalid').val('').focus();
                        }, 2000);
                        return;
                    }

                    // Validate SKU exists in database (real-time validation)
                    validateSkuRealTime(input, currentSku);

                    // Only add new field if we're not already in the process
                    if (!STATE.isAddingNewField && input.closest('.sku-input-container').is(':last-child')) {
                        STATE.isAddingNewField = true;
                        setTimeout(() => {
                            addNewSkuField();
                            STATE.isAddingNewField = false;
                        }, CONFIG.NEW_FIELD_DELAY);
                    }

                    // No auto-submit - user must manually save
                    $('#skuScanningIndicator').removeClass('active');
                }
            }

            /**
             * Real-time SKU validation function with smart countdown logic
             */
            function validateSkuRealTimeWithSmartCountdown(input, sku) {
                // Add loading indicator
                input.addClass('is-validating');

                $.ajax({
                    url: "{{ route('history-sales.validate-sku') }}",
                    method: 'POST',
                    data: {
                        sku: sku,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    timeout: 3000,
                    success: function(response) {
                        input.removeClass('is-validating');
                        $('#skuScanningIndicator').removeClass('active');

                        if (response.valid) {
                            // SKU is valid, show success briefly
                            input.removeClass('is-invalid').addClass('is-valid');

                            // Remove success class after 2 seconds
                            setTimeout(() => {
                                input.removeClass('is-valid');
                            }, 2000);

                            // Add new field if we're not already in the process
                            if (!STATE.isAddingNewField && input.closest('.sku-input-container').is(
                                    ':last-child')) {
                                STATE.isAddingNewField = true;
                                setTimeout(() => {
                                    addNewSkuField();
                                    STATE.isAddingNewField = false;
                                }, CONFIG.NEW_FIELD_DELAY);
                            }

                            // No auto-submit - user must manually save

                        } else {
                            // SKU is invalid, mark as error but DON'T start countdown
                            input.removeClass('is-valid').addClass('is-invalid');
                            showAlert('error', 'SKU Tidak Valid!', response.message);

                            // Clear the invalid SKU after delay - NO COUNTDOWN if this was the only SKU
                            setTimeout(() => {
                                input.removeClass('is-invalid').val('').focus();
                            }, 3000);
                        }
                    },
                    error: function(xhr) {
                        input.removeClass('is-validating');
                        $('#skuScanningIndicator').removeClass('active');

                        // Handle validation error - NO COUNTDOWN on error
                        const errorData = xhr.responseJSON;
                        if (errorData && errorData.message) {
                            input.addClass('is-invalid');
                            showAlert('error', 'SKU Error!', errorData.message);

                            setTimeout(() => {
                                input.removeClass('is-invalid').val('').focus();
                            }, 3000);
                        }
                    }
                });
            }

            /**
             * Real-time SKU validation function
             */
            function validateSkuRealTime(input, sku) {
                // Add loading indicator
                input.addClass('is-validating');

                $.ajax({
                    url: "{{ route('history-sales.validate-sku') }}",
                    method: 'POST',
                    data: {
                        sku: sku,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    timeout: 3000,
                    success: function(response) {
                        input.removeClass('is-validating');

                        if (response.valid) {
                            // SKU is valid, show success briefly
                            input.removeClass('is-invalid').addClass('is-valid');

                            // Remove success class after 2 seconds
                            setTimeout(() => {
                                input.removeClass('is-valid');
                            }, 2000);
                        } else {
                            // SKU is invalid, mark as error
                            input.removeClass('is-valid').addClass('is-invalid');
                            showAlert('error', 'SKU Tidak Valid!', response.message);

                            // Clear the invalid SKU after delay
                            setTimeout(() => {
                                input.removeClass('is-invalid').val('').focus();
                            }, 3000);
                        }
                    },
                    error: function(xhr) {
                        input.removeClass('is-validating');

                        // Handle validation error
                        const errorData = xhr.responseJSON;
                        if (errorData && errorData.message) {
                            input.addClass('is-invalid');
                            showAlert('error', 'SKU Error!', errorData.message);

                            setTimeout(() => {
                                input.removeClass('is-invalid').val('').focus();
                            }, 3000);
                        }
                    }
                });
            }

            function isDuplicateSkuInForm(sku, currentInput) {
                let duplicate = false;
                const currentContainer = currentInput.closest('.sku-input-container');

                $('.sku-input-container').not(currentContainer).each(function() {
                    const container = $(this);
                    let existingSku = '';

                    // Get SKU value based on input type in this container
                    const textInput = container.find('.sku-input');
                    const selectInput = container.find('.sku-select');

                    if (textInput.is(':visible') && !textInput.prop('disabled')) {
                        existingSku = textInput.val().trim();
                    } else if (selectInput.is(':visible') && !selectInput.prop('disabled')) {
                        existingSku = selectInput.val() || selectInput.attr('data-selected-sku') || '';
                        existingSku = existingSku.trim();
                    }

                    if (existingSku && existingSku === sku) {
                        duplicate = true;
                        return false;
                    }
                });
                return duplicate;
            }



            function showAlert(icon, title, message, timer = 2000) {
                const titles = {
                    'success': 'Berhasil!',
                    'error': 'Terjadi Kesalahan!',
                    'warning': 'Perhatian!',
                    'info': 'Informasi'
                };

                Swal.fire({
                    title: title || titles[icon] || '',
                    text: message,
                    icon: icon,
                    timer: timer,
                    showConfirmButton: false
                });
            }

            function showError(elementId, message) {
                $(`#${elementId}`).text(message).show();
                showAlert('error', 'Terjadi Kesalahan!', message);
            }

            function addNewSkuField() {
                // Always add new field when called (validation is done before calling this function)
                // This simplifies the logic and makes it work like a scanner

                if (STATE.isAddingNewField) return; // Prevent double addition
                STATE.isAddingNewField = true;
                STATE.skuCounter++;
                const newIndex = STATE.skuCounter;
                const textDisplay = STATE.skuInputMode === 'text' ? 'block' : 'none';
                const selectDisplay = STATE.skuInputMode === 'select' ? 'block' : 'none';
                const textDisabled = STATE.skuInputMode === 'text' ? '' : 'disabled';
                const selectDisabled = STATE.skuInputMode === 'select' ? '' : 'disabled';

                const newSkuContainer = `
                        <div class="sku-input-container" data-sku-index="${newIndex}">
                            <div class="sku-mode-indicator">
                                <div class="mode-text">
                                    <i class="fas fa-keyboard"></i> Mode Input: <span class="sku-mode-text">${STATE.skuInputMode === 'text' ? 'Scanner/Manual' : 'Select Dropdown'}</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary toggle-sku-mode" title="Toggle antara Scanner dan Select">
                                    <i class="fas fa-exchange-alt"></i> Toggle
                                </button>
                            </div>

                            <div class="sku-input-row">
                                <div class="sku-input-field">
                                    <!-- Text Input -->
                            <input type="text" class="form-control scanner-input sku-input" 
                                        name="no_sku[]" required placeholder="Ketik atau pindai nomor SKU di sini..." 
                                        id="sku-text-input-${newIndex}" style="display: ${textDisplay};" ${textDisabled}>
                                    
                                    <!-- Select Input -->
                                    <div class="sku-select-container" style="display: ${selectDisplay};">
                                        <select class="form-control sku-select" name="no_sku_select[]" 
                                            id="sku-select-input-${newIndex}" ${selectDisabled}>
                                            <option value="">-- Pilih atau cari SKU --</option>
                                        </select>
                                    </div>
                                </div>

                            <input type="number" class="form-control qty-input" 
                                    name="qty[]" value="1" min="1" title="Jumlah barang">
                                
                            <button type="button" class="btn btn-danger btn-remove-sku" title="Hapus SKU">
                                <i class="fas fa-minus"></i>
                            </button>
                            </div>
                            
                            <div class="scanning-indicator" id="skuScanningIndicator-${newIndex}">
                                <i class="fas fa-circle-notch fa-spin"></i> Memindai...
                            </div>
                        </div>
                    `;
                $('#sku-container').append(newSkuContainer);

                // Initialize Choices.js for the new select if in select mode
                if (STATE.skuInputMode === 'select') {
                    const newSelectElement = document.getElementById(`sku-select-input-${newIndex}`);
                    if (newSelectElement) {
                        initializeChoicesForSelect(newSelectElement, newIndex);
                    }
                }

                // Bind events to the new container
                bindSkuFieldEvents($('.sku-input-container:last'));

                // Update remove button states
                updateRemoveButtonStates();

                // Auto focus to new SKU input with 2-second countdown
                ensureNewSkuFocus(newIndex);

                STATE.isAddingNewField = false;
            }

            // Bind events to SKU field container
            function bindSkuFieldEvents(container) {
                // Bind remove button click
                container.find('.btn-remove-sku').on('click', function() {
                    const containerToRemove = $(this).closest('.sku-input-container');
                    const containerCount = $('.sku-input-container').length;
                    const index = containerToRemove.data('sku-index');

                    // Always maintain at least 1 SKU container
                    if (containerCount > 1) {
                        // Destroy Choices.js instance if exists
                        if (STATE.choicesInstances[index]) {
                            try {
                                STATE.choicesInstances[index].destroy();
                                delete STATE.choicesInstances[index];
                            } catch (e) {
                                console.warn('Error destroying Choices instance:', e);
                            }
                        }

                        containerToRemove.remove();
                        updateRemoveButtonStates();
                    } else {
                        // If this is the last container, just clear it but don't remove
                        containerToRemove.find('.sku-input').val('').removeClass('is-valid is-invalid');
                        containerToRemove.find('.qty-input').val('1');

                        // Clear select and reset Choices.js if in select mode
                        const selectElement = containerToRemove.find('.sku-select');

                        if (STATE.choicesInstances[index]) {
                            try {
                                STATE.choicesInstances[index].removeActiveItems();
                            } catch (e) {
                                console.warn('Error clearing Choices instance:', e);
                            }
                        }
                        selectElement.val('').removeAttr('data-selected-sku');

                        // Show tooltip near the button instead of popup alert
                        const removeBtn = $(this);
                        removeBtn.attr('data-bs-original-title',
                                'Field telah dibersihkan. Minimal harus ada 1 field SKU.')
                            .tooltip('show');

                        // Hide tooltip after 3 seconds
                        setTimeout(() => {
                            removeBtn.attr('data-bs-original-title',
                                    'Minimal harus ada 1 field SKU')
                                .tooltip('hide');
                        }, 3000);

                        // Focus the appropriate input
                        setTimeout(() => {
                            if (STATE.skuInputMode === 'text') {
                                containerToRemove.find('.sku-input').focus();
                            } else {
                                const choicesInput = containerToRemove.find(
                                    '.choices__input--cloned');
                                if (choicesInput.length) {
                                    choicesInput.focus();
                                }
                            }
                        }, 100);
                    }
                });

                // Bind toggle mode button for individual containers
                container.find('.toggle-sku-mode').on('click', function() {
                    toggleSkuInputMode();
                });

                // Bind SKU input events
                const skuInput = container.find('.sku-input');
                skuInput.on('input', function() {
                    handleSkuInputWithDelay($(this));
                });

                skuInput.on('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        handleSkuInputWithDelay($(this));
                    }
                });

                // Bind quantity input events
                const qtyInput = container.find('.qty-input');
                qtyInput.on('change', function() {
                    const value = parseInt($(this).val());
                    if (value < 1) {
                        $(this).val(1);
                    }
                });
            }

            // Update remove button states
            function updateRemoveButtonStates() {
                const containers = $('.sku-input-container');
                const removeButtons = containers.find('.btn-remove-sku');

                if (containers.length <= 1) {
                    // Disable remove button if only one container - but keep it visible
                    removeButtons.prop('disabled', true).attr('title', 'Minimal harus ada 1 field SKU');
                } else {
                    // Enable all remove buttons if more than one container
                    removeButtons.prop('disabled', false).attr('title', 'Hapus SKU');
                }
            }

            function resetSkuForm() {
                clearTimeout(STATE.newFieldTimer);

                // Destroy all Choices.js instances except the first one
                Object.keys(STATE.choicesInstances).forEach(index => {
                    if (index !== '0') {
                        try {
                            STATE.choicesInstances[index].destroy();
                            delete STATE.choicesInstances[index];
                        } catch (e) {
                            console.warn('Error destroying Choices instance:', e);
                        }
                    }
                });

                // Remove all SKU containers except the first one (always maintain at least 1)
                $('.sku-input-container:not(:first)').remove();

                // Reset first container values but keep the container
                const firstContainer = $('.sku-input-container:first');
                firstContainer.find('.sku-input').val('').removeClass('is-valid is-invalid');
                firstContainer.find('.qty-input').val('1');

                // Reset select and destroy/recreate Choices.js instance if exists
                const firstSelect = firstContainer.find('.sku-select');
                if (STATE.choicesInstances[0]) {
                    try {
                        STATE.choicesInstances[0].destroy();
                        delete STATE.choicesInstances[0];
                    } catch (e) {
                        console.warn('Error destroying Choices instance:', e);
                    }
                }
                firstSelect.val('').removeAttr('data-selected-sku');

                $('#skuError').hide();
                resetScanningIndicator();

                // Reset counter but ensure we always have the first container
                STATE.skuCounter = 0;
                firstContainer.attr('data-sku-index', '0');

                // Update remove button states (will disable the button since only 1 container remains)
                updateRemoveButtonStates();

                // Auto focus back to first appropriate input after reset
                setTimeout(() => {
                    if (STATE.skuInputMode === 'text') {
                        $('.sku-input:first').focus().select();
                    } else {
                        // Re-initialize Choices.js for the first select
                        const firstSelectElement = firstContainer.find('.sku-select')[0];
                        if (firstSelectElement && STATE.hasValidResi) {
                            initializeChoicesForSelect(firstSelectElement, 0);
                        }
                    }
                }, 100);
            }

            function resetForm() {
                clearTimeout(STATE.newFieldTimer);

                // Clear any existing focus countdowns
                clearFocusCountdowns();

                // Destroy all Choices.js instances
                Object.keys(STATE.choicesInstances).forEach(index => {
                    try {
                        STATE.choicesInstances[index].destroy();
                        delete STATE.choicesInstances[index];
                    } catch (e) {
                        console.warn('Error destroying Choices instance:', e);
                    }
                });

                $('#no_resi').val('').prop('disabled', false);
                // Reset scanner active indicator
                $('#no_resi').addClass('scanner-active');
                $('.sku-input').removeClass('scanner-active');

                $('.sku-input-container:not(:first)').remove();

                // Reset first container
                const firstContainer = $('.sku-input-container:first');
                firstContainer.find('.sku-input').val('').prop('disabled', true);
                firstContainer.find('.sku-select').val('').removeAttr('data-selected-sku').prop('disabled', true);
                firstContainer.find('.qty-input').val('1');
                firstContainer.find('.btn-remove-sku').prop('disabled', true);

                $('.error-feedback').hide();
                resetScanningIndicator();

                // Reset counter and index
                STATE.skuCounter = 0;
                firstContainer.attr('data-sku-index', '0');

                // Update remove button states
                updateRemoveButtonStates();

                STATE.isProcessing = false;
                STATE.hasValidResi = false;

                // Auto focus with helper function (immediate, no countdown for manual reset)
                ensureNoResiFocus(false);
            }



            async function submitForm() {
                if (STATE.isProcessing || !STATE.hasValidResi) return;

                STATE.isProcessing = true;

                // Tampilkan indikator loading
                const loadingIndicator = Swal.fire({
                    title: 'Menyimpan Data',
                    html: 'Mohon tunggu sebentar...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Dapatkan data form dan jangan kirim data kosong
                const formData = new FormData(document.getElementById('historySaleForm'));
                const formDataObj = {};

                // Kumpulkan SKUs dan quantities valid
                const skus = [];
                const quantities = [];

                // Get No Resi value to check for duplicates
                const noResi = $('#no_resi').val().trim();

                // Check for duplicate values between No Resi and SKUs
                let hasDuplicateWithResi = false;

                $('.sku-input-container').each(function(index) {
                    const container = $(this);
                    let sku = '';

                    // Get SKU value based on current input mode
                    if (STATE.skuInputMode === 'text') {
                        sku = container.find('.sku-input').val().trim();
                    } else {
                        const selectElement = container.find('.sku-select');
                        sku = selectElement.val() || selectElement.attr('data-selected-sku') || '';
                        sku = sku.trim();
                    }

                    // Only process SKUs that have valid content and are not empty
                    if (sku.length >= CONFIG.MIN_SKU_LENGTH && sku !== '') {
                        // Check if SKU matches No Resi
                        if (sku === noResi) {
                            hasDuplicateWithResi = true;
                            return false; // break the loop
                        }

                        // Additional validation: ensure SKU is not just whitespace
                        if (sku.replace(/\s/g, '').length > 0) {
                            skus.push(sku);
                            quantities.push(container.find('.qty-input').val() || 1);
                        }
                    }
                });

                // Check if we have at least one valid SKU
                if (skus.length === 0) {
                    loadingIndicator.close();
                    showAlert('warning', 'Perhatian!',
                        'Minimal satu SKU harus diisi dengan benar sebelum menyimpan.');
                    STATE.isProcessing = false;
                    return;
                }

                // If duplicate found between No Resi and No SKU, show warning and stop submission
                if (hasDuplicateWithResi) {
                    loadingIndicator.close();
                    showAlert('warning', 'Perhatian!',
                        'Nilai No Resi tidak boleh sama dengan No SKU. Silakan periksa kembali.');
                    STATE.isProcessing = false;
                    return;
                }

                // Buat data yang akan dikirim
                formDataObj.no_resi = noResi;
                formDataObj.no_sku = skus;
                formDataObj.qty = quantities;
                formDataObj._token = $('meta[name="csrf-token"]').attr('content');
                formDataObj.allow_duplicates = !STATE.isScannerActive;

                try {
                    // Eksekusi request AJAX dengan waktu timeout yang lebih pendek
                    const response = await $.ajax({
                        url: "{{ route('history-sales.store') }}",
                        type: "POST",
                        data: formDataObj,
                        dataType: 'json',
                        timeout: 5000, // 5 detik timeout
                        cache: false
                    });

                    loadingIndicator.close();
                    handleSubmitResponse(response);
                } catch (error) {
                    console.error('Error submitting form:', error);
                    loadingIndicator.close();
                    handleSubmitError(error);
                } finally {
                    STATE.isProcessing = false;
                }
            }

            function handleSubmitResponse(response) {
                if (response.status === 'success') {
                    // Reset the form immediately to prepare for the next entry
                    resetForm();

                    // Show success message
                    showAlert('success', 'Berhasil!', response.message, 1000);

                    // Auto focus to no_resi input after successful insert with 3-second countdown
                    ensureNoResiFocus(true);

                    // Force immediate table reload with the new data - try 3 times in case of issues
                    setTimeout(() => {
                        try {
                            if (table) {
                                // Force a full reload to ensure we get the latest data
                                table.ajax.reload(null, false);

                                // Add a backup reload in case the first one doesn't catch the latest data
                                setTimeout(() => table.ajax.reload(null, false), 500);
                            }
                        } catch (e) {
                            console.warn('Error refreshing table:', e);
                        }
                    }, 300);
                } else {
                    throw new Error(response.message || 'Terjadi kesalahan pada server');
                }
            }

            function handleSubmitError(error) {
                const errorData = error.responseJSON;

                // Check if this is a partial reset error (specific SKU issue)
                if (errorData && errorData.partial_reset && errorData.sku_index !== undefined) {
                    // Only reset the specific SKU field that has the error
                    const errorIndex = errorData.sku_index;
                    const skuContainers = $('.sku-input-container');

                    if (skuContainers[errorIndex]) {
                        const errorContainer = $(skuContainers[errorIndex]);
                        const skuInput = errorContainer.find('.sku-input');
                        const qtyInput = errorContainer.find('.qty-input');

                        // Clear only the problematic SKU field
                        skuInput.val('').focus().addClass('is-invalid');
                        qtyInput.val('1');

                        // Show error message specific to this field
                        showAlert('error', 'SKU Error!',
                            `${errorData.message}\n\nField SKU ke-${errorIndex + 1} telah direset. Silakan perbaiki dan lanjutkan.`
                        );

                        // Remove invalid class after a few seconds
                        setTimeout(() => {
                            skuInput.removeClass('is-invalid');
                        }, 3000);

                        // Don't reset the entire form, keep other valid SKUs
                        return;
                    }
                }

                // Fallback to full error handling for other types of errors
                showError('skuError', errorData?.message || error.message || 'Failed to save data');
                setTimeout(() => {
                    resetForm();
                    // Ensure focus after error reset
                    setTimeout(() => {
                        $('#no_resi').focus().select();
                    }, 100);
                }, CONFIG.RESET_TIMEOUT);
            }

            /**
             * Update current date info display
             */
            function updateCurrentDateInfo() {
                const today = new Date();
                const options = {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                    timeZone: 'Asia/Jakarta'
                };

                // Format tanggal ke Bahasa Indonesia
                const indonesianDate = today.toLocaleDateString('id-ID', options);

                // Update text pada elemen
                $('.current-date-info strong').text(indonesianDate);
            }

            /**
             * Helper function to ensure consistent auto focus behavior with delay
             */
            function ensureNoResiFocus(showCountdown = false) {
                // Clear any existing countdowns first
                clearFocusCountdowns();

                if (showCountdown) {
                    // Show countdown for auto focus
                    let countdown =
                        1.5; // COUNTDOWN DURATION: Change this value to modify countdown time (in seconds)
                    const countdownElement = $('<div id="focus-countdown">Auto focus ke No Resi dalam ' +
                        countdown + ' detik</div>');
                    $('body').append(countdownElement);

                    window.focusCountdownInterval = setInterval(() => {
                        countdown -=
                            0.1; // COUNTDOWN DECREMENT: Decrease by 0.1 seconds for smoother countdown
                        if (countdown > 0) {
                            countdownElement.text('Auto focus ke No Resi dalam ' + countdown.toFixed(1) +
                                ' detik');
                        } else {
                            clearInterval(window.focusCountdownInterval);
                            countdownElement.fadeOut(300, function() {
                                $(this).remove();
                            });

                            // Focus after countdown
                            setTimeout(() => {
                                const $noResi = $('#no_resi');
                                $noResi.focus();

                                setTimeout(() => {
                                    $noResi.select();
                                    $noResi.addClass('scanner-active');
                                }, 50);
                            }, 100);
                        }
                    }, 100); // COUNTDOWN INTERVAL: Update every 100ms for smooth countdown
                } else {
                    // Immediate focus (for manual resets, etc.)
                    setTimeout(() => {
                        const $noResi = $('#no_resi');
                        $noResi.focus();

                        setTimeout(() => {
                            $noResi.select();
                            $noResi.addClass('scanner-active');
                        }, 50);
                    }, 100);
                }
            }

            /**
             * Helper function for auto focus to SKU input with delay
             */
            function ensureSkuFocus(showCountdown = false) {
                // Clear any existing countdowns first
                clearFocusCountdowns();

                if (showCountdown) {
                    // Show countdown for auto focus to SKU
                    let countdown =
                        1.5; // COUNTDOWN DURATION: Change this value to modify countdown time (in seconds)
                    const countdownElement = $('<div id="sku-focus-countdown">Auto focus ke No SKU dalam ' +
                        countdown + ' detik</div>');
                    $('body').append(countdownElement);

                    window.skuFocusCountdownInterval = setInterval(() => {
                        countdown -=
                            0.1; // COUNTDOWN DECREMENT: Decrease by 0.1 seconds for smoother countdown
                        if (countdown > 0) {
                            countdownElement.text('Auto focus ke No SKU dalam ' + countdown.toFixed(1) +
                                ' detik');
                        } else {
                            clearInterval(window.skuFocusCountdownInterval);
                            countdownElement.fadeOut(300, function() {
                                $(this).remove();
                            });

                            // Focus after countdown
                            setTimeout(() => {
                                const $firstSku = $('.sku-input:first');
                                $firstSku.focus();

                                setTimeout(() => {
                                    $firstSku.select();
                                    $firstSku.addClass('scanner-active');
                                }, 50);
                            }, 100);
                        }
                    }, 100); // COUNTDOWN INTERVAL: Update every 100ms for smooth countdown
                } else {
                    // Immediate focus (for manual actions, etc.)
                    setTimeout(() => {
                        const $firstSku = $('.sku-input:first');
                        $firstSku.focus();

                        setTimeout(() => {
                            $firstSku.select();
                            $firstSku.addClass('scanner-active');
                        }, 50);
                    }, 100);
                }
            }

            /**
             * Helper function for auto focus to new SKU input with 2-second delay and countdown
             */
            function ensureNewSkuFocus(newIndex) {
                // Clear any existing countdowns first
                clearFocusCountdowns();

                // Show countdown for auto focus to new SKU field
                let countdown = 2.0; // 2-second delay as requested
                const countdownElement = $('<div id="new-sku-focus-countdown">Auto focus ke field SKU baru dalam ' +
                    countdown + ' detik</div>');

                // Add specific styling for new SKU focus countdown
                countdownElement.css({
                    'position': 'fixed',
                    'top': '60px', // Position below other countdowns
                    'right': '20px',
                    'padding': '12px 18px',
                    'border-radius': '8px',
                    'z-index': '9999',
                    'font-weight': 'bold',
                    'font-size': '14px',
                    'box-shadow': '0 4px 12px rgba(0, 0, 0, 0.15)',
                    'animation': 'slideInRight 0.3s ease-out',
                    'transition': 'all 0.3s ease',
                    'background': 'linear-gradient(135deg, #FF9800, #F57C00)',
                    'color': 'white'
                });

                $('body').append(countdownElement);

                window.newSkuFocusCountdownInterval = setInterval(() => {
                    countdown -= 0.1; // Decrease by 0.1 seconds for smooth countdown
                    if (countdown > 0) {
                        countdownElement.text('Auto focus ke field SKU baru dalam ' + countdown.toFixed(1) +
                            ' detik');
                    } else {
                        clearInterval(window.newSkuFocusCountdownInterval);
                        countdownElement.fadeOut(300, function() {
                            $(this).remove();
                        });

                        // Focus after countdown based on current input mode
                        setTimeout(() => {
                            if (STATE.skuInputMode === 'text') {
                                const newTextInput = $(`#sku-text-input-${newIndex}`);
                                newTextInput.focus();

                                setTimeout(() => {
                                    newTextInput.select();
                                    newTextInput.addClass('scanner-active');

                                    // Brief flash to indicate focus
                                    newTextInput.css('box-shadow', '0 0 10px #FF9800');
                                    setTimeout(() => {
                                        newTextInput.css('box-shadow', '');
                                    }, 1000);
                                }, 50);
                            } else {
                                // For Choices.js, focus the search input
                                const choicesInput = $(
                                    `.sku-input-container[data-sku-index="${newIndex}"] .choices__input--cloned`
                                );
                                if (choicesInput.length) {
                                    choicesInput.focus();

                                    // Add visual indicator for the Choices.js container
                                    const choicesContainer = $(
                                        `.sku-input-container[data-sku-index="${newIndex}"] .choices`
                                    );
                                    choicesContainer.addClass('scanner-active');
                                    choicesContainer.css('box-shadow', '0 0 10px #FF9800');
                                    setTimeout(() => {
                                        choicesContainer.css('box-shadow', '');
                                    }, 1000);
                                }
                            }
                        }, 100);
                    }
                }, 100); // Update every 100ms for smooth countdown
            }

            /**
             * Helper function to clear any existing countdown timers and elements
             */
            function clearFocusCountdowns() {
                // Clear any existing countdown elements
                $('#focus-countdown, #sku-focus-countdown, #new-sku-focus-countdown').remove();

                // Clear any running intervals (this is handled by the functions themselves, but good to be safe)
                clearInterval(window.focusCountdownInterval);
                clearInterval(window.skuFocusCountdownInterval);
                clearInterval(window.newSkuFocusCountdownInterval);
            }

            // Initialize scanner, SKU handlers, and form buttons when DOM is ready
            $(document).ready(function() {
                // Initialize scanner toggle
                initScannerToggle();

                // Initialize SKU mode toggle
                initSkuModeToggle();

                // Initialize handlers
                initNoResiHandler();
                initSkuHandler();
                initFormButtons();

                // Initialize edit modal
                initEditModal();

                // Initialize current date display
                updateCurrentDateInfo();

                // Auto focus to No Resi field on page load
                setTimeout(() => {
                    $('#no_resi').focus();
                }, 300);

                // Initialize state
                STATE.skuCounter = 0;

                // Ensure first container has proper data attribute
                const firstContainer = $('.sku-input-container:first');
                if (firstContainer.length && !firstContainer.data('sku-index')) {
                    firstContainer.attr('data-sku-index', '0');
                }
            });

            // Initialize DataTable components
            $(document).ready(function() {
                // Prevent DataTables from showing error messages in console
                $.fn.dataTable.ext.errMode = 'none';

                // Setup AJAX CSRF token
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Check and remove duplicate thead elements that might already exist
                if ($('#history-sales-table thead').length > 1) {
                    $('#history-sales-table thead:gt(0)').remove();
                }

                // Initialize DataTable with optimized configuration
                table = $('#history-sales-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    scrollX: true,
                    dom: 'frtip',
                    bAutoWidth: false,
                    ordering: true,
                    searching: true,
                    stateSave: false,
                    paging: true,
                    fixedHeader: false,
                    deferRender: true, // Improves rendering performance
                    pageLength: 10, // Reduced from 25 for faster loading
                    lengthMenu: [
                        [10, 25, 50],
                        [10, 25, 50]
                    ],
                    searchDelay: 500, // Delay search requests
                    ajax: {
                        url: "{{ route('history-sales.data') }}",
                        type: "POST",
                        data: function(d) {
                            d._token = "{{ csrf_token() }}";
                            if ($('#showArchived').hasClass('active')) {
                                d.only_trashed = true;
                            } else if ($('#showActive').hasClass('active')) {
                                d.only_trashed = false;
                            }

                            // Tambahkan indikator loading
                            $('.table-wrapper').addClass('table-loading');
                        },
                        dataSrc: function(json) {
                            updateCurrentDateInfo();
                            // Hapus indikator loading setelah data dimuat
                            $('.table-wrapper').removeClass('table-loading');
                            return json.data;
                        },
                        error: function(xhr, error, thrown) {
                            console.warn('AJAX error:', error);
                            // Hapus indikator loading ketika terjadi error
                            $('.table-wrapper').removeClass('table-loading');
                            showAlert('error', 'Terjadi Kesalahan!',
                                'Gagal memuat data. Silakan coba lagi.');
                        }
                    },
                    columns: [{
                            data: 'no',
                            name: 'no',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'no_resi',
                            name: 'no_resi'
                        },
                        {
                            data: 'no_sku',
                            name: 'no_sku',
                            className: 'align-top'
                        },
                        {
                            data: 'qty',
                            name: 'qty',
                            className: 'align-top',
                            searchable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at',
                            visible: false,
                            searchable: false
                        },
                        {
                            data: 'actions',
                            name: 'actions',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [4, 'desc']
                    ],
                    language: {
                        processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><span class="ms-2">Memuat data...</span>',
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        zeroRecords: "Tidak ada data yang ditemukan",
                        info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                        infoEmpty: "Tidak ada data yang tersedia",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        search: "Cari:",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        },
                        emptyTable: "Tidak ada data dalam tabel",
                        infoPostFix: "",
                        thousands: ".",
                        loadingRecords: "Memuat...",
                        aria: {
                            sortAscending: ": aktifkan untuk mengurutkan kolom naik",
                            sortDescending: ": aktifkan untuk mengurutkan kolom turun"
                        }
                    },
                    drawCallback: function() {
                        // Ensure no duplicate headers
                        if ($('#history-sales-table thead').length > 1) {
                            $('#history-sales-table thead:not(#main-table-header)').remove();
                        }

                        // Remove any DataTables generated header
                        $('.dataTable > thead:gt(0)').remove();

                        // Make sure tooltip works for action buttons
                        $('[title]').tooltip();
                    },
                    initComplete: function() {
                        // Update date info on first load
                        updateCurrentDateInfo();

                        // Fix header issues
                        if ($('#history-sales-table thead').length > 1) {
                            $('#history-sales-table thead:not(#main-table-header)').remove();
                        }

                        // Improve search performance by adding debounce
                        const searchInput = $('div.dataTables_filter input');
                        searchInput.unbind();
                        searchInput.bind('input', debounce(function(e) {
                            table.search(this.value).draw();
                        }, 500));

                        // Add observer to detect DOM changes only when necessary
                        try {
                            const tableElement = document.getElementById('history-sales-table');
                            if (tableElement) {
                                const observer = new MutationObserver(function(mutations) {
                                    if ($('#history-sales-table thead').length > 1) {
                                        $('#history-sales-table thead:not(#main-table-header)')
                                            .remove();
                                    }
                                });

                                observer.observe(tableElement, {
                                    childList: true,
                                    subtree: true
                                });
                            }
                        } catch (e) {
                            console.warn('MutationObserver error:', e);
                        }
                    }
                });

                // Debounce function to improve performance
                function debounce(func, wait) {
                    let timeout;
                    return function() {
                        const context = this,
                            args = arguments;
                        clearTimeout(timeout);
                        timeout = setTimeout(function() {
                            func.apply(context, args);
                        }, wait);
                    };
                }

                // Add SKU button click handler in edit modal
                $('#add-edit-sku-btn').on('click', function() {
                    const skuHtml = `
                        <div class="edit-sku-container d-flex mb-2">
                            <input type="text" class="form-control me-2" name="edit_no_sku[]" placeholder="SKU">
                            <input type="number" class="form-control me-2" name="edit_qty[]" value="1" min="1" style="width: 120px;">
                            <button type="button" class="btn btn-danger remove-edit-sku">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>`;
                    $('#edit-sku-container').append(skuHtml);
                });

                // Remove SKU button click handler in edit modal
                $(document).on('click', '.remove-edit-sku', function() {
                    // Don't remove the last container, just clear it
                    if ($('.edit-sku-container').length > 1) {
                        $(this).closest('.edit-sku-container').remove();
                    } else {
                        $(this).closest('.edit-sku-container').find('input[name="edit_no_sku[]"]')
                            .val('');
                        $(this).closest('.edit-sku-container').find('input[name="edit_qty[]"]').val(
                            '1');
                    }
                });

                // Initialize edit functionality
                try {
                    initializeHistoryEdit(table);
                } catch (e) {
                    console.warn('Edit functionality initialization error:', e);
                }

                // Initialize UI components (DataTable specific)
                initFilterButtons();

                // Initialize focus on No Resi input with helper function (immediate, no countdown for initial load)
                ensureNoResiFocus(false);

                // Add touch scroll indicator behavior
                const tableWrapper = $('.table-wrapper');
                if (tableWrapper.length && tableWrapper[0].scrollWidth > tableWrapper[0].clientWidth) {
                    $('.table-scroll-indicator').show();
                }

                // Hide scroll indicator after user has scrolled
                tableWrapper.on('scroll', function() {
                    $('.table-scroll-indicator').fadeOut();
                });
            });
        });
    </script>
@endsection
