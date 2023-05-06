(function (factory) {
  typeof define === 'function' && define.amd ? define(factory) :
  factory();
})((function () { 'use strict';

  function getClosest(elem, selector) {
    if (!elem.matches && !elem.mozMatchesSelector) {
      return null;
    }
    while (elem !== document.body) {
      elem = elem.parentElement;
      if (elem.matches) {
        if (elem.matches(selector)) {
          return elem;
        }
      } else if (elem.mozMatchesSelector) {
        if (elem.mozMatchesSelector(selector)) {
          return elem;
        }
      }
    }
  }
  document.addEventListener('DOMContentLoaded', function (e) {
    // DOM element where the Timeline will be attached
    var visBlock = document.querySelector('[data-js="timeline-horizontal"]');
    if (!visBlock) {
      console.warn('%cPoints are not exist for the Timeline yet, or error in Timeline found. Please check settings.', 'padding:15px;font-size:12px;font-weight:bold;');
      return;
    }

    // const dataBlock = visBlock.parentNode.querySelector('[data-js="timeline-data"]');
    var container = document.querySelector('.tx-timeline[data-tl_id]');
    var pointContainer = container.querySelector('.timeline');
    var dataId = container.dataset.tl_id;
    var ajaxURL = container.dataset.url;
    var handleClickVisItem = function handleClickVisItem(e) {
      e.preventDefault();
      var pointId = getClosest(e.target, '.vis-point').dataset.id;
      var templateElem = visBlock.nextElementSibling;
      var pointNode = templateElem.content.cloneNode(true);
      var pointBlock = pointNode.querySelector('.timeline[data-point_id="' + pointId + '"]');
      pointContainer.innerHTML = pointBlock.innerHTML;
    };

    // GET data by action

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4) {
        try {
          var data = JSON.parse(xhr.responseText);
          console.log('Status ' + xhr.readyState);
          if (data.points && data.points.length) {
            // Prepare visual part
            var dataVisual = [];
            data.points.forEach(function (point, i) {
              var dateFormat = dayjs(point.date.date, "YYYY-MM-DD").format('DD MMM YYYY');
              dataVisual.push({
                id: "tl-" + dataId + "-" + point.id,
                type: 'point',
                start: dateFormat,
                content: "<strong>" + point.title + "</strong><span>" + dateFormat + "</span>"
              });
            });

            // Create a DataSet (allows two way data-binding)
            var items = new vis.DataSet(dataVisual);

            // Configuration for the Timeline
            var options = {
              dataAttributes: ['id'],
              height: 200,
              groupHeightMode: 'fixed'
            };

            // Create a Timeline
            var timeline = new vis.Timeline(visBlock, items, options);

            // Open content block by clicked point
            container.querySelectorAll('.vis-item-content').forEach(function (point) {
              point.addEventListener('click', handleClickVisItem);
            });
          }
        } catch (e) {
          console.error(e);
        }
      }
    };

    // Test using middleware:
    // xhr.open('GET', 'http://localhost:8000/index.php?tlinfo=true&pid=' + dataId);

    xhr.open('GET', ajaxURL);
    xhr.send();
  });

}));
//# sourceMappingURL=setup.umd.js.map
