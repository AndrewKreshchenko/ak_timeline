(function (factory) {
  typeof define === 'function' && define.amd ? define(factory) :
  factory();
})((function () { 'use strict';

  function _toConsumableArray(arr) {
    return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
  }
  function _arrayWithoutHoles(arr) {
    if (Array.isArray(arr)) return _arrayLikeToArray(arr);
  }
  function _iterableToArray(iter) {
    if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
  }
  function _unsupportedIterableToArray(o, minLen) {
    if (!o) return;
    if (typeof o === "string") return _arrayLikeToArray(o, minLen);
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if (n === "Object" && o.constructor) n = o.constructor.name;
    if (n === "Map" || n === "Set") return Array.from(o);
    if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
  }
  function _arrayLikeToArray(arr, len) {
    if (len == null || len > arr.length) len = arr.length;
    for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];
    return arr2;
  }
  function _nonIterableSpread() {
    throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
  }

  // Utility functions
  Object.prototype.joinTplNodes = function (selector) {
    var _Array$prototype$conc;
    var nodes = Array.from(this).map(function (elem) {
      var tplNode = elem.content.cloneNode(true);
      var tplElements = tplNode.querySelectorAll(selector);
      return Array.prototype.slice.call(tplElements);
    });
    return (_Array$prototype$conc = Array.prototype.concat).call.apply(_Array$prototype$conc, _toConsumableArray(nodes));
  };

  // Handlers for DOM ready
  document.addEventListener('DOMContentLoaded', function (e) {
    // DOM element where the Timeline will be attached
    // const dataBlock = visBlock.parentNode.querySelector('[data-js="timeline-data"]');
    // const container = document.querySelector('.tx-timeline[data-tl_id]');
    var points = document.querySelectorAll('.tx-timeline .timeline');

    //----------
    // Segments
    if (document.querySelector('[data-js="timeline-segment"]')) {
      // Make array of points from each segment
      var tplPoints = document.querySelectorAll('[data-js="timeline-segment"]').joinTplNodes('.timeline');

      // Spread points of each segments
      // const pointNode = segment.content.cloneNode(true);
      // const tplPoints = pointNode.querySelectorAll('.timeline');
      var pointsLen = tplPoints.length;
      var timePoints = Array.from(points).map(function (point) {
        var dateTime = point.querySelector('time').getAttribute('datetime');
        return {
          date: new Date(dateTime),
          dateStr: dateTime,
          isBC: Boolean(point.dataset.not_ad)
        };
      });
      var timeTplPoints = tplPoints.map(function (point, i) {
        return {
          index: i,
          date: new Date(point.dataset.date),
          dateStr: point.dataset.date,
          isBC: Boolean(point.dataset.not_ad)
        };
      });
      timeTplPoints.sort(function (prev, next) {
        return next.isBC ? next.date - prev.date : prev.date - next.date;
      });
      var pointDateStr, pointTime, iterate;
      for (var i = 0, m = 0; i < pointsLen; i++) {
        pointDateStr = points[i].querySelector('time').getAttribute('datetime');
        pointTime = new Date(pointDateStr);
        console.log(i, pointDateStr, 'm=' + m, 'n=' + n);
        iterate = true;
        while (iterate) {
          if (timePoints[i].isBC) {
            console.log('\nTime point is B. C.');
            if (timeTplPoints[m].isBC && timeTplPoints[m].date >= pointTime) {
              console.log('Both are BC. %cAppend', 'color:green;');
              points[i].before(tplPoints[m]);
              m++;
            } else {
              console.log('Both are BC');
              iterate = false;
            }
          } else {
            console.log('\nTime point is A. D.');
            if (!timeTplPoints[m]) {
              return;
            }
            if (timeTplPoints[m].isBC) {
              console.log('Tpl point is BC. %cAppend', 'color:green;');
              points[i].before(tplPoints[m]);
              m++;
            } else if (timeTplPoints[m].date <= pointTime) {
              console.log('Both are AD. %cAppend', 'color:green;');
              points[i].before(tplPoints[m]);
              m++;
            } else {
              console.log('Both are AD');
              iterate = false;
            }
          }
        }
      }
      console.log('end');
    }
  });

}));
//# sourceMappingURL=setup.js.map
