// Fungsi untuk load komponen
document.addEventListener('DOMContentLoaded', async () => {
    const components = document.querySelectorAll('[data-include]');
    
    for (const component of components) {
        const file = component.getAttribute('data-include');
        try {
            const response = await fetch(file);
            if (!response.ok) throw new Error(`Failed to load ${file}`);
            component.outerHTML = await response.text();
        } catch (error) {
            console.error(error);
            component.innerHTML = `<p class="text-red-500">Error loading ${file}</p>`;
        }
    }
});