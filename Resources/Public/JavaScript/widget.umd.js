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

    class Widget {
        block;
        initOrder;
        _name;
        options;
        static logStyle = [
            'padding:3px 6px;color:#099299,background-color:#ccebed;font-family:monospace;font-size:15px;',
            'padding:3px 0;color:#099299,font-family:monospace;font-size:13px;'
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
            console.info(`%cWidget ${this.getName()} is initialized.`, Widget.logStyle[0]);
        }
        logException(message) {
            const logStyle = 'padding:3px 6px;color:#fd710d,background-color:#ffe8cc;font-family:monospace;font-size:13px;';
            console.warn('%c' + message, logStyle);
        }
    }
    class WidgetCollapsible extends Widget {
        constructor(block, initOrder, options) {
            super(block, initOrder);
            this.options = {
                selector: options.selector || `[data-js="${this.block}"]`
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
            const attachCollapseListener = () => {
                Array.from(container.querySelectorAll('.widget-accordion button')).forEach((elem) => {
                    elem.onclick = function (e) {
                        e.preventDefault();
                        const collapseElem = elem.parentElement.nextElementSibling;
                        if (collapseElem.classList.contains('is-collapsed')) {
                            collapseElem.classList.remove('is-collapsed');
                            elem.setAttribute('aria-expanded', 'false');
                        }
                        else {
                            collapseElem.classList.add('is-collapsed');
                            elem.setAttribute('aria-expanded', 'true');
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
                            attachCollapseListener();
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
            attachCollapseListener();
        }
    }
    class WidgetFormFilter extends Widget {
        _error = false;
        constructor(block, initOrder, options) {
            super(block, initOrder);
            this.options = {
                timelineType: options.timelineType,
                container: options.container,
                totalPoints: options.pointsLen ? options.pointsLen : this.block.querySelectorAll('.timeline-point').length,
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
        info() {
            super.info();
            if (this.options.timelineType === 'v') {
                console.info('(for vertical timeline)', Widget.logStyle[1]);
            }
            else if (this.options.timelineType === 'h') {
                console.info('(for horizontal timeline)', Widget.logStyle[1]);
            }
            else {
                this.logException(`Timeline type should be speciified to initialize ${this.getName()}.`);
                this.setError();
            }
        }
        init() {
            const formSubmitHandler = (e) => {
                e.preventDefault();
                const rules = {
                    itemsLimit: 0,
                    range: [],
                    expression: '',
                    checkByExp: (point) => {
                        const title = point.querySelector('.tl-content-main h3').innerText;
                        return title.toLowerCase().indexOf(rules.expression.toLowerCase()) === -1;
                    },
                    checkByItemsLimit: (index) => index > rules.itemsLimit - 1,
                    checkByRange: (point) => {
                        if (Boolean(point.dataset.not_ad)) {
                            return true;
                        }
                        const pointDate = new Date(point.querySelector('time').getAttribute('datetime'));
                        return pointDate < rules.range[0] || pointDate > rules.range[1];
                    }
                };
                const formData = Object.fromEntries(new FormData(this.block));
                if (formData.searchexp.length && typeof formData.searchexp === 'string') {
                    rules.expression = formData.searchexp;
                }
                if (formData.limitnumber) {
                    rules.itemsLimit = Number(formData.limitnumber);
                }
                if (typeof formData.datestart === 'string' && formData.datestart.length
                    && typeof formData.dateend === 'string' && formData.dateend.length) {
                    rules.range = [new Date(formData.datestart), new Date(formData.dateend)];
                }
                if (this.options.timelineType === 'h') {
                    console.log('h');
                }
                else if (this.options.timelineType === 'v' && this.options.container) {
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

            const rangeField = document.getElementById('tl_widget-limit-number');
            rangeField.setAttribute('max', this.options.totalPoints);
            rangeField.setAttribute('value', this.options.totalPoints);
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

    exports.Widget = Widget;
    exports.WidgetCollapsible = WidgetCollapsible;
    exports.WidgetFormFilter = WidgetFormFilter;
    exports.WidgetScrollspy = WidgetScrollspy;

    Object.defineProperty(exports, '__esModule', { value: true });

}));

