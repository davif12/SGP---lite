<footer class="app-footer mt-auto">
    <div class="container-fluid">
        <div class="row align-items-center py-3">
            <div class="col-md-6">
                <div class="d-flex align-items-center text-muted">
                    <span class="me-3">&copy; {{ date('Y') }} SGP Lite</span>
                    <span class="me-3">|</span>
                    <span class="me-3">Versão 2.0</span>
                    <span class="me-3">|</span>
                    <span>Sprint 2 - Épicos</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center justify-content-md-end">
                    <div class="d-flex align-items-center text-muted me-4">
                        <i class="bi bi-clock me-1"></i>
                        <span id="current-time"></span>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="#" class="text-muted" title="Documentação">
                            <i class="bi bi-book"></i>
                        </a>
                        <a href="#" class="text-muted" title="Suporte">
                            <i class="bi bi-question-circle"></i>
                        </a>
                        <a href="#" class="text-muted" title="GitHub">
                            <i class="bi bi-github"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
// Update current time
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('pt-BR', { 
        hour: '2-digit', 
        minute: '2-digit'
    });
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}

// Update time immediately and then every minute
updateTime();
setInterval(updateTime, 60000);
</script>

<style>
.app-footer {
    background: var(--white);
    border-top: 1px solid var(--gray-200);
    font-size: 0.875rem;
}

.app-footer a {
    transition: color var(--transition-fast);
}

.app-footer a:hover {
    color: var(--primary) !important;
}

[data-theme="dark"] .app-footer {
    background: var(--dark-surface);
    border-top-color: var(--dark-border);
}

[data-theme="dark"] .app-footer .text-muted {
    color: var(--gray-400) !important;
}
</style>
