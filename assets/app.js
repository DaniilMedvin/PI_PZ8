(function () {
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    var scene = document.querySelector('.scene');
    if (!scene) {
        return;
    }

    var tints = [
        'rgba(0, 122, 255, 0.10)',
        'rgba(90, 200, 250, 0.16)',
        'rgba(120, 120, 140, 0.08)'
    ];

    var cubes = [
        { x: 6, y: 12, s: 90, z: -200, spin: 38, bob: 7, delay: -2 },
        { x: 78, y: 8, s: 130, z: -420, spin: 52, bob: 9, delay: -5 },
        { x: 88, y: 58, s: 70, z: 40, spin: 30, bob: 6, delay: -1 },
        { x: 14, y: 68, s: 150, z: -360, spin: 60, bob: 10, delay: -7 },
        { x: 46, y: 80, s: 60, z: 80, spin: 34, bob: 8, delay: -3 },
        { x: 58, y: 6, s: 56, z: -100, spin: 28, bob: 6, delay: -4 },
        { x: 30, y: 38, s: 100, z: -600, spin: 70, bob: 11, delay: -6 },
        { x: 70, y: 36, s: 110, z: -520, spin: 64, bob: 12, delay: -8 }
    ];

    var inner = document.createElement('div');
    inner.className = 'scene-inner';

    cubes.forEach(function (c, i) {
        var wrap = document.createElement('div');
        wrap.className = 'float';
        wrap.style.setProperty('--x', c.x + '%');
        wrap.style.setProperty('--y', c.y + '%');
        wrap.style.setProperty('--z', c.z + 'px');
        wrap.style.setProperty('--bob', c.bob + 's');
        wrap.style.setProperty('--delay', c.delay + 's');

        var cube = document.createElement('div');
        cube.className = 'cube';
        cube.style.setProperty('--s', c.s + 'px');
        cube.style.setProperty('--spin', c.spin + 's');
        cube.style.setProperty('--tint', tints[i % tints.length]);

        for (var f = 0; f < 6; f++) {
            var face = document.createElement('div');
            face.className = 'face';
            cube.appendChild(face);
        }

        wrap.appendChild(cube);
        inner.appendChild(wrap);
    });

    scene.appendChild(inner);

    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)');
    var frame = null;

    window.addEventListener('pointermove', function (event) {
        if (reduce.matches || frame) {
            return;
        }
        frame = requestAnimationFrame(function () {
            var rx = (event.clientY / window.innerHeight - 0.5) * -6;
            var ry = (event.clientX / window.innerWidth - 0.5) * 8;
            inner.style.transform = 'rotateX(' + rx.toFixed(2) + 'deg) rotateY(' + ry.toFixed(2) + 'deg)';
            frame = null;
        });
    });
})();
