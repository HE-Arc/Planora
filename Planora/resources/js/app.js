// Theme commun uniquement. Aucune logique propre aux pages.
const themeButton = document.querySelector('[data-theme-toggle]');
function syncThemeLabel() {
    const dark = document.documentElement.dataset.theme === 'dark';
    if (!themeButton) return;
    const label = dark ? 'Activer le mode clair' : 'Activer le mode sombre';
    themeButton.setAttribute('aria-label', label);
    themeButton.querySelector('[data-theme-label]').textContent = dark ? 'Mode clair' : 'Mode sombre';
}
syncThemeLabel();
themeButton?.addEventListener('click', () => {
    const theme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = theme;
    try { localStorage.setItem('planora-theme', theme); } catch (_) {}
    syncThemeLabel();
});