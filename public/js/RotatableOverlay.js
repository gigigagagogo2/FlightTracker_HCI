export default class RotatableOverlay extends google.maps.OverlayView {
    constructor(position, imageUrl, angle) {
        super();
        this.position = position;
        this.imageUrl = imageUrl;
        this.angle = angle;
        this.div = null;
    }

    onAdd() {
        this.div = document.createElement('div');
        this.div.style.position = 'absolute';
        this.div.style.zIndex = '-1';
        this.div.innerHTML = `<img src="${this.imageUrl}" style="transform: rotate(${this.angle}deg); width:40px;" alt="plane">`;

        /** @type {google.maps.MapPanes} */
        const panes = this.getPanes();
        panes.overlayMouseTarget.appendChild(this.div);

        this.div.addEventListener('click', (event) => {
            // Rilancia l'evento sull'overlay stesso
            google.maps.event.trigger(this, 'click', event);
        });

    }

    draw() {
        const projection = this.getProjection();
        if (!projection || !this.position || !this.div) return;

        const point = projection.fromLatLngToDivPixel(this.position);
        if (point) {
            const width = this.div.offsetWidth;
            const height = this.div.offsetHeight;

            this.div.style.left = (point.x - width / 2) + 'px';
            this.div.style.top = (point.y - height / 2) + 'px';
        }
    }

    onRemove() {
        if (this.div) {
            this.div.remove();
            this.div = null;
        }
    }

    setPosition(position) {
        this.position = position;
        if (this.getMap()) {
            this.draw();
        }
    }

}
