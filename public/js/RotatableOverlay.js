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
        panes.overlayImage.appendChild(this.div);
    }

    draw() {
        const point = this.getProjection().fromLatLngToDivPixel(this.position);

        if (point && this.div) {
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
        this.draw();
    }

}
