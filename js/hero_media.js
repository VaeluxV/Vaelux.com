document.addEventListener("DOMContentLoaded", () => {
    let index = 0;
    let currentLayer = 'A';
    const delayBetween = 10000;
    let isTransitioning = false;

    const getLayer = id => document.getElementById(`layer${id}`);

    function showMedia(mediaItem, targetLayer, callback) {
        const layer = getLayer(targetLayer);
        layer.style.backgroundImage = `url('${mediaItem.path}')`;
        callback();
    }

    function transitionToNext() {
        if (isTransitioning) return;
        isTransitioning = true;

        const nextLayer = currentLayer === 'A' ? 'B' : 'A';
        const current = getLayer(currentLayer);
        const next = getLayer(nextLayer);

        const mediaItem = media[index];
        index = (index + 1) % media.length;

        showMedia(mediaItem, nextLayer, () => {
            next.classList.add('visible');
            current.classList.remove('visible');
            currentLayer = nextLayer;
            isTransitioning = false;
            setTimeout(transitionToNext, delayBetween);
        });
    }

    transitionToNext();
});
