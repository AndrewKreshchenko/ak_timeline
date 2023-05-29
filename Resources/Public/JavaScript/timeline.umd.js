/**
 * Copyright (c) 2023 Andrii Kreshchenko
 * mail2andyk@gmail.com
 */

(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.tl = {}));
})(this, (function (exports) { 'use strict';

    function getClosest(elem, selector) {
        if (!elem.matches) {
            return null;
        }
        while (elem !== document.body) {
            elem = elem.parentElement;
            if (elem.matches) {
                if (elem.matches(selector)) {
                    return elem;
                }
            }
        }
    }

    if (typeof Object.prototype.joinTplNodes !== 'function') {
        Object.defineProperty(Object.prototype, 'joinTplNodes', {
            value: function (selector) {
                const nodes = Array.from(this).map((elem) => {
                    const tplNode = elem.content.cloneNode(true);
                    const tplElements = tplNode.querySelectorAll(selector);
                    return Array.prototype.slice.call(tplElements);
                });
                return nodes.flat();
            }
        });
    }

    class Timeline {
        type;
        container;
        dateStart;
        dateEnd;
        _pointsLen;
        constructor(type = 'n', container, dateStart, dateEnd) {
            this.type = type;
            this.container = container;
        }
        setPointsLength = (len) => {
            this._pointsLen = len;
        };
        getPointsLength = () => {
            return this._pointsLen;
        };
        setRange = (dateStart, dateEnd) => {
            this.dateStart = dateStart;
            this.dateEnd = dateEnd;
        };
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
        logException(index) {
            const logStyle = 'padding:3px 6px;color:#fd710d,background-color:#ffe8cc;font-family:monospace;font-size:13px;';
            switch (index) {
                case 0:
                    console.warn('%cTimeline has no points or markup has not proper structure.', logStyle);
                    break;
                case 1:
                    console.warn('%cPoints of derived timeline have already been spread by widgets.collapsiblePoints.', logStyle);
                    break;
            }
        }
    }
    class VerticalTimeline extends Timeline {
        points;
        constructor(type = 'n', container, points) {
            super(type, container);
            this.points = points;
            this.setPointsLength(this.points.length);
        }
        spreadDerivedSegments(tplElems) {
            if (!this.getPointsLength()) {
                this.logException(0);
                return;
            }
            const tplPoints = tplElems.joinTplNodes('.timeline-point');
            const timePoints = Array.from(this.points).map(point => {
                const dateTime = point.querySelector('time').getAttribute('datetime');
                return {
                    dateTL: {
                        date: new Date(dateTime),
                        isBC: Boolean(point.dataset.not_ad)
                    },
                    dateStr: dateTime
                };
            });
            const tplPointsLen = tplPoints.length;
            const timeTplPoints = tplPoints.map((point, i) => ({
                index: i,
                dateTL: {
                    date: new Date(point.dataset.date),
                    isBC: Boolean(point.dataset.not_ad)
                },
                dateStr: point.dataset.date,
            }));
            timeTplPoints.sort((prev, next) => {
                if (next.dateTL.isBC) {
                    return next.dateTL.isBC > prev.dateTL.isBC ? 1 : -1;
                }
                else {
                    return next.dateTL.isBC < prev.dateTL.isBC ? 1 : -1;
                }
            });
            let iterate;
            for (let i = 0, m = 0; i < tplPointsLen; i++) {
                iterate = true;
                while (iterate) {
                    if (timePoints[i].dateTL.isBC) {
                        if (timeTplPoints[m].dateTL.isBC && timeTplPoints[m].dateTL.date >= timePoints[i].dateTL.date) {
                            this.points[i].before(tplPoints[m]);
                            m++;
                        }
                        else {
                            iterate = false;
                        }
                    }
                    else {
                        if (!timeTplPoints[m]) {
                            return;
                        }
                        if (timeTplPoints[m].dateTL.isBC || timeTplPoints[m].dateTL.date <= timePoints[i].dateTL.date) {
                            this.points[i].before(tplPoints[m]);
                            m++;
                        }
                        else {
                            iterate = false;
                        }
                    }
                }
            }
        }
    }
    class HorizontalTimeline extends Timeline {
        visTimeline;
        constructor(type = 'n', container, visTimeline) {
            super(type, container);
            this.visTimeline = visTimeline;
        }
        handleClickVisItem = (e, block, container) => {
            if (!(e.target instanceof HTMLElement)) {
                return;
            }
            e.preventDefault();
            const pointId = getClosest(e.target, '.vis-point').dataset.id;
            const templateElem = block.nextElementSibling;
            if (templateElem instanceof HTMLTemplateElement) {
                const pointNode = templateElem.content.cloneNode(true);
                const pointBlock = pointNode.querySelector('.timeline[data-point_id="' + pointId + '"]');
                container.innerHTML = pointBlock.innerHTML;
            }
        };
    }

    exports.HorizontalTimeline = HorizontalTimeline;
    exports.Timeline = Timeline;
    exports.VerticalTimeline = VerticalTimeline;

    Object.defineProperty(exports, '__esModule', { value: true });

}));
//# sourceMappingURL=timeline.umd.js.map
