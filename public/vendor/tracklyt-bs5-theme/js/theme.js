/**
 * Tracklyt - Theme JavaScript
 * Interactive components and utilities
 */

(function() {
  'use strict';

  // Theme Configuration
  const TracklytTheme = {
    version: '1.0.0',
    initialized: false,

    // Initialize all theme components
    init: function() {
      if (this.initialized) return;
      
      this.initSidebar();
      this.initDropdowns();
      this.initTooltips();
      this.initAnimations();
      this.initFormValidation();
      this.initPriceAnimations();
      this.initScrollEffects();
      
      this.initialized = true;
      console.log('Tracklyt Theme v' + this.version + ' initialized');
    },

    // Sidebar Toggle for Mobile
    initSidebar: function() {
      const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
      const sidebar = document.querySelector('.sidebar');
      const overlay = document.querySelector('.sidebar-overlay');
      
      if (!sidebarToggle || !sidebar) return;
      
      // Create overlay if it doesn't exist
      if (!overlay) {
        const newOverlay = document.createElement('div');
        newOverlay.className = 'sidebar-overlay';
        document.body.appendChild(newOverlay);
      }
      
      const sidebarOverlay = overlay || document.querySelector('.sidebar-overlay');
      
      sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
      });
      
      sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
      });
    },

    // Enhanced Dropdown Functionality
    initDropdowns: function() {
      const dropdowns = document.querySelectorAll('.dropdown-toggle');
      
      dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('click', function(e) {
          e.preventDefault();
          const menu = this.nextElementSibling;
          if (menu && menu.classList.contains('dropdown-menu')) {
            menu.classList.toggle('show');
          }
        });
      });
      
      // Close dropdowns when clicking outside
      document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
          document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
            menu.classList.remove('show');
          });
        }
      });
    },

    // Initialize Bootstrap Tooltips
    initTooltips: function() {
      if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
      }
    },

    // Scroll-triggered Animations
    initAnimations: function() {
      const animatedElements = document.querySelectorAll('[data-animate]');
      
      if (!animatedElements.length) return;
      
      const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
          if (entry.isIntersecting) {
            const animationType = entry.target.getAttribute('data-animate');
            entry.target.classList.add('animate-' + animationType);
            observer.unobserve(entry.target);
          }
        });
      }, {
        threshold: 0.1
      });
      
      animatedElements.forEach(function(el) {
        observer.observe(el);
      });
    },

    // Form Validation Enhancement
    initFormValidation: function() {
      const forms = document.querySelectorAll('.needs-validation');
      
      forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
      
      // Real-time validation
      const inputs = document.querySelectorAll('.form-control, .form-select');
      inputs.forEach(function(input) {
        input.addEventListener('blur', function() {
          if (this.value) {
            this.classList.add(this.checkValidity() ? 'is-valid' : 'is-invalid');
          }
        });
        
        input.addEventListener('input', function() {
          if (this.classList.contains('is-invalid') || this.classList.contains('is-valid')) {
            this.classList.remove('is-invalid', 'is-valid');
            this.classList.add(this.checkValidity() ? 'is-valid' : 'is-invalid');
          }
        });
      });
    },

    // Animated Price Updates
    initPriceAnimations: function() {
      window.animatePrice = function(element, newValue, duration = 1000) {
        const oldValue = parseFloat(element.textContent.replace(/[^0-9.-]+/g, ''));
        // get currency symbol dynamically from element
        const currencySymbol = element.textContent.replace(/[0-9.,\s]/g, '');
        const startTime = performance.now();
        
        function update(currentTime) {
          const elapsed = currentTime - startTime;
          const progress = Math.min(elapsed / duration, 1);
          
          const currentValue = oldValue + (newValue - oldValue) * progress;
          element.textContent = currencySymbol + currentValue.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
          
          if (progress < 1) {
            requestAnimationFrame(update);
          }
        }
        
        requestAnimationFrame(update);
      };
    },

    // Scroll Effects
    initScrollEffects: function() {
      let lastScroll = 0;
      const navbar = document.querySelector('.navbar');
      
      if (!navbar) return;
      
      window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll <= 0) {
          navbar.classList.remove('scroll-up', 'scroll-down');
          return;
        }
        
        if (currentScroll > lastScroll && !navbar.classList.contains('scroll-down')) {
          // Scrolling down
          navbar.classList.remove('scroll-up');
          navbar.classList.add('scroll-down');
        } else if (currentScroll < lastScroll && navbar.classList.contains('scroll-down')) {
          // Scrolling up
          navbar.classList.remove('scroll-down');
          navbar.classList.add('scroll-up');
        }
        
        lastScroll = currentScroll;
      });
    }
  };

  // Utility Functions
  window.Tracklyt = {
    // Format currency
    formatCurrency: function(amount, currency = 'USD') {
      return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency
      }).format(amount);
    },

    // Format number with commas
    formatNumber: function(number, decimals = 2) {
      return parseFloat(number).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    },

    // Show toast notification
    toast: function(message, type = 'info', duration = 3000) {
      const toastContainer = document.querySelector('.toast-container') || this.createToastContainer();
      
      const toastEl = document.createElement('div');
      toastEl.className = 'toast align-items-center text-white bg-' + type + ' border-0';
      toastEl.setAttribute('role', 'alert');
      toastEl.innerHTML = `
        <div class="d-flex">
          <div class="toast-body">${message}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      `;
      
      toastContainer.appendChild(toastEl);
      
      if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        const toast = new bootstrap.Toast(toastEl, { delay: duration });
        toast.show();
        
        toastEl.addEventListener('hidden.bs.toast', function() {
          toastEl.remove();
        });
      } else {
        // Fallback without Bootstrap
        toastEl.style.display = 'block';
        setTimeout(function() {
          toastEl.remove();
        }, duration);
      }
    },

    // Create toast container
    createToastContainer: function() {
      const container = document.createElement('div');
      container.className = 'toast-container position-fixed top-0 end-0 p-3';
      container.style.zIndex = '9999';
      document.body.appendChild(container);
      return container;
    },

    // Copy to clipboard
    copyToClipboard: function(text, successMessage = 'Copied to clipboard!') {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
          window.Tracklyt.toast(successMessage, 'success');
        }).catch(function() {
          window.Tracklyt.toast('Failed to copy', 'danger');
        });
      } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.select();
        try {
          document.execCommand('copy');
          window.Tracklyt.toast(successMessage, 'success');
        } catch (err) {
          window.Tracklyt.toast('Failed to copy', 'danger');
        }
        document.body.removeChild(textArea);
      }
    },

    // Loading state for buttons
    setButtonLoading: function(button, loading = true) {
      if (loading) {
        button.dataset.originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Loading...';
      } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText || button.innerHTML;
      }
    },

    // Smooth scroll to element
    scrollTo: function(element, offset = 0) {
      const targetElement = typeof element === 'string' ? document.querySelector(element) : element;
      if (!targetElement) return;
      
      const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - offset;
      window.scrollTo({
        top: targetPosition,
        behavior: 'smooth'
      });
    },

    // Debounce function
    debounce: function(func, wait = 300) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    },

    // Throttle function
    throttle: function(func, limit = 300) {
      let inThrottle;
      return function(...args) {
        if (!inThrottle) {
          func.apply(this, args);
          inThrottle = true;
          setTimeout(() => inThrottle = false, limit);
        }
      };
    }
  };

  // Auto-initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
      TracklytTheme.init();
    });
  } else {
    TracklytTheme.init();
  }

  // Expose TracklytTheme to window
  window.TracklytTheme = TracklytTheme;

})();
