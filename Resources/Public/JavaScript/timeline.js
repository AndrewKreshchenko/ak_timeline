'use strict';
class Timeline {
    dateStart;
    dateEnd;
    constructor(dateStart = new Date(), dateEnd = new Date()) {
        this.dateStart = dateStart;
        this.dateEnd = dateEnd;
    }
    log() {
        console.log(`Timeline (${this.dateStart.toDateString()} - ${this.dateEnd.toDateString()}).`);
    }
}

//# sourceMappingURL=index.js.map
