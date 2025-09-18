<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recovery - Your Trusted Mental Health Companion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#00424E',
                        secondary: '#0094B8',
                        'text-light': '#F1F1F1',
                    }
                }
            }
        }
    </script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #00424E 0%, #0094B8 100%);
        }
        .hero-pattern {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.1) 1px, transparent 0);
            background-size: 20px 20px;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .section-padding {
            padding: 5rem 0;
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="font-sans bg-white">
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/95 backdrop-blur-sm z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-r from-primary to-secondary rounded-lg flex items-center justify-center mr-3">
                            <span class="text-white font-bold text-xl">R</span>
                        </div>
                        <span class="text-2xl font-bold text-primary">Recovery</span>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="#home" class="text-black hover:text-primary transition-colors">Home</a>
                        <a href="#about" class="text-black hover:text-primary transition-colors">About</a>
                        <a href="#services" class="text-black hover:text-primary transition-colors">Services</a>
                        <a href="#how-it-works" class="text-black hover:text-primary transition-colors">How It Works</a>
                        <a href="#contact" class="text-black hover:text-primary transition-colors">Contact</a>
                    </div>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-black hover:text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="#home" class="block px-3 py-2 text-black hover:text-primary">Home</a>
                <a href="#about" class="block px-3 py-2 text-black hover:text-primary">About</a>
                <a href="#services" class="block px-3 py-2 text-black hover:text-primary">Services</a>
                <a href="#how-it-works" class="block px-3 py-2 text-black hover:text-primary">How It Works</a>
                <a href="#contact" class="block px-3 py-2 text-black hover:text-primary">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="gradient-bg hero-pattern min-h-screen flex items-center pt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 items-center">
                <div class="mb-10 lg:mb-0">
                    <h1 class="text-4xl md:text-6xl font-bold text-white mb-6 leading-tight">
                        Recovery
                        <span class="block text-3xl md:text-5xl text-text-light mt-2">
                            Your Trusted Mental Health Companion
                        </span>
                    </h1>
                    <p class="text-xl text-text-light mb-8 leading-relaxed">
                        Making mental health services accessible to everyone with secure online consultations, licensed professionals, and seamless medicine delivery.
                    </p>
                    <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex">
                        <button class="w-full sm:w-auto bg-white text-primary px-8 py-4 rounded-lg font-semibold hover:bg-text-light transition-colors flex items-center justify-center hover-scale">
                            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                            </svg>
                            Download on Play Store
                        </button>
                        <button class="w-full sm:w-auto border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-primary transition-colors hover-scale">
                            Learn More
                        </button>
                    </div>
                </div>
                <div class="lg:text-center">
                    <div class="glass-effect rounded-2xl p-8 animate-float">
                        <div class="bg-white rounded-xl p-6 shadow-2xl max-w-sm mx-auto">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-semibold text-primary">Mental Health Care</h3>
                                    <p class="text-sm text-gray-600">Available 24/7</p>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-700">Book Appointment</span>
                                    <div class="w-6 h-6 bg-secondary rounded-full"></div>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-700">Video Consultation</span>
                                    <div class="w-6 h-6 bg-secondary rounded-full"></div>
                                </div>
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm text-gray-700">Medicine Delivery</span>
                                    <div class="w-6 h-6 bg-secondary rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Us Section -->
    <section id="about" class="section-padding bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-primary mb-4">About Recovery</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    We're dedicated to making mental health services accessible to everyone through innovative technology and compassionate care.
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h3 class="text-2xl font-bold text-primary mb-6">Our Mission & Vision</h3>
                    <div class="space-y-6">
                        <div class="border-l-4 border-secondary pl-6">
                            <h4 class="text-lg font-semibold text-black mb-2">Mission</h4>
                            <p class="text-gray-600">Making mental health services accessible to everyone through secure, professional, and convenient online consultations.</p>
                        </div>
                        <div class="border-l-4 border-secondary pl-6">
                            <h4 class="text-lg font-semibold text-black mb-2">Vision</h4>
                            <p class="text-gray-600">A future where quality mental health care is available to all, breaking down barriers and stigma.</p>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="text-center p-6 bg-gray-50 rounded-xl hover-scale">
                        <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-primary mb-2">Privacy</h4>
                        <p class="text-sm text-gray-600">Your data is secure and confidential</p>
                    </div>
                    
                    <div class="text-center p-6 bg-gray-50 rounded-xl hover-scale">
                        <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-primary mb-2">Trust</h4>
                        <p class="text-sm text-gray-600">Licensed professionals you can rely on</p>
                    </div>
                    
                    <div class="text-center p-6 bg-gray-50 rounded-xl hover-scale">
                        <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-primary mb-2">Accessibility</h4>
                        <p class="text-sm text-gray-600">Available anywhere, anytime</p>
                    </div>
                    
                    <div class="text-center p-6 bg-gray-50 rounded-xl hover-scale">
                        <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-semibold text-primary mb-2">Professional Care</h4>
                        <p class="text-sm text-gray-600">Expert mental health support</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="section-padding bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-primary mb-4">Our Services</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Comprehensive mental health services for both patients and healthcare professionals
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12">
                <!-- Services for Patients -->
                <div class="bg-white rounded-2xl shadow-lg p-8 hover-scale">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-primary">For Patients</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center mr-3 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-black mb-1">Book Appointments</h4>
                                <p class="text-gray-600 text-sm">Schedule sessions with licensed psychiatrists and therapists</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center mr-3 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-black mb-1">Video Consultations</h4>
                                <p class="text-gray-600 text-sm">Secure online therapy sessions from anywhere</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center mr-3 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-black mb-1">Medicine Delivery</h4>
                                <p class="text-gray-600 text-sm">Order prescribed medicines directly from the app</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center mr-3 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-black mb-1">Progress Tracking</h4>
                                <p class="text-gray-600 text-sm">Track appointments and medicine deliveries</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services for Doctors -->
                <div class="bg-white rounded-2xl shadow-lg p-8 hover-scale">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-primary">For Doctors</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center mr-3 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-black mb-1">Easy Registration</h4>
                                <p class="text-gray-600 text-sm">Register and get verified on our secure platform</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center mr-3 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-black mb-1">Secure Sessions</h4>
                                <p class="text-gray-600 text-sm">Provide secure online therapy sessions</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center mr-3 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-black mb-1">Patient Management</h4>
                                <p class="text-gray-600 text-sm">Manage appointments and patient interactions easily</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center mr-3 mt-1">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-black mb-1">Earn Income</h4>
                                <p class="text-gray-600 text-sm">Earn through consultations on our platform</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="section-padding bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-primary mb-4">How It Works</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Simple steps to get started with Recovery - whether you're a patient seeking care or a doctor joining our platform
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-16">
                <!-- Patient Journey -->
                <div>
                    <h3 class="text-2xl font-bold text-primary mb-8 text-center">Patient Journey</h3>
                    <div class="space-y-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">1</div>
                            <div class="flex-1 p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-semibold text-black mb-2">Download the Recovery App</h4>
                                <p class="text-gray-600 text-sm">Get started by downloading our secure app from the Play Store</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">2</div>
                            <div class="flex-1 p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-semibold text-black mb-2">Book and Consult Online</h4>
                                <p class="text-gray-600 text-sm">Schedule an appointment and have your consultation via secure video call</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">3</div>
                            <div class="flex-1 p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-semibold text-black mb-2">Order Medicines & Track Progress</h4>
                                <p class="text-gray-600 text-sm">Get prescribed medicines delivered and track your mental health journey</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor Journey -->
                <div>
                    <h3 class="text-2xl font-bold text-primary mb-8 text-center">Doctor Journey</h3>
                    <div class="space-y-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">1</div>
                            <div class="flex-1 p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-semibold text-black mb-2">Register on the App</h4>
                                <p class="text-gray-600 text-sm">Sign up as a healthcare professional on our platform</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">2</div>
                            <div class="flex-1 p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-semibold text-black mb-2">Complete Verification</h4>
                                <p class="text-gray-600 text-sm">Get verified and approved to start practicing on our platform</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">3</div>
                            <div class="flex-1 p-4 bg-gray-50 rounded-lg">
                                <h4 class="font-semibold text-black mb-2">Start Consulting Patients</h4>
                                <p class="text-gray-600 text-sm">Begin providing online consultations and earn through our platform</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Payment Flow Section -->
    <section class="section-padding bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-primary mb-4">Payment Flow</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Transparent and secure payment process that ensures safety for both patients and doctors
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl p-6 shadow-lg text-center hover-scale">
                    <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white font-bold text-xl">1</span>
                    </div>
                    <h3 class="text-lg font-semibold text-primary mb-3">Patients Pay</h3>
                    <p class="text-gray-600 text-sm">Patients securely pay through integrated payment gateways when booking appointments or ordering medicines</p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-lg text-center hover-scale">
                    <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white font-bold text-xl">2</span>
                    </div>
                    <h3 class="text-lg font-semibold text-primary mb-3">Recovery Processes</h3>
                    <p class="text-gray-600 text-sm">Recovery platform manages transactions, deducts platform fees, and ensures secure handling</p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-lg text-center hover-scale">
                    <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white font-bold text-xl">3</span>
                    </div>
                    <h3 class="text-lg font-semibold text-primary mb-3">Doctors Get Paid</h3>
                    <p class="text-gray-600 text-sm">Verified doctors receive earnings directly in their registered bank accounts after successful consultations</p>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-lg text-center hover-scale">
                    <div class="w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white font-bold text-xl">4</span>
                    </div>
                    <h3 class="text-lg font-semibold text-primary mb-3">Transparency</h3>
                    <p class="text-gray-600 text-sm">Both patients and doctors can track their payment history directly inside the app</p>
                </div>
            </div>
        </div>
    </section>

    <!-- App Showcase Section -->
    <section class="section-padding bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-primary mb-4">App Showcase</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Experience the ease of use and reliability of our Recovery app with these key features
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center hover-scale">
                    <div class="bg-gray-100 rounded-2xl p-8 mb-4 h-64 flex items-center justify-center">
                        <div class="bg-white rounded-xl p-6 shadow-lg w-full max-w-xs">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-primary">Book Appointment</h4>
                                <div class="w-6 h-6 bg-secondary rounded-full"></div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-2 bg-gray-200 rounded"></div>
                                <div class="h-2 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-8 bg-primary rounded text-white text-xs flex items-center justify-center mt-4">Book Now</div>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-primary">Appointment Booking</h3>
                </div>
                
                <div class="text-center hover-scale">
                    <div class="bg-gray-100 rounded-2xl p-8 mb-4 h-64 flex items-center justify-center">
                        <div class="bg-white rounded-xl p-6 shadow-lg w-full max-w-xs">
                            <div class="flex items-center justify-center mb-4">
                                <div class="w-20 h-20 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="h-2 bg-gray-200 rounded mb-2"></div>
                                <div class="h-6 bg-secondary rounded text-white text-xs flex items-center justify-center">Join Call</div>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-primary">Video Consultation</h3>
                </div>
                
                <div class="text-center hover-scale">
                    <div class="bg-gray-100 rounded-2xl p-8 mb-4 h-64 flex items-center justify-center">
                        <div class="bg-white rounded-xl p-6 shadow-lg w-full max-w-xs">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-primary">Medicine Order</h4>
                                <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                    <span class="text-xs">Medicine A</span>
                                    <span class="text-xs font-semibold">$10</span>
                                </div>
                                <div class="h-6 bg-primary rounded text-white text-xs flex items-center justify-center">Order Now</div>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-primary">Medicine Ordering</h3>
                </div>
                
                <div class="text-center hover-scale">
                    <div class="bg-gray-100 rounded-2xl p-8 mb-4 h-64 flex items-center justify-center">
                        <div class="bg-white rounded-xl p-6 shadow-lg w-full max-w-xs">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-primary">Payment</h4>
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary mb-2">$50.00</div>
                                <div class="text-xs text-gray-600 mb-3">Payment Successful</div>
                                <div class="h-2 bg-green-200 rounded"></div>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-primary">Payment Confirmation</h3>
                </div>
            </div>

            <div class="text-center mt-12">
                <button class="bg-gradient-to-r from-primary to-secondary text-white px-8 py-4 rounded-lg font-semibold hover:shadow-lg transition-shadow flex items-center justify-center mx-auto hover-scale">
                    <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                    </svg>
                    Download Recovery App
                </button>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section-padding gradient-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white mb-4">Contact Us</h2>
                <p class="text-xl text-text-light max-w-3xl mx-auto">
                    Have questions or need support? We're here to help you on your mental health journey
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12 items-start">
                <div>
                    <div class="space-y-8">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-2">Business Inquiries</h3>
                                <p class="text-text-light">business@recovery.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-2">Patient & Doctor Support</h3>
                                <p class="text-text-light">support@recovery.com</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mr-4 flex-shrink-0">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 1v16a1 1 0 001 1h8a1 1 0 001-1V5H7z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white mb-2">Follow Us</h3>
                                <div class="flex space-x-4">
                                    <a href="#" class="text-text-light hover:text-white transition-colors">Facebook</a>
                                    <a href="#" class="text-text-light hover:text-white transition-colors">Twitter</a>
                                    <a href="#" class="text-text-light hover:text-white transition-colors">LinkedIn</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-xl">
                    <h3 class="text-2xl font-bold text-primary mb-6">Quick Contact Form</h3>
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                            <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option>General Inquiry</option>
                                <option>Patient Support</option>
                                <option>Doctor Registration</option>
                                <option>Business Partnership</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 rounded-lg font-semibold hover:shadow-lg transition-shadow">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-secondary to-white rounded-lg flex items-center justify-center mr-3">
                            <span class="text-primary font-bold text-xl">R</span>
                        </div>
                        <span class="text-2xl font-bold">Recovery</span>
                    </div>
                    <p class="text-text-light mb-4">Your trusted mental health companion, making quality care accessible to everyone.</p>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <div class="space-y-2">
                        <a href="#about" class="block text-text-light hover:text-white transition-colors">About Us</a>
                        <a href="#services" class="block text-text-light hover:text-white transition-colors">Services</a>
                        <a href="#how-it-works" class="block text-text-light hover:text-white transition-colors">How It Works</a>
                        <a href="#contact" class="block text-text-light hover:text-white transition-colors">Contact</a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Legal</h4>
                    <div class="space-y-2">
                        <a href="#" class="block text-text-light hover:text-white transition-colors">Privacy Policy</a>
                        <a href="#" class="block text-text-light hover:text-white transition-colors">Terms & Conditions</a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-600 mt-8 pt-8 text-center">
                <p class="text-text-light">Recovery © 2025 – All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Close mobile menu if open
                    mobileMenu.classList.add('hidden');
                }
            });
        });

        // Add scroll effect to navigation
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 100) {
                nav.classList.add('shadow-lg');
            } else {
                nav.classList.remove('shadow-lg');
            }
        });

        // Form submission handler
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you for your message! We will get back to you soon.');
            this.reset();
        });

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all sections for animation
        document.querySelectorAll('section').forEach(section => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(20px)';
            section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(section);
        });
    </script>
</body>
</html>