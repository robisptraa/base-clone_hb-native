document.addEventListener('DOMContentLoaded', function() {
    // Animasi sederhana untuk card hover
    const cards = document.querySelectorAll('#portfolio .card');
    cards.forEach(card => {
        card.addEventListener('mouseover', () => {
            card.style.transform = 'scale(1.05)';
        });
        card.addEventListener('mouseout', () => {
            card.style.transform = 'scale(1)';
        });
    });
});
