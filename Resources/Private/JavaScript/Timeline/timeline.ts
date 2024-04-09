'use strict';

import { TLVariantType, TLDateType, TplPointsType, TimelineI } from './types';
import { getClosest } from './utils/helpers';
import './utils/base';

class Timeline implements TimelineI {
  readonly type: TLVariantType;
  container: HTMLElement;
  dateStart?: TLDateType;
  dateEnd?: TLDateType;
  _pointsLen?: number;

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
    const logStyle = 'padding:3px 6px;font-family:monospace;font-size:13px;color:#fd710d;background-color:#ffe8cc;';

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

export class VerticalTimeline extends Timeline {
  points: NodeListOf<HTMLElement>;

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


// ------------
// Timeline variants

/**
 * Horizontal timeline
 * 
 * This Timeline may take vis.js library under the hood:
 * 'visTimeline' - vis.Timeline object
 * 
 * @TODO develop extended vis.js Timeline with A. D. and B. C. ranges
 * (https://github.com/visjs/vis-timeline#contribute)
 */

export class HorizontalTimeline extends Timeline {
  visTimeline: ObjectConstructor;
  // visItems: ObjectConstructor;

  constructor(type: TLVariantType = 'n', container: HTMLElement, visTimeline: ObjectConstructor) {
    super(type, container);
    this.visTimeline = visTimeline;

    this.init();
  }

  init() {
    // @TODO optimize markup or provide other options to constructor
    const handleClickVisItem = (e: Event): void => {
      if (!(e.target instanceof HTMLElement)) {
        return;
      }
    
      e.preventDefault();
    
      const pointId = getClosest(e.target, '.vis-point').dataset.id;
      const templateElem = this.container.querySelector('[data-js="timeline-data"]');
    
      if (templateElem instanceof HTMLTemplateElement) {
        const pointNode = templateElem.content.cloneNode(true);
        const pointBlock = (pointNode as HTMLElement).querySelector('.timeline[data-point_id="' + pointId + '"]');
    
        this.container.querySelector('.timeline').innerHTML = pointBlock.innerHTML;
      }
    }

    // Open content block by clicked point
    this.container.querySelectorAll('.vis-item-content').forEach((point) => {
      point.addEventListener('click', handleClickVisItem);
    });
  }
}
