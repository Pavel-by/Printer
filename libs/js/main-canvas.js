function fillCanvas(parentId) {
    particlesJS.load(parentId, '/libs/json/main-particles-config.json', function() {
        console.log('callback - particles.js config loaded');
    });
}