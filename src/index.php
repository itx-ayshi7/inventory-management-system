<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Invexa Plus - Enterprise Inventory Management System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #1a365d;
      --secondary-color: #2c5282;
      --accent-color: #4299e1;
      --light-bg: #f7fafc;
      --text-color: #2d3748;
      --border-color: #e2e8f0;
      --section-bg: white;
      --page-title: #2b6cb0;
    }
    body.dark{
      --page-title: white;
      --light-bg: #1a202c;
      --text-color: #f7fafc;
      --border-color: #2d3748;
      --section-bg: #2d3748;
    }
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
      background-color: var(--light-bg);
      color: var(--text-color);
      scroll-behavior: smooth;
      line-height: 1.6;
    }

    header {
      background-color: var(--section-bg);
      color: var(--text-color);
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    body.dark header,
    body.dark footer {
      background-color: #000000;
    }

   
    /* Logo & Title */
    .nav-title {
      display: flex;
      align-items: center;
      color: black;
      margin-right: auto;
    }

    .nav-title img {
      width: 60px;
      height: 60px;
      margin-right: 1rem;
      transition: all 0.5s ease;
    }

    .nav-title img:hover {
      transform: rotate(360deg) scale(1.1);
    }

    .nav-title h2 {
      margin: 0;
      color: var(--text-color);
      font-size: 1.8rem;
      font-weight: 600;
    }

    .logo-heading {
      font-family: "Brush Script MT";
      font-size: 30px;
      color: #2b6cb0;
      font-weight: bolder;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary-color);
    }

    nav {
      margin-left: 0;
    }
    nav ul li i{
      color:#2b6cb0;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 1.5rem;
      margin: 0;
      padding: 0;
      justify-content: flex-end;
    }

    nav ul li {
      position: relative;
    }

    nav ul li a {
      color: var(--text-color);
      text-decoration: none;
      font-weight: 500;
      padding: 0.5rem 0;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      position: relative;
    }

    nav ul li a::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: 0;
      left: 0;
      background-color: var(--accent-color);
      transition: width 0.3s ease;
    }

    nav ul li a:hover {
      color: var(--accent-color);
    }

    nav ul li a:hover::after {
      width: 100%;
    }

    /* Dark mode adjustments */
    body.dark nav ul li a::after {
      background-color: var(--accent-color);
    }

    /* Mobile adjustments */
    @media (max-width: 768px) {
      nav ul li a::after {
        display: none;
      }
      
      nav ul li a:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
      }
    }

    nav ul li ul {
      display: none;
      position: absolute;
      background-color: white;
      min-width: 200px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      border-radius: 4px;
      padding: 0.5rem 0;
    }

    nav ul li:hover ul {
      display: block;
      animation: fadeSlideIn 0.3s ease forwards;
    }

    nav ul li ul li a {
      padding: 0.75rem 1.5rem;
      display: block;
    }

    nav ul li ul li a:hover {
      background-color: var(--light-bg);
    }

    .hero {
      padding: 6rem 2rem;
      text-align: center;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      position: relative;
      overflow: hidden;
    }

    .hero-video {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      opacity: 0.05;
      z-index: 0;
    }

    .hero-content {
      position: relative;
      z-index: 1;
    }

    .hero h1 {
      font-size: 3.5rem;
      margin-bottom: 1.5rem;
      font-weight: 800;
    }

    .hero p {
      font-size: 1.25rem;
      margin-bottom: 2rem;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
      opacity: 0.9;
    }

    .cta-button {
      background-color: white;
      color: var(--primary-color);
      border: none;
      padding: 1rem 2rem;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      border-radius: 6px;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .cta-button:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    section {
      padding: 5rem 2rem;
      max-width: 1200px;
      margin: auto;
    }

    section h2 {
      color: var(--primary-color);
      margin-bottom: 2rem;
      font-size: 2.5rem;
      text-align: center;
    }

    .features {
      display: flex;
      overflow-x: auto;
      gap: 20px;
      padding: 20px 0;
      scroll-snap-type: x mandatory;
      -webkit-overflow-scrolling: touch;
      scrollbar-width: none;
      -ms-overflow-style: none;
    }

    .features::-webkit-scrollbar {
      display: none;
    }

    .feature {
      flex: 0 0 280px;
      scroll-snap-align: start;
      min-width: 280px;
      margin: 0;
      padding: 1.5rem;
      border: 1px solid var(--border-color);
      border-radius: 12px;
      background-color: var(--section-bg);
      transition: all 0.3s ease;
    }

    .feature h3 {
      color: var(--page-title);
      margin-bottom: 1rem;
      font-size: 1rem;
    }

    .feature:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0,0,0,0.1);
    }

    #about h2{
      color: var(--page-title);
    }
    .feature-icon {
      font-size: 2rem;
      color: var(--accent-color);
      margin-bottom: 1rem;
    }

    .footer-pro {
      background-color: var(--primary-color);
      color: white;
      padding: 4rem 2rem 2rem;
    }

    .footer-grid {
      max-width: 1200px;
      margin: auto;
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1.5fr;
      gap: 3rem;
    }

    .footer-section {
      transition: transform 0.3s ease;
    }

    .footer-section:hover {
      transform: translateY(-5px);
    }

    .footer-section.brand {
      padding-right: 2rem;
    }

    .brand-title {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: white;
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .brand-title img {
      width: 60px;
      height: 60px;
      object-fit: contain;
    }

    .brand-title .logo-heading {
      color: var(--accent-color);
      margin-left: 0.5rem;
    }

    .footer-description {
      color: rgba(255,255,255,0.8);
      line-height: 1.6;
      margin-bottom: 1.5rem;
    }

    .footer-section h3 {
      margin-bottom: 1.5rem;
      font-size: 1.2rem;
      position: relative;
      display: inline-block;
      color: white;
    }

    .footer-section h3::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -5px;
      left: 0;
      background-color: var(--accent-color);
      transition: width 0.3s ease;
    }

    .footer-section:hover h3::after {
      width: 100%;
    }

    .footer-section ul {
      list-style: none;
    }

    .footer-section ul li {
      margin-bottom: 0.75rem;
      transition: transform 0.3s ease;
    }

    .footer-section ul li:hover {
      transform: translateX(5px);
    }

    .footer-section a {
      color: rgba(255,255,255,0.8);
      text-decoration: none;
      transition: all 0.3s ease;
      position: relative;
      padding-left: 0;
    }

    .footer-section a:hover {
      color: white;
      padding-left: 5px;
    }

    .footer-section a::before {
      content: '→';
      position: absolute;
      left: -20px;
      opacity: 0;
      transition: all 0.3s ease;
    }

    .footer-section a:hover::before {
      opacity: 1;
      left: -15px;
    }

    .social-icons {
      display: flex;
      gap: 1rem;
      margin-top: 1.5rem;
    }

    .social-icons a {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background-color: rgba(255,255,255,0.1);
      color: white;
      transition: all 0.3s ease;
    }

    .social-icons a:hover {
      background-color: var(--accent-color);
      transform: translateY(-3px);
    }

    .footer-bottom {
      text-align: center;
      margin-top: 3rem;
      padding-top: 2rem;
      border-top: 1px solid rgba(255,255,255,0.1);
    }

    .footer-bottom p {
      transition: opacity 0.3s ease;
      color: rgba(255,255,255,0.8);
    }

    .footer-bottom p:hover {
      opacity: 0.8;
    }


    /* Responsive Design */
    @media (max-width: 1024px) {
      .features {
        grid-template-columns: repeat(2, 1fr);
      }

      .footer-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
      }

      .hero h1 {
        font-size: 2.8rem;
      }
    }

    @media (max-width: 768px) {
      header {
        padding: 1rem;
      }

      nav {
        margin-left: 0;
        position: fixed;
        top: 0;
        right: -100%;
        width: 250px;
        height: 100vh;
        background: var(--primary-color);
        padding: 4rem 1rem 1rem;
        transition: right 0.3s ease;
        z-index: 1000;
      }

      nav.active {
        right: 0;
      }

      nav ul {
        flex-direction: column;
        gap: 0.5rem;
        justify-content: flex-start;
      }

      nav ul li {
        width: 100%;
      }

      nav ul li a {
        color: white;
        padding: 0.75rem;
        border-radius: 4px;
      }

      nav ul li a:hover {
        background: rgba(255, 255, 255, 0.1);
      }

      /* Add overlay when menu is active */
      .nav-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
      }

      .nav-overlay.active {
        display: block;
      }

      .hero {
        padding: 4rem 1rem;
      }

      .hero h1 {
        font-size: 2rem;
      }

      .hero p {
        font-size: 1rem;
        padding: 0 1rem;
      }
      .

      .cta-button {
        display: block;
        width: 100%;
        max-width: 300px;
        margin: 0.5rem auto;
      }

      .features {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }

      section {
        padding: 3rem 1rem;
      }

      section h2 {
        font-size: 2rem;
      }

      .footer-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
      }

      .footer-section {
        text-align: center;
      }

      .social-icons {
        justify-content: center;
      }

      .footer-section a::before {
        display: none;
      }
    }

    @media (max-width: 480px) {
      .nav-title img {
        width: 40px;
        height: 40px;
      }

      .nav-title h2 {
        font-size: 1.5rem;
      }

      .hero h1 {
        font-size: 1.8rem;
      }

      .feature {
        padding: 1rem;
      }

      .feature-icon {
        font-size: 1.5rem;
      }

      .footer-section h3 {
        font-size: 1.1rem;
      }

      .feature {
        flex: 0 0 250px;
        min-width: 250px;
      }
    }

    /* Add smooth transitions for responsive changes */
    .feature, .footer-section, nav ul li a {
      transition: all 0.3s ease;
    }

    /* Improve touch targets on mobile */
    @media (max-width: 768px) {
      nav ul li a, .cta-button, .social-icons a {
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
      }
    }

    /* Add hamburger menu styles */
    .menu-toggle {
      display: none;
      background: var(--primary-color);
      color: white;
      border: none;
      padding: 0.5rem;
      border-radius: 4px;
      cursor: pointer;
      z-index: 1001;
      width: 40px;
      height: 40px;
      position: relative;
    }

    .menu-toggle i {
      font-size: 1.5rem;
      transition: all 0.3s ease;
    }

    @media (max-width: 768px) {
      header {
        padding: 1rem;
        position: relative;
      }

      .menu-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
      }

      nav {
        margin-left: 0;
        position: fixed;
        top: 0;
        right: -100%;
        width: 250px;
        height: 100vh;
        background: var(--primary-color);
        padding: 4rem 1rem 1rem;
        transition: right 0.3s ease;
        z-index: 1000;
      }

      nav.active {
        right: 0;
      }

      nav ul {
        flex-direction: column;
        gap: 0.5rem;
      }

      nav ul li {
        width: 100%;
      }

      nav ul li a {
        color: white;
        padding: 0.75rem;
        border-radius: 4px;
        display: block;
      }

      nav ul li a:hover {
        background: rgba(255, 255, 255, 0.1);
      }

      .nav-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
      }

      .nav-overlay.active {
        display: block;
      }
    }

    /* Creative Features Showcase */
    .features-showcase {
      padding: 4rem 2rem;
      background: var(--light-bg);
    }

    .showcase-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    .showcase-item {
      position: relative;
      height: 300px;
      border-radius: 20px;
      overflow: hidden;
      cursor: pointer;
    }

    .showcase-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .showcase-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 2rem;
      color: white;
      transform: translateY(100%);
      transition: transform 0.5s ease;
    }

    .showcase-item:hover img {
      transform: scale(1.1);
    }

    .showcase-item:hover .showcase-overlay {
      transform: translateY(0);
    }

    .showcase-overlay h3 {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
    }

    .showcase-overlay p {
      opacity: 0.9;
    }

    @media (max-width: 768px) {
      .timeline-container::after {
        left: 31px;
      }
      
      .timeline-item {
        width: 100%;
        padding-left: 70px;
        padding-right: 25px;
      }
      
      .timeline-item:nth-child(even) {
        left: 0;
      }
      
      .timeline-content::before,
      .timeline-icon {
        left: -45px !important;
        right: auto !important;
      }
    }

    /* Enhanced Timeline Styles */
    .timeline-section {
      padding: 6rem 2rem;
      background: var(--section-bg);
      position: relative;
      overflow: hidden;
    }

    .timeline-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(135deg, var(--primary-color) 0%, transparent 100%);
      opacity: 0.05;
    }

    .timeline-container {
      max-width: 1200px;
      margin: 0 auto;
      position: relative;
      padding: 2rem 0;
    }

    .timeline-container::after {
      content: '';
      position: absolute;
      width: 4px;
      background: linear-gradient(to bottom, var(--accent-color), var(--primary-color));
      top: 0;
      bottom: 0;
      left: 50%;
      margin-left: -2px;
      border-radius: 2px;
      box-shadow: 0 0 20px rgba(66, 153, 225, 0.3);
    }

    .timeline-item {
      padding: 20px 40px;
      position: relative;
      width: 50%;
      opacity: 0;
      transform: translateX(-100px);
      transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .timeline-item.visible {
      opacity: 1;
      transform: translateX(0);
    }

    .timeline-item:nth-child(even) {
      left: 50%;
      transform: translateX(100px);
    }

    .timeline-item:nth-child(even).visible {
      transform: translateX(0);
    }

    .timeline-content {
      padding: 2rem;
      background: var(--light-bg);
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      position: relative;
      transition: all 0.3s ease;
    }

    .timeline-content:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .timeline-content h3 {
      color: var(--primary-color);
      font-size: 2rem;
      margin-bottom: 1rem;
      position: relative;
      display: inline-block;
    }

    .timeline-content h3::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background: var(--accent-color);
      transition: width 0.3s ease;
    }

    .timeline-content:hover h3::after {
      width: 100%;
    }

    .timeline-content p {
      color: var(--text-color);
      line-height: 1.6;
      font-size: 1.1rem;
    }

    .timeline-icon {
      position: absolute;
      width: 50px;
      height: 50px;
      background: var(--accent-color);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.5rem;
      box-shadow: 0 0 20px rgba(66, 153, 225, 0.5);
      z-index: 2;
      top: 50%;
      transform: translateY(-50%);
    }

    .timeline-item:nth-child(odd) .timeline-icon {
      right: -35px;
    }

    .timeline-item:nth-child(even) .timeline-icon {
      left: -35px;
    }

    @media (max-width: 768px) {
      .timeline-container::after {
        left: 31px;
      }
      
      .timeline-item {
        width: 100%;
        padding-left: 70px;
        padding-right: 25px;
      }
      
      .timeline-item:nth-child(even) {
        left: 0;
      }
      
      .timeline-icon {
        left: -35px !important;
        right: auto !important;
      }
    }

    /* Smooth Animations */
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .hero {
      animation: fadeIn 1s ease-out;
    }

    .hero h1 {
      animation: slideUp 0.8s ease-out;
    }

    .hero p {
      animation: slideUp 0.8s ease-out 0.2s backwards;
    }

    .hero .cta-button {
      animation: slideUp 0.8s ease-out 0.4s backwards;
    }

    .feature {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease-out;
    }

    .feature.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .timeline-item {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease-out;
    }

    .timeline-item.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .showcase-item {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease-out;
    }

    .showcase-item.visible {
      opacity: 1;
      transform: translateY(0);
    }

    section h2 {
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease-out;
    }

    section h2.visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* Smooth hover effects */
    .feature:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .showcase-item:hover {
      transform: scale(1.02);
    }

    .timeline-content:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    /* Add transition for smooth hover effects */
    .feature, .showcase-item, .timeline-content {
      transition: all 0.3s ease;
    }
    /* Heading styles for dark theme */

body.dark section h2,
body.dark .feature h3,
body.dark .timeline-content h3,
body.dark .showcase-overlay h3 {
  color: #87CEEB !important;  /* Sky blue color */
}


    /* Mobile Responsive Styles for Our Journey Section */
    @media (max-width: 768px) {
        .timeline-section h2 {
            font-size: 1.5rem;
        }

        .timeline-content h3 {
            font-size: 1rem;
        }

        .timeline-content p {
            font-size: 0.6rem;
        }
    }

    @media (max-width: 480px) {
        .timeline-section h2 {
            font-size: 1rem;
        }

        .timeline-content h3 {
            font-size: 0.9rem;
        }

        .timeline-content p {
            font-size: 0.6rem;
        }
    }

    /* Dropdown Menu Styles */
    .dropdown-menu {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      background-color: var(--section-bg);
      min-width: 200px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      border-radius: 4px;
      padding: 0.5rem 0;
      z-index: 1000;
      animation: fadeSlideIn 0.3s ease forwards;
    }

    .dropdown-menu li {
      width: 100%;
    }

    .dropdown-menu li a {
      padding: 0.75rem 1.5rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      color: var(--text-color);
      transition: all 0.3s ease;
    }

    .dropdown-menu li a:hover {
      background-color: var(--light-bg);
      color: var(--accent-color);
    }

    .dropdown-menu li a i {
      font-size: 1rem;
      width: 20px;
      text-align: center;
    }

    @keyframes fadeSlideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Dark mode adjustments for dropdown */
    body.dark .dropdown-menu {
      background-color: var(--section-bg);
      border: 1px solid var(--border-color);
    }

    body.dark .dropdown-menu li a:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="nav-title">
      <img src="assets/logo1.png" width="60" height="60" alt="Logo">
      <h2>Invexa <span class="logo-heading" style="color: #2b6cb0;">Plus</span></h2>
    </div>
    <button class="menu-toggle" aria-label="Toggle navigation menu">
      <i class="fas fa-bars"></i>
    </button>
    <nav>
      <ul>
        <li>
          <a href="#" class="dropdown-toggle">
            <i class="fas fa-home"></i>
            Home
            <i class="fas fa-chevron-down" style="font-size: 0.8rem; margin-left: 0.5rem;"></i>
          </a>
          <ul class="dropdown-menu">
            <li>
              <a href="#features">
                <i class="fas fa-star"></i>
                Features
              </a>
            </li>
            <li>
              <a href="#about">
                <i class="fas fa-info-circle"></i>
                Our Journey
              </a>
            </li>
            <li>
              <a href="#contact">
                <i class="fas fa-envelope"></i>
                Contact
              </a>
            </li>
            <li>
              <a href="#features-showcase">
                <i class="fas fa-star"></i>
                Explore Our Features
              </a>
            </li>
          </ul>
        </li>
        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="products.php"><i class="fas fa-boxes"></i> Products</a></li>
        <li>
          <a href="#" class="dropdown-toggle">
            <i class="fas fa-shopping-cart"></i>
            Orders
            <i class="fas fa-chevron-down" style="font-size: 0.8rem; margin-left: 0.5rem;"></i>
          </a>
          <ul class="dropdown-menu">
            <li>
              <a href="customer_order.php">
                <i class="fas fa-users"></i>
                Customer Orders
              </a>
            </li>
            <li>
              <a href="order.php">
                <i class="fas fa-shopping-basket"></i>
                Manage Orders
              </a>
            </li>
            <li>
              <a href="Trackorder.php">
                <i class="fas fa-truck"></i>
                Track Orders
              </a>
            </li>
          </ul>
        </li>
        <li><a href="setting.php"><i class="fas fa-cog"></i> Settings</a></li>
      </ul>
    </nav>
    <div class="nav-overlay"></div>
  </header>

  <div class="hero">
    <video class="hero-video" autoplay muted loop>
      <source src="assets/herov-vmake.mp4" type="video/mp4">
    </video>
    <div class="hero-content">
      <h1>Transform Your Inventory Management</h1>
      <p>Enterprise-grade inventory management system designed to streamline operations, reduce costs, and drive business growth.</p>
      <a href="login.php" class="cta-button">Login</a>
      <a href="signup.php" class="cta-button">Sign Up</a>
    </div>
  </div>

  <section id="features">
    <h2>Enterprise Features</h2>
    <div class="features">
      <div class="feature">
        <div class="feature-icon"><i class="fas fa-boxes"></i></div>
        <h3>Advanced Inventory Control</h3>
        <p>Real-time tracking, automated reordering, and intelligent stock level management across multiple locations.</p>
      </div>
      <div class="feature">
        <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
        <h3>Analytics & Reporting</h3>
        <p>Comprehensive dashboards and customizable reports to make data-driven business decisions.</p>
      </div>
      <div class="feature">
        <div class="feature-icon"><i class="fas fa-sync"></i></div>
        <h3>Order Management</h3>
        <p>Streamlined order processing, fulfillment tracking, and customer communication tools.</p>
      </div>
      <div class="feature">
        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
        <h3>Enterprise Security</h3>
        <p>Role-based access control, audit trails, and enterprise-grade data protection.</p>
      </div>
    </div>
  </section>

  <section id="about">
    <h2>Why Choose Invexa Plus?</h2>
    <p style="text-align: center; max-width: 800px; margin: 0 auto;">
      Invexa Plus is trusted by Fortune 500 companies and growing businesses worldwide. Our platform combines powerful features with intuitive design, backed by 24/7 enterprise support and a 99.9% uptime guarantee.
    </p>
  </section>

  <section class="timeline-section">
    <h2>Our Journey</h2>
    <div class="timeline-container">
      <div class="timeline-item">
        <div class="timeline-content">
          <div class="timeline-icon">
            <i class="fas fa-lightbulb"></i>
          </div>
          <h3>2025</h3>
          <p>Started as a student project with a vision to create an innovative inventory management solution for small businesses.</p>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-content">
          <div class="timeline-icon">
            <i class="fas fa-code"></i>
          </div>
          <h3>2025</h3>
          <p>Developed the core features including real-time inventory tracking, user authentication, and basic reporting system.</p>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-content">
          <div class="timeline-icon">
            <i class="fas fa-users"></i>
          </div>
          <h3>2025</h3>
          <p>Launched beta version with initial user testing and feedback from fellow students and local businesses.</p>
        </div>
      </div>
      <div class="timeline-item">
        <div class="timeline-content">
          <div class="timeline-icon">
            <i class="fas fa-graduation-cap"></i>
          </div>
          <h3>2025</h3>
          <p>Project completion and presentation as part of academic portfolio, showcasing practical web development skills.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="features-showcase" id="features-showcase">
    <h2>Explore Our Features</h2>
    <div class="showcase-grid">
      <div class="showcase-item">
        <img src="assets/insights.jpg" alt="Real-time Analytics">
        <div class="showcase-overlay">
          <h3>Real-time Analytics</h3>
          <p>Monitor your inventory with live dashboards and insights</p>
        </div>
      </div>
      <div class="showcase-item">
        <img src="assets/track.jpg" alt="Order Tracking">
        <div class="showcase-overlay">
          <h3>Order Tracking</h3>
          <p>Real-time order tracking and status updates for better customer experience</p>
        </div>
      </div>
      <div class="showcase-item">
        <img src="assets/inventb2.jpg" alt="Mobile Access">
        <div class="showcase-overlay">
          <h3>Mobile Access</h3>
          <p>Manage your inventory from anywhere, anytime</p>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer-pro">
    <div class="footer-grid">
      <!-- Branding -->
      <div class="footer-section brand">
        <h2 class="brand-title">
          <img src="assets/whiteLogo.png" width="60" height="60" alt="Logo">
          Invexa <span class="logo-heading">Plus</span>
        </h2>
        <p class="footer-description">
        We power inventory excellence with intelligent tools that streamline operations, 
        cut manual tasks, and enable smarter business decisions.
        </p>
      </div>
  
      <!-- Navigation -->
      <div class="footer-section">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="products.php">Products</a></li>
          <li><a href="orders.php">Orders</a></li>
          <li><a href="reports.php">Reports</a></li>
        </ul>
      </div>
  
      <div class="footer-section">
        <h3>Privacy & Legal</h3>
        <ul>
          <li><a href="privacy.html">Privacy Policy</a></li>
          <li><a href="#">Terms of Service</a></li>
          <li><a href="#">Cookie Policy</a></li>
          <li><a href="#">Security Notice</a></li>
        </ul>
      </div>
      
      <!-- Contact Info -->
      <div class="footer-section" id="contact">
        <h3>Contact</h3>
        <p>Email: <a href="mailto:support@invexaplus.com">support@invexaplus.com</a></p>
        <p>Phone: +92-311*******</p>
        <div class="social-icons">
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
          <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
  
    <div class="footer-bottom">
      <p>&copy; 2025 Invexa Plus. All rights reserved. | Built with ❤️ by Team Invexa</p>
    </div>
  </footer>

  <script src="theme.js"></script>
  <script>
    // Mobile menu functionality
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('nav');
    const overlay = document.querySelector('.nav-overlay');

    menuToggle.addEventListener('click', () => {
      nav.classList.toggle('active');
      overlay.classList.toggle('active');
      // Toggle menu icon
      const icon = menuToggle.querySelector('i');
      if (nav.classList.contains('active')) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-times');
      } else {
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
      }
    });

    overlay.addEventListener('click', () => {
      nav.classList.remove('active');
      overlay.classList.remove('active');
      // Reset menu icon
      const icon = menuToggle.querySelector('i');
      icon.classList.remove('fa-times');
      icon.classList.add('fa-bars');
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!nav.contains(e.target) && !menuToggle.contains(e.target)) {
        nav.classList.remove('active');
        overlay.classList.remove('active');
        // Reset menu icon
        const icon = menuToggle.querySelector('i');
        icon.classList.remove('fa-times');
        icon.classList.add('fa-bars');
      }
    });

    // Smooth scroll animation
    const animateOnScroll = () => {
      const elements = document.querySelectorAll('.feature, .timeline-item, .showcase-item, section h2');
      
      elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementBottom = element.getBoundingClientRect().bottom;
        
        if (elementTop < window.innerHeight * 0.8 && elementBottom > 0) {
          element.classList.add('visible');
        }
      });
    };

    // Run animation on scroll
    window.addEventListener('scroll', animateOnScroll);
    // Run animation on load
    window.addEventListener('load', animateOnScroll);

    // Add dropdown functionality
    document.addEventListener('DOMContentLoaded', function() {
      const dropdownToggle = document.querySelector('.dropdown-toggle');
      const dropdownMenu = document.querySelector('.dropdown-menu');

      dropdownToggle.addEventListener('click', function(e) {
        e.preventDefault();
        dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function(e) {
        if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
          dropdownMenu.style.display = 'none';
        }
      });

      // Close dropdown when clicking on a menu item
      const dropdownItems = dropdownMenu.querySelectorAll('a');
      dropdownItems.forEach(item => {
        item.addEventListener('click', function() {
          dropdownMenu.style.display = 'none';
        });
      });
    });
  </script>
</body>
</html>
