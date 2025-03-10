(function() {
    const items = document.querySelectorAll('.grid__item');
    items.forEach((el, pos) => {
        const bttn = el.querySelector('button.particles-button');
        //const bttnBack = el.querySelector('button.action');
        
        let particlesOpts =
            {type: 'triangle',
            easing: 'easeOutQuart',
            size: 5,
            particlesAmountCoefficient: 4,
            oscillationCoefficient: 2,
            color: function() {
            return Math.random() < 0.5 ? '#000000' : '#ffffff';}};
        particlesOpts.complete = () => {
            //podria enviar los datos aqui


        };
        const particles = new Particles(bttn, particlesOpts);
        
        let buttonVisible = true;
        bttn.addEventListener('click', () => {
            if ( !particles.isAnimating() && buttonVisible ) {
                particles.disintegrate();
                buttonVisible = !buttonVisible;
            }
        });
    });

})();
