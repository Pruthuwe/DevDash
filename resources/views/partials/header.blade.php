<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Chandani Enterprises')</title>
    
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-icon.png') }}" sizes="16x16">
    
    <!-- Remix Icon Font CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}">
    
    <!-- Apex Chart CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/apexcharts.css') }}">
    
    <!-- Data Table CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/dataTables.min.css') }}">
    
    <!-- Text Editor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor-katex.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.atom-one-dark.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.quill.snow.css') }}">
    
    <!-- Date Picker CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/flatpickr.min.css') }}">
    
    <!-- Calendar CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/full-calendar.css') }}">
    
    <!-- Vector Map CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/jquery-jvectormap-2.0.5.css') }}">
    
    <!-- Popup CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/magnific-popup.css') }}">
    
    <!-- Slick Slider CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/slick.css') }}">
    
    <!-- Prism CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/prism.css') }}">
    
    <!-- File Upload CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/file-upload.css') }}">
    
    <!-- Audio Player CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/lib/audioplayer.css') }}">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
    <!-- Custom Sticky Navbar CSS -->
    <style>
        .navbar-header {
            position: sticky !important;
            top: 0 !important;
            z-index: 1000 !important;
            background-color: var(--white) !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
    </style>
    
    @stack('styles')
</head>
