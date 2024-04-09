/**
 * Copyright (c) 2023 Andrii Kreshchenko
 * mail2andyk@gmail.com
 */

(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.tlw = {}));
})(this, (function (exports) { 'use strict';

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

    function getTemplateElem(element, tplSelector) {
        const itemNode = element.content.cloneNode(true);
        if (!tplSelector) {
            return itemNode.firstElementChild;
        }
        return itemNode.querySelectorAll(tplSelector);
    }
    function getClosest(elem, selector) {
        if (!elem.matches) {
            return null;
        }
        if (elem.matches(selector)) {
            return elem;
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

    function getBCDate(date, isBC) {
        if (isBC) {
            const dateSlices = [Number(date.slice(0, 4)), date.slice(4) + 'T00:00:00'];
            const diff = new Date((1970 - dateSlices[0]) + dateSlices[1]);
            return new Date(diff.valueOf() - 62167226524000);
        }
        else {
            return new Date(date);
        }
    }

    class Widget {
        block;
        initOrder;
        _name;
        options;
        static logStyle = [
            'padding:3px 6px;font-family:monospace;font-size:15px;color:#099299;background-color:#ccebed;',
            'padding:3px 0;font-family:monospace;font-size:13px;color:#099299;',
        ];
        constructor(block, initOrder, options) {
            this.block = block;
            this.initOrder = initOrder;
        }
        setName = (name) => {
            this._name = name;
        };
        getName = () => {
            return this._name;
        };
        info() {
            console.info('%cWidget ' + this.getName() + ' is initialized.', Widget.logStyle[0]);
        }
        logException(message) {
            const logStyle = 'padding:3px 6px;color:#fd710d;background-color:#ffe5db;font-family:monospace;font-size:14px;';
            console.warn(message, logStyle);
        }
    }
    class WidgetCollapsible extends Widget {
        constructor(block, initOrder, options) {
            super(block, initOrder);
            this.options = {
                selector: options.selector || `[data-js="${this.block}"]`,
                collapseTime: options.collapseTime || 400,
            };
            this.setName('WidgetCollapsible');
            this.info();
        }
        info() {
            super.info();
            if (this.initOrder === 0) {
                console.info('It\'s preferable to use lower initialization order than other in rest widgets on the page (if any other exist).', Widget.logStyle[1]);
            }
        }
        init(container, points, tplElems) {
            const tplPoints = Array.from(document.querySelectorAll(tplElems)).joinTplNodes('.timeline-point');
            if (!container || !points.length || !tplPoints.length) {
                this.logException('No data available.');
                return;
            }
            const timePoints = Array.from(points).map(point => {
                const dateTime = point.querySelector('time').getAttribute('datetime');
                return {
                    dateTL: {
                        date: new Date(dateTime),
                        isBC: Boolean(point.dataset.not_ad)
                    },
                    dateStr: dateTime
                };
            });
            let html = '';
            const insert = (index, labelSuffix) => {
                if (html.length) {
                    const pointId = `point${index}${(labelSuffix || '1')}`.replace(/\W/g, '');
                    let widgetCPElem = getTemplateElem(this.block);
                    if (typeof widgetCPElem) {
                        widgetCPElem.querySelector('button').setAttribute('aria-controls', pointId);
                        const collapseElem = widgetCPElem.querySelector('[data-js="collapse"]');
                        collapseElem.id = pointId;
                        collapseElem.innerHTML = html;
                        points[index].before(widgetCPElem);
                        widgetCPElem = null;
                        html = '';
                    }
                }
            };
            const attachCollapseListener = (timing) => {
                Array.from(container.querySelectorAll('.widget-accordion button')).forEach((elem) => {
                    elem.onclick = function (e) {
                        e.preventDefault();
                        const collapseElem = elem.parentElement.nextElementSibling;
                        if (collapseElem.classList.contains('is-collapsed')) {
                            if (typeof collapseElem.animate === 'function') {
                                collapseElem.animate([
                                    { height: collapseElem.offsetHeight + 'px' },
                                    { height: 0 },
                                ], {
                                    duration: timing,
                                });
                            }
                            setTimeout(() => {
                                elem.setAttribute('aria-expanded', 'false');
                                collapseElem.classList.remove('is-collapsed');
                                collapseElem.style.height = '0';
                            }, timing);
                        }
                        else {
                            collapseElem.style.height = 'auto';
                            const collapseElemHeight = collapseElem.offsetHeight;
                            elem.setAttribute('aria-expanded', 'true');
                            collapseElem.classList.add('is-collapsed');
                            if (typeof collapseElem.animate === 'function') {
                                collapseElem.animate([
                                    { height: 0 },
                                    { height: collapseElemHeight + 'px' },
                                ], {
                                    duration: timing,
                                });
                            }
                        }
                    };
                });
            };
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
                            html += tplPoints[m].outerHTML.trim();
                            m++;
                        }
                        else {
                            insert(i, timePoints[i].dateStr);
                            iterate = false;
                        }
                    }
                    else {
                        if (!timeTplPoints[m]) {
                            insert(i, timePoints[i].dateStr);
                            attachCollapseListener(this.options.collapseTime);
                            return;
                        }
                        if (timeTplPoints[m].dateTL.isBC || timeTplPoints[m].dateTL.date <= timePoints[i].dateTL.date) {
                            html += tplPoints[m].outerHTML.trim();
                            m++;
                        }
                        else {
                            insert(i, timePoints[i].dateStr);
                            iterate = false;
                        }
                    }
                }
            }
            insert(tplPointsLen - 1, 'last');
            attachCollapseListener(this.options.collapseTime);
        }
    }
    class WidgetFormFilter extends Widget {
        _error = false;
        constructor(block, initOrder, options) {
            super(block, initOrder);
            this.options = {
                timelineType: options.timelineType,
                container: options.container,
                position: options.position,
                timelineVis: options.timelineVis,
                dataset: options.dataset,
                totalPoints: this.getTotalPoints(options)
            };
            this.setName('WidgetFormFilter');
            this.info();
        }
        setError() {
            this._error = true;
        }
        getError() {
            return this._error;
        }
        getTotalPoints(options) {
            if (!options) {
                return;
            }
            if (typeof options.pointsLen === 'number') {
                return options.pointsLen;
            }
            if (options.timelineType === 'v') {
                return this.block.querySelectorAll('.timeline-point').length;
            }
            else if (options.timelineType === 'h' && typeof options.timelineVis === 'object') {
                return options.timelineVis.getSelection().length;
            }
        }
        info() {
            super.info();
            if (this.options.timelineType === 'v') {
                console.info('%c(for vertical timeline)', Widget.logStyle[1]);
            }
            else if (this.options.timelineType === 'h') {
                console.info('%c(for horizontal timeline)', Widget.logStyle[1]);
                if (typeof this.options.timelineVis !== 'object' || typeof this.options.dataset !== 'object') {
                    this.logException(`%cFor ${this.getName()} and horizontal timeline 'timelineVis' and 'datasetVis' options should point to vis.js library in Horizontal timeline to run ${this.getName()}.`);
                    this.setError();
                }
            }
            else {
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
            const toggleFormHandler = (e) => {
                e.preventDefault();
                const parent = getClosest(e.target, 'button').parentElement;
                if (parent.classList.contains('active')) {
                    parent.classList.remove('active');
                }
                else {
                    parent.classList.add('active');
                }
            };
            const formSubmitHandler = (e) => {
                e.preventDefault();
                const rules = {
                    itemsLimit: 0,
                    range: [],
                    expression: '',
                    checkByExp: null,
                    checkByItemsLimit: (index) => index > (rules.itemsLimit ? rules.itemsLimit - 1 : 0),
                    checkByRange: null
                };
                const formData = Object.fromEntries(new FormData(this.block));
                if (typeof formData.searchexp === 'string' && formData.searchexp.length) {
                    rules.expression = formData.searchexp;
                }
                if (formData.limitnumber) {
                    rules.itemsLimit = Number(formData.limitnumber);
                }
                if (typeof formData.datestart === 'string' && formData.datestart.length
                    && typeof formData.dateend === 'string' && formData.dateend.length) {
                    const ruleDateStart = getBCDate(formData.datestart, Boolean(typeof formData.datestart_not_ad === 'string')), ruleDateEnd = getBCDate(formData.dateend, Boolean(typeof formData.datesend_not_ad === 'string'));
                    rules.range = [ruleDateStart, ruleDateEnd];
                }
                if (this.options.timelineType === 'h') {
                    rules.checkByExp = (item) => item.toLowerCase().indexOf(rules.expression.toLowerCase()) === -1;
                    rules.checkByRange = (item) => {
                        return item.date < rules.range[0] || item.date > rules.range[1];
                    };
                    console.log('h', rules, this.options.dataset);
                    if (this.options.datasetState) {
                        this.options.datasetState.length = 0;
                    }
                    else {
                        this.options.datasetState = [];
                    }
                    let hide = false;
                    this.options.dataset.forEach((item, index) => {
                        hide = (rules.expression.length && rules.checkByExp(item.title))
                            || rules.checkByItemsLimit(index)
                            || (rules.range.length && rules.checkByRange(item.dateTL));
                        if (!hide) {
                            this.options.datasetState.push(this.options.dataset[index]);
                        }
                    });
                    this.options.timelineVis.setItems(this.options.datasetState);
                    this.options.timelineVis.redraw();
                }
                else if (this.options.timelineType === 'v' && this.options.container) {
                    rules.checkByExp = (item) => {
                        const title = item.querySelector('.tl-content-main h3').innerText;
                        return title.toLowerCase().indexOf(rules.expression.toLowerCase()) === -1;
                    };
                    rules.checkByRange = (item) => {
                        if (Boolean(item.dataset.not_ad)) {
                            return true;
                        }
                        const pointDate = new Date(item.querySelector('time').getAttribute('datetime'));
                        return pointDate < rules.range[0] || pointDate > rules.range[1];
                    };
                    let hide = false;
                    const points = this.options.container.querySelectorAll(':scope > .timeline-point');
                    Array.from(points).forEach((elem, index) => {
                        if (elem.classList.contains('timeline-point')) {
                            hide = (rules.expression.length && rules.checkByExp(elem))
                                || (rules.itemsLimit && rules.checkByItemsLimit(index))
                                || (rules.range.length && rules.checkByRange(elem));
                            if (hide) {
                                elem.style.display = 'none';
                            }
                            else if (elem.getAttribute('style')) {
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
    class WidgetScrollspy extends Widget {
        constructor(block, initOrder, options) {
            super(block, initOrder);
            this.options = {
                timelineType: options.timelineType
            };
            this.setName('WidgetScrollspy');
            this.info();
        }
        info() {
            super.info();
        }
    }

    exports.WidgetCollapsible = WidgetCollapsible;
    exports.WidgetFormFilter = WidgetFormFilter;
    exports.WidgetScrollspy = WidgetScrollspy;

    Object.defineProperty(exports, '__esModule', { value: true });

}));
//# sourceMappingURL=widget.umd.js.map
