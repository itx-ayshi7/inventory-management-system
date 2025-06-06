// Theme handling
document.addEventListener('DOMContentLoaded', function() {
  // Check for saved theme preference
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'dark') {
    document.body.classList.add('dark');
    // If we're on the settings page, update the toggle
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
      themeToggle.checked = true;
    }
    // Update logos for dark theme
    const headerLogo = document.querySelector('.nav-title img');
    const footerLogo = document.querySelector('.brand-title img');
    if (headerLogo) headerLogo.src = 'assets/whiteLogo.png';
    if (footerLogo) footerLogo.src = 'assets/whiteLogo.png';
  }
});

