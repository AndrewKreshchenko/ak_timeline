'use strict';

import { TLVariantType, TLDateType, TplPointsType, TimelineI } from './types';
import './utils/base';

export class Timeline implements TimelineI {
  readonly type: TLVariantType;
  container: HTMLElement;
  dateStart?: TLDateType;
  dateEnd?: TLDateType;
  _pointsLen?: number;

  // Normal signature with defaults\
  // NOTE make dateStart as tuple Date() and flag B. C.
  constructor(type: TLVariantType = 'n', container: HTMLElement, dateStart?: TLDateType, dateEnd?: TLDateType) {
    this.type = type;
    this.container = container;
  }

  protected setPointsLength = (len: number) => {
    this._pointsLen = len;
  }

  protected getPointsLength = () => {
    return this._pointsLen;
  }

  protected setRange = (dateStart: TLDateType, dateEnd: TLDateType) => {
    this.dateStart = dateStart;
    this.dateEnd = dateEnd;
  }

  logRange() {
    return `Timeline (${this.dateStart.date.toDateString()} - ${this.dateEnd.date.toDateString()}).`;
  }

  log() {
    if (this.type === 'n') {
      console.log(`Your Timeline type is not defined. Please provide appropriate key: \
      'v' - vertical\
      'h' - horizontal\
      'b' - bars-styled\
      'p' - pie\
      'n' - none of above types (generate Exception)`);
      return;
    }

    if (this.dateStart && this.dateEnd) {
      console.log(this.logRange());
    }
  }

  logException(index: number) {
    const logStyle = 'padding:3px 6px;color:#fd710d,background-color:#ffe8cc;font-family:monospace;font-size:13px;';

    switch(index) {
      case 0:
        console.warn('%cTimeline has no points or markup has not proper structure.', logStyle);
        break;
      case 1:
        console.warn('%cPoints of derived timeline have already been spread by widgets.collapsiblePoints.', logStyle);
        break;
      default:
        break;
    }
  }
}


// ------------
// Timeline variants (composition)

/**
 * Vertical timeline
 */

// type Constructor<T = {}> = new (...args: any[]) => T;

export class VerticalTimeline extends Timeline {
  points: NodeListOf<HTMLElement>;

  // constructor(...args: (keyof TimelineI)[]) {
  constructor(type: TLVariantType = 'n', container: HTMLElement, points: NodeListOf<HTMLElement>) {
    super(type, container);
    this.points = points;

    this.setPointsLength(this.points.length);
  }

  spreadDerivedSegments(tplElems: any) {
    if (!this.getPointsLength()) {
      this.logException(0);
      return;
    }

    const tplPoints = tplElems.joinTplNodes('.timeline-point');

    const timePoints: Array<Pick<TplPointsType, 'dateTL' | 'dateStr'>> = Array.from(this.points).map(point => {
      const dateTime = point.querySelector('time').getAttribute('datetime');
      return {
        dateTL: {
          date: new Date(dateTime),
          isBC: Boolean(point.dataset.not_ad)
        },
        dateStr: dateTime
      }
    });

    // Spread points of each segments
    // If no segments, tplPointsLen == 0 and still used in the current scope
    const tplPointsLen = tplPoints.length;
    const timeTplPoints: Array<TplPointsType> = tplPoints.map((point: HTMLElement, i: number) => ({
      index: i,
      dateTL: {
        date: new Date(point.dataset.date),
        isBC: Boolean(point.dataset.not_ad)
      },
      dateStr: point.dataset.date,
    }));

    // TODO provide an algorytm to sort it properly
    timeTplPoints.sort((prev: TplPointsType, next: TplPointsType) => {
      if (next.dateTL.isBC) {
        return next.dateTL.isBC > prev.dateTL.isBC ? 1 : -1;
      } else {
        return next.dateTL.isBC < prev.dateTL.isBC ? 1 : -1;
      }
    });

    // For each shown point of parent timeline check through the points of derived timelines 
    let iterate;

    for (let i = 0, m = 0; i < tplPointsLen; i++) {
      iterate = true;

      // Iterate through child points since their dates prevail over a point is being tested 
      while (iterate) {
        if (timePoints[i].dateTL.isBC) {
          if (timeTplPoints[m].dateTL.isBC && timeTplPoints[m].dateTL.date >= timePoints[i].dateTL.date) {
            this.points[i].before(tplPoints[m]);
            m++;
          } else {
            iterate = false;
          }
        } else {
          if (!timeTplPoints[m]) {
            // Tpl points list is drain
            return;
          }

          if (timeTplPoints[m].dateTL.isBC || timeTplPoints[m].dateTL.date <= timePoints[i].dateTL.date) {
            this.points[i].before(tplPoints[m]);
            m++;
          } else {
            iterate = false;
          }
        }
      }
    }
  }
}

// function Warrior<TBase extends Constructor>(Base: TBase) {
//   return class extends Base {
//     say: string = 'Attaaack';
//     attack() { console.log("attacking...") }
//   }
// }

// function Wings<TBase extends Constructor>(Base: TBase) {
//   return class extends Base {
//     fly() { console.log('flying...') }
//   }
// }
