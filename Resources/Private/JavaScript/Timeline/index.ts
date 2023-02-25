'use strict';

class Timeline {
  dateStart: Date;
  dateEnd: Date;
 
  // Normal signature with defaults
  constructor(dateStart = new Date(), dateEnd = new Date()) {
    this.dateStart = dateStart;
    this.dateEnd = dateEnd;
  }

  log() {
    console.log(`Timeline (${this.dateStart.toDateString()} - ${this.dateEnd.toDateString()}).`);
  }
}
