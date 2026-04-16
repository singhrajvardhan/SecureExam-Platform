<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>SmartExam Support | 24/7 AI-Powered Help Desk</title>
    <link rel="icon" type="image/png" sizes="32x32" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%233b82f6'%3E%3Cpath d='M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5'/%3E%3C/svg%3E">
    <meta name="description" content="Get instant support for SmartExam. Submit tickets, get help with proctoring issues, account access, exam results, and technical assistance.">
    <meta name="author" content="Rajvardhan Singh Badgujar">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0c15;
            color: #ffffff;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-universe {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            background: radial-gradient(ellipse at 20% 30%, #0f0c29, #0a0a1a, #02020c);
        }

        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: linear-gradient(rgba(59, 130, 246, 0.08) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(59, 130, 246, 0.08) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: gridFloat 20s linear infinite;
            pointer-events: none;
        }

        @keyframes gridFloat {
            0% { transform: translateY(0px) translateX(0px); opacity: 0.5; }
            100% { transform: translateY(60px) translateX(60px); opacity: 1; }
        }

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            pointer-events: none;
            animation: orbMove 25s infinite alternate ease-in-out;
        }

        .orb-1 { width: 50vw; height: 50vw; background: radial-gradient(circle, rgba(59, 130, 246, 0.4), transparent); top: -20%; left: -20%; animation-duration: 30s; }
        .orb-2 { width: 60vw; height: 60vw; background: radial-gradient(circle, rgba(139, 92, 246, 0.35), transparent); bottom: -30%; right: -20%; animation-duration: 35s; animation-direction: alternate-reverse; }
        .orb-3 { width: 40vw; height: 40vw; background: radial-gradient(circle, rgba(236, 72, 153, 0.25), transparent); top: 40%; left: 60%; animation-duration: 28s; }

        @keyframes orbMove {
            0% { transform: translate(0, 0) scale(1); opacity: 0.5; }
            100% { transform: translate(5%, 8%) scale(1.2); opacity: 0.9; }
        }

        /* Floating Particles */
        .particle-field {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }
        .particle {
            position: absolute;
            background: linear-gradient(135deg, #38bdf8, #a855f7);
            border-radius: 50%;
            opacity: 0.5;
            animation: particleFloat 12s infinite alternate;
        }
        @keyframes particleFloat {
            0% { transform: translateY(0px) translateX(0px); opacity: 0.2; }
            100% { transform: translateY(-80px) translateX(50px); opacity: 0.8; }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            position: relative;
            z-index: 10;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            background: rgba(10, 15, 30, 0.6);
            backdrop-filter: blur(16px);
            border-radius: 2rem;
            padding: 0.8rem 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(59, 130, 246, 0.4);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            background: linear-gradient(145deg, #3b82f6, #8b5cf6);
            width: 48px;
            height: 48px;
            border-radius: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
            animation: pulseGlow 2s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
            50% { box-shadow: 0 0 0 12px rgba(59, 130, 246, 0); }
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #fff, #a5b4fc);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .nav-links {
            display: flex;
            gap: 0.5rem;
        }

        .nav-link {
            color: #cbd5e6;
            text-decoration: none;
            padding: 0.6rem 1.3rem;
            border-radius: 2rem;
            font-weight: 500;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.03);
        }

        .nav-link i { margin-right: 8px; }
        .nav-link:hover, .nav-link.active {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.4), rgba(139, 92, 246, 0.3));
            color: white;
        }

        /* Main Support Card */
        .support-card {
            background: rgba(15, 25, 45, 0.55);
            backdrop-filter: blur(12px);
            border-radius: 2rem;
            border: 1px solid rgba(59, 130, 246, 0.35);
            overflow: hidden;
        }

        .support-header {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.1));
            padding: 2rem;
            border-bottom: 1px solid rgba(59, 130, 246, 0.3);
            text-align: center;
        }

        .support-header h1 {
            font-size: 2.5rem;
            background: linear-gradient(135deg, #38bdf8, #c084fc, #f472b6);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
        }

        .support-header p {
            color: #cbd5e6;
            font-size: 1rem;
        }

        .support-body {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 2rem;
            padding: 2rem;
        }

        /* Form Styles */
        .form-section {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 1.5rem;
            padding: 1.8rem;
        }

        .form-section h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #3b82f6;
            padding-left: 1rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #cbd5e1;
            font-size: 0.9rem;
        }

        .form-group label i {
            margin-right: 6px;
            color: #3b82f6;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.9rem 1rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(59, 130, 246, 0.4);
            border-radius: 1rem;
            color: white;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 12px rgba(59, 130, 246, 0.3);
            background: rgba(255, 255, 255, 0.12);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border: none;
            padding: 1rem;
            border-radius: 2rem;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.5);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        /* Success Message */
        .success-message {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid #10b981;
            border-radius: 1rem;
            padding: 1.2rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideIn 0.5s ease;
        }

        .success-message i {
            font-size: 2rem;
            color: #10b981;
        }

        .success-message h3 {
            color: #6ee7b7;
            margin-bottom: 0.3rem;
        }

        .success-message p {
            color: #cbd5e6;
            font-size: 0.9rem;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid #ef4444;
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fca5a5;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Info Section */
        .info-section {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 1.5rem;
            padding: 1.8rem;
        }

        .info-section h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid #f97316;
            padding-left: 1rem;
        }

        .info-card {
            background: rgba(59, 130, 246, 0.1);
            border-radius: 1rem;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 3px solid #3b82f6;
        }

        .info-card i {
            font-size: 1.5rem;
            color: #3b82f6;
            margin-bottom: 0.5rem;
            display: inline-block;
        }

        .info-card h3 {
            font-size: 1.1rem;
            margin-bottom: 0.3rem;
        }

        .info-card p {
            color: #cbd5e6;
            font-size: 0.85rem;
        }

        .contact-options {
            margin-top: 1rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.8rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 1rem;
            margin-bottom: 0.8rem;
            transition: all 0.2s;
        }
        .contact-item:hover {
            background: rgba(59, 130, 246, 0.15);
            transform: translateX(5px);
        }

        .contact-item i {
            width: 35px;
            height: 35px;
            background: rgba(59, 130, 246, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
        }

        .footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(59, 130, 246, 0.3);
            font-size: 0.85rem;
            color: #94a3b8;
        }

        .footer a {
            color: #60a5fa;
            text-decoration: none;
        }

        /* Loading animation for button */
        .spinner-icon {
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .container { padding: 1rem; }
            .support-body { grid-template-columns: 1fr; }
            .navbar { flex-direction: column; gap: 1rem; }
            .support-header h1 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

<div class="bg-universe"></div>
<div class="grid-overlay"></div>
<div class="orb orb-1"></div>
<div class="orb orb-2"></div>
<div class="orb orb-3"></div>
<div class="particle-field" id="particleField"></div>

<div class="container">
    <!-- Navigation -->
    <div class="navbar">
        <div class="logo">
            <div class="logo-icon"><i class="fas fa-brain"></i></div>
            <div class="logo-text">SmartExam<span style="font-size:0.9rem;">®</span></div>
        </div>
        <div class="nav-links">
            <a href="#" class="nav-link"><i class="fas fa-home"></i> Home</a>
            <a href="#" class="nav-link"><i class="fas fa-graduation-cap"></i> Student</a>
            <a href="#" class="nav-link"><i class="fas fa-chalkboard-user"></i> Teacher</a>
            <a href="#" class="nav-link active"><i class="fas fa-headset"></i> Support</a>
        </div>
    </div>

    <!-- Main Support Card -->
    <div class="support-card">
        <div class="support-header">
            <h1><i class="fas fa-headset"></i> 24/7 Support Center</h1>
            <p>Our AI-powered support team is here to help you. Submit a ticket and we'll get back to you within 2 hours.</p>
        </div>

        <div class="support-body">
            <!-- Support Form Section -->
            <div class="form-section" id="formSection">
                <h2><i class="fas fa-paper-plane"></i> Submit a Support Ticket</h2>
                
                <!-- Success Message Container (hidden by default) -->
                <div id="successMessage" style="display: none;" class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <h3>✓ Ticket Submitted Successfully!</h3>
                        <p>Thank you for reaching out to us. Our support team has received your ticket and will connect with you within 2 hours. We truly appreciate your patience.</p>
                        <p style="margin-top: 8px;"><i class="fas fa-envelope"></i> A confirmation email has been sent to your inbox.</p>
                        <button type="button" id="closeSuccessBtn" style="margin-top: 10px; background: rgba(255,255,255,0.1); border: none; padding: 5px 15px; border-radius: 20px; color: white; cursor: pointer; font-size: 0.8rem;">Submit Another Ticket →</button>
                    </div>
                </div>
                
                <!-- Error Message Container -->
                <div id="errorMessage" style="display: none;" class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Submission Failed</strong><br>
                        <span id="errorText">Please try again or contact us directly at support@smartexam.com</span>
                    </div>
                </div>
                
                <!-- Web3Forms Form -->
                <form id="supportForm">
                    <!-- Access Key (valid Web3Forms demo key - replace with your own in production) -->
                    <input type="hidden" name="access_key" value="02a0b4d2-f9a4-447d-9e23-0bea55933c02">
                    
                    <!-- Spam prevention honeypot -->
                    <input type="checkbox" name="botcheck" style="display: none;">
                    
                    <!-- Custom subject and from name for better email sorting -->
                    <input type="hidden" name="subject" value="New Support Ticket from SmartExam">
                    <input type="hidden" name="from_name" value="SmartExam Support System">
                    
                    <!-- Redirect to false prevents page redirect, we handle via AJAX -->
                    <input type="hidden" name="redirect" value="false">
                    
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name *</label>
                        <input type="text" name="name" id="name" required placeholder="Enter your full name">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address *</label>
                        <input type="email" name="email" id="email" required placeholder="you@example.com">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Ticket Category</label>
                        <select name="category" id="category">
                            <option value="Technical Issue">🔧 Technical Issue</option>
                           
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-comment-dots"></i> Message *</label>
                        <textarea name="message" id="message" required placeholder="Please describe your issue in detail..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-paper-plane"></i> Submit Ticket
                    </button>
                    
                    <p style="font-size: 0.7rem; color: #94a3b8; text-align: center; margin-top: 1rem;">
                        <i class="fas fa-lock"></i> Your data is encrypted and secure · We reply within 2h
                    </p>
                </form>
            </div>
            
            <!-- Information Section -->
            <div class="info-section">
                <h2><i class="fas fa-info-circle"></i> Help & Resources</h2>
                
                <div class="info-card">
                    <i class="fas fa-clock"></i>
                    <h3>Response Time</h3>
                    <p>Average response: &lt; 2 hours during business hours. Emergency support available 24/7.</p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-robot"></i>
                    <h3>AI-Powered Assistance</h3>
                    <p>Our intelligent system categorizes and prioritizes tickets for fastest resolution.</p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-book"></i>
                    <h3>Knowledge Base</h3>
                    <p>Check our FAQ and documentation for instant answers to common questions.</p>
                </div>
                
                <div class="contact-options">
                    <h3 style="margin-bottom: 0.8rem; font-size: 1rem;"><i class="fas fa-phone-alt"></i> Alternative Contact</h3>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email Support</strong><br>
                            <small>rajvardhancoder@gmail.com</small>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-comments"></i>
                        <div>
                            <strong>Live Chat</strong><br>
                            <small>Available 9 AM - 9 PM IST</small>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fab fa-whatsapp"></i>
                        <div>
                            <strong>WhatsApp Support</strong><br>
                            <small>+91 97702 89936</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer">
        <p><i class="far fa-copyright"></i> Copyright © 2026 <a href="#">Rajvardhan Singh Badgujar</a> | AI Proctoring · Next‑Gen Integrity Suite</p>
        <p style="margin-top: 0.5rem;">
            <a href="#">Privacy Policy</a> • <a href="#">Terms of Service</a> • <a href="#">Security</a>
        </p>
    </div>
</div>

<script>
    // Create floating particles for background effect
    (function createParticles() {
        const container = document.getElementById('particleField');
        if (!container) return;
        for (let i = 0; i < 60; i++) {
            const p = document.createElement('div');
            p.classList.add('particle');
            const size = Math.random() * 8 + 2;
            p.style.width = size + 'px';
            p.style.height = size + 'px';
            p.style.left = Math.random() * 100 + '%';
            p.style.top = Math.random() * 100 + '%';
            p.style.animationDuration = (Math.random() * 18 + 10) + 's';
            p.style.animationDelay = (Math.random() * 5) + 's';
            p.style.background = `radial-gradient(circle, rgba(59,130,246,${Math.random() * 0.5 + 0.2}), rgba(139,92,246,0.2))`;
            container.appendChild(p);
        }
    })();

    // DOM elements
    const form = document.getElementById('supportForm');
    const submitBtn = document.getElementById('submitBtn');
    const successDiv = document.getElementById('successMessage');
    const errorDiv = document.getElementById('errorMessage');
    const errorTextSpan = document.getElementById('errorText');
    
    // Helper: Show error message
    function showError(message) {
        errorTextSpan.textContent = message;
        errorDiv.style.display = 'flex';
        successDiv.style.display = 'none';
        // Auto scroll to error
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        // Auto hide after 6 seconds
        setTimeout(() => {
            if (errorDiv.style.display === 'flex') {
                errorDiv.style.display = 'none';
            }
        }, 6000);
    }
    
    // Helper: Clear previous messages and reset form state
    function resetMessages() {
        errorDiv.style.display = 'none';
        successDiv.style.display = 'none';
    }
    
    // Success handler with "submit another" button
    function handleSuccess() {
        // Show success message
        successDiv.style.display = 'flex';
        // Clear all form fields
        form.reset();
        // Scroll success into view
        successDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Add event listener for "Submit Another Ticket" button
        const closeBtn = document.getElementById('closeSuccessBtn');
        if (closeBtn) {
            closeBtn.onclick = () => {
                successDiv.style.display = 'none';
                // Optionally focus on name field
                document.getElementById('name').focus();
            };
        }
    }
    
    // Form submission handler using fetch to Web3Forms API
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Get field values
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const message = document.getElementById('message').value.trim();
        
        // Enhanced validation
        if (!name || !email || !message) {
            showError('Please fill in all required fields (Name, Email, and Message).');
            return;
        }
        
        // Email format validation
        const emailRegex = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
        if (!emailRegex.test(email)) {
            showError('Please enter a valid email address (e.g., name@domain.com).');
            return;
        }
        
        if (message.length < 10) {
            showError('Please provide more details (at least 10 characters) so we can assist you better.');
            return;
        }
        
        // Hide any previous messages
        resetMessages();
        
        // Show loading state on button
        const originalButtonHTML = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-pulse spinner-icon"></i> Submitting...';
        submitBtn.disabled = true;
        
        // Prepare FormData
        const formData = new FormData(form);
        
        try {
            // Submit to Web3Forms API with proper timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 15000); // 15 sec timeout
            
            const response = await fetch('https://api.web3forms.com/submit', {
                method: 'POST',
                body: formData,
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);
            const result = await response.json();
            
            if (result.success) {
                handleSuccess();
            } else {
                // Provide more specific error if available
                let errorMsg = 'Submission failed. Please try again or contact support directly.';
                if (result.message) errorMsg = result.message;
                showError(errorMsg);
            }
        } catch (error) {
            console.error('Web3Forms Error:', error);
            if (error.name === 'AbortError') {
                showError('Request timed out. Please check your internet connection and try again.');
            } else {
                showError('Network error. Please verify your connection and try again later.');
            }
        } finally {
            // Restore button state
            submitBtn.innerHTML = originalButtonHTML;
            submitBtn.disabled = false;
        }
    });
    
    // Optional: Animation on scroll (smooth fade-in)
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    const formSectionElem = document.querySelector('.form-section');
    const infoSectionElem = document.querySelector('.info-section');
    if (formSectionElem) {
        formSectionElem.style.opacity = '0';
        formSectionElem.style.transform = 'translateY(20px)';
        formSectionElem.style.transition = 'all 0.6s ease';
        observer.observe(formSectionElem);
    }
    if (infoSectionElem) {
        infoSectionElem.style.opacity = '0';
        infoSectionElem.style.transform = 'translateY(20px)';
        infoSectionElem.style.transition = 'all 0.6s ease';
        observer.observe(infoSectionElem);
    }
    
    // Additional client-side category icon preview (optional enhancement)
    const categorySelect = document.getElementById('category');
    if (categorySelect) {
        const updateCategoryIcon = () => {};
        categorySelect.addEventListener('change', updateCategoryIcon);
    }
    
    // Small effect: Clear error when user starts typing in any field
    const inputs = ['name', 'email', 'message'];
    inputs.forEach(id => {
        const field = document.getElementById(id);
        if (field) {
            field.addEventListener('input', () => {
                if (errorDiv.style.display === 'flex') {
                    errorDiv.style.display = 'none';
                }
            });
        }
    });
</script>
</body>
</html>
