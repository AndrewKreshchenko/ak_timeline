'use strict';

import { TLDateType } from './types';
import { WidgetI, TplPointsType } from './types';
import './utils/base';
import { getTemplateElem, getClosest } from './utils/helpers';

/**
 * @class Widget
 * 
 * Base Widget
 */
export class Widget implements WidgetI {
  block: HTMLElement | HTMLTemplateElement;
  initOrder: number;
  _name: string;
  options?: any;

  static logStyle = [
    'padding:3px 6px;font-family:monospace;font-size:15px;color:#099299;background-color:#ccebed;',
    'padding:3px 0;font-family:monospace;font-size:13px;color:#099299;',
  ];

  constructor(block: HTMLElement | null, initOrder: number, options?: any) {
    this.block = block;
    this.initOrder = initOrder;
  }

  protected setName = (name: string) => {
    this._name = name;
  }

  protected getName = () => {
    return this._name;
  }

  info() {
    console.info('%cWidget ' + this.getName() + ' is initialized.', Widget.logStyle[0]);
  }

  logException(message: string) {
    const logStyle = 'padding:3px 6px;color:#fd710d;background-color:#ffe5db;font-family:monospace;font-size:14px;';

    console.warn(message, logStyle);
  }
}

/**
 * @class WidgetCollapsible
 * 
 * Turns derived segments into collapsible elements
 */
export class WidgetCollapsible extends Widget implements WidgetI {
  // constructor(...args: (keyof TimelineI)[]) {
  constructor(block: HTMLTemplateElement, initOrder: number, options?: any) {
    super(block, initOrder);

    this.options = {
      selector: options.selector || `[data-js="${this.block}"]`,
      collapseTime: options.collapseTime || 400,
    }

    this.setName('WidgetCollapsible');
    this.info();
  }

  info() {
    super.info();

    if (this.initOrder === 0) {
      console.info('It\'s preferable to use lower initialization order than other in rest widgets on the page (if any other exist).', Widget.logStyle[1]);
    }
  }

  init(container: HTMLElement, points: NodeListOf<HTMLElement>, tplElems: string): void {
    const tplPoints = Array.from(document.querySelectorAll(tplElems)).joinTplNodes('.timeline-point');

    if (!container || !points.length || !tplPoints.length) {
      this.logException('No data available.');
      return;
    }

    const timePoints: Array<Pick<TplPointsType, 'dateTL' | 'dateStr'>> = Array.from(points).map(point => {
      const dateTime = point.querySelector('time').getAttribute('datetime');
      return {
        dateTL: {
          date: new Date(dateTime),
          isBC: Boolean(point.dataset.not_ad)
        },
        dateStr: dateTime
      }
    });

    let html = '';

    const insert = (index: number, labelSuffix: string) => {
      if (html.length) {
        const pointId = `point${index}${(labelSuffix || '1')}`.replace(/\W/g, '');
        let widgetCPElem = getTemplateElem(this.block as HTMLTemplateElement);

        if (typeof widgetCPElem) {
          (widgetCPElem as HTMLElement).querySelector('button').setAttribute('aria-controls', pointId);

          const collapseElem = (widgetCPElem as HTMLElement).querySelector('[data-js="collapse"]');

          collapseElem.id = pointId;
          collapseElem.innerHTML = html;
          points[index].before((widgetCPElem as HTMLElement));

          (widgetCPElem as HTMLElement) = null;
          html = '';
        }
      }
    }

    const attachCollapseListener = (timing: number) => {
      Array.from(container.querySelectorAll('.widget-accordion button')).forEach((elem: HTMLElement) => {
        elem.onclick = function(e) {
          e.preventDefault();

          const collapseElem = elem.parentElement.nextElementSibling;

          if (collapseElem.classList.contains('is-collapsed')) {
            if (typeof (collapseElem as HTMLElement).animate === 'function') {
              collapseElem.animate(
                [
                  { height: (collapseElem as HTMLElement).offsetHeight + 'px' },
                  { height: 0 },
                ],
                {
                  duration: timing,
                }
              );
            }
            setTimeout(() => {
              elem.setAttribute('aria-expanded', 'false');
              collapseElem.classList.remove('is-collapsed');
              (collapseElem as HTMLElement).style.height = '0';
            }, timing);
          } else {
            (collapseElem as HTMLElement).style.height = 'auto';

            const collapseElemHeight = (collapseElem as HTMLElement).offsetHeight;

            elem.setAttribute('aria-expanded', 'true');
            collapseElem.classList.add('is-collapsed');

            // @TODO implement polyfill in helpers.js
            if (typeof (collapseElem as HTMLElement).animate === 'function') {
              collapseElem.animate(
                [
                  { height: 0 },
                  { height: collapseElemHeight + 'px' },
                ],
                {
                  duration: timing,
                }
              );
            }
          }
        }
      });
    }

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
            html += tplPoints[m].outerHTML.trim();
            m++;
          } else {
            insert(i, timePoints[i].dateStr);
            iterate = false;
          }
        } else {
          if (!timeTplPoints[m]) {
            insert(i, timePoints[i].dateStr);
            attachCollapseListener(this.options.collapseTime);
            return;
          }

          if (timeTplPoints[m].dateTL.isBC || timeTplPoints[m].dateTL.date <= timePoints[i].dateTL.date) {
            html += tplPoints[m].outerHTML.trim();
            m++;
          } else {
            insert(i, timePoints[i].dateStr);
            iterate = false;
          }
        }
      }
    }

    insert(tplPointsLen - 1, 'last');
    attachCollapseListener(this.options.collapseTime);
  }

  // spread(tplElems: any) {
  //   if (!this.getPointsLength()) {
  //     this.logException(0);
  //     return;
  //   }
  // }
}

/**
 * @class WidgetFormFilter
 * 
 * @TODO provide types for options
 * @TODO describe there options available
 * 
 * Widget provide filter methods of data (points).
 * Works for both vertical and horizontal Timeline types.
 */

export class WidgetFormFilter extends Widget implements WidgetI {
  _error: boolean = false;

  constructor(block: HTMLFormElement, initOrder: number, options?: any) {
    super(block, initOrder);

    this.options = {
      timelineType: options.timelineType,
      container: options.container,
      position: options.position,
      timelineVis: options.timelineVis,
      dataset: options.dataset,
      totalPoints: this.getTotalPoints(options) // options.pointsLen ? options.pointsLen : this.block.querySelectorAll('.timeline-point').length,
    }

    this.setName('WidgetFormFilter');
    this.info();
  }

  setError() {
    this._error = true;
  }

  getError(): boolean {
    return this._error;
  }

  getTotalPoints(options?: any) {
    if (!options) {
      return;
    }

    if (typeof options.pointsLen === 'number') {
      return options.pointsLen;
    }

    if (options.timelineType === 'v') {
      return this.block.querySelectorAll('.timeline-point').length;
    } else if (options.timelineType === 'h' && typeof options.timelineVis === 'object') {
      return options.timelineVis.getSelection().length;
    }
  }

  info() {
    super.info();

    if (this.options.timelineType === 'v') {
      console.info('%c(for vertical timeline)', Widget.logStyle[1]);
    } else if (this.options.timelineType === 'h') {
      console.info('%c(for horizontal timeline)', Widget.logStyle[1]);
      if (typeof this.options.timelineVis !== 'object' || typeof this.options.dataset !== 'object') {
        this.logException(`%cFor ${this.getName()} and horizontal timeline 'timelineVis' and 'datasetVis' options should point to vis.js library in Horizontal timeline to run ${this.getName()}.`);
        this.setError();
      }
    } else {
      this.logException(`%cTimeline type should be speciified to initialize ${this.getName()}.`);
      this.setError();
    }

    if (!this.options.totalPoints) {
      this.logException(`%cLack of options provided for ${this.getName()} to get total points. You may take a look:\n'pointsLen' - for vertical timeline,\n'timelineVis' - for horizontal timeline.\n`);
      this.setError();
    }
  }

  init() {
    if (this.getError()) {
      this.logException(`%cTimeline ${this.getName()} with this set of options could not initialize.`);
      return;
    }

    const toggleFormHandler = (e: Event) => {
      e.preventDefault();

      const parent = getClosest((e.target as HTMLElement), 'button').parentElement;

      if (parent.classList.contains('active')) {
        parent.classList.remove('active');
      } else {
        parent.classList.add('active');
      }
    }

    const formSubmitHandler = (e: Event) => {
      e.preventDefault();
  
      // Rules to filter
      const rules: {
        itemsLimit: number,
        range: Date[],
        expression: string,
        checkByExp: (item: HTMLElement|string) => Boolean | null,
        checkByItemsLimit: (index: number) => Boolean,
        checkByRange: (item: HTMLElement|TLDateType) => Boolean
      } = {
        itemsLimit: 0,
        range: [],
        expression: '',
        checkByExp: null,
        checkByItemsLimit: (index: number) => index > (rules.itemsLimit ? rules.itemsLimit - 1 : 0),
        checkByRange: null
      };
  
      // Get fields
      const formData = Object.fromEntries(new FormData((this.block as HTMLFormElement)));

      if (typeof formData.searchexp === 'string' && formData.searchexp.length) {
        rules.expression = formData.searchexp;
      }
  
      if (formData.limitnumber) {
        rules.itemsLimit = Number(formData.limitnumber);
      }
  
      if (typeof formData.datestart === 'string' && formData.datestart.length
        && typeof formData.dateend === 'string' && formData.dateend.length) {
        rules.range = [new Date(formData.datestart), new Date(formData.dateend)];
      }
  
      // Filter points
      if (this.options.timelineType === 'h') {
        rules.checkByExp = (item: string) =>
          item.toLowerCase().indexOf(rules.expression.toLowerCase()) === -1;

        rules.checkByRange = (item: TLDateType) => {
          if (item.isBC === true) {
            // TODO Provide check also by B. C. values
            return true;
          }

          return item.date < rules.range[0] || item.date > rules.range[1];
        }

        // console.log('h', rules, this.options.dataset);
        if (this.options.datasetState) {
          this.options.datasetState.length = 0;
        } else {
          this.options.datasetState = [];
        }

        let hide: Boolean = false;
  
        this.options.dataset.forEach((item: any, index: number) => {
          hide = (rules.expression.length && rules.checkByExp(item.title))
            || rules.checkByItemsLimit(index)
            || (rules.range.length && rules.checkByRange(item.dateTL));
  
          if (!hide) {
            this.options.datasetState.push(this.options.dataset[index]);
          }
        });

        // @TODO Check instance of this.options.timelineVis
        this.options.timelineVis.setItems(this.options.datasetState);
        this.options.timelineVis.redraw();
      } else if (this.options.timelineType === 'v' && this.options.container) {
        rules.checkByExp = (item: HTMLElement) => {
          const title = (item.querySelector('.tl-content-main h3') as HTMLElement).innerText;
  
          return title.toLowerCase().indexOf(rules.expression.toLowerCase()) === -1;
        }
        rules.checkByRange = (item: HTMLElement) => {
          if (Boolean(item.dataset.not_ad)) {
            // TODO Provide check also by B. C. values
            return true;
          }
  
          const pointDate = new Date(item.querySelector('time').getAttribute('datetime'));
  
          return pointDate < rules.range[0] || pointDate > rules.range[1];
        }

        let hide: Boolean = false;
        const points = this.options.container.querySelectorAll(':scope > .timeline-point');
  
        Array.from(points).forEach((elem: HTMLElement, index: number) => {
          if (elem.classList.contains('timeline-point')) {
            hide = (rules.expression.length && rules.checkByExp(elem))
              || (rules.itemsLimit && rules.checkByItemsLimit(index))
              || (rules.range.length && rules.checkByRange(elem));
  
            if (hide) {
              elem.style.display = 'none';
            } else if (elem.getAttribute('style')) {
              elem.removeAttribute('style');
            }
          }
        });
      }
    };

    if (this.options.position === 'top') {
      this.block.classList.add('widget-form-filter-top');
    }

    const rangeField = document.getElementById('tl_widget-limit-number');

    rangeField.setAttribute('max', this.options.totalPoints);
    rangeField.setAttribute('value', this.options.totalPoints);

    this.block.previousElementSibling.addEventListener('click', toggleFormHandler);
    this.block.addEventListener('submit', formSubmitHandler);
  }
}

/**
 * @class WidgetScrollspy
 * 
 * Timeline data Navigator
 * Active links change depeding on scroll/swipe position
 */

// NOTE use getVisibleItems() for horiz. timeline
export class WidgetScrollspy extends Widget implements WidgetI {
  constructor(block: HTMLTemplateElement, initOrder: number, options?: any) {
    super(block, initOrder);

    this.options = {
      timelineType: options.timelineType
    }

    this.setName('WidgetScrollspy');
    this.info();
  }

  info() {
    super.info();
  }
}
