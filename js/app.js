// JavaScript pour confirmer les actions
document.addEventListener('DOMContentLoaded', function() {
    // Ajouter confirmation sur les formulaires sensibles
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement...';
            }
        });
    });
});