<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Privacy Policy | ExamGuardian Proctor – Secure & Trusted</title>
    <meta name="description" content="ExamGuardian Proctor Privacy Policy – Learn how we protect your personal data, camera usage, proctoring security, and your rights under GDPR/CCPA.">
    <meta name="robots" content="index, follow">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f6f9fc;
            color: #1a2634;
            line-height: 1.5;
            scroll-behavior: smooth;
        }

        /* modern hero section */
        .hero-privacy {
            background: linear-gradient(125deg, #0B1120 0%, #1a1f3a 100%);
            color: white;
            padding: 90px 24px 70px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero-privacy::before {
            content: "";
            position: absolute;
            top: -30%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(102,126,234,0.2), transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-privacy::after {
            content: "";
            position: absolute;
            bottom: -20%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(118,75,162,0.2), transparent 70%);
            border-radius: 50%;
        }
        .hero-content {
            max-width: 850px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        .hero-content h1 {
            font-size: 3.6rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #FFFFFF, #b9c8ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 20px;
        }
        .hero-content p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 650px;
            margin: 0 auto;
        }
        .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(8px);
            padding: 8px 20px;
            border-radius: 60px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 24px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        /* container and card layout */
        .container-policy {
            max-width: 1280px;
            margin: -40px auto 60px;
            padding: 0 28px;
            position: relative;
            z-index: 3;
        }
        .policy-card {
            background: #ffffff;
            border-radius: 36px;
            box-shadow: 0 25px 45px -12px rgba(0,0,0,0.2), 0 1px 2px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: all 0.2s;
        }
        .last-updated-bar {
            background: #f0f4fa;
            padding: 18px 32px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.9rem;
            color: #2c3e66;
        }
        .btn-back-modern {
            background: white;
            border: 1px solid #cbd5e1;
            padding: 10px 20px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            color: #1e293b;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }
        .btn-back-modern:hover {
            background: #f1f5f9;
            transform: translateX(-4px);
            border-color: #667eea;
        }

        /* table of contents - grid style */
        .toc-modern {
            background: #fafcff;
            margin: 32px 32px 24px;
            padding: 28px 32px;
            border-radius: 28px;
            border: 1px solid #eef2f8;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }
        .toc-modern h3 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .toc-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 12px;
            list-style: none;
        }
        .toc-grid li a {
            color: #2c3e66;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
            padding: 6px 0;
        }
        .toc-grid li a i {
            color: #667eea;
            font-size: 0.85rem;
            width: 20px;
        }
        .toc-grid li a:hover {
            color: #4f46e5;
            transform: translateX(5px);
        }

        /* content sections */
        .policy-content {
            padding: 8px 40px 48px 40px;
        }
        .section-block {
            margin-bottom: 48px;
            scroll-margin-top: 30px;
        }
        .section-block h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #0f172a;
            border-left: 5px solid #667eea;
            padding-left: 20px;
            margin-bottom: 24px;
        }
        .section-block h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1e293b;
            margin: 24px 0 12px 0;
        }
        .section-block p {
            color: #334155;
            margin-bottom: 16px;
            line-height: 1.6;
        }
        .section-block ul, .section-block ol {
            margin: 16px 0 20px 32px;
            color: #2d3a4e;
        }
        .section-block li {
            margin: 8px 0;
        }
        .highlight-card {
            background: linear-gradient(115deg, #f0f4ff 0%, #f9f0ff 100%);
            border-radius: 24px;
            padding: 24px 28px;
            margin: 28px 0;
            border-left: 4px solid #667eea;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        }
        .badge-icon {
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1rem;
            color: #2d3a66;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            margin: 24px 0;
        }
        th {
            background: #eef2ff;
            padding: 14px 16px;
            text-align: left;
            font-weight: 700;
            color: #1e293b;
        }
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #e2edf7;
            color: #2c3e50;
        }
        .footer-modern {
            background: #0a0e1c;
            color: #a5b4fc;
            text-align: center;
            padding: 48px 24px;
            margin-top: 20px;
            border-top: 1px solid #1e293b;
        }
        .footer-links a {
            color: #cbd5ff;
            text-decoration: none;
            margin: 0 14px;
            transition: 0.2s;
        }
        .footer-links a:hover {
            color: white;
        }
        @media (max-width: 780px) {
            .hero-content h1 { font-size: 2.2rem; }
            .policy-content { padding: 8px 24px 40px 24px; }
            .toc-modern { margin: 20px 20px 16px; padding: 20px; }
            .section-block h2 { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

<section class="hero-privacy">
    <div class="hero-content">
        <div class="hero-badge"><i class="fas fa-shield-heart"></i> Trust & Transparency</div>
        <h1>Privacy Policy</h1>
        <p>Your data is protected with enterprise-grade security. We're committed to safeguarding your academic journey.</p>
    </div>
</section>

<div class="container-policy">
    <div class="policy-card">
        <div class="last-updated-bar">
            <span><i class="far fa-calendar-alt"></i> Last Updated: March 15, 2024 | Version 2.1</span>
            <a href="index.php" class="btn-back-modern"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <!-- dynamic table of contents -->
        <div class="toc-modern">
            <h3><i class="fas fa-list-ul"></i> On this page</h3>
            <ul class="toc-grid">
                <li><a href="#introduction"><i class="fas fa-info-circle"></i> Introduction</a></li>
                <li><a href="#information-collect"><i class="fas fa-database"></i> Information we collect</a></li>
                <li><a href="#how-we-use"><i class="fas fa-chart-line"></i> How we use data</a></li>
                <li><a href="#camera-data"><i class="fas fa-camera"></i> Camera & face detection</a></li>
                <li><a href="#data-security"><i class="fas fa-lock"></i> Data security</a></li>
                <li><a href="#data-retention"><i class="fas fa-clock"></i> Retention policy</a></li>
                <li><a href="#cookies"><i class="fas fa-cookie-bite"></i> Cookies & tracking</a></li>
                <li><a href="#third-party"><i class="fas fa-share-alt"></i> Third-party services</a></li>
                <li><a href="#user-rights"><i class="fas fa-user-shield"></i> Your rights</a></li>
                <li><a href="#children"><i class="fas fa-child"></i> Children's privacy</a></li>
                <li><a href="#changes"><i class="fas fa-history"></i> Policy changes</a></li>
                <li><a href="#contact"><i class="fas fa-headset"></i> Contact us</a></li>
            </ul>
        </div>

        <div class="policy-content">
            <!-- Introduction -->
            <div id="introduction" class="section-block">
                <h2>1. Introduction</h2>
                <p>Welcome to <strong>ExamGuardian Proctor</strong> — a next-gen online examination platform with real-time cheating prevention. We value your privacy above all. This Privacy Policy explains how we collect, process, store, and protect your personal information when you access our platform, take exams, or use proctoring features.</p>
                <p>By using ExamGuardian Proctor, you agree to the practices described in this document. If you have any concerns, please reach out to our Data Protection Officer.</p>
            </div>

            <!-- Information collect -->
            <div id="information-collect" class="section-block">
                <h2>2. Information We Collect</h2>
                <h3>📘 Personal & Academic Data</h3>
                <ul>
                    <li><strong>Student details:</strong> Full name, Roll number, Application ID, Email, Phone, Course enrollment.</li>
                    <li><strong>Faculty/Proctor data:</strong> Employee ID, Department, institutional email.</li>
                    <li><strong>Login credentials:</strong> Hashed passwords, MFA tokens, session records.</li>
                </ul>
                <h3>📊 Examination & Performance Data</h3>
                <ul>
                    <li>Answers, timestamps, score history, question-level analytics.</li>
                    <li>Exam metadata: start/end times, IP logs, device fingerprint.</li>
                </ul>
                <h3>🎥 Proctoring & Monitoring Data</h3>
                <ul>
                    <li>Live camera feed analysis (face detection, presence logs).</li>
                    <li>Tab switch events, window focus changes, copy/paste detection.</li>
                    <li>Environment snapshots only during suspicious behavior.</li>
                </ul>
            </div>

            <!-- how we use -->
            <div id="how-we-use" class="section-block">
                <h2>3. How We Use Your Information</h2>
                <p>Every piece of data serves a clear educational or security purpose:</p>
                <ul>
                    <li><i class="fas fa-gavel"></i> <strong>Integrity enforcement:</strong> Real-time cheating detection & proctoring logs.</li>
                    <li><i class="fas fa-id-card"></i> <strong>Identity verification:</strong> Ensuring registered candidates appear for exams.</li>
                    <li><i class="fas fa-chart-simple"></i> <strong>Analytics & improvement:</strong> Enhance user experience and exam fairness.</li>
                    <li><i class="fas fa-bell"></i> <strong>Communications:</strong> Important exam alerts, security updates.</li>
                </ul>
                <div class="highlight-card">
                    <div class="badge-icon"><i class="fas fa-video fa-fw"></i> <strong>Camera usage note:</strong> Video stream is processed in real time for face presence detection; raw footage is never stored permanently. Only violation timestamps and metadata are retained for audit purposes.</div>
                </div>
            </div>

            <!-- camera data deep dive -->
            <div id="camera-data" class="section-block">
                <h2>4. Camera and Face Detection Data</h2>
                <h3>🎯 When & why we access your camera</h3>
                <p>Camera access is strictly limited to active exam sessions. You must grant permission before starting any proctored test. The system never records continuously — it analyzes anonymized face vectors to verify attention.</p>
                <h3>🔒 What is captured?</h3>
                <ul>
                    <li>Face presence/multiple faces detection (anti-cheating).</li>
                    <li>Face absence events (if you move away from screen).</li>
                    <li>Low-confidence identity frames (blurry/lighting issues).</li>
                    <li>No facial recognition databases or biometric profiling.</li>
                </ul>
                <h3>🗑️ Retention of camera logs</h3>
                <p>Face detection logs are stored for <strong>30 days</strong> and automatically purged. In case of academic misconduct investigation, flagged screenshots are retained for 90 days, accessible only by authorized exam officials.</p>
            </div>

            <!-- Data security -->
            <div id="data-security" class="section-block">
                <h2>5. Data Security — Fort Knox Grade</h2>
                <p>We apply military-grade encryption and proactive monitoring:</p>
                <ul>
                    <li><i class="fas fa-key"></i> <strong>Encryption:</strong> AES-256 at rest & TLS 1.3 in transit.</li>
                    <li><i class="fas fa-shield-virus"></i> <strong>Access controls:</strong> Role-based strict permissions + MFA for admins.</li>
                    <li><i class="fas fa-eye"></i> <strong>Continuous auditing:</strong> Weekly penetration tests & anomaly detection.</li>
                </ul>
                <div class="highlight-card">
                    <i class="fas fa-bell"></i> <strong>Breach notification:</strong> In the unlikely event of a breach, affected users will be notified within 72 hours with remediation steps.
                </div>
            </div>

            <!-- retention table -->
            <div id="data-retention" class="section-block">
                <h2>6. Data Retention Policy</h2>
                <p>We follow data minimization & scheduled deletion:</p>
                <table>
                    <thead>
                        <tr><th>Data Category</th><th>Retention Period</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>Student account (active)</td><td>Until deletion request + 30 days grace</td></tr>
                        <tr><td>Exam results & transcripts</td><td>5 years (academic regulation)</td></tr>
                        <tr><td>Camera / face detection logs</td><td>30 days</td></tr>
                        <tr><td>Tab switch / activity logs</td><td>90 days</td></tr>
                        <tr><td>Evidence of misconduct (screenshots)</td><td>90 days</td></tr>
                        <tr><td>System & error logs</td><td>180 days</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- cookies -->
            <div id="cookies" class="section-block">
                <h2>7. Cookies & Tracking Technologies</h2>
                <p>We use essential and performance cookies to keep your exam experience smooth. Session cookies are mandatory for login & security. Analytics cookies are anonymized.</p>
                <p>You can disable non-essential cookies via browser settings, but exam functionality requires session cookies to be enabled.</p>
            </div>

            <!-- third party -->
            <div id="third-party" class="section-block">
                <h2>8. Third-Party Services</h2>
                <p>We rely on trusted, certified partners:</p>
                <ul>
                    <li><strong>Cloud Infrastructure:</strong> AWS / Google Cloud (GDPR compliant, data residency options).</li>
                    <li><strong>Email notifications:</strong> SendGrid / Amazon SES (no marketing tracking).</li>
                    <li><strong>CDN:</strong> Cloudflare for performance (anonymized requests).</li>
                </ul>
                <p>All third parties are bound by Data Processing Agreements and cannot use your data for their own purposes.</p>
            </div>

            <!-- user rights -->
            <div id="user-rights" class="section-block">
                <h2>9. Your Privacy Rights (GDPR / CCPA / LGPD)</h2>
                <p>Depending on your location, you have the right to:</p>
                <ul>
                    <li><strong>Access:</strong> Receive a copy of your personal data.</li>
                    <li><strong>Rectification:</strong> Correct inaccurate information.</li>
                    <li><strong>Erasure:</strong> Request deletion (“right to be forgotten”).</li>
                    <li><strong>Restrict processing:</strong> Limit how we use your data.</li>
                    <li><strong>Data portability:</strong> Export your exam data in JSON/CSV.</li>
                    <li><strong>Withdraw consent:</strong> For camera usage or optional processing.</li>
                </ul>
                <div class="highlight-card">
                    <i class="fas fa-envelope-open-text"></i> To exercise your rights, email <strong>privacy@examguardian.com</strong> with subject “Data Request”. We respond within 30 days.
                </div>
            </div>

            <!-- children -->
            <div id="children" class="section-block">
                <h2>10. Children’s Privacy</h2>
                <p>ExamGuardian Proctor is intended for higher education institutions and professional certification. We do not knowingly collect data from children under 13. If you are a parent and believe your child’s data was submitted, contact us immediately for removal.</p>
            </div>

            <!-- changes -->
            <div id="changes" class="section-block">
                <h2>11. Changes to This Privacy Policy</h2>
                <p>We evolve with regulations. Material changes will be announced via email and dashboard notification. The “Last Updated” date indicates the latest revision. Continued use after changes constitutes acceptance.</p>
            </div>

            <!-- contact -->
            <div id="contact" class="section-block">
                <h2>12. Contact Our Privacy Team</h2>
                <p>Have questions or concerns? We're here to help:</p>
                <ul>
                    <li><i class="fas fa-envelope"></i> <strong>Privacy inquiries:</strong> <a href="mailto: rajvardhancoder@gmail.com"> rajvardhancoder@gmail.com</a></li>
                    <li><i class="fas fa-phone-alt"></i> <strong>DPO hotline:</strong> +91 9770289936</li>
                    <li><i class="fas fa-building"></i> <strong>Postal:</strong> </li>
                    <li><i class="fas fa-clock"></i> Response within 2 business days</li>
                </ul>
                <div class="highlight-card">
                    <i class="fas fa-user-tie"></i> <strong>Data Protection Officer:</strong> Elena M. Carter, CIPP/E<br> rajvardhancoder@gmail.com
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer-modern">
    <div class="footer-links">
        <a href="privacy-policy.php"><i class="fas fa-lock"></i> Privacy</a>
        <a href="terms-of-service.php"><i class="fas fa-file-contract"></i> Terms of Service</a>
        <a href="contact.php"><i class="fas fa-headset"></i> Support</a>
        <a href="index.php"><i class="fas fa-home"></i> ExamPortal</a>
    </div>
    <p style="margin-top: 28px;">&copy; 2026 ExamGuardian Proctor — Integrity-driven online proctoring. All rights reserved.</p>
    <p style="font-size: 0.8rem; margin-top: 12px;"></p>
</footer>

<script>
    // smooth anchor scroll + active highlight (optional)
    document.querySelectorAll('.toc-grid a, .section-block a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId.startsWith('#')) {
                e.preventDefault();
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });
    // small interactive for back button hover
    console.log("Privacy policy ready — full design active");
</script>
</body>
</html>
