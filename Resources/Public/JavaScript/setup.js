(function (factory) {
  typeof define === 'function' && define.amd ? define(factory) :
  factory();
})((function () { 'use strict';

  /**
   * Timeline setup script
   */

  // Handlers for DOM ready
  document.addEventListener('DOMContentLoaded', function (e) {
    // DOM element where the Timeline will be attached
    var container = document.querySelector('.tx-timeline');
    var points = container.querySelectorAll('.timeline-point');
    // NOTE For this time handle only vertical and horizontal timelines
    var timelineType = container.classList.contains('timeline-vertical') ? 'v' : container.classList.contains('timeline-horizontal') ? 'h' : 'n';
    var timeline;
    if (timelineType === 'v') {
      timeline = new tl.VerticalTimeline(timelineType, container, points);
    }

    //----------
    // Segments
    if (document.querySelector('[data-js="timeline-segment"]')) {
      // Make array of points from each segment
      document.querySelectorAll('[data-js="timeline-segment"]').joinTplNodes('.timeline-point');

      // Register widgets and set respective order to work them properly
      var tlWidgets = {};
      if (container.querySelector('[data-tl_widget]')) {
        // Go through each widget and populate 'tlWidgets' object with widgets
        // Thus sill be possible to set needed options for particular widgets.
        Array.from(container.querySelectorAll('[data-tl_widget]')).forEach(function (widgetElem) {
          switch (widgetElem.dataset.js) {
            case 'widget-accordion':
              tlWidgets.accordion = new tlw.WidgetCollapsible(widgetElem, 0, '.widget-accordion');
              break;
            case 'widget-form-filter':
              if (timelineType === 'v') {
                tlWidgets.formFilter = new tlw.WidgetFormFilter(widgetElem, 1, {
                  timelineType: timelineType,
                  container: container,
                  pointsLen: points.length
                });
              }
              // else if (timelineType === 'h') {
              //   tlWidgets.formFilter = 'init after vis.Timeline';
              // }
              break;
            case 'widget-scrollspy':
              tlWidgets.scrollspy = new tlw.WidgetScrollspy(widgetElem, 1);
              break;
          }
        });

        // widgets.options = {
        //   ajaxURL: 
        // }
      }

      // Use widgets methods (initialize)
      if (timelineType === 'v') {
        if (tlWidgets.accordion) {
          tlWidgets.accordion.init(container, points, '[data-js="timeline-segment"]');
        } else {
          timeline.spreadDerivedSegments(container.querySelectorAll('[data-js="timeline-segment"]'));
        }
        if (tlWidgets.formFilter) {
          tlWidgets.formFilter.init();
        }
      }
    }
    if (timelineType === 'h' && container.dataset.url) {
      // DOM element where the Timeline will be attached
      var visBlock = document.querySelector('[data-js="timeline-horizontal"]');
      if (!visBlock) {
        console.warn('%cPoints are not exist for the Timeline yet, or error in Timeline found. Please check settings.', 'padding:15px;font-size:12px;font-weight:bold;');
        return;
      }
      var pointContainer = container.querySelector('.timeline');
      var handleClickVisItem = function handleClickVisItem(e) {
        e.preventDefault();
        var pointId = getClosest(e.target, '.vis-point').dataset.id;
        var templateElem = visBlock.nextElementSibling;
        var pointNode = templateElem.content.cloneNode(true);
        var pointBlock = pointNode.querySelector('.timeline[data-point_id="' + pointId + '"]');
        pointContainer.innerHTML = pointBlock.innerHTML;
      };
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
                  id: "tl-" + container.dataset.tl_id + "-" + point.id,
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
              timeline = new tl.HorizontalTimeline(timelineType, container, new vis.Timeline(visBlock, items, options));
              var tlwFormFilter = document.querySelector('[data-js="widget-form-filter"]');
              if (tlwFormFilter) {
                new tlw.WidgetFormFilter(tlwFormFilter, 1, {
                  timelineType: 'h',
                  container: container,
                  pointsLen: dataVisual.length,
                  // pass vis.js Timeline instance to use vis.Timeline API
                  timelineVis: timeline.visTimeline
                }).init();
              }

              // Open content block by clicked point
              // container.querySelectorAll('.vis-item-content').forEach(function (point) {
              //   point.addEventListener('click', handleClickVisItem);
              // });
            }
          } catch (e) {
            console.error(e);
          }
        }
      };
      xhr.open('GET', container.dataset.url);
      xhr.send();
    }
  });

}));
