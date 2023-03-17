'use strict';

import format from './utils/format';

class Timeline {
  dateStart: Date;
  dateEnd: Date;
 
  // Normal signature with defaults\
  // NOTE make dateStart as tuple Date() and flag B. C.
  constructor(dateStart = new Date(), dateEnd = new Date()) {
    this.dateStart = dateStart;
    this.dateEnd = dateEnd;
  }

  log() {
    console.log(`Timeline (${this.dateStart.toDateString()} - ${this.dateEnd.toDateString()}).`);
  }

  logDate() {
    console.log(`Timeline (${format(this.dateStart, true)} - ${this.dateEnd.toDateString()}).`);
  }
}
